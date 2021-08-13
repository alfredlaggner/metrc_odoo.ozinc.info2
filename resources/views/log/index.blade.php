@extends('layouts.app')
@section('title', 'Driver Logs')
@section('content')
    <div class="container">
        <br/>
        @if (\Session::has('success'))
            <div class="alert alert-success">
                <p>{{ \Session::get('success') }}</p>
            </div><br/>
        @endif

        @if ($logs->count())
            @foreach($logs as $log)
                @if (! $viewer)
                    <h5>Driver: {{$log->driver->name}}</h5>
                @else
                    {{ "" }}
                @endif
                @break
            @endforeach
            <table class="table table-bordered hoverable table-responsive-sm">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Salesorder</th>
                    <th>Scheduled</th>
                    <th>Customer</th>
                    <th>SalesPerson</th>
                    @if (! $viewer)

                        <th>Deliver</th>
                    @else
                        {{ "" }}
                    @endif

                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @php
                    //  dd($logs);
                @endphp
                @foreach($logs as $log)
                    @php
                            @endphp

                    <tr>
                        <td>{{$log->id}}</td>
                        <td>SO{{$log->saleinvoice_id}}</td>
                        <td>{{substr($log->created_at,0,10)}}</td>
                        <td>{{$log->customer_name}}</td>
                        <td>{{$log->salesperson_name}}</td>
                        {{--
                                        <td>{{$log->saleinvoices->invoice_id}}</td>
                        --}}
                        @if (! $viewer)
                            <td><a href="{{route('edit_action', $log->saleinvoice_id)}}"
                                   class="btn btn-warning">Deliver</a>
                            </td>
                        @else
                            {{ "" }}
                        @endif


                        <td><a href="{{route('display_notes', [$log->id,$log->saleinvoice_id])}}"
                               class="btn btn-sm btn-primary">View Report</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{--
                    <form action="{{action('DriverLogController@create')}}" method="get">
                        @csrf
                        <input name="_method" type="hidden" value="CREATE">
                        <button class="btn btn-primary" type="submit">Add Log</button>
                        <a href="{{ route('go-home') }}" class="btn btn-outline-primary btn-sm" role="button"
                           aria-pressed="true">Home</a>
                    </form>
            --}}
        @else
            <h5>Nothing to deliver today for {{Auth::user()->name }}</h5>
        @endif

    </div>
@endsection
