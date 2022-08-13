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