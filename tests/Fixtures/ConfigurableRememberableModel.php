<?php

namespace Pittacusw\Core\Tests\Fixtures;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Pittacusw\Core\Traits\RememberTrait;

class ConfigurableRememberableModel extends Model {

  use RememberTrait;

  protected                   $rememberFor         = 120;
  protected                   $rememberCacheTag    = 'custom-tag';
  protected                   $rememberCachePrefix = 'custom-prefix';
  protected                   $rememberCacheDriver = 'array';
  protected DateTimeInterface $rememberForDateTime;

  public function __construct(array $attributes = []) {
    parent::__construct($attributes);

    $this->rememberForDateTime = now()->addMinute();
  }

  public function exposedRememberFor()
  : int {
    return $this->getRememberFor();
  }

  public function exposedRememberForDateTime()
  : DateTimeInterface {
    $originalRememberFor = $this->rememberFor;
    $this->rememberFor   = $this->rememberForDateTime;

    try {
      return $this->getRememberFor();
    } finally {
      $this->rememberFor = $originalRememberFor;
    }
  }

  public function exposedRememberCacheTag()
  : ?string {
    return $this->getRememberCacheTag();
  }

  public function exposedRememberCachePrefix()
  : string {
    return $this->getRememberCachePrefix();
  }

  public function exposedRememberCacheDriver()
  : ?string {
    return $this->getRememberCacheDriver();
  }
}
