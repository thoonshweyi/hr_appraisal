<?php

// use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DepartmentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/user_login/{employee_id}/{password}', [LoginController::class, 'user_login'])->name('user_login');

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/make_as_read/{notification_id}/{document_id}', [HomeController::class, 'make_as_read'])->name('home.make_as_read');
    Route::get('/see_document/{document_id}/{type}/{notification_id}', [HomeController::class, 'see_document'])->name('home.see_document');
    Route::get('/notifications', [HomeController::class, 'notifications'])->name('notification');
    Route::get('lang/{locale}', [LocalizationController::class, 'index'])->name('lang');

    Route::resource('roles', RoleController::class);
    Route::get('/users/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/users/update_profile', [UserController::class, 'update_profile'])->name('user.update_profile');

    Route::resource('users', UserController::class);

    Route::resource('branches', BranchController::class);
    Route::resource('departments', DepartmentController::class);
    Route::get('/faqs/faqlist', [FAQController::class, 'faqlist'])->name('faqs.faqlist');

    Route::resource('faqs', FAQController::class);



    Route::get('/download_import_userguide', [HomeController::class, 'getDownload'])->name('getDownload');


});
