<?php
// app/Http/Middleware/AllowFraming.php
namespace App\Http\Middleware;

use Closure;

class AllowFraming {
    public function handle($request, Closure $next) {
        $response = $next($request);

        // Retire X-Frame-Options et autorise seulement ton domaine WP
        $response->headers->remove('X-Frame-Options');
        $response->headers->set(
            'Content-Security-Policy',
            "frame-ancestors 'self' https://migration.ego-network.org/"
        );

        return $response;
    }
}
