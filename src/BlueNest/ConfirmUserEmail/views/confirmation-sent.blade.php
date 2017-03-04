@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Confirmation Email Sent</div>

                <div class="panel-body">
                    @if(!Auth::check())
                        Please register/login to use the tool.
                    @else
                        <div class="alert alert-success" role="alert">Confirmation email sent to {{Auth::user()->email}}. Please check your inbox.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
