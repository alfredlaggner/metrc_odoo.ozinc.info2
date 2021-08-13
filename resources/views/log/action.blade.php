@extends('layouts.app')
@section('title', 'Driver Logs')
<style>
    td.qty {
        text-align: right;
    }

    td.qty-changed {
        text-align: right;
        color: #e3342f;
    }
</style>
@section('content')
    <div class="container">
        <br/>
        @if (\Session::has('success'))
            <div class="alert alert-success">
                <p>{{ \Session::get('success') }}</p>
            </div><br/>
        @endif
        @foreach($logs as $log)
            <div class="card" >
                <div class="card-header">
                    <h5 class="card-title text-center">{{$log->custome_name}}</h5>
                    <h6 class="card-subtitle mb-2 text-muted text-center">SO{{$log->saleinvoice_id}}</h6>
                </div>
                <div class="card-body">
                    <div class="mt-3">
                        <p class="card-text"><b>Salesperson:</b> {{$log->salesperson_name}}
                            <b>Email:</b> {{$log->salesperson_email}} {{$log->id}}</p>
                        <p class="card-text"><b>Driver:</b> {{$log->driver->first_name}} {{$log->driver->last_name}}</p>
                        <p class="card-text"><b>Vehicle:</b> {{$log->vehicle->plate}}</p>
                    </div>
                    <hr>
                    <div class="">

                        <h5 class="mb-3"><b>SO{{$log->saleinvoice_id}}</b></h5>

                        <table class="table table-bordered table-hover tablesaw tablesaw-stack" id="sales_order_table">
                            <thead>
                            <tr>
                                <th scope="col">Number</th>
                                <th scope="col">Name</th>
                                <th scope="col">Unit</th>
                                <th scope="col">Qty</th>
                                <th scope="col">New</th>
                                <th scope="col">Price</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($log->order_lines as $salesorder)
                                @php
                                    $total_price = 0;
                                     $quantity = $salesorder->quantity;
                                      if ($salesorder->quantity_corrected){
                                         $quantity = $salesorder->quantity_corrected;
                                         $total_price = $quantity * $salesorder->unit_price;
                                      }
                                        else {
                                            $quantity = $salesorder->quantity;
                                            $total_price = $quantity * $salesorder->unit_price;
                                        }
                                @endphp
                                <tr>
                                    <td>{{$salesorder->code}}</td>
                                    <td>{{$salesorder->name}}</td>
                                    <td class="qty">{{$salesorder->unit_price}}</td>
                                    <td class="qty">{{$salesorder->quantity}}</td>
                                    <td class="qty-changed">{{$salesorder->quantity_corrected}}</td>
                                    <td class="qty">{{number_format($total_price , 2 ,"." ,"," )}}</td>
                                    {{--
                                                                     @php  dd($total); @endphp
                                    --}}
                                    <td>
                                        <a href="{{route('detail-edit', ['id' => $salesorder->id, 'total' => $total, 'log_id' => $log->id])}}"
                                           class="btn btn-warning">Edit</a></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td>Corrected Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="qty">{{number_format($total + $total * 0.24 , 2 ,"." ,"," )}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-between">
                        <div class="col">
                            <a href="{{route('create_notes', [$log->id,$log->saleinvoice_id,$total])}}"
                               class="btn btn-sm btn-warning">Edit Report</a>
                        </div>
                        <div class="col">
                            <a href="{{route('display_notes', [$log->id,$log->saleinvoice_id])}}"
                               class="btn btn-sm btn-primary">View Report</a>
                        </div>
                        <div class="col">
                            <a href="{{route('print_so_report', [$log->id,$log->saleinvoice_id,$total])}}"
                               class="btn btn-sm btn-primary">Print Report</a>
                        </div>
                        <div class="col">
                            <a href="{{route('delivery_done', [$log->id])}}"
                               class="btn btn-sm btn-success">Delivery Done</a>
                        </div>
                        <div class="col">
                            @if ( ! $viewer)
                                <a href="{{route('go-home')}}"
                                   class="btn btn-outline-primary btn-sm">Home</a>
                            @else
                                <a href="{{route('go-viewer')}}"
                                   class="btn btn-outline-primary btn-sm">Home</a>
                            @endif
                        </div>

                    </div>

                </div>
            </div>
        @endforeach
    </div>

@endsection
