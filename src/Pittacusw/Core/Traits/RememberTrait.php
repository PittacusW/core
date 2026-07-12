<?php

namespace Pittacusw\Core\Traits;

use DateTimeInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Pittacusw\Core\Query\RememberableBuilder;

trait RememberTrait {

  public static function bootRememberTrait()
  : void {
    static::created(fn($model) => $model->flushRememberCache());
    static::saved(fn($model) => $model->flushRememberCache());
    static::updated(fn($model) => $model->flushRememberCache());
    static::deleted(fn($model) => $model->flushRememberCache());

    if (in_array(SoftDeletes::class, class_uses_recursive(static::class), TRUE)) {
      static::restored(fn($model) => $model->flushRememberCache());
    }
  }

  /**
   * Invalidation rotates the tag's version token instead of deleting cached
   * entries. Cached query keys embed the token, so rotating it orphans every
   * previous entry at once — including entries written AFTER the rotation by
   * requests that read the database BEFORE the mutation. Deleting keys (tag
   * flush or scan) cannot close that race; rotating the namespace can.
   * Orphaned entries age out through their TTL.
   */
  public function flushRememberCache()
  : void {
    RememberableBuilder::rotateCacheVersion(
     app('cache')->driver($this->getRememberCacheDriver()),
     $this->getRememberCacheTag(),
    );
  }

  protected function getRememberCacheTag()
  : string {
    if (property_exists($this, 'rememberCacheTag')) {
      return $this->rememberCacheTag;
    }

    return $this->getTable();
  }

  protected function getRememberCachePrefix()
  : string {
    if (property_exists($this, 'rememberCachePrefix')) {
      return $this->rememberCachePrefix;
    }

    return "rememberable:{$this->getTable()}";
  }

  protected function newBaseQueryBuilder()
  : Builder {
    $connection = $this->getConnection();
    $builder    = new RememberableBuilder(
     $connection,
     $connection->getQueryGrammar(),
     $connection->getPostProcessor(),
    );

    $builder->remember($this->getRememberFor());
    $builder->onCacheFlush(fn() => $this->flushRememberCache());
    $builder->cacheTags($this->getRememberCacheTag());
    $builder->prefix($this->getRememberCachePrefix());

    if (($cacheDriver = $this->getRememberCacheDriver()) !== NULL) {
      $builder->cacheDriver($cacheDriver);
    }

    return $builder;
  }

  protected function getRememberFor()
  : DateTimeInterface|int {
    return property_exists($this, 'rememberFor') ? $this->rememberFor : 31536000;
  }

  protected function getRememberCacheDriver()
  : ?string {
    return property_exists($this, 'rememberCacheDriver')
     ? $this->rememberCacheDriver
     : NULL;
  }
}
