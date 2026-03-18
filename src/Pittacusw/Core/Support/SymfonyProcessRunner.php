<?php

namespace Pittacusw\Core\Support;

use Symfony\Component\Process\Process;
use Pittacusw\Core\Contracts\RunsExternalProcesses;

final class SymfonyProcessRunner implements RunsExternalProcesses {

  public function run(array $command, ?string $workingDirectory = null)
  : ProcessResult {
    $process = new Process($command, $workingDirectory ?? base_path());
    $process->setTimeout(null);
    $process->run();

    return new ProcessResult(
      $process->getExitCode() ?? 1,
      $process->getOutput(),
      $process->getErrorOutput(),
    );
  }
}
