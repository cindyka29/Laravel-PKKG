<?php

use App\Http\Controllers\IuranController;
use App\Http\Controllers\KasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\ProgramController;


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
        Route::post("/user/reset-password/{id}",[UserController::class,'resetPassword']);
        Route::post("/user/status/{id}",[UserController::class,'updateStatus']);
        Route::get("/user/{id}",[UserController::class,"show"]);
        Route::put("/user/{id}",[UserController::class,"update"]);
        Route::delete("/user/{id}",[UserController::class,"destroy"]);
        Route::post("/user/change-password/{id}",[UserController::class,"changePassword"]);

        // activity
        Route::get("/activity",[ActivityController::class,'index']);
        Route::post("/activity",[ActivityController::class,'store']);
        Route::get("/activity/{id}",[ActivityController::class,'show']);
        Route::put("/activity/{id}",[ActivityController::class,'update']);
        Route::delete("/activity/{id}",[ActivityController::class,'destroy']);
        Route::get("/activity-month",[ActivityController::class,'getByMonth']);
        Route::get("/activity-date",[ActivityController::class,'getByDate']);
        Route::post("/activity/documentation",[ActivityController::class,'addDocumentation']);
        Route::delete("/activity/documentation/{image_id}",[ActivityController::class,'deleteDocumentation']);

        // absence
        Route::get("/absence/user/{user_id}",[AbsenceController::class,'index']);
        Route::post("/absence",[AbsenceController::class,'store']);
        Route::get("/absence/{id}",[AbsenceController::class,'show']);
        Route::put("/absence/{id}",[AbsenceController::class,'update']);
        Route::delete("/absence/{id}",[AbsenceController::class,'destroy']);
        Route::get("/absence/{activity_id}/activity",[AbsenceController::class,'getUserAbsentByActivity']);
        Route::get("/absence/not/{activity_id}/activity",[AbsenceController::class,'getUserNotAbsentByActivity']);

        // Iuran
        Route::post("/iuran",[IuranController::class,'store']);
        Route::get("/iuran/report",[IuranController::class,'reportIuran']);
        Route::get("/iuran/month",[IuranController::class,"getMonthIuran"]);
        Route::get("/iuran/user/{user_id}",[IuranController::class,'index']);
        Route::get("/iuran/{id}",[IuranController::class,'show']);
        Route::put("/iuran/{id}",[IuranController::class,'update']);
        Route::delete("/iuran/{id}",[IuranController::class,'destroy']);
        Route::get("/iuran/{activity_id}/activity",[IuranController::class,'getUserIuranByActivity']);
        Route::get("/iuran/not/{activity_id}/activity",[IuranController::class,'getUserNotIuranByActivity']);
        Route::get("/iuran/month/xls",[IuranController::class,"exportIuran"]);

        // Program
        Route::get("/program",[ProgramController::class,'index']);
        Route::post("/program",[ProgramController::class,'store']);
        Route::get("/program/{id}",[ProgramController::class,'show']);
        Route::post("/program-update/{id}",[ProgramController::class,'update']);
        Route::delete("/program/{id}",[ProgramController::class,'destroy']);

        //Kas
        Route::get("/kas/report",[KasController::class,'reportKas']);
        Route::get("/kas/month/xls",[KasController::class,"exportKas"]);
        Route::get("/kas/month",[KasController::class,"getMonthKas"]);
        Route::get("/kas/activity/{activity_id}",[KasController::class,"getByActivityId"]);
        Route::get("/kas",[KasController::class,'index']);
        Route::post("/kas",[KasController::class,'store']);
        Route::get("/kas/{id}",[KasController::class,'show']);
        Route::post("/kas-update/{id}",[KasController::class,'update']);
        Route::delete("/kas/{id}",[KasController::class,'destroy']);

    });
});
