<?php

namespace App\Http\Controllers;

use App\MetrcItem;
use App\MetrcPackage;
use App\Product;
use Illuminate\Http\Request;

class MetrcOdooController extends Controller
{
    public function getProducts()
    {
        $products = Product::has('item1', NULL)->get();
        $products_count = $products->count();
        //     $items = MetrcItem::whereNull('product_id')->get();
        $items = MetrcItem::has('product1')->orderby('product_id', 'desc')->get();

        return view('metrc.sync_products', compact('products', 'items', 'products_count'));
    }

    public function synchronize(Request $request)
    {
        dd($request);
    }

    public function related_product($metrc_id, $metrc_product_name)
    {
        $products = Product::search($metrc_product_name)->get();
        $products_count = count($products);
        return view('metrc.odoo_products', compact('products', 'products_count', 'metrc_product_name', 'metrc_id'));
    }

    public function selected_product($ext_id, $metrc_id)
    {
        $item = MetrcItem::where('metrc_id', $metrc_id)->first();
        //   dd($item);
        $item->product_id = $ext_id;
        $item->save();

        $product = Product::where('ext_id', $ext_id)->first();
        $product->metrc_id = $metrc_id;
        $product->save();

        $package = MetrcPackage::where('item', 'like', $item->name)->first();
        if ($package) {
            $package->product_id = $item->product_id;
            $package->save();
            echo "Item " . $item->name . " updated with " . $item->product_id . "<br>";
        } else {
            echo "Item " . $item->name . " not fount. " . "<br>";
        }
        echo "product ext_id= " . $ext_id . "<br>";
        echo "product metrc_id= " . $metrc_id . "<br>";

        dd($product);
//    return view('metrc.odoo_products', compact('products', 'products_count','metrc_product_name'));
    }

    public function update_all_items()
    {
        $items = MetrcItem::all();
        foreach ($items as $item) {
            $package = MetrcPackage::where('item', 'like', $item->name)->orderby('id', 'desc')->first();
            if ($package) {
                $item->product_id = $package->product_id;
                $item->update();
                echo "$item->name" . " updated with " . $package->product_id . "<br>";
            }

        }
        dd("done");
        //   dd($item);
        $item->product_id = $ext_id;
        $item->save();

        $product = Product::where('ext_id', $ext_id)->first();
        $product->metrc_id = $metrc_id;
        $product->save();

        $package = MetrcPackage::where('item', 'like', $item->name)->first();
        if ($package) {
            $package->product_id = $item->product_id;
            $package->save();
            echo "Item " . $item->name . " updated with " . $item->product_id . "<br>";
        } else {
            echo "Item " . $item->name . " not fount. " . "<br>";
        }
        echo "product ext_id= " . $ext_id . "<br>";
        echo "product metrc_id= " . $metrc_id . "<br>";

        dd($product);
//    return view('metrc.odoo_products', compact('products', 'products_count','metrc_product_name'));
    }
}
