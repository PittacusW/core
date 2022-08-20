<?php

namespace Pittacusw\Core\Commands;

use Illuminate\Console\Command;

class ComposerInstallCommand extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'composer:install';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Installs composer packages';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle() {
    $this->line(exec("composer install"));
  }
}
