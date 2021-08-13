@extends('layouts.app')
@section('title', 'Driver Notes')
@section('content')
    @php

        if (!$log->delivery_time)
        {
            $delivery_date = date('Y-m-d');
            $delivery_time = date('H:i');
    }
    else {
            $delivery_date = substr($log->delivery_time,0,10);
            $delivery_time = substr($log->delivery_time,11);
    }
    @endphp

    <div class="container">
        <div class="card" style="width: 100%">
            <div class="card-body">
                <div class="card-header">
                    <h3 class="text-center card-title">Delivery Report for SO{{$log->saleinvoice_id}}</h3>
                </div>
                <div class="container">
                    <h4>Delivery finished</h4>
                </div>
                <div class="row">
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="form-group col-md-4" style="margin-top:60px">
                            @if ( ! $viewer)
                                <a href="{{route('go-home')}}"
                                   class="btn btn-outline-primary btn-sm"  role="button"
                                   aria-pressed="true">Home</a>
                            @else
                                <a href="{{route('go-viewer')}}"
                                   class="btn btn-outline-primary btn-sm"  role="button"
                                   aria-pressed="true">Home</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection