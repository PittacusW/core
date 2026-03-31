<?php

namespace Pittacusw\Core\Jobs;

use RuntimeException;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\GitHubWebhooks\Models\GitHubWebhookCall;

class HandlePushWebhook implements ShouldQueue {

  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function __construct(public GitHubWebhookCall $webhookCall) { }

  public function handle()
  : void {
    if (!config('pittacusw-core.deployment.enabled', TRUE)) {
      return;
    }

    $lock = Cache::lock(
     'pittacusw-core:deployment',
     (int) config('pittacusw-core.deployment.lock_seconds', 600),
    );

    if (!$lock->get()) {
      $this->release((int) config('pittacusw-core.deployment.retry_delay_seconds', 30));

      return;
    }

    try {
      $this->runCommand('git:pull');
      $this->runCommand('composer:install');
    } finally {
      $lock->release();
    }
  }

  protected function runCommand(string $command)
  : void {
    $exitCode = Artisan::call($command);

    if ($exitCode !== 0) {
      throw new RuntimeException(sprintf(
                                  'The [%s] command failed with exit code %d.',
                                  $command,
                                  $exitCode,
                                 ));
    }
  }
}
