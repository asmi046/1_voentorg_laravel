<?php

namespace App\Console\Commands;

use App\Models\ProductPrices;
use Illuminate\Console\Command;

class PriceUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление цен из фалов import0_1.xml, offers0_1.xml';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Составляем таблицу соответствия UID и Штрихкода...");

        $patch = public_path('/shopbase/import0_1.xml');
        $xmlFile = file_get_contents($patch);
        $xmlObject = simplexml_load_string($xmlFile);

        $uid_barcod = [];

        for  ($i = 0; $i < count($xmlObject->Каталог->Товары->Товар); $i++)
        {
            if (isset($xmlObject->Каталог->Товары->Товар[$i]->Ид)) {
                $this->info($i." - ".(string)$xmlObject->Каталог->Товары->Товар[$i]->Штрихкод);
                $uid_barcod[(string)$xmlObject->Каталог->Товары->Товар[$i]->Ид] = (string)$xmlObject->Каталог->Товары->Товар[$i]->Штрихкод;
            }

        }

        $this->info("Найдено: ".count($xmlObject->Каталог->Товары->Товар));

        $patch = public_path('/shopbase/offers0_1.xml');
        $xmlFile = file_get_contents($patch);
        $xmlObject = simplexml_load_string($xmlFile);


        $exist = 0;
        $no_exist = 0;

        for  ($i = 0; $i < count($xmlObject->ПакетПредложений->Предложения->Предложение); $i++)
        {
            if (isset($uid_barcod[(string)$xmlObject->ПакетПредложений->Предложения->Предложение[$i]->Ид]))
            {
                $this->comment("UID: ".(string)$xmlObject->ПакетПредложений->Предложения->Предложение[$i]->Ид);
                $this->comment("Штрихкод: ".$uid_barcod[(string)$xmlObject->ПакетПредложений->Предложения->Предложение[$i]->Ид]);

                $product = ProductPrices::where('sku', $uid_barcod[(string)$xmlObject->ПакетПредложений->Предложения->Предложение[$i]->Ид])->first();


                if ($product)
                {
                    $this->info("Найден продукт: ".$product->product_info->title);
                    $exist++;
                }

                else
                    {
                        $this->error("Продукт не найден");
                        $no_exist++;
                    }

                $this->line("-------------");
            }


        }

        $this->comment("Результаты:");
        $this->info("Товаров на сайте: ".ProductPrices::count());
        $this->info("Найдено: ".$exist);
        $this->info("Не найдено: ".$no_exist);

    }
}
