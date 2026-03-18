<?php

namespace Pittacusw\Core\Tests\Support;

use Pittacusw\Core\Support\ProcessResult;
use Pittacusw\Core\Contracts\RunsExternalProcesses;

class FakeProcessRunner implements RunsExternalProcesses {

  /**
   * @var array<int, array<int, string>>
   */
  public array $commands = [];

  /**
   * @param array<int, ProcessResult> $results
   */
  public function __construct(private array $results) { }

  public function run(array $command, ?string $workingDirectory = null)
  : ProcessResult {
    $this->commands[] = $command;

    return array_shift($this->results) ?? new ProcessResult(0);
  }
}
