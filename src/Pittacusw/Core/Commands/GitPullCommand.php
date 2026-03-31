<?php

namespace Pittacusw\Core\Commands;

class GitPullCommand extends BaseProcessCommand {

  protected $signature = 'git:pull';

  protected $description = 'Pulls changes from the repository';

  public function handle()
  : int {
    return $this->runExternalCommand([
                                      'git',
                                      'pull'
                                     ]);
  }
}
