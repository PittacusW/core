<?php

namespace Pittacusw\Core\Query;

use DateTimeInterface;

class RememberableBuilder extends \Illuminate\Database\Query\Builder {

  protected ?string $cacheKey = null;
  protected DateTimeInterface|int|null $cacheSeconds = null;
  protected array|string|null $cacheTags = null;
  protected ?string $cacheDriver = null;
  protected string $cachePrefix = 'rememberable';

  public function get($columns = ['*']) {
    if ($this->cacheSeconds !== null) {
      return $this->getCached($columns);
    }

    return parent::get($columns);
  }

  public function getCached($columns = ['*']) {
    if ($this->columns === null) {
      $this->columns = $columns;
    }

    return $this->resolveFromCache(
      $this->getCacheKey(),
      $this->getCacheCallback($columns),
    );
  }

  public function pluck($column, $key = null) {
    if ($this->cacheSeconds !== null) {
      return $this->pluckCached($column, $key);
    }

    return parent::pluck($column, $key);
  }

  public function pluckCached($column, $key = null) {
    return $this->resolveFromCache(
      $this->getCacheKey($column . $key),
      $this->pluckCacheCallback($column, $key),
    );
  }

  public function remember(DateTimeInterface|int $seconds, ?string $key = null)
  : static {
    $this->cacheSeconds = $seconds;
    $this->cacheKey = $key;

    return $this;
  }

  public function rememberForever(?string $key = null)
  : static {
    return $this->remember(-1, $key);
  }

  public function dontRemember()
  : static {
    $this->cacheSeconds = null;
    $this->cacheKey = null;
    $this->cacheTags = null;

    return $this;
  }

  public function doNotRemember()
  : static {
    return $this->dontRemember();
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

  public function getCacheKey(mixed $appends = null)
  : string {
    return $this->cachePrefix . ':' . ($this->cacheKey ?: $this->generateCacheKey($appends));
  }

  public function generateCacheKey(mixed $appends = null)
  : string {
    return hash('sha256', $this->connection->getName() . $this->toSql() . serialize($this->getBindings()) . serialize($appends));
  }

  protected function resolveFromCache(string $key, \Closure $callback) {
    $cache = $this->getCache();
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

  protected function getCacheCallback(array $columns)
  : \Closure {
    return function() use ($columns) {
      $this->cacheSeconds = null;

      return $this->get($columns);
    };
  }

  protected function pluckCacheCallback(string $column, mixed $key = null)
  : \Closure {
    return function() use ($column, $key) {
      $this->cacheSeconds = null;

      return $this->pluck($column, $key);
    };
  }
}
