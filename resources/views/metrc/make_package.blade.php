@extends('layouts.app')
@section('title', 'Make Package')
@section('content')
    <style>
        .select2-container--default .select2-selection--single {
            padding: 6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
        }
    </style>
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="error">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row justify-content-center align-items-center ">
            <div class="col col-sm-9 col-md-9 col-lg-9 col-xl-9">
                <div class="card text-center ">
                    <div class="card-header">
                        <h6>Sales Order: {{$sale_order_full}} </h6>
                        <h4>{{$name}}</h4>
                        <p> Order Line: {{$line_number}}</p>
                    </div>
                    <div class="card-body">
                        <script>
                            function clearErrors() {
                                var form = document.getElementById('errors').querySelectorAll('li.error');
                                for (i = 0; i <= form.length; i++) {
                                    form[i].innerText = '';
                                }
                            }
                        </script>

                        {!! Form::open(['route' => 'create_package','class' => 'form-signin']) !!}
                        <input hidden name="line_number" value="{{$line_number}}">
                        <input hidden name="id" value="{{$id}}">

                        <div class="form-group text-left">
                            <label for="tag">Select Source Package</label>
                            <select class="form-control form-control-lg" id="myoption" style="height:35px;"
                                    name="source_package">
                                @foreach ($source_packages as $sp)
                                    @php
                                         $item = $sp->item;
                                    @endphp
                                    @if ($sp->tag == $tag)
                                        <option selected
                                                value={{$sp->tag}}>{{$item . " x- " . "Date: " . $sp->date . " - " .  "Quantity: " .  $sp->quantity . " "  . $sp->uom .  " - " . $sp->tag . " - " .  $sp->source_tag  }} </option>
                                    @else
                                        <option
                                            value={{$sp->tag}}>{{$item . " x- " . "Date: " . $sp->date . " - " .  "Quantity: " .  $sp->quantity . " "  . $sp->uom . " - " . $sp->tag  . " - " .  $sp->source_tag }} </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group text-left">
                            <label for="tag">Select a new tag</label>
                            <select class="form-control form-control-lg" id="myoption_tag" style="height:35px;"
                                    name="new_package">
                                @foreach ($tags as $tag)
                                    @if($tag->tag == $new_package)
                                        <option selected
                                                value={{$tag->tag}}>{{$tag->tag}}
                                        </option>
                                    @else
                                        <option
                                            value={{$tag->tag}}>{{$tag->tag}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group text-left">
                            <label for="tag">Item</label>
                            <input class="form-control form-control-lg" name="item" type="text"
                                   value="{{$name}}" data-toggle="popover" data-trigger="hover" title="Help"
                                   data-content="Change item name">
                        </div>

                        <div class="form-group text-left">
                            <label for="tag">Quantity</label>
                            <input class="form-control form-control-lg" name="quantity" type="text"
                                   value="{{$quantity}}" data-toggle="popover" data-trigger="hover" title="Help"
                                   data-content="Change quantity for this sales line">
                        </div>
                        {{--
                                                @php dd($sp); @endphp
                        --}}
                        <div class="form-group text-left">
                            <label for="tag">Uom</label>
                            <select class="form-control form-control-lg" name="uom" id=uom type="text"
                                    value="{{$sp->uom}}" data-toggle="popover" data-trigger="hover" title="Help"
                                    data-content="Change Unit of Measure for this sales line">
                                @foreach ($uoms as $u)
                                    @if($u->Name == $uom)
                                        <option selected
                                                value={{$u->Name}}>{{$u->Name}}
                                        </option>
                                    @else
                                        <option
                                            value={{$u->Name}}>{{$u->Name}}
                                        </option>
                                    @endif

                                @endforeach
                            </select>
                        </div>


                        <div class="form-group text-left">
                            <div class="row">
                                <div class="col">
                                    <button id="printLable" name="action" value="create"
                                            class="btn btn-sm btn-primary btn-block" type="submit" data-toggle="toolkit"
                                            title="Help"
                                            data-content="Create a new Metrc package">Create Package
                                    </button>
                                </div>
                                <div class="col">
                                    <button id="printlabel2" name="action" value="discard"
                                            class="btn btn-sm btn-primary btn-block" type="submit" data-toggle="popover"
                                            data-trigger="hover" title="Help"
                                            data-content="Remove this product from manifest creation">Discard Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>


                </div>

                <div class="card-footer text-muted">
                    <p style="font-size: 75%;margin-top: 1em">Version 1</p>
                </div>
            </div>
        </div>
    </div>
    </body>

    <script>
        var job = document.getElementById("myoption_tag");
        //  if (!job.options[job.selectedIndex].defaultSelected) alert("#job has changed");
        var is_updated = !job.options[job.selectedIndex].defaultSelected;

    </script>

    <script>
        // In your Javascript (external .js resource or <script> tag)
        $("#myoption").select2({
            //   theme: "classic",
            //  width: 'resolve' ,
            height: 'resolve'
        });
        $("#myoption_tag").select2({
            //   theme: "classic",
            //  width: 'resolve' ,
            height: 'resolve'
        });
    </script>
    <script>
        function clearErrors() {
            var form = document.getElementById('errors').querySelectorAll('li.error');
            for (i = 0; i <= form.length; i++) {
                form[i].innerText = '';
            }
        }
    </script>
    <script type="text/javascript">
        function myChangeFunction(input1) {
            var input2 = document.getElementById('myInput2');
            input2.value = input1.value;
        }
    </script>

    </html>
@endsection
