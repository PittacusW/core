<?php

namespace Pittacusw\Core\Tests\Feature;

use Pittacusw\Core\Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Pittacusw\Core\Jobs\HandlePushWebhook;
use Pittacusw\Core\Providers\CoreServiceProvider;
use Spatie\GitHubWebhooks\Http\Controllers\GitHubWebhooksController;

class CoreServiceProviderTest extends TestCase {

  public function test_it_registers_the_default_push_webhook_job()
  : void {
    $this->assertSame(
     HandlePushWebhook::class,
     config('github-webhooks.jobs.push'),
    );
  }

  public function test_it_does_not_override_an_existing_push_webhook_job()
  : void {
    config()->set('github-webhooks.jobs', [
     'push' => ExistingPushWebhookJob::class,
    ]);

    (new CoreServiceProvider($this->app))->packageBooted();

    $this->assertSame(
     ExistingPushWebhookJob::class,
     config('github-webhooks.jobs.push'),
    );
  }

  public function test_it_does_not_register_a_push_job_when_a_wildcard_handler_exists()
  : void {
    config()->set('github-webhooks.jobs', [
     '*' => ExistingWildcardWebhookJob::class,
    ]);

    (new CoreServiceProvider($this->app))->packageBooted();

    $this->assertSame(
     ['*' => ExistingWildcardWebhookJob::class],
     config('github-webhooks.jobs'),
    );
  }

  public function test_it_registers_the_github_webhook_route()
  : void {
    $route = collect(Route::getRoutes()
                          ->getRoutes())
     ->first(fn($route) => $route->uri() === 'api/github');

    $this->assertNotNull($route);
    $this->assertContains('POST', $route->methods());
    $this->assertSame(GitHubWebhooksController::class, $route->getActionName());
  }

  public function test_the_github_webhook_route_reaches_the_controller()
  : void {
    $response = $this->postJson('/api/github', []);

    $response->assertForbidden();
    $response->assertExactJson([
                                'message' => 'invalid signature',
                               ]);
  }
}

class ExistingPushWebhookJob {

}

class ExistingWildcardWebhookJob {

}
