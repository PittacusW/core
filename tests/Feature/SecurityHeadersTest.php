<?php

namespace Pittacusw\Core\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Pittacusw\Core\Tests\TestCase;
use Pittacusw\Core\Middlewares\SecurityHeaders;

class SecurityHeadersTest extends TestCase {

  protected function setUp()
  : void {
    parent::setUp();

    Route::get('/headers', fn() => response('ok'));
  }

  public function test_it_adds_safe_default_security_headers()
  : void {
    $response = $this->get('/headers');

    $response->assertOk();
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('Referrer-Policy', 'no-referrer');
    $response->assertHeaderMissing('Strict-Transport-Security');
    $response->assertHeaderMissing('Permissions-Policy');
    $response->assertHeaderMissing('Public-Key-Pins-Report-Only');
    $response->assertHeaderMissing('X-XSS-Protection');
  }

  public function test_it_adds_hsts_only_on_secure_requests()
  : void {
    config()->set('pittacusw-core.security_headers.hsts.preload', TRUE);

    $response = (new SecurityHeaders())->handle(
      Request::create('https://example.com/headers', 'GET'),
      fn() => response('ok'),
    );

    $this->assertSame(
      'max-age=31536000; includeSubDomains; preload',
      $response->headers->get('Strict-Transport-Security'),
    );
  }

  public function test_it_can_be_disabled()
  : void {
    config()->set('pittacusw-core.security_headers.enabled', FALSE);

    $response = $this->get('/headers');

    $response->assertHeaderMissing('X-Content-Type-Options');
    $response->assertHeaderMissing('X-Frame-Options');
    $response->assertHeaderMissing('Referrer-Policy');
  }
}
