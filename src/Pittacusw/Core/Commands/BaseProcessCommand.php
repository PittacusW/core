<?php

namespace Pittacusw\Core\Commands;

use Illuminate\Console\Command;
use Pittacusw\Core\Support\ProcessResult;
use Pittacusw\Core\Contracts\RunsExternalProcesses;

abstract class BaseProcessCommand extends Command {

  public function __construct(protected readonly RunsExternalProcesses $processRunner) {
    parent::__construct();
  }

  protected function executeExternalCommand(array $command)
  : ProcessResult {
    $result = $this->processRunner->run($command, base_path());

    $this->writeProcessOutput($result);

    return $result;
  }

  protected function failForProcessResult(array $command, ProcessResult $result)
  : int {
    $this->components->error(sprintf(
      'Command failed with exit code %d: %s',
      $result->exitCode,
      implode(' ', $command),
    ));

    return self::FAILURE;
  }

  protected function runExternalCommand(array $command)
  : int {
    $result = $this->executeExternalCommand($command);

    if ($result->successful()) {
      return self::SUCCESS;
    }

    return $this->failForProcessResult($command, $result);
  }

  protected function writeProcessOutput(ProcessResult $result)
  : void {
    if ($result->output !== '') {
      $this->output->write($result->output);
    }

    if ($result->errorOutput !== '') {
      $this->output->write($result->errorOutput);
    }
  }
}
