<?php

namespace Pittacusw\Core\Providers;

use Illuminate\Routing\Router;
use Pittacusw\Core\Support\Package;
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

}