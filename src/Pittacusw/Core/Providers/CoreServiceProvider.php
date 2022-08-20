<?php

namespace Pittacusw\Core\Providers;

use Illuminate\Routing\Router;
use Spatie\LaravelPackageTools\Package;
use Pittacusw\Core\Commands\GitAddCommand;
use Pittacusw\Core\Commands\GitPullCommand;
use Pittacusw\Core\Middlewares\SecurityHeaders;
use Pittacusw\Core\Commands\ComposerInstallCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CoreServiceProvider extends PackageServiceProvider {

  public function configurePackage(Package $package)
  : void {
    $package->name('pittacusw-core')
            ->hasConfigFile()
            ->hasMigration('create_github_webhook_calls_table')
            ->hasMiddleware(SecurityHeaders::class)
            ->hasCommand(GitAddCommand::class)
            ->hasCommand(GitPullCommand::class)
            ->hasCommand(ComposerInstallCommand::class);
  }

  public function hasMiddleware(string $middleware, string $middlewareGroup = NULL)
  : self {
    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);

    if ($middlewareGroup) {
      $kernel->prependMiddlewareToGroup($middlewareGroup, $middleware);
    } else {
      $kernel->pushMiddleware($middleware);
    }

    return $this;
  }

  public function hasMiddlewares(array $middlewareClassNames, string $middlewareGroup = NULL)
  : self {
    collect($middlewareClassNames)->each(
     fn($middlewareClassName) => $this->hasMiddleware($middlewareClassName, $middlewareGroup)
    );

    return $this;
  }
}