<?php

namespace App\Providers;

use App\Models\Option;
use App\Models\Category;
use App\Models\Celebration;
use App\Models\Shop;
use Illuminate\Support\ServiceProvider;

use View;

class OptionsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $all_options = Option::all();
            $categoryes = Category::all();

            $shops = Shop::all();

            $opt = [];

            foreach ($all_options as $otion) {
                $opt[$otion['name']] = $otion['value'];
            }

            $struct_cat = [];

            foreach ($categoryes as $item)
                if (!isset($item->parent))
                {
                    $struct_cat[$item->id]['main'] = $item;
                    $struct_cat[$item->id]['sub'] = [];
                }

            foreach ($categoryes as $item)
                if (isset($item->parent))
                {
                    $struct_cat[$item->parent]['sub'][] = $item;
                }

            View::share('options', $opt);
            View::share('all_cat', $struct_cat);
            View::share('shops', $shops);
        });
    }
}
