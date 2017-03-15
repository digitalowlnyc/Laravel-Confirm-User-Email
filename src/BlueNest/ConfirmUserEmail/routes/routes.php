<?php

use Symfony\Component\HttpFoundation\Request;
use BlueNest\ConfirmUserEmail\Models\ConfirmationCode;
use BlueNest\ConfirmUserEmail\ConfirmEmailHelpers;
use App\User;

Route::group(['middleware' => array('web')], function () {
    Route::get('/user-confirmation', function () {
        return view('confirm-user-email::email-confirmation');
    });

    Route::get('/do-confirmation/{confirmationCode}', function ($confirmationCode) {
        $confirmationQueryRes = ConfirmationCode::where("confirmation_code", "=", $confirmationCode)->get();

        if(count($confirmationQueryRes) === 0) {
            $data = [
                "type" => "error_message",
                "heading" => "Error activating account",
                "message" => "Invalid code or bad link used"
            ];
            return view("confirm-user-email::message")->with("data", $data);
        }

        $confirmationRecord = $confirmationQueryRes[0];

        if($confirmationRecord->expired == 1) {
            $data = [
                "type" => "error_message",
                "heading" => "Error activating account",
                "message" => "The link has expired or been used already"
            ];
            return view("confirm-user-email::message")->with("data", $data);
        }

        $user = User::find($confirmationRecord->user_id);
        $user->is_confirmed = 1;
        $user->save();

        $confirmationRecord->expire();

        $data = [
            "type" => "ok_message",
            "heading" => "Account activated!",
            "message" => "You have successfully activated your account!"
        ];
        return view("confirm-user-email::message")->with("data", $data);
    });


    Route::post('/send-confirmation', function (Request $request) {
        if(config('app.confirm.gcaptcha-enabled', false)) {
            if(!ConfirmEmailHelpers::checkCaptcha($request->input("g-recaptcha-response"))) {
                $validator = Validator::make([], []);
                $validator->errors()->add('g-captcha-input', 'Captcha response invalid');
                return redirect("user-confirmation")->withErrors($validator);
            }
        };

        ConfirmEmailHelpers::sendConfirmationEmail(Auth::user());

        return view('confirm-user-email::confirmation-sent');
    });
});