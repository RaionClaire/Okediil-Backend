<?php

use Illuminate\Support\Facades\Route;

Route::get('log-viewers', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
