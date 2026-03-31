<?php

namespace Pittacusw\Core\Contracts;

use Pittacusw\Core\Support\ProcessResult;

interface RunsExternalProcesses {

  public function run(array $command, ?string $workingDirectory = NULL)
  : ProcessResult;
}
