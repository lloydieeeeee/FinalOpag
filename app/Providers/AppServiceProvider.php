<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Providers\UserCredentialProvider; // ← must have this

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Auth::provider('user_credential', function ($app, array $config) {
            return new UserCredentialProvider(
                $app['hash'],
                $config['model']
            );
        });
    }
}