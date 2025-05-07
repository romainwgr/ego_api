<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jumbojett\OpenIDConnectClient;
use App\Models\User;

/**
 * Handle Google OpenID Connect authentication using the jumbojett/openid-connect-php library.
 *
 */
class OidcController extends Controller
{
    
    private function client(): OpenIDConnectClient {
        $c = new OpenIDConnectClient(
            'https://accounts.google.com',
            config('services.google.client_id'),
            config('services.google.client_secret')
        );
        $c->setRedirectURL(config('services.google.redirect'));
        $c->addScope(['openid','email','profile']);
        return $c;
    }
    
    public function redirectToGoogle()  { $this->client()->authenticate(); }
    
    public function callbackToGoogle()  {
        $oidc  = $this->client();
        $oidc->authenticate();
    
        $email = $oidc->requestUserInfo('email');
        $sub   = $oidc->getVerifiedClaims('sub');
    
        $user = User::updateOrCreate(
            ['email'     => $email],     
            ['google_id'=> $sub]         
        );
        Auth::login($user);
        return view('home');

    }
    
}
