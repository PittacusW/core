<?php

namespace Pittacusw\Core\Tests\Feature;

use Pittacusw\Core\Tests\TestCase;

class CompatibilityMetadataTest extends TestCase {

  public function test_package_metadata_supports_laravel_nine_through_thirteen()
  : void {
    $composer = json_decode(
      file_get_contents(__DIR__ . '/../../composer.json'),
      TRUE,
      512,
      JSON_THROW_ON_ERROR,
    );

    $this->assertSame(
      '^9.26|^10.0|^11.0|^12.0|^13.0',
      $composer['require']['laravel/framework'],
    );
    $this->assertArrayNotHasKey('watson/rememberable', $composer['require']);
    $this->assertArrayHasKey('orchestra/testbench', $composer['require-dev']);
    $this->assertSame(
      '^10.5|^11.5|^12.0',
      $composer['require-dev']['phpunit/phpunit'],
    );
  }
}
