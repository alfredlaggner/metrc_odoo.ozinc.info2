@extends('layouts.app')
@section('title', 'Driver Logs')
@section('content')
    <div class="container">

        <div class="card">
            <div class="card-header">
                <h3>{{$to_view['customer_name']}}</h3>
                <p>{{$to_view['customer_street']}} {{$to_view['customer_city']}}, {{$to_view['customer_zip']}}</p>
                <p>SO{{$to_view['saleorder_number']}}</p>
            </div>

            {!! Form::open(['route' => 'make_manifests']) !!}
            <div class="card-body">
                <input name="customer_id" type="hidden" value="{{$to_view['customer_id']}}">
                <input name="saleorder_number" type="hidden" value="{{$to_view['saleorder_number']}}">
                <input name="sale_order_full" type="hidden" value="{{$to_view['sale_order_full']}}">

                <div class="row  justify-content-center">

                    <div class="col-6">
                        <div class="form-group text-left">
                            <label for="license_number">License Number</label>
                            <input class="form-control" name="license_number" type="text"
                                   placeholder="License Number" value="{{$to_view['license_number']}}">
                        </div>
                        <div class="form-group text-left">
                            <label for="license_number">Exp</label>
                            <input class="form-control" name="license_exp" type="text"
                                   placeholder="License Number" value="{{$to_view['license_exp']}}">
                        </div>

                        <div class="form-group text-left">
                            <label for="planned_route">Planned Route</label>

                            <textarea class="form-control" id="planned_route" name="planned_route"
                                      rows="7">{{$to_view['planned_route']}}</textarea>
                        </div>
                        <div class="form-group text-left">
                        </div>

                    </div>
                    <div class="col-6">
                        <div class="form-group text-left">
                            <label for="est_leave">Estimated Leave</label>
                            <input class="form-control form-control-" name="est_leave" type="text"
                                   value="{{$to_view['est_leave']}}">
                        </div>
                        <div class="form-group text-left">
                            <label for="est_leave">Estimated Arrive</label>
                            <input class="form-control form-control-" name="est_arrive" type="text"
                                   value="{{$to_view['est_arrive']}}">
                        </div>
                        <div class="form-group text-left">
                            <label for="driverInput">Driver</label>
                            <select id="driverInput" name="driver" class="custom-select" autofocus>
                                @foreach ($to_view['drivers'] as $driver)
                                    @if ($driver->id == $to_view['driver_id'])
                                        <option selected
                                                value='{!! $driver->id!!}'>{!! $driver->first_name . ' ' . $driver->last_name!!}</option>
                                    @else
                                        <option
                                            value='{!! $driver->id!!}'>{!! $driver->first_name . ' ' . $driver->last_name!!}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group text-left">
                            @php
                                @endphp
                            <label for="inputVehicle">Vehicle</label>
                            <select id="vehicleInput" name="vehicle" class="custom-select" required>
                                @foreach ($to_view['vehicles'] as $vehicle)
                                    @if ($vehicle->id == $to_view['vehicle_id'])
                                        <option selected
                                                value='{!! $vehicle->id!!}'>{!! $vehicle->make . ' ' . $vehicle->model . ' - ' .  $vehicle->plate !!}</option>
                                    @else
                                        <option
                                            value='{!! $vehicle->id!!}'>{!! $vehicle->make . ' ' . $vehicle->model . ' - ' .  $vehicle->plate !!}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                {{--
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="py-3 text-center">Directions</h5>
                                    </div>
                                </div>

                                <div class="row  justify-content-center">
                                    <div class="col-9">
                                        <div style="height: 100%;" id="map"></div>
                                    </div>
                                    <div class="col-3">
                                        <div id="right-panel"></div>
                                    </div>
                                </div>
                --}}

                <div class="row">
                    <div class="col-12">
                        <a name="error_message"></a>
                        <h5 class="py-3 text-center">Saleslines</h5>
                    </div>
                </div>
                <div class="container">
                    <style>
                        .lot {
                            min-width: 300px;
                            max-width: 300px;
                            overflow: hidden;
                        }
                    </style>
                    @if ($error_message)
                        @php
                            $error_message = explode('|',$error_message);
                   //  dd(count($error_message));
                        @endphp
                        <div id="errors" class="alert alert-danger " role="alert">
                            @for($i = 0; $i < count($error_message); $i++)
                                <p class="error">{{$error_message[$i]}} </p>
                            @endfor
                        </div>
                        <div class="row mb-2 d-flex justify-content-end">
                            <div class="col-3">
                                <button id="mybutton" class="btn btn-sm btn-outline-warning btn-block" type="reset"
                                        onclick="clearErrors()">
                                    Remove Messages
                                </button>
                            </div>
                        </div>

                    @endif

                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            {{--
                                                        <th scope="col">#</th>
                            --}}
                            <th scope="col">Sales Line</th>
                            <th scope="col">Package Label</th>
                            <th scope="col">SKU</th>
                            <th scope="col">Name</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Price</th>
                            <th scope="col">Total</th>
                            <th scope="col">Package</th>
                            <th scope="col">Edit</th>
                        </tr>
                        </thead>
                        <tbody>
                        @for($i=0; $i <  $to_view['line_count']; $i++)
                            <tr>
                                {{--
                                                                <td scope="row">{{$i+1}}</td>
                                --}}
                                <td>{{$view_saleslines[$i]['line_number']}}</td>

                                <td><input style='width:235px' type="text"
                                           name='tag[]'
                                           class="form-control"
                                           id="tag{{$i}}"
                                           value="{{$view_saleslines[$i]['metrc_package_created']}}"
                                           data-toggle="popover"
                                           data-trigger="hover"
                                           title="Help"
                                           data-content="Scan or type tag number of package.">
                                </td>

                                <input type="hidden" name="salesline[]"
                                       value="{{$view_saleslines[$i]['total']}}">
                                <input id="line_id{{$i}}" type="hidden" name="id[]"
                                       value="{{$view_saleslines[$i]['id']}}">
                                <td>{{$view_saleslines[$i]['code']}}</td>
                                <td>{{$view_saleslines[$i]['name']}}</td>
                                <td>{{$view_saleslines[$i]['quantity']}}</td>
                                {{--
                                                                <td>{{$view_saleslines[$i]['metrc_package_created']}}</td>
                                --}}
                                <td>{{number_format($view_saleslines[$i]['price'],2)}}</td>
                                <td>{{$view_saleslines[$i]['total']}}</td>

                                <td class=""><a href="{{route('make_package',$view_saleslines[$i])}}"
                                                class="btn btn-sm btn-success">Package</a></td>

                                </td>
                                <td><a href="{{route('edit_orderline',$view_saleslines[$i])}}"
                                       class="btn btn-sm btn-success">Edit</a></td>
                            </tr>


                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
                            <script>

                                $(document).ready(function () {

                                    $(document).on("change", "#tag{{$i}}", function (e) {
                                        $.ajaxSetup({
                                            headers: {
                                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                                            }
                                        });
                                        e.preventDefault();
                                        var tag = {
                                            tag: $('#tag{{$i}}').val(),
                                            id: $('#line_id{{$i}}').val(),
                                        };
                                        var type = "POST";
                                        var ajaxurl = '{{route("update_tag")}}';
                                        $.ajax({
                                            type: type,
                                            url: ajaxurl,
                                            data: tag,
                                            dataType: 'json',
                                            success: function (data) {
                                                console.log(data)
                                            },
                                            error: function (data) {
                                                console.log(data);
                                            }
                                        });
                                    });
                                });
                            </script>

                        @endfor
                        </tbody>
                    </table>
                </div>

                <div class="card-footer text-muted">
                    <div class=" form-label-group text-left">
                        <div class="row">
                            <div class="col">
                                <button id="printLable" class="btn btn-sm btn-primary btn-block"
                                        name="return_value"
                                        value="manifest" type="submit">Create Metrc Template
                                </button>
                            </div>
                            <div class="col">
                                <button class="btn btn-sm btn-primary btn-block" name="return_value"
                                        value="abort"
                                        type="submit">Return without Manifest
                                </button>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}

                    <p style="font-size: 75%;margin-top: 1em">Version 1</p>
                </div>
            </div>
            </html>
            @endsection
            <script>
                function clearErrors() {
                    /*
                                        var form = document.getElementById('errors').removeClass("div.alert-danger");
                    */
                    //  alert('xxx');
                    var paras = document.getElementsByClassName('error');

                    while (paras[0]) {
                        paras[0].parentNode.removeChild(paras[0]);
                    }
                    var elem = document.getElementById("errors");
                    elem.parentNode.removeChild(elem);

                    document.getElementById("mybutton").remove();
                }
            </script>

