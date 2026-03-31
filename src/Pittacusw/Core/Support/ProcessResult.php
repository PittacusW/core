<?php

namespace Pittacusw\Core\Support;

final class ProcessResult {

  public function __construct(
   public readonly int    $exitCode,
   public readonly string $output = '',
   public readonly string $errorOutput = '',
  ) {
  }

  public function successful()
  : bool {
    return $this->exitCode === 0;
  }
}
