<?php

namespace Pittacusw\Core\Middlewares;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders {

 public function handle(Request $request, Closure $next) {
  header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
  header('X-XSS-Protection: 1; mode=block');
  header('X-Content-Type-Options: nosniff');
  header('X-Frame-Options: DENY');
   header('Content-Security-Policy', "default-src 'self'");
  header('Referrer-Policy: no-referrer');
  header('Permissions-Policy: ');
  header('Public-Key-Pins-Report-Only: max-age=5184000 ; pin-sha256=\'d7qzRu9zOECb90Uez27xWltNsj0e1Md7GkYYkVoZWmM=\' ; pin-sha256=\'E9CZ9INDbd+2eRQozYqqbQ2yXLVKB7+xcprMF+44U1g=\' ;');

  return $next($request);
 }
}
