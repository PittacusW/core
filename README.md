# Pittacusw Core

Shared Laravel package for Pittacusw projects.

This package currently provides:

- a default GitHub webhook endpoint at `/api/github`
- a queued deployment job that runs `git pull` and `composer install`
- safe, configurable security headers middleware
- `RememberTrait` for model query caching with automatic cache invalidation
- artisan helpers for `git:add`, `git:pull`, and `composer:install`
- package translations

## Compatibility

- PHP: `^8.2`
- Laravel: `^9.26|^10.0|^11.0|^12.0|^13.0`

The runtime package keeps Laravel compatibility in its own constraints. For package tests, use the matching `orchestra/testbench` major for the Laravel version under test.

## Installation

```bash
composer require pittacusw/core
```

Publish the package config if you want to override the defaults:

```bash
php artisan vendor:publish --tag="pittacusw-core-config"
```

If you use the GitHub webhook endpoint, you must also configure the webhook secret in the host application:

```env
GITHUB_WEBHOOK_SECRET=your-webhook-secret
```

## Configuration

The package config file is `config/pittacusw-core.php`.

### Deployment

These settings control the queued `push` webhook handler:

- `PITTACUSW_CORE_DEPLOYMENT_ENABLED`
- `PITTACUSW_CORE_DEPLOYMENT_LOCK_SECONDS`
- `PITTACUSW_CORE_DEPLOYMENT_RETRY_DELAY_SECONDS`

Behavior:

- the package registers `Pittacusw\Core\Jobs\HandlePushWebhook` as the default `push` job only when `github-webhooks.jobs.push` is not already defined by the host app
- deployment runs under a cache lock to prevent overlapping `git pull` and `composer install` executions
- command failures throw, so the queue worker marks the job as failed instead of silently continuing

### Security Headers

These settings control the global middleware registered by the package:

- `PITTACUSW_CORE_SECURITY_HEADERS_ENABLED`
- `PITTACUSW_CORE_SECURITY_HEADERS_FRAME_OPTIONS`
- `PITTACUSW_CORE_SECURITY_HEADERS_REFERRER_POLICY`
- `PITTACUSW_CORE_SECURITY_HEADERS_HSTS_ENABLED`
- `PITTACUSW_CORE_SECURITY_HEADERS_HSTS_MAX_AGE`
- `PITTACUSW_CORE_SECURITY_HEADERS_HSTS_INCLUDE_SUBDOMAINS`
- `PITTACUSW_CORE_SECURITY_HEADERS_HSTS_PRELOAD`

Default headers:

- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `Referrer-Policy: no-referrer`
- `Strict-Transport-Security` on secure requests only

The middleware intentionally does not emit deprecated headers such as `X-XSS-Protection` or `Public-Key-Pins-Report-Only`.

## GitHub Webhooks

The package registers:

- `POST /api/github`

This route points to Spatie's GitHub webhook controller. The route is always available when the package service provider is loaded.

Important notes:

- signature validation is handled by `spatie/laravel-github-webhooks`
- the host application still owns the authoritative `github-webhooks` config
- if you need a custom `push` job, define `github-webhooks.jobs.push` in the host app and the package will not override it
- the webhook route does not publish or own the Spatie migration anymore; the webhook storage table should come from `spatie/laravel-github-webhooks`

## RememberTrait

`RememberTrait` adds model-wide query caching and cache invalidation.

Example:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Pittacusw\Core\Traits\RememberTrait;

class Country extends Model
{
    use RememberTrait;
}
```

Defaults:

- caches model queries for `31536000` seconds
- uses a model-specific cache prefix
- uses cache tags when the current cache store supports them
- flushes cached queries after `saved`, `deleted`, and `restored` events

Optional model properties:

- `$rememberFor`
- `$rememberCachePrefix`
- `$rememberCacheTag`
- `$rememberCacheDriver`

## Console Commands

The package exposes:

- `php artisan git:add {message?}`
- `php artisan git:pull`
- `php artisan composer:install`

These commands execute external processes through Symfony Process instead of shell-constructed `exec()` strings.

## Testing

The package now includes package-owned PHPUnit coverage for:

- route registration and webhook wiring
- service-provider config behavior
- safe security headers
- `RememberTrait` cache invalidation
- external process command dispatch
- deployment locking and failure behavior

Run the package tests from the package root:

```bash
composer install
composer test
```

## Upgrade Notes

Recent internal hardening changed a few package details while preserving the public behavior used by existing projects:

- webhook migration ownership moved back to `spatie/laravel-github-webhooks`
- `RememberTrait` no longer depends on `watson/rememberable`
- deployment commands now fail loudly instead of silently swallowing command errors
- security headers are response-based and configurable
