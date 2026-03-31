<?php

namespace Pittacusw\Core\Query;

use Closure;
use DateTimeInterface;
use Illuminate\Database\Query\Builder;

class RememberableBuilder extends Builder {

  protected ?string                    $cacheKey           = NULL;
  protected DateTimeInterface|int|null $cacheSeconds       = NULL;
  protected array|string|null          $cacheTags          = NULL;
  protected ?string                    $cacheDriver        = NULL;
  protected string                     $cachePrefix        = 'rememberable';
  protected                            $cacheFlushCallback = NULL;

  public function get($columns = ['*']) {
    if ($this->cacheSeconds !== NULL) {
      return $this->getCached($columns);
    }

    return parent::get($columns);
  }

  public function getCached($columns = ['*']) {
    if ($this->columns === NULL) {
      $this->columns = $columns;
    }

    return $this->resolveFromCache(
     $this->getCacheKey(),
     $this->getCacheCallback($columns),
    );
  }

  protected function resolveFromCache(string $key, Closure $callback) {
    $cache   = $this->getCache();
    $seconds = $this->cacheSeconds;

    if ($seconds instanceof DateTimeInterface || $seconds > 0) {
      return $cache->remember($key, $seconds, $callback);
    }

    return $cache->rememberForever($key, $callback);
  }

  protected function getCache() {
    $cache = $this->getCacheDriver();

    return $this->cacheTags ? $cache->tags($this->cacheTags) : $cache;
  }

  protected function getCacheDriver() {
    return app('cache')->driver($this->cacheDriver);
  }

  public function remember(DateTimeInterface|int $seconds, ?string $key = NULL)
  : static {
    $this->cacheSeconds = $seconds;
    $this->cacheKey     = $key;

    return $this;
  }

  public function rememberForever(?string $key = NULL)
  : static {
    return $this->remember(- 1, $key);
  }

  public function getCacheKey(mixed $appends = NULL)
  : string {
    return $this->cachePrefix . ':' . ($this->cacheKey ?: $this->generateCacheKey($appends));
  }

  public function generateCacheKey(mixed $appends = NULL)
  : string {
    return hash('sha256', $this->connection->getDatabaseName() . $this->toSql() . serialize($this->getBindings()) . serialize($appends));
  }

  protected function getCacheCallback(array $columns)
  : Closure {
    return function() use ($columns) {
      $this->cacheSeconds = NULL;

      return $this->get($columns);
    };
  }

  public function pluck($column, $key = NULL) {
    if ($this->cacheSeconds !== NULL) {
      return $this->pluckCached($column, $key);
    }

    return parent::pluck($column, $key);
  }

  public function pluckCached($column, $key = NULL) {
    return $this->resolveFromCache(
     $this->getCacheKey($column . $key),
     $this->pluckCacheCallback($column, $key),
    );
  }

  protected function pluckCacheCallback(string $column, mixed $key = NULL)
  : Closure {
    return function() use ($column, $key) {
      $this->cacheSeconds = NULL;

      return $this->pluck($column, $key);
    };
  }

  public function doNotRemember()
  : static {
    return $this->dontRemember();
  }

  public function dontRemember()
  : static {
    $this->cacheSeconds = NULL;
    $this->cacheKey     = NULL;
    $this->cacheTags    = NULL;

    return $this;
  }

  public function prefix(string $prefix)
  : static {
    $this->cachePrefix = $prefix;

    return $this;
  }

  public function cacheTags(array|string $cacheTags)
  : static {
    $this->cacheTags = $cacheTags;

    return $this;
  }

  public function cacheDriver(string $cacheDriver)
  : static {
    $this->cacheDriver = $cacheDriver;

    return $this;
  }

  public function onCacheFlush(callable $callback)
  : static {
    $this->cacheFlushCallback = $callback;

    return $this;
  }

  public function insert(array $values) {
    $result = parent::insert($values);

    $this->flushCacheAfterMutation($result);

    return $result;
  }

  protected function flushCacheAfterMutation(mixed $result)
  : void {
    if (!$result || !is_callable($this->cacheFlushCallback)) {
      return;
    }

    call_user_func($this->cacheFlushCallback);
  }

  public function insertOrIgnore(array $values) {
    $result = parent::insertOrIgnore($values);

    $this->flushCacheAfterMutation($result);

    return $result;
  }

  public function insertGetId(array $values, $sequence = NULL) {
    $result = parent::insertGetId($values, $sequence);

    $this->flushCacheAfterMutation($result !== 0 && $result !== NULL);

    return $result;
  }

  public function updateFrom(array $values) {
    $result = parent::updateFrom($values);

    $this->flushCacheAfterMutation($result);

    return $result;
  }

  public function upsert(array $values, array|string $uniqueBy, ?array $update = NULL) {
    $result = parent::upsert($values, $uniqueBy, $update);

    $this->flushCacheAfterMutation($result);

    return $result;
  }

  public function truncate() {
    parent::truncate();

    $this->flushCacheAfterMutation(TRUE);
  }
}
