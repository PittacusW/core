<?php

namespace Pittacusw\Core\Controllers;

use Illuminate\Http\Request;
use PittacusW\Core\Jobs\GitPull;
use Illuminate\Routing\Controller;

class GithubController extends Controller {

  public function hook(Request $request) {
    GitPull::dispatch();
  }
}