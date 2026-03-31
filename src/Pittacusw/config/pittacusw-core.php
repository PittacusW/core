<?php

return [
 'deployment' => [
  'enabled'             => env('PITTACUSW_CORE_DEPLOYMENT_ENABLED', TRUE),
  'lock_seconds'        => (int) env('PITTACUSW_CORE_DEPLOYMENT_LOCK_SECONDS', 600),
  'retry_delay_seconds' => (int) env('PITTACUSW_CORE_DEPLOYMENT_RETRY_DELAY_SECONDS', 30),
 ],

 'security_headers' => [
  'enabled'         => env('PITTACUSW_CORE_SECURITY_HEADERS_ENABLED', TRUE),
  'frame_options'   => env('PITTACUSW_CORE_SECURITY_HEADERS_FRAME_OPTIONS', 'DENY'),
  'referrer_policy' => env('PITTACUSW_CORE_SECURITY_HEADERS_REFERRER_POLICY', 'no-referrer'),
  'hsts'            => [
   'enabled'            => env('PITTACUSW_CORE_SECURITY_HEADERS_HSTS_ENABLED', TRUE),
   'max_age'            => (int) env('PITTACUSW_CORE_SECURITY_HEADERS_HSTS_MAX_AGE', 31536000),
   'include_subdomains' => env('PITTACUSW_CORE_SECURITY_HEADERS_HSTS_INCLUDE_SUBDOMAINS', TRUE),
   'preload'            => env('PITTACUSW_CORE_SECURITY_HEADERS_HSTS_PRELOAD', FALSE),
  ],
 ],
];
