@extends('layouts.app')
@section('title', 'Driver Logs')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Syncronize Metrc Items with Odoo Products</h3>
            </div>

            {{--           {!! Form::open(['route' => 'synchronize']) !!}
       {{--
                       <input name="customer_id" type="hidden" value="{{$to_view['customer_id']}}">
                       <input name="saleorder_number" type="hidden" value="{{$to_view['saleorder_number']}}">
                       <input name="sale_order_full" type="hidden" value="{{$to_view['sale_order_full']}}">

           --}}

            <div class="card-body">
                <style>
                    .lot {
                        min-width: 300px;
                        max-width: 300px;
                        overflow: hidden;
                    }
                </style>
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">Metrc Id</th>
                        <th scope="col">Metrc Item Name</th>
                        <th scope="col">Odoo Product Id</th>
                        <th scope="col">Odoo Product Name</th>
{{--
                        <th scope="col">id</th>
                        <th scope="col">Name</th>
--}}
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php /*dd($products);  */?>
                    @foreach ($items as $item)
                        <tr>
{{--
                            <td>{{$product->item->metrc_id}}</td>
                            <td>{{$product->item->name}}</td>
--}}
                            <td>{{$item->metrc_id}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->product_id}}</td>
                            <td>{{$item->product1->name}}</td>
                            <td class=""><a href="{{route('related_product',[$item->metrc_id,$item->name])}}"
                                            class="btn btn-success">Search Odoo Product</a></td>

                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
{{--
            <div class="card-footer text-muted">
                <div class=" form-label-group text-left">
                    <label for="printLable">&nbsp;</label>

                    <button id="printLable" class="btn btn-lg btn-primary btn-block" type="submit">Update
                    </button>
                </div>

                {!! Form::close() !!}

                <p style="font-size: 75%;margin-top: 1em">Version 1</p>
            </div>
--}}
        </div>
    </div>
    </html>
@endsection
