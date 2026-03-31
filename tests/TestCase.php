<?php

namespace Pittacusw\Core\Tests;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Pittacusw\Core\Providers\CoreServiceProvider;
use Spatie\GitHubWebhooks\GitHubWebhooksServiceProvider;

abstract class TestCase extends Orchestra {

  protected function getPackageProviders($app)
  : array {
    return [
     GitHubWebhooksServiceProvider::class,
     CoreServiceProvider::class,
    ];
  }

  protected function defineEnvironment($app)
  : void {
    $app['config']->set('app.key', 'base64:9gFJ3NV+M2Gw6OV4xJe2X6FugmU6W1k7Dbmd8r3H4XA=');
    $app['config']->set('cache.default', 'array');
    $app['config']->set('database.default', 'testing');
    $app['config']->set('database.connections.testing', [
     'driver'   => 'sqlite',
     'database' => ':memory:',
     'prefix'   => '',
    ]);
    $app['config']->set('queue.default', 'sync');
    $app['config']->set('github-webhooks.signing_secret', 'test-secret');
    $app['config']->set('github-webhooks.jobs', []);
  }

  protected function setUp()
  : void {
    parent::setUp();

    Schema::create('rememberable_models', function(Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->timestamps();
      $table->softDeletes();
    });
  }

  protected function tearDown()
  : void {
    Schema::dropIfExists('rememberable_models');

    parent::tearDown();
  }
}
