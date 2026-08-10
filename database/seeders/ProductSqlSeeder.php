<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ProductSqlSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting SQL seeder...');

        $this->clearTargetTables();

        $sqlFiles = [
            public_path('tmp/products.sql'),
            public_path('tmp/product_prices.sql')
        ];

        foreach ($sqlFiles as $file) {
            if (File::exists($file)) {
                $this->command->info("Processing file: {$file}");

                $sql = File::get($file);

                $this->executeSql($sql);
            } else {
                $this->command->warn("File not found: {$file}");
            }
        }

        $this->command->info('SQL seeder completed successfully!');
    }

    private function clearTargetTables(): void
    {
        $tables = ['product_images', 'product_prices', 'products'];

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($tables as $table) {
                if (!Schema::hasTable($table)) {
                    continue;
                }

                try {
                    DB::table($table)->truncate();
                    $this->command->info("Cleared table: {$table}");
                } catch (\Throwable $e) {
                    $this->command->warn("Could not clear table {$table}: {$e->getMessage()}");
                }
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function executeSql(string $sql): void
    {
        $sql = $this->prepareSqlForImport($sql);

        $statements = $this->parseSqlStatements($sql);

        foreach ($statements as $statement) {
            $statement = trim($statement);

            if ($statement === '') {
                continue;
            }

            if (preg_match('/^(DROP|CREATE|ALTER|LOCK|UNLOCK|SET)\b/i', $statement)) {
                continue;
            }

            if ($this->isInsertLikeStatement($statement)) {
                $this->importInsertStatement($statement);
                continue;
            }

            try {
                DB::unprepared($statement . ';');
            } catch (\Exception $e) {
                $this->command->error("Error executing SQL statement: {$e->getMessage()}");
                $this->command->line('Statement: ' . substr($statement, 0, 120) . '...');
            }
        }
    }

    private function prepareSqlForImport(string $sql): string
    {
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*[\s\S]*?\*\//', '', $sql);
        $sql = preg_replace('/^SET\s+[^;]+;$/mi', '', $sql);
        $sql = preg_replace('/^START\s+TRANSACTION;$/mi', '', $sql);
        $sql = preg_replace('/^COMMIT;$/mi', '', $sql);
        $sql = preg_replace('/\b(INSERT|REPLACE)\s+INTO\s+`?(product_prices|products)`?/i', 'INSERT IGNORE INTO `\2`', $sql);

        return $sql;
    }

    private function parseSqlStatements(string $sql): array
    {
        $statements = [];
        $currentStatement = '';
        $delimiter = ';';
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $escapeNext = false;
        $inLineComment = false;
        $inBlockComment = false;
        $parenCount = 0;

        for ($i = 0, $length = strlen($sql); $i < $length; $i++) {
            $char = $sql[$i];
            $nextChar = $i + 1 < $length ? $sql[$i + 1] : '';

            if ($inLineComment) {
                $currentStatement .= $char;

                if ($char === "\n") {
                    $inLineComment = false;
                }

                continue;
            }

            if ($inBlockComment) {
                $currentStatement .= $char;

                if ($char === '*' && $nextChar === '/') {
                    $currentStatement .= $nextChar;
                    $i++;
                    $inBlockComment = false;
                }

                continue;
            }

            if ($inSingleQuote) {
                $currentStatement .= $char;

                if ($escapeNext) {
                    $escapeNext = false;
                    continue;
                }

                if ($char === '\\') {
                    $escapeNext = true;
                    continue;
                }

                if ($char === "'") {
                    if ($nextChar === "'") {
                        $currentStatement .= $nextChar;
                        $i++;
                    } else {
                        $inSingleQuote = false;
                    }
                }

                continue;
            }

            if ($inDoubleQuote) {
                $currentStatement .= $char;

                if ($escapeNext) {
                    $escapeNext = false;
                    continue;
                }

                if ($char === '\\') {
                    $escapeNext = true;
                    continue;
                }

                if ($char === '"') {
                    $inDoubleQuote = false;
                }

                continue;
            }

            if ($char === '-' && $nextChar === '-') {
                $currentStatement .= $char . $nextChar;
                $i++;
                $inLineComment = true;
                continue;
            }

            if ($char === '/' && $nextChar === '*') {
                $currentStatement .= $char . $nextChar;
                $i++;
                $inBlockComment = true;
                continue;
            }

            if ($char === "'") {
                $inSingleQuote = true;
                $currentStatement .= $char;
                continue;
            }

            if ($char === '"') {
                $inDoubleQuote = true;
                $currentStatement .= $char;
                continue;
            }

            if ($char === '(') {
                $parenCount++;
            }

            if ($char === ')' && $parenCount > 0) {
                $parenCount--;
            }

            $currentStatement .= $char;

            if ($char === $delimiter && $parenCount === 0) {
                $statement = trim($currentStatement);

                if (!empty($statement)) {
                    $statements[] = rtrim($statement, ';');
                }

                $currentStatement = '';
            }
        }

        if (!empty(trim($currentStatement))) {
            $statements[] = trim($currentStatement);
        }

        return array_values(array_filter($statements, static function (string $statement): bool {
            return trim($statement) !== '';
        }));
    }

    private function isInsertLikeStatement(string $statement): bool
    {
        return preg_match('/^\s*(INSERT(?:\s+IGNORE)?|REPLACE)\s+INTO\s+/i', $statement) === 1;
    }

    private function importInsertStatement(string $statement): void
    {
        $table = $this->extractTableName($statement);

        if ($table === null || !in_array($table, ['products', 'product_prices'], true)) {
            return;
        }

        $columnNames = $this->extractColumnNames($statement);
        $rows = $this->extractRowValues($statement);

        if ($columnNames === [] || $rows === []) {
            return;
        }

        foreach ($rows as $row) {
            $payload = [];

            foreach ($columnNames as $index => $columnName) {
                $payload[$columnName] = $row[$index] ?? null;
            }

            try {
                DB::table($table)->insertOrIgnore($payload);
            } catch (\Throwable $e) {
                $this->command->error("Error importing row into {$table}: {$e->getMessage()}");
            }
        }
    }

    private function extractTableName(string $statement): ?string
    {
        if (preg_match('/^\s*(?:INSERT(?:\s+IGNORE)?|REPLACE)\s+INTO\s+`?([a-zA-Z0-9_]+)`?/i', $statement, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    private function extractColumnNames(string $statement): array
    {
        if (preg_match('/^\s*(?:INSERT(?:\s+IGNORE)?|REPLACE)\s+INTO\s+`?[a-zA-Z0-9_]+`?\s*\((.*?)\)\s*VALUES/i', $statement, $matches) !== 1) {
            return [];
        }

        $columns = array_map(static function (string $column): string {
            return trim($column, " `\t\n\r");
        }, preg_split('/\s*,\s*/', $matches[1]) ?: []);

        return array_values(array_filter($columns, static function (string $column): bool {
            return $column !== '';
        }));
    }

    private function extractRowValues(string $statement): array
    {
        if (preg_match('/VALUES\s*(.+)$/is', $statement, $matches) !== 1) {
            return [];
        }

        $body = trim($matches[1]);
        $body = rtrim($body, ';');
        $rows = [];
        $rowFragments = [];
        $current = '';
        $depth = 0;
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $escapeNext = false;

        for ($i = 0, $length = strlen($body); $i < $length; $i++) {
            $char = $body[$i];
            $nextChar = $i + 1 < $length ? $body[$i + 1] : '';

            if ($inSingleQuote) {
                $current .= $char;

                if ($escapeNext) {
                    $escapeNext = false;
                    continue;
                }

                if ($char === '\\') {
                    $escapeNext = true;
                    continue;
                }

                if ($char === "'") {
                    if ($nextChar === "'") {
                        $current .= $nextChar;
                        $i++;
                    } else {
                        $inSingleQuote = false;
                    }
                }

                continue;
            }

            if ($inDoubleQuote) {
                $current .= $char;

                if ($escapeNext) {
                    $escapeNext = false;
                    continue;
                }

                if ($char === '\\') {
                    $escapeNext = true;
                    continue;
                }

                if ($char === '"') {
                    $inDoubleQuote = false;
                }

                continue;
            }

            if ($char === "'") {
                $inSingleQuote = true;
                $current .= $char;
                continue;
            }

            if ($char === '"') {
                $inDoubleQuote = true;
                $current .= $char;
                continue;
            }

            if ($char === '(') {
                $depth++;
                $current .= $char;
                continue;
            }

            if ($char === ')') {
                if ($depth > 0) {
                    $depth--;
                }

                $current .= $char;

                if ($depth === 0) {
                    $rowFragments[] = trim($current);
                    $current = '';
                }

                continue;
            }

            if ($depth === 0 && $char === ',') {
                continue;
            }

            $current .= $char;
        }

        foreach ($rowFragments as $rowFragment) {
            if (trim($rowFragment) !== '') {
                $rows[] = $this->parseRowValuesPayload($rowFragment);
            }
        }

        return array_values(array_filter($rows, static function (array $row): bool {
            return $row !== [];
        }));
    }

    private function parseRowValuesPayload(string $row): array
    {
        $row = trim($row);
        $row = trim($row, '()');

        $values = [];
        $current = '';
        $depth = 0;
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $escapeNext = false;

        for ($i = 0, $length = strlen($row); $i < $length; $i++) {
            $char = $row[$i];
            $nextChar = $i + 1 < $length ? $row[$i + 1] : '';

            if ($inSingleQuote) {
                $current .= $char;

                if ($escapeNext) {
                    $escapeNext = false;
                    continue;
                }

                if ($char === '\\') {
                    $escapeNext = true;
                    continue;
                }

                if ($char === "'") {
                    if ($nextChar === "'") {
                        $current .= $nextChar;
                        $i++;
                    } else {
                        $inSingleQuote = false;
                    }
                }

                continue;
            }

            if ($inDoubleQuote) {
                $current .= $char;

                if ($escapeNext) {
                    $escapeNext = false;
                    continue;
                }

                if ($char === '\\') {
                    $escapeNext = true;
                    continue;
                }

                if ($char === '"') {
                    $inDoubleQuote = false;
                }

                continue;
            }

            if ($char === "'") {
                $inSingleQuote = true;
                $current .= $char;
                continue;
            }

            if ($char === '"') {
                $inDoubleQuote = true;
                $current .= $char;
                continue;
            }

            if ($char === '(') {
                $depth++;
                $current .= $char;
                continue;
            }

            if ($char === ')' && $depth > 0) {
                $depth--;
                $current .= $char;
                continue;
            }

            if ($char === ',' && $depth === 0) {
                $values[] = $this->normalizeValueToken(trim($current));
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if (trim($current) !== '') {
            $values[] = $this->normalizeValueToken(trim($current));
        }

        return $values;
    }

    private function normalizeValueToken(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (strtoupper($value) === 'NULL') {
            return null;
        }

        if (str_starts_with($value, "'") && str_ends_with($value, "'")) {
            $inner = substr($value, 1, -1);
            $inner = str_replace("''", "'", $inner);
            $inner = preg_replace("/\\\\'/", "'", $inner) ?? $inner;

            return $inner;
        }

        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float) $value : (int) $value;
        }

        return $value;
    }
}
