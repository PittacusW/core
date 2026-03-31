<?php

namespace Pittacusw\Core\Tests\Feature;

use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Pittacusw\Core\Tests\TestCase;
use Pittacusw\Core\Tests\Fixtures\RememberableModel;
use Pittacusw\Core\Tests\Fixtures\ConfigurableRememberableModel;

class RememberTraitTest extends TestCase {

  public function test_it_flushes_cached_queries_after_updates()
  : void {
    $model = RememberableModel::create(['name' => 'Alpha']);

    $this->assertSame('Alpha', RememberableModel::query()
                                                ->value('name'));

    DB::table('rememberable_models')
      ->whereKey($model->getKey())
      ->update(['name' => 'Beta']);

    $this->assertSame('Alpha', RememberableModel::query()
                                                ->value('name'));

    $model->name = 'Gamma';
    $model->save();

    $this->assertSame('Gamma', RememberableModel::query()
                                                ->value('name'));
  }

  public function test_it_flushes_cached_queries_after_eloquent_builder_updates()
  : void {
    $model = RememberableModel::create(['name' => 'Alpha']);

    $this->assertSame('Alpha', RememberableModel::query()
                                                ->value('name'));

    RememberableModel::query()
                     ->whereKey($model->getKey())
                     ->update(['name' => 'Beta']);

    $this->assertSame('Beta', RememberableModel::query()
                                               ->value('name'));
  }

  public function test_it_flushes_cached_queries_after_deletes()
  : void {
    $model = RememberableModel::create(['name' => 'Alpha']);

    $this->assertSame(1, RememberableModel::query()
                                          ->count());

    $model->delete();

    $this->assertSame(0, RememberableModel::query()
                                          ->count());
  }

  public function test_it_flushes_cached_queries_after_restore()
  : void {
    $model = RememberableModel::create(['name' => 'Alpha']);

    $this->assertSame(1, RememberableModel::query()
                                          ->count());

    $model->delete();
    $this->assertSame(0, RememberableModel::query()
                                          ->count());

    $model->restore();

    $this->assertSame(1, RememberableModel::query()
                                          ->count());
  }

  public function test_it_supports_model_level_overrides_for_the_remember_configuration()
  : void {
    $model = new ConfigurableRememberableModel();

    $this->assertSame(120, $model->exposedRememberFor());
    $this->assertSame('custom-tag', $model->exposedRememberCacheTag());
    $this->assertSame('custom-prefix', $model->exposedRememberCachePrefix());
    $this->assertSame('array', $model->exposedRememberCacheDriver());
  }

  public function test_it_supports_datetime_expirations_for_model_level_remember_for_overrides()
  : void {
    $model = new ConfigurableRememberableModel();

    $this->assertInstanceOf(DateTimeInterface::class, $model->exposedRememberForDateTime());
  }
}
