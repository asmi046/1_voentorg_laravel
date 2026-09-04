<?php

namespace Tests\Unit;

use Database\Seeders\ProductSqlSeeder;
use PHPUnit\Framework\TestCase;

class ProductSqlSeederTest extends TestCase
{
    public function test_prepare_sql_for_import_rewrites_insert_statements_and_keeps_html_content(): void
    {
        $seeder = new ProductSqlSeeder;
        $reflection = new \ReflectionClass($seeder);
        $method = $reflection->getMethod('prepareSqlForImport');
        $method->setAccessible(true);

        $sql = "INSERT INTO `products` (`title`, `description`) VALUES ('demo', '<p>Price: 10&nbsp;руб.;</p>');\nREPLACE INTO `product_prices` (`product_id`) VALUES (1);";

        $prepared = $method->invoke($seeder, $sql);

        $this->assertStringContainsString('INSERT IGNORE INTO `products`', $prepared);
        $this->assertStringContainsString('INSERT IGNORE INTO `product_prices`', $prepared);
        $this->assertStringContainsString('<p>Price: 10&nbsp;руб.;</p>', $prepared);
    }
}
