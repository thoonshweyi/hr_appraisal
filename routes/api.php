<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PeerToPeersController;
use App\Http\Controllers\Api\AppraisalFormsController;
use App\Http\Controllers\Api\PrintHistoriesController;
use App\Http\Controllers\Api\AppraisalCyclesController;

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

Route::apiResource("appraisalcycles",AppraisalCyclesController::class,["as"=>"api"]);
Route::get('/appraisalcycles/{id}/assessorformsdashboard', [AppraisalCyclesController::class, 'assessorformsdashboard'])->name('appraisalcycles.assessorformsdashboard');
Route::get('/appraisalcycles/{id}/bybranchesdashboard', [AppraisalCyclesController::class, 'bybranchesdashboard'])->name('appraisalcycles.bybranchesdashboard');
Route::get('/appraisalcycles/{id}/appraisalformdashboard', [AppraisalCyclesController::class, 'appraisalformdashboard'])->name('appraisalcycles.appraisalformdashboard');
Route::get('/appraisalcyclesactivecycles', [AppraisalCyclesController::class, 'activecycle'])->name('appraisalcycles.activecycle');


Route::get('/appraisalformssendnotis', [AppraisalFormsController::class, 'sendnotis'])->name('appraisalforms.sendnotis');
Route::get('/appraisalformsassessordashboard', [AppraisalFormsController::class, 'assessordashboard'])->name('appraisalforms.assessordashboard');
