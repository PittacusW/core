<?php

use Illuminate\Support\Facades\Route;
use Pittacusw\Core\Controllers\GithubController;

Route::post('github', [
 GithubController::class,
 'hook'
])
     ->name('pittacusw.github.hook');
