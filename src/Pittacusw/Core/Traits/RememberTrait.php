<?php

namespace Pittacusw\Core\Traits;

use Watson\Rememberable\Rememberable;

trait RememberTrait {

  use Rememberable;

  protected $rememberFor;
  protected $rememberCacheTag;

  public function __construct(array $attributes = []) {
    $this->rememberFor      = now()->addYear();
    $this->rememberCacheTag = $this->table;
    parent::__construct($attributes);
  }

  public static function bootRememberTrait() {
    static::creating(fn($model) => self::flushCache($model->table));
    static::updating(fn($model) => self::flushCache($model->table));
  }
}
