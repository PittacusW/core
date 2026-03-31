<?php

namespace Pittacusw\Core\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Pittacusw\Core\Traits\RememberTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class RememberableModel extends Model {

  use RememberTrait, SoftDeletes;

  protected $fillable = [
   'name',
  ];
}
