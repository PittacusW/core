<?php

namespace Pittacusw\Core\Traits;

use Watson\Rememberable\Rememberable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
    static::creating(fn($model) => self::flushCache($model));
    static::updating(fn($model) => self::flushCache($model));
  }

  protected static function flushCache($model) {
    $tag = $model->getTable();
    $cacheStore = Cache::getStore();

    if (method_exists($cacheStore, 'tags')) {
      Cache::tags($tag)->flush();
      return;
    }

    $baseKey = strtolower(class_basename($model));
    $keyPrefix = "rememberable:$baseKey:";

    if (method_exists($cacheStore, 'connection') && method_exists($cacheStore->connection(), 'keys')) {
      $redis = $cacheStore->connection();
      $keys = $redis->keys("*$keyPrefix*");

      foreach ($keys as $key) {
        $plainKey = Str::after($key, config('cache.prefix') . ':');
        Cache::forget($plainKey);
      }
    } else {
      logger()->warning("RememberTrait: Cache driver does not support tags or key enumeration. Cache not flushed for {$model->getTable()}.");
    }
  }
}
