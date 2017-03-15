<?php

namespace BlueNest\ConfirmUserEmail\Middleware;

use BlueNest\ConfirmUserEmail\ConfirmEmailHelpers;
use BlueNest\ConfirmUserEmail\Models\ConfirmationCode;
use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserIsConfirmed
{
    /**
     * The guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(Auth::check()) {
            $user = Auth::user();
            if(!$user->is_confirmed) {
                if(!ConfirmationCode::existsFor($user->id)) {
                    ConfirmEmailHelpers::sendConfirmationEmail($user);
                    return new Response(view('confirm-user-email::confirmation-sent'));
                } else {
                    return redirect('user-confirmation');
                }
            }
        }

        return $next($request);
    }
}
