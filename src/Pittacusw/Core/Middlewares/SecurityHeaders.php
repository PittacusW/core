<?php

namespace Pittacusw\Core\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders {

  public function handle(Request $request, Closure $next)
  : Response {
    /** @var Response $response */
    $response = $next($request);

    if (! config('pittacusw-core.security_headers.enabled', TRUE)) {
      return $response;
    }

    if (! $response->headers->has('X-Content-Type-Options')) {
      $response->headers->set('X-Content-Type-Options', 'nosniff');
    }

    if (! $response->headers->has('X-Frame-Options')) {
      $response->headers->set(
        'X-Frame-Options',
        config('pittacusw-core.security_headers.frame_options', 'DENY'),
      );
    }

    if (! $response->headers->has('Referrer-Policy')) {
      $response->headers->set(
        'Referrer-Policy',
        config('pittacusw-core.security_headers.referrer_policy', 'no-referrer'),
      );
    }

    if ($request->isSecure() && config('pittacusw-core.security_headers.hsts.enabled', TRUE)) {
      $hsts = ['max-age=' . (int) config('pittacusw-core.security_headers.hsts.max_age', 31536000)];

      if (config('pittacusw-core.security_headers.hsts.include_subdomains', TRUE)) {
        $hsts[] = 'includeSubDomains';
      }

      if (config('pittacusw-core.security_headers.hsts.preload', FALSE)) {
        $hsts[] = 'preload';
      }

      $response->headers->set('Strict-Transport-Security', implode('; ', $hsts));
    }

    return $response;
  }
}
