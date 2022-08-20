<?php

use Illuminate\Support\Facades\Route;
use Pittacusw\Core\Controllers\GithubController;
use Spatie\GitHubWebhooks\Http\Controllers\GitHubWebhooksController;

Route::post('api/github', GitHubWebhooksController::class);