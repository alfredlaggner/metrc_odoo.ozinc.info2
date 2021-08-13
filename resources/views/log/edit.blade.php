@extends('layouts.app')
@section('title', 'Driver Logs')
@section('content')
    <h4 class="text-center">Edit Driver Log</h4><br/>
    <form method="post" action="{{action('VehicleController@update', $id)}}">
        @csrf
        <input name="_method" type="hidden" value="PATCH">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="form-group col-md-4">
                <label for="make">Sale Order:</label>
                <input type="text" class="form-control" name="make" value="{{$driverlog->saleinvoice_id}}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="form-group col-md-4">
                <label for="model">Driver:</label>
                <input type="text" class="form-control" name="model" value="{{$driverlog->driver_id}}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="form-group col-md-4">
                <label for="plate">Vehicle:</label>
                <input type="text" class="form-control" name="plate" value="{{$driverlog->vehicle_id}}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="form-group col-md-4">
                <label for="plate">Salesperson:</label>
                <input type="text" class="form-control" name="plate" value="{{$driverlog->salesperson_id}}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="form-group col-md-4">
                <label for="plate">Customer:</label>
                <input type="text" class="form-control" name="plate" value="{{$driverlog->customer_id}}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="form-group col-md-4" style="margin-top:60px">
                <button type="submit" class="btn btn-success" style="margin-left:38px">Update</button>

                <a href="{{ route('go-home') }}" class="btn btn-outline-primary btn-sm" role="button"
                   aria-pressed="true">Home</a>

            </div>
        </div>
    </form>
@endsection