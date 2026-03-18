<?php

namespace Pittacusw\Core\Tests\Support;

class FakeLock {

  public bool $released = FALSE;

  public function __construct(private bool $canAcquire) { }

  public function get()
  : bool {
    return $this->canAcquire;
  }

  public function release()
  : void {
    $this->released = TRUE;
  }
}
