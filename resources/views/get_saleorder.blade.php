@extends('layouts.app')
@section('title', 'Driver Logs')
@section('content')

    <div class="container-fluid h-100">
        <div class="row justify-content-center align-items-center ">
            <div class="col-6">
                <div class="card text-center ">
                    <div class="card-header"><h3>Metrc Manifest</h3>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="accordion" id="accordionExample">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                                data-target="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                            Create Metrc Manifest
                                        </button>
                                    </h2>
                                </div>

                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                     data-parent="#accordionExample">
                                    <div class="card-body">
                                        {!! Form::open(['route' => 'get_order','class' => 'form-signin']) !!}

                                        <div class="form-group text-left">
                                            <label for="inputVehicle">Sale Order</label>
                                            <input class="form-control form-control-lg" name="saleorder_number"
                                                   type="text"
                                                   placeholder="Sale order number">
                                            </select>
                                        </div>
                                        <div class="form-group text-left">
                                            <div class="col-3">
                                                <button id="printLable" class="btn btn-lg btn-primary btn-block"
                                                        type="submit">
                                                    Continue
                                                </button>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                                data-target="#collapseTwo" aria-expanded="false"
                                                aria-controls="collapseTwo">
                                            Import Metrc Tags
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                     data-parent="#accordionExample">
                                    <div class="card-body">
                                        {!! Form::open(['route' => 'do_import','name' =>'importing', 'files' => true, 'class' =>'form-signin' ]) !!}

                                        <div class="form-group text-left">
                                            <label for="import_file">Import Tags</label>
                                            <input class="form-control" name="import_file" type="file">
                                        </div>

                                        <div class="form-group text-left">
                                            <div class="col-3">
                                                <button id="printlabel" class="btn btn-lg btn-primary btn-block"
                                                        type="submit">Import
                                                </button>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingThree">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                                data-target="#collapseThree" aria-expanded="false"
                                                aria-controls="collapseTree">
                                            Import Source Packages
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                     data-parent="#accordionExample">
                                    <div class="card-body">
                                        {!! Form::open(['route' => 'import_packets','name' =>'packets', 'files' => true, 'class' =>'form-signin' ]) !!}

                                        <div class="form-group text-left">
                                            <label for="import_file">Import Source Packages</label>
                                            <input class="form-control" name="import_file" type="file">
                                        </div>

                                        <div class="form-group text-left">
                                            <div class="col-3">
                                                <button id="printlabel" class="btn btn-lg btn-primary btn-block"
                                                        type="submit">Import
                                                </button>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <p class="mb-0">
                                        <a href="/admin/resources/users" >Drivers</a>
                                    </p>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <p class="mb-0">
                                        <a href="/admin/resources/vehicles">Vehicles</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <p class="text-muted text-center">&copy;
                            @php
                                $copyYear = 2018; // Set your website start date
                                $curYear = date('Y'); // Keeps the second year updated
                                echo $copyYear . (($copyYear != $curYear) ? '-' . $curYear : '')
                            @endphp
                            Oz Distribution, Inc.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="js/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="js/mdb.js"></script>

    </body>
    </html>
@endsection
