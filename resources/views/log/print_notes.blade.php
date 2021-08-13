<div class="container">
    <div class="card" style="width: 100%">
        <div class="card-header">
            <h3 class="text-center card-title">Delivery Report for SO{{$log->saleinvoice_id }}</h3>
        </div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h5>{{$log->customer->name}}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{$log->customer->license}}</h6>
                        <p class="card-text"><b>Sold by:</b> {{$log->salesperson->name}}
                        <p class="card-text"><b>Driver:</b> {{$log->driver->first_name}} {{$log->driver->last_name}}
                        </p>
                        <p class="card-text"><b>Vehicle:</b> {{$log->vehicle->plate}}</p>

                    </div>
                    <div class="col">

                        <p><b>Delivered On:</b> {{$log->delivered_at}}</p>
                        <p><b>Total Price:</b> ${{number_format($log->total , 2 ,"." ,"," )}}</p>
                        <p><b>Collected:</b>&nbsp;&nbsp;${{number_format($log->collected , 2 ,"." ,"," ) }}
                        </p>
                        <p><b>Driver Comments:</b>
                            @for ($i = 0; $i < count($selected_notes); $i++)
                                {{$selected_notes[$i]}},
                            @endfor
                        </p>
                        <p><b>Notes:</b> {{$log->notes}}</p>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">

                    <div class="text-center">
                        <p><b>Corrected Sales Lines: </b></p>
                        <table class="table-bordered" id="sales_order_table">
                            <thead>
                            <tr>
                                <th>Number</th>
                                <th>Name</th>
                                <th>Qty</th>
                                <th>New</th>
                                <th>Price</th>
                                <th>Comments</th>
                                <th>Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($log->order_lines as $order_line)
                                @if ($order_line->quantity_corrected)
                                    @php
                                        $quantity = $order_line->quantity_corrected ? $order_line->quantity_corrected : $order_line->quantity;
                                        $total_price = $quantity * $order_line->unit_price;
                                    @endphp
                                    <tr>
                                        <td>{{$order_line->code}}</td>
                                        <td>{{$order_line->name}}</td>
                                        <td class="qty">  {{$order_line->quantity}}</td>
                                        <td class="qty-changed">  {{$order_line->quantity_corrected}}</td>
                                        <td class="qty">  {{number_format($total_price , 2 ,"." ,"," )}}</td>
                                        <td>  {{$order_line->line_note_id}}
                                        <td>  {{$order_line->note}}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-4"></div>
            </div>
        </div>
    </div>
</div>
