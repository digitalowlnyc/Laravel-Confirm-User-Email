@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $data["heading"] }}</div>

                    <div class="panel-body">
                        @if($data["type"] === "ok_message")
                            <div class="alert alert-success" role="alert">{{ $data["message"] }}</div>
                            @else
                            <div class="alert alert-danger" role="alert">{{ $data["message"] }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
