<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrderTimeSpan implements FromView
{
    protected $all_spans;
    protected $dateFrom;
    protected $dateTo;

    public function __construct(array $all_spans, $dateFrom, $dateTo )
    {
        $this->all_spans = $all_spans;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function view(): View
    {
        $all_spans = $this->all_spans;
        $dateFrom = $this->dateFrom;
        $dateTo = $this->dateTo;
        return view('exports.odoo_products',compact('all_spans','dateFrom','dateTo'));
    }
}
