<?php

use Symfony\Component\HttpFoundation\Request;
use BlueNest\ConfirmUserEmail\Models\ConfirmationCode;
use App\User;

function sendConfirmationEmail($user) {
    Mail::send('confirm-user-email::auth.emails.confirmation', ["user" => $user], function($message) use($user) {
        $email = $user->email;
        $message->to($email);
        $subject = 'Please confirm your email';
        if(config('app.name', false)) {
            $subject = config('app.name') . ': ' . $subject;
        }
        $message->subject($subject);
    });
}

function checkCaptcha($googleCaptchaResponse) {
    if(!Config::has('app.confirm.gcaptcha-secret-key')) {
        throw new Exception('gcaptcha-secret-key is missing from app config');
    }
    $API_SECRET = config('app.confirm.gcaptcha-secret-key');
    $API_URL = "https://www.google.com/recaptcha/api/siteverify";

    $data = array('secret' => $API_SECRET, 'response' => $googleCaptchaResponse);

    $handle = curl_init($API_URL);
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, True);

    $response = curl_exec($handle);

    if(!$response) {
        die("Failed calling Google captcha API");
    }

    $responseArray = json_decode($response, true);

    return $responseArray["success"] === True;
}

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
            if(!checkCaptcha($request->input("g-recaptcha-response"))) {
                $validator = Validator::make([], []);
                $validator->errors()->add('g-captcha-input', 'Captcha response invalid');
                return redirect("user-confirmation")->withErrors($validator);
            }
        };

        sendConfirmationEmail(Auth::user());

        return view('confirm-user-email::confirmation-sent');
    });
});