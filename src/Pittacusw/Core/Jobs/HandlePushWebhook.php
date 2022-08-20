<?php

namespace Pittacusw\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\GitHubWebhooks\Models\GitHubWebhookCall;

class HandlePushWebhook implements ShouldQueue {

  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public GitHubWebhookCall $gitHubWebhookCall;

  public function __construct(public GitHubWebhookCall $webhookCall) { }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle() {
    Artisan::call('git:pull');
    Artisan::call('composer:install');
  }
}
