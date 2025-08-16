<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PeerToPeersController;
use App\Http\Controllers\Api\PrintHistoriesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/getrecentassessees', [PeerToPeersController::class, 'employeesRecentAssessees'])->name('peertopeers.employeesRecentAssessees');
Route::get('/getrecentassessors', [PeerToPeersController::class, 'employeesRecentAssessors'])->name('peertopeers.employeesRecentAssessors');

Route::get("/assessmentnetwork/{assessor_user_id}/{appraisal_cycle_id}/",[PeerToPeersController::class,"assessmentnetwork"])->name('assessmentnetwork');

Route::apiResource("printhistories",PrintHistoriesController::class,["as"=>"api"]);
