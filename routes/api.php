<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::middleware([
    'api'
])->group(function (){
    Route::post("/login",[UserController::class,'auth']);
    Route::middleware(['auth:api'])->group(function (){

        //Auth
        Route::post("/validate-token",[UserController::class,'validateToken']);
        Route::delete("/logout",[UserController::class,'logout']);

        Route::get("/user",[UserController::class,'index']);

    });
});
