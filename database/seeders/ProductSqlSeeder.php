<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductSqlSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting SQL seeder...');

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

    private function executeSql(string $sql): void
    {
        $statements = $this->parseSqlStatements($sql);

        foreach ($statements as $statement) {
            if (empty(trim($statement))) {
                continue;
            }

            try {
                DB::unprepared($statement);
            } catch (\Exception $e) {
                $this->command->error("Error executing SQL statement: {$e->getMessage()}");
                $this->command->line("Statement: " . substr($statement, 0, 100) . '...');
            }
        }
    }

    private function parseSqlStatements(string $sql): array
    {
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*[\s\S]*?\*\//', '', $sql);
        $sql = preg_replace('/^SET\s+[^;]+;$/mi', '', $sql);
        $sql = preg_replace('/^START\s+TRANSACTION;$/mi', '', $sql);
        $sql = preg_replace('/^COMMIT;$/mi', '', $sql);
        $sql = str_replace('INSERT INTO `product_prices`', 'REPLACE INTO `product_prices`', $sql);
        $sql = str_replace('INSERT INTO `products`', 'REPLACE INTO `products`', $sql);

        $statements = [];
        $currentStatement = '';
        $delimiter = ';';
        $inString = false;
        $stringChar = '';
        $escapeNext = false;

        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];

            if ($escapeNext) {
                $currentStatement .= $char;
                $escapeNext = false;
                continue;
            }

            if ($char === '\\') {
                $currentStatement .= $char;
                $escapeNext = true;
                continue;
            }

            if (($char === '\'' || $char === '"') && !$escapeNext) {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === $stringChar) {
                    $inString = false;
                    $stringChar = '';
                }
            }

            $currentStatement .= $char;

            if ($char === $delimiter && !$inString) {
                $statements[] = trim($currentStatement);
                $currentStatement = '';
            }
        }

        if (!empty(trim($currentStatement))) {
            $statements[] = trim($currentStatement);
        }

        return $statements;
    }
}
