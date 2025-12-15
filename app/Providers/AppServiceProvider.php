<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\HttpClient\HttpClient;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            
        
            $frontendUrl = 'https://migration.ego-network.org';
            return $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        });
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        Mail::extend('brevo', function (array $config = []) {
            
            $client = HttpClient::create([
                'proxy' => 'http://proxy.ipsl.upmc.fr:3128', 
                'timeout' => 60,
                'max_duration' => 60,
            ]);

            return (new BrevoTransportFactory(null, $client))->create(
                new Dsn(
                    'brevo+api',
                    'default',
                    $config['key']
                )
            );
        });
    }
}
