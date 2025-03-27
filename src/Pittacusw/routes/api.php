<?php

use Illuminate\Support\Facades\Route;

Route::post('api/github', \Spatie\GitHubWebhooks\Http\Controllers\GitHubWebhooksController::class);