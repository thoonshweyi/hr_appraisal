<?php

// use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\GradesController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\StatusesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DeptGroupsController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\RatingScalesController;


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
    Route::get('/faqs/faqlist', [FAQController::class, 'faqlist'])->name('faqs.faqlist');

    Route::resource('faqs', FAQController::class);

    Route::get('/download_import_userguide', [HomeController::class, 'getDownload'])->name('getDownload');


    // Start Status
    Route::get("/statuses",[StatusesController::class,"index"])->name("statuses.index");
    Route::post("/statuses",[StatusesController::class,"store"])->name("statuses.store");
    Route::put("/statuses/{id}",[StatusesController::class,"update"])->name("statuses.update");
    Route::delete("/statuses/{id}",[StatusesController::class,"destroy"])->name("statuses.destroy");
    // End Status

    // Start RatingScale
    Route::get("/ratingscales",[RatingScalesController::class,"index"])->name("ratingscales.index");
    Route::post("/ratingscales",[RatingScalesController::class,"store"])->name("ratingscales.store");
    Route::put("/ratingscales/{id}",[RatingScalesController::class,"update"])->name("ratingscales.update");
    Route::delete("/ratingscales/{id}",[RatingScalesController::class,"destroy"])->name("ratingscales.destroy");
    Route::post("/ratingscalesstatus",[RatingScalesController::class,"changestatus"])->name("ratingscales.changestatus");
    // End RatingScale

    Route::get("/grades",[GradesController::class,"index"])->name("grades.index");
    Route::post("/grades",[GradesController::class,"store"])->name("grades.store");
    Route::put("/grades/{id}",[GradesController::class,"update"])->name("grades.update");
    Route::delete("/grades/{id}",[GradesController::class,"destroy"])->name("grades.destroy");
    Route::post("/gradesstatus",[GradesController::class,"changestatus"])->name("grades.changestatus");

    Route::get("/deptgroups",[DeptGroupsController::class,"index"])->name("deptgroups.index");
    Route::post("/deptgroups",[DeptGroupsController::class,"store"])->name("deptgroups.store");
    Route::put("/deptgroups/{id}",[DeptGroupsController::class,"update"])->name("deptgroups.update");
    Route::delete("/deptgroups/{id}",[DeptGroupsController::class,"destroy"])->name("deptgroups.destroy");
    Route::post("/deptgroupsstatus",[DeptGroupsController::class,"changestatus"])->name("deptgroups.changestatus");
    Route::post("/deptgroups_excel_import",[DeptGroupsController::class,"excel_import"])->name("deptgroups.excel_import");


    Route::get("/departments",[DepartmentsController::class,"index"])->name("departments.index");
    Route::post("/departments",[DepartmentsController::class,"store"])->name("departments.store");
    Route::put("/departments/{id}",[DepartmentsController::class,"update"])->name("departments.update");
    Route::delete("/departments/{id}",[DepartmentsController::class,"destroy"])->name("departments.destroy");
    Route::post("/departmentsstatus",[DepartmentsController::class,"changestatus"])->name("departments.changestatus");
    Route::post("/departments_excel_import",[DepartmentsController::class,"excel_import"])->name("departments.excel_import");


});
