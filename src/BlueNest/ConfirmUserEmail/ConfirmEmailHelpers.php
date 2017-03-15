<?php
/**
 * Creator: Bryan Mayor
 * Company: Blue Nest Digital, LLC
 * Date: 3/7/17
 * Time: 3:08 AM
 * License: (All rights reserved)
 */

namespace BlueNest\ConfirmUserEmail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class ConfirmEmailHelpers
{
    static function sendConfirmationEmail($user) {
        Mail::send('confirm-user-email::auth.emails.confirmation', ["user" => $user], function ($message) use ($user) {
            $email = $user->email;
            $message->to($email);
            $subject = 'Please confirm your email';
            if(config('app.name', false)) {
                $subject = config('app.name') . ': ' . $subject;
            }
            $message->subject($subject);
        });
    }

    static function checkCaptcha($googleCaptchaResponse) {
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
}