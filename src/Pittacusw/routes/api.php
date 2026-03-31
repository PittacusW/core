<?php

use Illuminate\Support\Facades\Route;
use Spatie\GitHubWebhooks\Http\Controllers\GitHubWebhooksController;

Route::post('api/github', GitHubWebhooksController::class);