<?php

namespace App\Filters;

use App\Models\ProductPrices;

class ProductFilter extends QueryFilter {


    public function sort($sort) {
        if (!empty($sort)) {
            $direction = "DESC";
            $field = "id";
            if ($sort === "Сначала дешевые"){
                $direction = "ASC";
                $field = "price";
            }

            if ($sort === "Сначала дорогие"){
                $direction = "DESC";
                $field = "price";
            }

            if ($field === 'id' ) {
                $this->builder->orderBy('id', $direction);
            } else {
                $this->builder
                    ->join('product_prices', 'product_prices.id', '=', 'products.id')
                    ->orderBy('product_prices.price', $direction);
            }

        } else {
            $this->builder->orderBy('id', 'DESC');
        }
            // $this->builder->whereHas('product_prices', function ($query) use ($sort) {
            //     if ($sort === "Сначала дешевые")
            //         $query->orderBy('price', "DESC");
            //     if ($sort === "Сначала дорогие")
            //         $query->orderBy('price', "ASC");
            // });


    }


    // public function order($order) {
    //         if ($order == "Сначала дешевые") $this->builder->orderBy('price', 'asc');
    //         if ($order == "Сначала дорогие") $this->builder->orderBy('price', 'desc');
    //         if ($order == "В алфавитном порядке") $this->builder->orderBy('title', 'asc');
    // }

    // public function subcat($subcat) {
    //     if (!empty($subcat)) $this->builder->whereIn("sub_category", $subcat);
    // }

    // public function osobennosti($osobennosti) {

    //     foreach ($osobennosti as $osb)
    //     {
    //         if ($osb == "Скидка") $this->builder->where("old_price", 0);
    //         if ($osb == "Хит продаж") $this->builder->where("hit", 1);
    //         if ($osb == "Новинки") $this->builder->where("new", 1);
    //     }

    // }
}
