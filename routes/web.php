<?php

// use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtpsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\GradesController;
use App\Http\Controllers\GendersController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\StatusesController;
use App\Http\Controllers\CriteriasController;
use App\Http\Controllers\DivisionsController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\PositionsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DeptGroupsController;
use App\Http\Controllers\AssFormCatsController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\PeerToPeersController;
use Pusher\PushNotifications\PushNotifications;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\RatingScalesController;
use App\Http\Controllers\AppraisalFormsController;
use App\Http\Controllers\PositionLevelsController;
use App\Http\Controllers\SubDepartmentsController;
use App\Http\Controllers\AppraisalCyclesController;
use App\Http\Controllers\AssesseeSummaryController;
use App\Http\Controllers\AttachFormTypesController;
use App\Http\Controllers\AgileDepartmentsController;
use App\Http\Controllers\PushNotificationController;
use Illuminate\Http\Request;

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

Route::group(['middleware' => ['auth','otp']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/make_as_read/{notification_id}/{document_id}', [HomeController::class, 'make_as_read'])->name('home.make_as_read');
    Route::get('/see_document/{document_id}/{type}/{notification_id}', [HomeController::class, 'see_document'])->name('home.see_document');
    Route::get('/notifications', [HomeController::class, 'notifications'])->name('notification');
    Route::get('lang/{locale}', [LocalizationController::class, 'index'])->name('lang');

    Route::resource('roles', RoleController::class);
    Route::get('/users/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/users/update_profile', [UserController::class, 'update_profile'])->name('user.update_profile');


    Route::resource('users', UserController::class);
    Route::get('/getfilteredassessees', [UserController::class, 'getFilteredAssessees'])->name('users.getfilteredassessees');


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


    Route::get("/divisions",[DivisionsController::class,"index"])->name("divisions.index");
    Route::post("/divisions",[DivisionsController::class,"store"])->name("divisions.store");
    Route::put("/divisions/{id}",[DivisionsController::class,"update"])->name("divisions.update");
    Route::delete("/divisions/{id}",[DivisionsController::class,"destroy"])->name("divisions.destroy");
    Route::post("/divisionsstatus",[DivisionsController::class,"changestatus"])->name("divisions.changestatus");
    Route::post("/divisions_excel_import",[DivisionsController::class,"excel_import"])->name("divisions.excel_import");


    Route::get("/agiledepartments",[AgileDepartmentsController::class,"index"])->name("agiledepartments.index");
    Route::post("/agiledepartments",[AgileDepartmentsController::class,"store"])->name("agiledepartments.store");
    Route::put("/agiledepartments/{id}",[AgileDepartmentsController::class,"update"])->name("agiledepartments.update");
    Route::delete("/agiledepartments/{id}",[AgileDepartmentsController::class,"destroy"])->name("agiledepartments.destroy");
    Route::post("/agiledepartmentsstatus",[AgileDepartmentsController::class,"changestatus"])->name("agiledepartments.changestatus");
    Route::post("/agiledepartments_excel_import",[AgileDepartmentsController::class,"excel_import"])->name("agiledepartments.excel_import");


    Route::get("/subdepartments",[SubDepartmentsController::class,"index"])->name("subdepartments.index");
    Route::post("/subdepartments",[SubDepartmentsController::class,"store"])->name("subdepartments.store");
    Route::put("/subdepartments/{id}",[SubDepartmentsController::class,"update"])->name("subdepartments.update");
    Route::delete("/subdepartments/{id}",[SubDepartmentsController::class,"destroy"])->name("subdepartments.destroy");
    Route::post("/subdepartmentsstatus",[SubDepartmentsController::class,"changestatus"])->name("subdepartments.changestatus");
    Route::post("/subdepartments_excel_import",[SubDepartmentsController::class,"excel_import"])->name("subdepartments.excel_import");

    Route::get("/sections",[SectionsController::class,"index"])->name("sections.index");
    Route::post("/sections",[SectionsController::class,"store"])->name("sections.store");
    Route::put("/sections/{id}",[SectionsController::class,"update"])->name("sections.update");
    Route::delete("/sections/{id}",[SectionsController::class,"destroy"])->name("sections.destroy");
    Route::post("/sectionsstatus",[SectionsController::class,"changestatus"])->name("sections.changestatus");
    Route::post("/sections_excel_import",[SectionsController::class,"excel_import"])->name("sections.excel_import");


    Route::get("/positions",[PositionsController::class,"index"])->name("positions.index");
    Route::post("/positions",[PositionsController::class,"store"])->name("positions.store");
    Route::put("/positions/{id}",[PositionsController::class,"update"])->name("positions.update");
    Route::delete("/positions/{id}",[PositionsController::class,"destroy"])->name("positions.destroy");
    Route::post("/positionsstatus",[PositionsController::class,"changestatus"])->name("positions.changestatus");
    Route::post("/positions_excel_import",[PositionsController::class,"excel_import"])->name("positions.excel_import");


    Route::get("/genders",[GendersController::class,"index"])->name("genders.index");
    Route::post("/genders",[GendersController::class,"store"])->name("genders.store");
    Route::put("/genders/{id}",[GendersController::class,"update"])->name("genders.update");
    Route::delete("/genders/{id}",[GendersController::class,"destroy"])->name("genders.destroy");
    Route::post("/gendersstatus",[GendersController::class,"changestatus"])->name("genders.changestatus");
    // Route::post("/genders_excel_import",[GendersController::class,"excel_import"])->name("genders.excel_import");

    Route::get("/positionlevels",[PositionLevelsController::class,"index"])->name("positionlevels.index");
    Route::post("/positionlevels",[PositionLevelsController::class,"store"])->name("positionlevels.store");
    Route::put("/positionlevels/{id}",[PositionLevelsController::class,"update"])->name("positionlevels.update");
    Route::delete("/positionlevels/{id}",[PositionLevelsController::class,"destroy"])->name("positionlevels.destroy");
    Route::post("/positionlevelsstatus",[PositionLevelsController::class,"changestatus"])->name("positionlevels.changestatus");
    // Route::post("/positionlevels_excel_import",[PositionLevelsController::class,"excel_import"])->name("positionlevels.excel_import");


    Route::get("/employees",[EmployeesController::class,"index"])->name("employees.index");
    Route::get("/employees/create",[EmployeesController::class,"create"])->name("employees.create");
    Route::post("/employees",[EmployeesController::class,"store"])->name("employees.store");
    Route::get("/employees/{id}",[EmployeesController::class,"show"])->name("employees.show");
    Route::get("/employees/{id}/edit",[EmployeesController::class,"edit"])->name("employees.edit");
    Route::put("/employees/{id}",[EmployeesController::class,"update"])->name("employees.update");
    Route::delete("/employees/{id}",[EmployeesController::class,"destroy"])->name("employees.destroy");
    Route::post("/employees_excel_import",[EmployeesController::class,"excel_import"])->name("employees.excel_import");
    Route::post("/employeesstatus",[EmployeesController::class,"changestatus"])->name("employees.changestatus");
    Route::put("/employees/{id}/profilepicture",[EmployeesController::class,"updateprofilepicture"])->name("employees.updateprofilepicture");


    Route::get("/assformcats",[AssFormCatsController::class,"index"])->name("assformcats.index");
    Route::get("/assformcats/create",[AssFormCatsController::class,"create"])->name("assformcats.create");
    Route::post("/assformcats",[AssFormCatsController::class,"store"])->name("assformcats.store");
    Route::get("/assformcats/{id}/edit",[AssFormCatsController::class,"edit"])->name("assformcats.edit");
    Route::put("/assformcats/{id}",[AssFormCatsController::class,"update"])->name("assformcats.update");
    Route::delete("/assformcats/{id}",[AssFormCatsController::class,"destroy"])->name("assformcats.destroy");
    Route::post("/assformcatsstatus",[AssFormCatsController::class,"changestatus"])->name("assformcats.changestatus");
    Route::post("/assformcats_excel_import",[AssFormCatsController::class,"excel_import"])->name("assformcats.excel_import");


    Route::get("/criterias",[CriteriasController::class,"index"])->name("criterias.index");
    Route::post("/criterias",[CriteriasController::class,"store"])->name("criterias.store");
    Route::get("/criterias/{id}/edit",[CriteriasController::class,"edit"])->name("criterias.edit");
    Route::put("/criterias/{id}",[CriteriasController::class,"update"])->name("criterias.update");
    Route::delete("/criterias/{id}",[CriteriasController::class,"destroy"])->name("criterias.destroy");
    Route::post("/criteriasstatus",[CriteriasController::class,"changestatus"])->name("criterias.changestatus");
    Route::post("/criterias_excel_import",[CriteriasController::class,"excel_import"])->name("criterias.excel_import");


    Route::get("/appraisalcycles",[AppraisalCyclesController::class,"index"])->name("appraisalcycles.index");
    Route::get("/appraisalcycles/create",[AppraisalCyclesController::class,"create"])->name("appraisalcycles.create");
    Route::post("/appraisalcycles",[AppraisalCyclesController::class,"store"])->name("appraisalcycles.store");
    Route::get("/appraisalcycles/{id}",[AppraisalCyclesController::class,"show"])->name("appraisalcycles.show");
    Route::get("/appraisalcycles/{id}/edit",[AppraisalCyclesController::class,"edit"])->name("appraisalcycles.edit");
    Route::put("/appraisalcycles/{id}",[AppraisalCyclesController::class,"update"])->name("appraisalcycles.update");
    Route::delete("/appraisalcycles/{id}",[AppraisalCyclesController::class,"destroy"])->name("appraisalcycles.destroy");
    Route::post("/appraisalcyclesstatus",[AppraisalCyclesController::class,"changestatus"])->name("appraisalcycles.changestatus");
    Route::get("/appraisalcycles/{id}/countdown",[AppraisalCyclesController::class,"countdown"])->name("appraisalcycles.countdown");



    Route::get("/peertopeers",[PeerToPeersController::class,"index"])->name("peertopeers.index");
    Route::get("/peertopeers/create",[PeerToPeersController::class,"create"])->name("peertopeers.create");
    Route::post("/peertopeers",[PeerToPeersController::class,"store"])->name("peertopeers.store");
    Route::get("/peertopeers/{id}",[PeerToPeersController::class,"show"])->name("peertopeers.show");
    Route::get("/peertopeers/{id}/edit",[PeerToPeersController::class,"edit"])->name("peertopeers.edit");
    Route::put("/peertopeers/{id}",[PeerToPeersController::class,"update"])->name("peertopeers.update");
    Route::delete("/peertopeers/{id}",[PeerToPeersController::class,"destroy"])->name("peertopeers.destroy");
    Route::post("peertopeersstatus",[PeerToPeersController::class,"changestatus"])->name("peertopeers.changestatus");
    Route::get('/getAssessorAssessees', [PeerToPeersController::class, 'getAssessorAssessees'])->name('users.getassessorassessees');



    Route::get("/attachformtypes",[AttachFormTypesController::class,"index"])->name("attachformtypes.index");
    Route::post("/attachformtypes",[AttachFormTypesController::class,"store"])->name("attachformtypes.store");
    Route::put("/attachformtypes/{id}",[AttachFormTypesController::class,"update"])->name("attachformtypes.update");
    Route::delete("/attachformtypes/{id}",[AttachFormTypesController::class,"destroy"])->name("attachformtypes.destroy");
    Route::post("/attachformtypesstatus",[AttachFormTypesController::class,"changestatus"])->name("attachformtypes.changestatus");
    Route::post("/attachformtypes_excel_import",[AttachFormTypesController::class,"excel_import"])->name("attachformtypes.excel_import");


    Route::get("/appraisalforms",[AppraisalFormsController::class,"index"])->name("appraisalforms.index");
    Route::get("/appraisalforms/create",[AppraisalFormsController::class,"create"])->name("appraisalforms.create");
    Route::post("/appraisalforms",[AppraisalFormsController::class,"store"])->name("appraisalforms.store");
    Route::get("/appraisalforms/{id}",[AppraisalFormsController::class,"show"])->name("appraisalforms.show");
    Route::get("/appraisalforms/{id}/edit",[AppraisalFormsController::class,"edit"])->name("appraisalforms.edit");
    Route::put("/appraisalforms/{id}",[AppraisalFormsController::class,"update"])->name("appraisalforms.update");
    Route::put("/appraisalformssavedraft/{id}",[AppraisalFormsController::class,"savedraft"])->name("appraisalforms.savedraft");
    Route::delete("/appraisalforms/{id}",[AppraisalFormsController::class,"destroy"])->name("appraisalforms.destroy");
    Route::get("/fillform",[AppraisalFormsController::class,"fillform"])->name("appraisalforms.fillform");
    Route::get("/appraisalformsprintpdf/{id}",[AppraisalFormsController::class,"printpdf"])->name("appraisalforms.printpdf");
    Route::get("/appraisalformsshowprintframe/{id}",[AppraisalFormsController::class,"showprintframe"])->name("appraisalforms.showprintframe");



    Route::get("/assesseesummary/{assessee_user_id}/{appraisal_cycle_id}/",[AssesseeSummaryController::class,"review"])->name("assesseesummary.review");
    Route::get("/assesseesummarysexport/{appraisal_cycle_id}/",[AssesseeSummaryController::class,"export"])->name("assesseesummary.export");


    Route::get("/{appraisal_cycle_id}/participantusers",[AppraisalCyclesController::class,"participantusers"])->name("participantusers.index");
    Route::get("/{appraisal_cycle_id}/assesseeusers",[AppraisalCyclesController::class,"assesseeusers"])->name("assesseeusers.index");
    Route::get("/{appraisal_cycle_id}/assessorusers",[AppraisalCyclesController::class,"assessorusers"])->name("assessorusers.index");





});
Route::group(['middleware' => ['auth']], function () {
    Route::get("/generateotps/{type}",[OtpsController::class,"generate"])->name("otps.generateotps");

    Route::post("/verifyotps/{type}",[OtpsController::class,"verify"]);
    Route::get("/otps/create",[OtpsController::class,"create"])->name("otps.create");
});

Route::get("/send-notification",[PushNotificationController::class,"sendNotification"])->name("sendNotification");

Route::post('/api/pusher-auth', function (Request $request) {
    $beamsClient = new PushNotifications([
        "instanceId" => config('services.beams.instance_id'),
        "secretKey" => config('services.beams.secret_key'),
    ]);

    $userId = $request->user_id;
    // dd($userId);
    // $userId = '1';

    $beamsToken = $beamsClient->generateToken($userId);

    return response()->json(['token' => $beamsToken]);
});
Route::get('/pusher-unsubscribe', function (Request $request) {
    // $userId = auth()->id(); // Get logged-in user ID
        $userId = '1';
    // Initialize Pusher Beams Client
    $beamsClient = new PushNotifications([
        "instanceId" => config('services.beams.instance_id'),
        "secretKey" => config('services.beams.secret_key'),
    ]);

    // Remove user from Pusher Beams
    $beamsClient->deleteUser($userId);

    // Logout the user
    // auth()->logout();
    // Session::flush();

    return response()->json(['message' => 'Logged out successfully']);
});
