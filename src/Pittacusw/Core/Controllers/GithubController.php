<?php

namespace Pittacusw\Core\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Pittacusw\Core\Middlewares\Jobs\GitPull;

class GithubController extends Controller {

  public function hook(Request $request) {
    GitPull::dispatch();
  }
}