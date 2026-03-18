<?php

namespace Pittacusw\Core\Commands;

class GitAddCommand extends BaseProcessCommand {

  protected $signature = 'git:add {message?}';

  protected $description = 'Commits and pushes the current working tree';

  public function handle()
  : int {
    $message = $this->argument('message') ?: 'Backup';

    if ($this->runExternalCommand(['git', 'add', '.']) !== self::SUCCESS) {
      return self::FAILURE;
    }

    $result = $this->executeExternalCommand(['git', 'diff', '--cached', '--quiet']);

    if ($result->exitCode === 0) {
      $this->components->info('No staged changes to commit.');

      return self::SUCCESS;
    }

    if ($result->exitCode !== 1) {
      return $this->failForProcessResult(['git', 'diff', '--cached', '--quiet'], $result);
    }

    if ($this->runExternalCommand(['git', 'commit', '-m', $message]) !== self::SUCCESS) {
      return self::FAILURE;
    }

    return $this->runExternalCommand(['git', 'push']);
  }
}
