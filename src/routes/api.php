<?php

use Illuminate\Support\Facades\Route;
use Pittacusw\Core\Controllers\GithubController;

Route::prefix('api')->githubWebhooks('github');
