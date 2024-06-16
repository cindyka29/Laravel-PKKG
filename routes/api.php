<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityController;


Route::middleware([
    'api'
])->group(function (){
    Route::post("/login",[UserController::class,'auth']);
    Route::middleware(['auth:api'])->group(function (){

        //Auth
        Route::post("/validate-token",[UserController::class,'validateToken']);
        Route::delete("/logout",[UserController::class,'logout']);

        // user
        Route::get("/user",[UserController::class,'index']);
        Route::post("/user",[UserController::class,'store']);
        Route::get("/user/{id}",[UserController::class,"show"]);
        Route::put("/user/{id}",[UserController::class,"update"]);
        Route::delete("/user/{id}",[UserController::class,"destroy"]);

        // activity
        Route::get("/activity",[ActivityController::class,'index']);
        Route::post("/activity",[ActivityController::class,'store']);
        Route::get("/activity/{id}",[ActivityController::class,'show']);
        Route::put("/activity/{id}",[ActivityController::class,'update']);
        Route::delete("/activity/{id}",[ActivityController::class,'destroy']);
        Route::get("/activity-month",[ActivityController::class,'getByMonth']);
    });
});
