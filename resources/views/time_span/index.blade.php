@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="accordion" id="accordionExample">

                    <div class="card">
                        <div class="card-header" id="headingEightPlus">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button"
                                        data-toggle="collapse"
                                        data-target="#collapseEightPlus" aria-expanded="false"
                                        aria-controls="collapseEightPlus">
                                    <h6>Sales Order Time Span</h6>
                                </button>
                            </h2>
                        </div>
                        <div id="collapseEightPlus" class="collapse" aria-labelledby="headingEightPlus"
                             data-parent="#accordionExample">
                            <div class="card-body">
                                <form method="post"
                                      action="{{route('so_time_span')}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="form-group col-md-4">
                                            <label for="months">Year:</label>
                                            <input class="form-control" name="year" type="text"
                                                   value="{{$year}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="form-group col-md-4">
                                            <label for="months">Select months range:</label>
                                            <select class="form-control" name="months[]" multiple>
                                                @foreach($months as $sp)
                                                    @if ($sp->month_id == $data['month'])
                                                        <option value="{{$sp->month_id}}"
                                                                selected>{{$sp->name}} </option>
                                                    @else
                                                        <option
                                                            value="{{$sp->month_id}}">{{$sp->name}} </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="form-group col-md-4">
                                            <button type="submit" name="display" value="display"
                                                    class="btn btn-primary">
                                                Ready set go
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <p class="text-muted text-center">&copy;
                        @php
                            $copyYear = 2018; // Set your website start date
                            $curYear = date('Y'); // Keeps the second year updated
                            echo $copyYear . (($copyYear != $curYear) ? '-' . $curYear : '');
                        @endphp
                        Oz Distribution, Inc.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
