@extends('layouts.app')
@section('title', 'Driver Logs')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Select Odoo product for {{$metrc_id}}-{{$metrc_product_name}}</h3>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">Product Id</th>
                        <th scope="col">Odoo Product Name</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php /*dd($products);  */?>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{$product->ext_id}}</td>
                            <td>{{$product->name}}</td>
                            <td class=""><a href="{{route('select_product',[$product->ext_id,$metrc_id])}}"
                                            class="btn btn-success">Select Odoo Product</a></td>
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
