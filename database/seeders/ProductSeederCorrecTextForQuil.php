<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


use Illuminate\Support\Facades\Storage;

use App\Models\Product;

class ProductSeederCorrecTextForQuil extends Seeder
{


    protected function minify_html($buffer) {

        $buffer = preg_replace(
            array(
                '/\>[^\S ]+/s',
                '/[^\S ]+\</s',
                '/(\s)+/s',
                '/<!--(?![^<]*noindex)(.*?)-->/'
            ),
            array(
                '>',
                '<',
                '\\1',
                ''
            ),
            $buffer
        );

        return $buffer;

    }

    protected function list_minifi($str) {
        $rez = str_replace("<ul> <li>", "<ul><li>", $str);
        $rez = str_replace("</li> </ul>", "</li></ul>", $rez);
        $rez = str_replace("</li> <li>", "</li><li>", $rez);
        return $rez;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $all_product = Product::all();

        $r_list = ["<ul> <li>", "</li> </ul>", "</li> <li>"];

        foreach ($all_product as $item) {
            print_r($item->title."\n\r");
            // $item->specification = str_replace($r_list, "", $item->specification);
            $item->specification = $this->list_minifi($this->minify_html($item->specification));
            $item->description = $this->list_minifi($this->minify_html($item->description));
            $item->short_description = $this->list_minifi($this->minify_html($item->short_description));
            $item->save();
        }
    }
}
