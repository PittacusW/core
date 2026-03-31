<?php

namespace Pittacusw\Core\Providers;

use Illuminate\Contracts\Http\Kernel;
use Spatie\LaravelPackageTools\Package;
use Pittacusw\Core\Commands\GitAddCommand;
use Pittacusw\Core\Jobs\HandlePushWebhook;
use Pittacusw\Core\Commands\GitPullCommand;
use Pittacusw\Core\Middlewares\SecurityHeaders;
use Pittacusw\Core\Support\SymfonyProcessRunner;
use Pittacusw\Core\Commands\ComposerInstallCommand;
use Pittacusw\Core\Contracts\RunsExternalProcesses;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CoreServiceProvider extends PackageServiceProvider {

  public function registeringPackage()
  : void {
    $this->app->bind(RunsExternalProcesses::class, SymfonyProcessRunner::class);
  }

  public function bootingPackage()
  : void {
    if (!config('pittacusw-core.security_headers.enabled', TRUE)) {
      return;
    }

    app(Kernel::class)->pushMiddleware(SecurityHeaders::class);
  }

  public function packageBooted()
  : void {
    $jobs = config('github-webhooks.jobs', []);

    if (!array_key_exists('push', $jobs) && !array_key_exists('*', $jobs)) {
      $jobs['push'] = HandlePushWebhook::class;

      config()->set('github-webhooks.jobs', $jobs);
    }
  }

  public function configurePackage(Package $package)
  : void {
    $package->name('pittacusw-core')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasRoute('api')
            ->hasCommand(GitAddCommand::class)
            ->hasCommand(GitPullCommand::class)
            ->hasCommand(ComposerInstallCommand::class);
  }

}
