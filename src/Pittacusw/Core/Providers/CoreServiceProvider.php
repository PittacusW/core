<?php

namespace Pittacusw\Core\Providers;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Pittacusw\Core\Commands\GitAddCommand;
use Pittacusw\Core\Commands\GitPullCommand;
use Pittacusw\Core\Middlewares\SecurityHeaders;
use Pittacusw\Core\Commands\ComposerInstallCommand;

class CoreServiceProvider extends ServiceProvider {

  protected $defer = FALSE;

  public function boot(Router $router, Kernel $kernel) {
    $this->publishes([
                      __DIR__ . '/../../config/github-webhooks.php' => config_path('github-webhooks.php'),
                      __DIR__ . '/../../migrations/2022_08_19_220300_create_github_webhook_calls_table.php' => base_path('database/migrations/2022_08_19_220300_create_github_webhook_calls_table.php'),
                     ]);
    $kernel->pushMiddleware(SecurityHeaders::class);
    $this->loadRoutesFrom(__DIR__ . '/../../../routes/api.php');
  }

  public function register() {
    $this->app->singleton('command.git.add', function($app) {
      return new GitAddCommand;
    });

    $this->app->singleton('command.git.pull', function($app) {
      return new GitPullCommand;
    });
    $this->app->singleton('command.composer.install', function($app) {
      return new ComposerInstallCommand;
    });
    $this->commands('command.git.add');
    $this->commands('command.git.pull');
    $this->commands('command.composer.install');
  }

}