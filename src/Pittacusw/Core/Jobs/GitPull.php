<?php

namespace Pittacusw\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GitPull implements ShouldQueue {

  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function handle() {
    exec('git pull');
  }
}
