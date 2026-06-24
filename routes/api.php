<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

/**
 * we can setup authentication if needed, for example using Laravel Sanctum for API token authentication 
 * but for now we will keep it simple and just return the news articles without authentication.
 * since this is a public API, we can also add rate limiting to prevent abuse, for example using Laravel's built-in rate limiting middleware.
 */ 

Route::get('/news', NewsController::class . '@index')->middleware('throttle:60,1'); // limit to 60 requests per minute
