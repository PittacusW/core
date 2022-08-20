<?php

namespace Pittacusw\Core\Support;

use Spatie\LaravelPackageTools\Package as BasePackage;

class Package extends BasePackage {

  public function hasMiddleware(string $middleware, string $middlewareGroup = NULL)
  : self {
    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);

    if ($middlewareGroup) {
      $kernel->prependMiddlewareToGroup($middlewareGroup, $middleware);
    } else {
      $kernel->pushMiddleware($middleware);
    }

    return $this;
  }

  public function hasMiddlewares(array $middlewareClassNames, string $middlewareGroup = NULL)
  : self {
    collect($middlewareClassNames)->each(
     fn($middlewareClassName) => $this->hasMiddleware($middlewareClassName, $middlewareGroup)
    );

    return $this;
  }
}