<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;






Route::prefix('')->group(function () {
 

    // Store (Create a new person)
    Route::post('/videos', [VideoController::class, 'store']);

        // Index (List all persons)
        Route::get('/', [App\Http\Controllers\VideoController::class, 'index']);

      // Show (Get a specific person by ID)
      Route::get('/videos/{id}', [VideoController::class, 'show']);

    // Delete (Delete a person by ID)
    Route::delete('/videos/{id}', [VideoController::class, 'destroy']);
});