Click here to confirm your account: <a href="{{ $link = url('do-confirmation', \BlueNest\ConfirmUserEmail\Models\ConfirmationCode::generateConfirmationCode($user->id)) }}"> {{ $link }} </a>