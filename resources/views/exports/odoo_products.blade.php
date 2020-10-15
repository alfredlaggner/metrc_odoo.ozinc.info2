    <div class="container">
        <div class="card">
            <div class='card-header'>
                <h3>Order to Delivery Times from {{$dateFrom}} to {{$dateTo}}</h3>
                <div class="col-auto">
                    @php
                        $serializeArray = serialize($all_spans);
                    @endphp

                    <a class="btn btn-primary" href="{{ route('export_so_time_span') }}">
                        Export to Excel
                    </a>
                </div>

            </div>
            <div class="card card-body">
                <table id="accounts" class="table table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th style="font-weight: normal" class="text-xl-left">Sales Order</th>
                        <th style="font-weight: normal" class="text-xl-left">Confirmed At</th>
                        <th style="font-weight: normal" class="text-xl-left">Picked</th>
                        <th style="font-weight: normal" class="text-xl-left">Picked at</th>
                        <th style="font-weight: normal" class="text-xl-left">Picked after</th>
                        <th style="font-weight: normal" class="text-xl-left">Validated</th>
                        <th style="font-weight: normal" class="text-xl-left">Validated at</th>
                        <th style="font-weight: normal" class="text-xl-left">Validated after</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($all_spans as $span)
                        <tr>
                            <td class="text-xl-left">{{$span['sales_order']}}</td>
                            <td class="text-xl-left"><b>{{$span['start']}}</b></td>
                            <td class="text-xl-left">{{$span['status_picked']}}</td>
                            <td class="text-xl-left">{{$span['picked_at']}}</td>
                            <td class="text-xl-left"><b>{{$span['span_picked']}}</b></td>
                            <td class="text-xl-left">{{$span['status_validated']}}</td>
                            <td class="text-xl-left">{{$span['validated_at']}}</td>
                            <td class="text-xl-left"><b>{{$span['span_validated']}}</b></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

