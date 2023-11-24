<?php

namespace App\Filters;

class ProductFilter extends QueryFilter {





    // public function order($order) {
    //         if ($order == "Сначала дешевые") $this->builder->orderBy('price', 'asc');
    //         if ($order == "Сначала дорогие") $this->builder->orderBy('price', 'desc');
    //         if ($order == "В алфавитном порядке") $this->builder->orderBy('title', 'asc');
    // }

    public function tm($tm) {
            if ($tm != "%") $this->builder->whereIn("tm", $tm);
    }

    public function ve($ve) {
            if ($ve != "%") $this->builder->whereHas('effects',
            function ($query) {
                $query->whereIn('name', $this->request->input('ve'));
            });
    }

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
