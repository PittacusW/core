<?php

namespace Pittacusw\Core\Commands;

class ComposerInstallCommand extends BaseProcessCommand {

  protected $signature = 'composer:install';

  protected $description = 'Installs composer packages';

  public function handle()
  : int {
    return $this->runExternalCommand([
                                      'composer',
                                      'install'
                                     ]);
  }
}
