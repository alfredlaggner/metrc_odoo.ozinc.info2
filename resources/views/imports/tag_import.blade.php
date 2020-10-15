@extends('layouts.app')
@section('title', 'Import Tags')
@section('content')

    <div class="container">
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
                <div class="card mt-4 ">
                    <div class="card-header">
                        <h6>Import Metrc Tags</h6>
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

                        {!! Form::open(['route' => 'do_import','name' =>'importing', 'files' => true, 'class' =>'form-control' ]) !!}

                        <div class="form-group text-left">
                            {!! Form::file('import_file',['class' => 'form-control']); !!}
                        </div>
                        <div class="form-group text-left">
                            {!! Form::submit('Upload File',['class' => 'form-control']);  !!}
                        </div>
                        <button id="printLable2" name="action" value="discard"
                                class="btn btn-lg btn-primary btn-block" type="submit" data-toggle="popover"
                                data-trigger="hover" title="Help"
                                data-content="Remove this product from manifest creation">Discard Changes
                        </button>

                        {!! Form::close() !!}
                    </div>
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Tag</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Commissioned</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tags as $tag)
                            <tr>
                                <td>{{$tag->tag}}</td>
                                <td>{{$tag->type}}</td>
                                <td>{{$tag->status}}</td>
                                <td>{{$tag->commissioned}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="card-footer text-muted">
                        <p style="font-size: 75%;margin-top: 1em">Version 1</p>
                    </div>
                </div>
            </div>
        </div>
        </body>
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

