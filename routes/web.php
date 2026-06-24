<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the News Aggregator API please visit api documentation for more information at /api/docs']);
});

