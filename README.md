# Laravel-Confirm-User-Email
Add the ability to require users to confirm their email after registering. 

# Features
- Option to 'resend confirmation email', which is protected by Google Recaptcha.

# Installation

Add via composer:

`composer require bluenest/confirm-user-email`

Add service providers by adding under 'providers' key in config/app.php

```php
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        BlueNest\ConfirmUserEmail\ConfirmEmailServiceProvider::class
```

Add a middleware group in app/Http/kernel.php. 

```php
'auth-confirm' => [
            \BlueNest\ConfirmUserEmail\Middleware\UserIsConfirmed::class,
            \Illuminate\Auth\Middleware\Authenticate::class
        ],
```

Apply your new middleware group to the routes you want confirmation for:

```php
Route::group(['middleware' => array('auth-confirm')], function () {
  // confirmed user routes go here
});
```

Update config/app.php with your gcaptcha configuration:

```php
<?php
    return [
      
    // ... truncated ...   
      
    'confirm' => [
        'gcaptcha-site-key' => 'PASTE_YOUR_SITE_KEY_HERE',
        'gcaptcha-secret-key' => 'PASTE_YOUR_SECRET_KEY_HERE',
        'gcaptcha-enabled' => true
    ]
];
```

