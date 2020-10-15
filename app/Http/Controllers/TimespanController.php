<?php

namespace App\Http\Controllers;

use App\Exports\OrderTimeSpan;
use App\SalesOrder;
use App\Month;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TimespanController extends Controller
{
    public function index()
    {
        $year = '2020';
        $months = Month::all();
        $data['month'] = 5;
        return view('time_span.index', compact('year', 'months', 'data'));
    }

    public function so_time_span(Request $request)
    {
        $timeFrame = [
            'months' => $request->get('months'),
            'year' => $request->get('year')];
        //  dd($timeFrame);

        $lastMonth = end($timeFrame['months']);
        //   dd($lastMonth);
        $dateFrom = substr(new Carbon($timeFrame['year'] . '-' . $timeFrame['months'][0] . '-01'), 0, 10);
        //    echo $dateFrom . "<br>";
        $dateTo = substr(new Carbon($timeFrame['year'] . '-' . $lastMonth . '-01'), 0, 10);
        $lastDay = date('t', strtotime($dateTo));
        $dateTo = substr(new Carbon($timeFrame['year'] . '-' . $lastMonth . '-' . $lastDay), 0, 10);
        //   echo $dateTo . "<br>";
        //     dd("xxx");
        $sales_orders = SalesOrder::select(DB::raw('*,EXTRACT(YEAR_MONTH FROM salesorders.confirmation_date) as summary_year_month'))
            ->whereBetween('salesorders.confirmation_date', [$dateFrom, $dateTo])
            ->has('stock_picking')
            ->with('stock_picking')
            ->orderBy('summary_year_month')
            ->get();

//dd($sales_orders->count());

        /*        $sales_orders = SalesOrder::where('created_at', '>', '2020-05-01')->orderBy('confirmation_date', 'desc')->with('stock_picking')->has('stock_picking')->get();
                // dd($sales_orders);*/
        $all_spans = [];
    //    array_push($all_spans,['dateFrom' => $dateFrom, 'dateTo' => $dateTo]);
        foreach ($sales_orders as $sales_order) {
            //  dd($sales_order);
            $begin_time = $sales_order->confirmation_date;
            $end_time = $sales_order->stock_picking->date_done;

            $start = new Carbon($sales_order->confirmation_date);
            $start = $start->addHours(5);

            $validated_at = new Carbon($sales_order->validation_done->date_done);
            $validated_at = $validated_at->subHours(7);
            $spanValidated = $start->diff($validated_at)->format('%d days %H:%I:%S');
            $picked_at = new Carbon($sales_order->picking_done->date_done);
            $picked_at = $picked_at->subHours(7);
            $spanPicked = $start->diff($picked_at)->format('%d days %H:%I:%S');

            if ($sales_order->validation_done->date_done > '0000-00-00 00:00:00') {
                     //         echo $sales_order->sales_order . " : " . $start . " - " . $validated_at . " = " . $spanValidated . "<br>";

                 //   echo $sales_order->sales_order . ' ' . $sales_order->confirmation_date  . "<br>";
                array_push($all_spans, [
                    'sales_order' => $sales_order->sales_order,
                    'start' => $start,
                    'status_validated' => $sales_order->validation_done->name,
                    'validated_at' => $validated_at,
                    'span_validated' => $spanValidated,
                    'status_picked' => $sales_order->picking_done->name,
                    'picked_at' => $picked_at,
                    'span_picked' => $spanPicked,
                ]);
            }
        }

     //   dd($all_spans);
        session(['spans' => $all_spans]);
        session(['dateFrom' => $dateFrom]);
        session(['dateTo' => $dateTo]);

        return view('time_span.results', compact('all_spans', 'dateFrom', 'dateTo'));
    }

    public function export_so_time_span(Request $request)
    {
        $all_spans = $request->session()->get('spans');
        $dateFrom = $request->session()->get('dateFrom');
        $dateTo = $request->session()->get('dateTo');

        return Excel::download(new OrderTimeSpan($all_spans,$dateFrom,$dateTo), 'Salesorders Times from Comfirmation to Validation .xlsx');
    }

}

