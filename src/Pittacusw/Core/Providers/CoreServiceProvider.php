<?php

namespace Pittacusw\Core\Providers;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Pittacusw\Core\Commands\GitAddCommand;
use Pittacusw\Core\Commands\GitPullCommand;
use PittacusW\Core\Middlewares\SecurityHeaders;
use Pittacusw\Core\Commands\ComposerInstallCommand;
use function base_path;

class CoreServiceProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = FALSE;

  /**
   * Bootstrap the application events.
   *
   * @return void
   */
  public function boot(Router $router, Kernel $kernel) {
    $kernel->pushMiddleware(SecurityHeaders::class);
    $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register() {
    $this->app->singleton('command.git.add', function($app) {
      return new GitAddCommand;
    });
    $this->commands('command.git.add');

    $this->app->singleton('command.git.pull', function($app) {
      return new GitPullCommand;
    });
    $this->commands('command.git.add');
    $this->app->singleton('command.composer.install', function($app) {
      return new ComposerInstallCommand;
    });
    $this->commands('command.git.add');
  }

  public function provides() {
  }
}