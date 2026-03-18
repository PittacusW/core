<?php

namespace Pittacusw\Core\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Pittacusw\Core\Traits\RememberTrait;

class RememberableModel extends Model {

  use RememberTrait, SoftDeletes;

  protected $fillable = [
    'name',
  ];
}
