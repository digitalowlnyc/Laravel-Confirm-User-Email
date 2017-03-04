@extends('layouts.app')
<script src='https://www.google.com/recaptcha/api.js'></script>

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Email Confirmation</div>
                    <div class="panel-body">
                        <div class="lead">You must first confirm your email before using this feature. A confirmation email was sent when you registered. Please use the form below if you need to request a confirmation email again.</div>

                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/send-confirmation') }}">
                            {{ csrf_field() }}

                            @if(config('app.confirm.gcaptcha-enabled', false) && Config::has('app.confirm.gcaptcha-site-key'))
                                <div class="form-group{{ $errors->has('g-captcha-input') ? ' has-error' : '' }}">
                                    <label for="account-id" class="col-md-4 control-label">Captcha</label>

                                    <div class="col-md-6">
                                        <div id="g-captcha-input" name="g-captcha-input" class="g-recaptcha" data-sitekey="{{config('app.confirm.gcaptcha-site-key')}}"></div>

                                        @if ($errors->has('g-captcha-input'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('g-captcha-input') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-btn fa-envelope-o"></i> Send Confirmation Email
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
