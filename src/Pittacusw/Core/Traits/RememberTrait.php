<?php

namespace Pittacusw\Core\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Pittacusw\Core\Query\RememberableBuilder;

trait RememberTrait {

  public static function bootRememberTrait()
  : void {
    static::saved(fn($model) => $model->flushRememberCache());
    static::deleted(fn($model) => $model->flushRememberCache());

    if (in_array(SoftDeletes::class, class_uses_recursive(static::class), TRUE)) {
      static::restored(fn($model) => $model->flushRememberCache());
    }
  }

  protected function newBaseQueryBuilder()
  : \Illuminate\Database\Query\Builder {
    $connection = $this->getConnection();
    $builder = new RememberableBuilder(
      $connection,
      $connection->getQueryGrammar(),
      $connection->getPostProcessor(),
    );

    $builder->remember($this->getRememberFor());

    if (($cacheTag = $this->getRememberCacheTag()) !== null) {
      $builder->cacheTags($cacheTag);
    }

    $builder->prefix($this->getRememberCachePrefix());

    if (($cacheDriver = $this->getRememberCacheDriver()) !== null) {
      $builder->cacheDriver($cacheDriver);
    }

    return $builder;
  }

  public function flushRememberCache()
  : void {
    $cacheStore = Cache::getStore();
    $tag = $this->getRememberCacheTag();

    if ($tag !== null && $this->cacheStoreSupportsTags()) {
      Cache::tags($tag)->flush();

      return;
    }

    $this->flushPrefixedCacheKeys($cacheStore, $this->getRememberCachePrefix());
  }

  protected function cacheStoreSupportsTags()
  : bool {
    return method_exists(Cache::getStore(), 'tags');
  }

  protected function getRememberFor()
  : \DateTimeInterface|int {
    return property_exists($this, 'rememberFor') ? $this->rememberFor : 31536000;
  }

  protected function getRememberCacheTag()
  : ?string {
    if (! $this->cacheStoreSupportsTags()) {
      return null;
    }

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

  protected function getRememberCacheDriver()
  : ?string {
    return property_exists($this, 'rememberCacheDriver')
      ? $this->rememberCacheDriver
      : null;
  }

  protected function flushPrefixedCacheKeys(object $cacheStore, string $keyPrefix)
  : void {
    if (method_exists($cacheStore, 'connection') && method_exists($cacheStore->connection(), 'scan')) {
      $connection = $cacheStore->connection();
      $cursor = null;
      $defaultCursor = '0';
      $cachePrefix = config('cache.prefix');
      $fullPrefix = $cachePrefix ? "{$cachePrefix}:" : '';

      do {
        $scanResult = $connection->scan($cursor, [
          'match' => "{$fullPrefix}{$keyPrefix}:*",
          'count' => 100,
        ]);

        if (! is_array($scanResult) || count($scanResult) !== 2) {
          break;
        }

        [$cursor, $keys] = $scanResult;

        foreach ($keys as $key) {
          Cache::forget(Str::after($key, $fullPrefix));
        }
      } while ((string) $cursor !== $defaultCursor);

      return;
    }

    logger()->warning("RememberTrait: Cache driver does not support tags or key enumeration. Cache not flushed for {$this->getTable()}.");
  }
}
