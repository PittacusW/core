<?php

namespace Pittacusw\Core\Commands;

use Illuminate\Console\Command;

class GitPullCommand extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'git:pull';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Pulls changes from the repository';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle() {
    $this->line(exec('git pull'));
  }
}
