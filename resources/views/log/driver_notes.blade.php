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
                    <h5 class="text-center">Delivery Report for SO{{$sale_order_id}}</h5>
                    <h6 class="text-center">Original Amount ${{ $log->total }}</h6>
                </div>
                <form method="post" action="{{action('DriverNoteController@update',$sale_order_id)}}">
                    @csrf

                    <input type="hidden" class="form-control" name="total"
                           value="{{$log->total}}">



                    <div class="form-goupxx">
                    <input name="_method" type="hidden" value="PATCH">
                    <input name="log_id" type="hidden" value="{{$log_id}}">

                    <div class="col-auto">
                        <label for="make">Enter Date delivered :</label>
                        <input type="date" class="form-control" value="{{$delivery_date}}"
                               name="delivery_date">
                    </div>

                    <div class="col-auto">
                        <label for="make">Enter Time delivered :</label>
                        <input type="time" class="form-control" value="{{$delivery_time}}"
                               name="delivery_time">
                    </div>
                    <div class="col-auto">
                        <label for="make">Enter collected amount:</label>
                        <div class="input-group-prepend">
                            <div class="input-group-text">$</div>
                            <input type="text" class="form-control" name="collected"
                                   value="{{$log->collected}}">
                        </div>
                    </div>

                    <div class="col-auto">
                        <label for="make">Reasons:</label>
                        <select multiple="multiple" class="form-control" name="note_id[]" size="6">

                            @foreach ($notes as $note)
                                @if (in_array ($note->id,$selected_notes))
                                    <option value="{{$note->id}}" selected>{{$note->id}} - {{$note->note}}   </option>
                                @else
                                    <option value="{{$note->id}}">{{$note->id}} - {{$note->note}}   </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="text_note">Notes:</label>
                        <textarea type="text" class="form-control" rows="3" name="text_note">{{$log->notes }}</textarea>
                    </div>
                    <button type="submit" class="btn-sm btn-success">Save</button>
                    <a href="{{ route('go-home') }}" class="btn btn-primary btn-sm" class="card-link">Home</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
