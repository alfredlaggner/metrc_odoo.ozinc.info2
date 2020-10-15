<?php

namespace App\Http\Controllers;

use App\SalesOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AutomaticManifests extends Controller
{
    public function index()
    {

        $new_salesorders = SalesOrder::where('is_manifest_created', '=', 0)
         //   ->where('created_at','>=',  date('Y-m-d', strtotime("-2 months")))
          //   where('invoice_status', '=', "to invoice")
        //    ->orderBy('created_at', 'desc')
            ->get();
        //where('updated_at','<=',  date('Y-m-d', strtotime("-20 days")))
          echo date('Y-m-d', strtotime("-20 days"));
        foreach ($new_salesorders as $new_salesorder) {
            echo $new_salesorder->sales_order . "<br>";
            echo $new_salesorder->create_date . "<br>";
        }
        echo $new_salesorders->count();
    }
}
