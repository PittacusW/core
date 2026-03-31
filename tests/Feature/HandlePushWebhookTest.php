<?php

namespace Pittacusw\Core\Tests\Feature;

use Mockery;
use RuntimeException;
use Pittacusw\Core\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Pittacusw\Core\Tests\Support\FakeLock;
use Pittacusw\Core\Jobs\HandlePushWebhook;
use Spatie\GitHubWebhooks\Models\GitHubWebhookCall;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

class HandlePushWebhookTest extends TestCase {

  public function test_it_does_nothing_when_deployment_is_disabled()
  : void {
    config()->set('pittacusw-core.deployment.enabled', FALSE);

    Cache::shouldReceive('lock')
         ->never();
    $kernel = Mockery::mock(ConsoleKernel::class);
    $kernel->shouldReceive('call')
           ->never();

    Artisan::swap($kernel);

    (new HandlePushWebhook(new GitHubWebhookCall()))->handle();

    $this->assertTrue(TRUE);
  }

  public function test_it_releases_the_job_when_a_deployment_is_already_running()
  : void {
    Cache::shouldReceive('lock')
         ->once()
         ->andReturn(new FakeLock(FALSE));

    $kernel = Mockery::mock(ConsoleKernel::class);
    $kernel->shouldReceive('call')
           ->never();

    Artisan::swap($kernel);

    $job = (new HandlePushWebhook(new GitHubWebhookCall()))->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(30);
  }

  public function test_it_runs_deployment_commands_under_a_lock()
  : void {
    $lock = new FakeLock(TRUE);

    Cache::shouldReceive('lock')
         ->once()
         ->andReturn($lock);

    $kernel = Mockery::mock(ConsoleKernel::class);
    $kernel->shouldReceive('call')
           ->once()
           ->with('git:pull')
           ->andReturn(0);

    $kernel->shouldReceive('call')
           ->once()
           ->with('composer:install')
           ->andReturn(0);

    Artisan::swap($kernel);

    (new HandlePushWebhook(new GitHubWebhookCall()))->handle();

    $this->assertTrue($lock->released);
  }

  public function test_it_releases_the_lock_when_a_command_fails()
  : void {
    $lock = new FakeLock(TRUE);

    Cache::shouldReceive('lock')
         ->once()
         ->andReturn($lock);

    $kernel = Mockery::mock(ConsoleKernel::class);
    $kernel->shouldReceive('call')
           ->once()
           ->with('git:pull')
           ->andReturn(1);

    $kernel->shouldReceive('call')
           ->never()
           ->with('composer:install');

    Artisan::swap($kernel);

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('The [git:pull] command failed with exit code 1.');

    try {
      (new HandlePushWebhook(new GitHubWebhookCall()))->handle();
    } finally {
      $this->assertTrue($lock->released);
    }
  }
}
