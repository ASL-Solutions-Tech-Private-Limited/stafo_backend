<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\admin\CmsController;
use App\Http\Controllers\admin\FAQController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\User\ShiftController;
use App\Http\Controllers\admin\LoginController;
use App\Http\Controllers\User\MyTeamController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\admin\BranchController;

use App\Http\Controllers\admin\TicketController;
use App\Http\Controllers\admin\PackageController;
use App\Http\Controllers\admin\EmployeeController;
use App\Http\Controllers\admin\FeaturesController;

use App\Http\Controllers\admin\FeedbackController;
use App\Http\Controllers\admin\AppbannerController;
use App\Http\Controllers\User\DepartmentController;
use App\Http\Controllers\User\SalarytypeController;
use App\Http\Controllers\User\LeavetypeController;
use App\Http\Controllers\User\ReimbursementController;
use App\Http\Controllers\User\CompoffLeaveController;
use App\Http\Controllers\User\SalryTypePackageController;

use App\Http\Controllers\User\UserBranchController;
use App\Http\Controllers\admin\AttendanceController;
use App\Http\Controllers\User\UserFeatureController;
use App\Http\Controllers\User\UserPackageController;
use App\Http\Controllers\admin\CompanyTypeController;
use App\Http\Controllers\admin\ContactFormController;
use App\Http\Controllers\admin\HelpContentController;
use App\Http\Controllers\admin\SiteSettingController;
use App\Http\Controllers\Location\LocationController;

use App\Http\Controllers\User\UserEmployeeController;
use App\Http\Controllers\admin\BusinessTypeController;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\admin\AdministratorController;

use App\Http\Controllers\admin\CompanyDetailController;
use App\Http\Controllers\User\UserAttendanceController;
use App\Http\Controllers\User\CompanyDocumentController;
use App\Http\Controllers\admin\adminompanyTypeController;
use App\Http\Controllers\admin\CallbackRequestController;
use App\Http\Controllers\User\EmployeeDocumentController;
use App\Http\Controllers\admin\ProprietorDetailController;
use App\Http\Controllers\User\UserCompanyDetailController;
use App\Http\Controllers\User\PerformanceController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\admin\AdminChatController;
use App\Http\Controllers\admin\NotificationController;
use App\Http\Controllers\admin\SalaryController;
use App\Http\Controllers\User\CRMController;
use App\Http\Controllers\admin\AdminCRMController;
use App\Http\Controllers\User\TaskController;
use App\Http\Controllers\admin\AdminTaskController;
use App\Http\Controllers\User\GraceSettingController;
use App\Http\Controllers\User\ExpenseController;
use App\Http\Controllers\User\VehicleController;
use App\Http\Controllers\User\TripController;

use App\Http\Controllers\admin\AdminExpenseController;
use App\Http\Controllers\admin\AdminVehicleController;
use App\Http\Controllers\admin\AdminTripController;
use App\Http\Controllers\admin\BlogCategoryController;
use App\Http\Controllers\User\UserHolidayController;
use App\Http\Controllers\admin\BlogTagController;
use App\Http\Controllers\admin\BlogController;


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


Route::get('/', [HomeController::class, 'index'])->name('index');
Route::post('/request-callback', [HomeController::class, 'requestCallback'])->name('request.callback');

Route::get('about-us', [HomeController::class, 'aboutUs'])->name('aboutUs');
Route::get('contact-us', [HomeController::class, 'contactUs'])->name('contactUs');
Route::get('price', [HomeController::class, 'price'])->name('price');
Route::get('blog', [HomeController::class, 'blog'])->name('blog');
Route::get('blog-details/{id}/{slug}', [HomeController::class, 'blogDetails'])->name('blogDetails');
Route::post('blog-comment-store/{id}', [HomeController::class, 'blogCommentStore'])->name('blogCommentStore');
Route::post('contact-submit', [HomeController::class, 'store'])->name('contact.submit');

Route::get('privacy-policy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('terms-condition', [HomeController::class, 'terms'])->name('terms');
Route::get('carrer', [HomeController::class, 'carrer'])->name('carrer');
Route::get('faq', [HomeController::class, 'faq'])->name('faq');
Route::post('success', [UserPackageController::class, 'success'])->name('successPackage');
Route::post('failure', [UserPackageController::class, 'failure'])->name('failurePackage');



Route::controller(LoginRegisterController::class)->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

// user dashborad
Route::namespace('App\Http\Controllers')->middleware('auth')->group(function () {
    Route::get('dashboard', 'DashboardController@index')->name('user.dashboard');

    Route::get('company/profile/edit', [UserCompanyDetailController::class, 'edit'])->name('company.profile.edit');

    Route::put('/company/profile/update', [UserCompanyDetailController::class, 'update'])->name('company.profile.update');

    Route::get('/companies-list', [UserCompanyDetailController::class, 'index'])->name('company.index'); // List all
    Route::get('/companies/referrals', [UserCompanyDetailController::class, 'referralList'])->name('referralList');

    Route::get('states/{country_id}', [UserCompanyDetailController::class, 'getStates'])->name('user.getStates');
    Route::get('cities/{state_id}', [UserCompanyDetailController::class, 'getCities'])->name('user.getCities');

    Route::get('upgrade-interested', [UserCompanyDetailController::class, 'upgradeInterested'])->name('upgradeInterested');

    // company feedback
    Route::get('company-feedback', [UserCompanyDetailController::class, 'feedback'])->name('company.feedback');
    Route::post('create-feedback', [UserCompanyDetailController::class, 'createFeedback'])->name('company.feedback.create');

    // company help here
    Route::get('company-help', [UserCompanyDetailController::class, 'helpList'])->name('company.helpList');

    // company tickets
    Route::get('company-tickets', [UserCompanyDetailController::class, 'tickets'])->name('company.tickets');
    Route::post('create-tickets/{ticket_id?}', [UserCompanyDetailController::class, 'createTickets'])->name('company.tickets.storeReply');

    Route::get('/tickets/{ticket}/replies', [UserCompanyDetailController::class, 'showReplies'])->name('ticket.showReplies');



    // Employee routes
    Route::get('employee', [UserEmployeeController::class, 'index'])->name('employee.index');
    Route::get('employee-create', [UserEmployeeController::class, 'create'])->name('employee.create');
    Route::post('employees-store', [UserEmployeeController::class, 'store'])->name('user.employees.store');
    Route::get('employees-show/{id}', [UserEmployeeController::class, 'show'])->name('user.employees.show');
    Route::delete('employees.delete/{employee}', [UserEmployeeController::class, 'destroy'])->name('user.employees.delete');
    Route::post('employees/{employee}/assign-branch', [UserEmployeeController::class, 'assignBranch'])->name('employee.assignBranch');
    Route::post('employees/{employee}/assign-department', [UserEmployeeController::class, 'assignDepartment'])->name('employee.assignDepartment');

    Route::get('employee/location/{id}', [UserEmployeeController::class, 'location'])->name('employee.location');

    Route::get('employees/export', [UserEmployeeController::class, 'export'])->name('employees.export');
    Route::post('employees/import', [UserEmployeeController::class, 'import'])->name('user.employees.import');
    // routes/web.php

    Route::post('employees/{employeeId}/assign-shift', [UserEmployeeController::class, 'assignShift'])->name('user.employees.assignShift');


    Route::get('employee/{id}/edit', [UserEmployeeController::class, 'edit'])->name('user.employees.edit');
    Route::put('employee/{id}', [UserEmployeeController::class, 'update'])->name('user.employees.update');
    Route::get('{id}/add-bank-account', [UserEmployeeController::class, 'showBankAccountForm'])->name('addBankAccount');
    Route::post('{id}/store-bank-account', [UserEmployeeController::class, 'storeBankAccount'])->name('storeBankAccount');

    Route::get('bank-account/edit/{id}', [UserEmployeeController::class, 'editBank'])->name('editBankAccount');
    Route::put('bank-account/update/{id}', [UserEmployeeController::class, 'updateBank'])->name('updateBankAccount');

    Route::get('employee/document-verification/{id}', [UserEmployeeController::class, 'documentVerification'])->name('documentVerification');
    Route::put('employee-data-update/{id}', [UserEmployeeController::class, 'employeesDataUpdate'])->name('employeesDataUpdate');

    // leave-list
    Route::get('leave-list', [UserEmployeeController::class, 'leaveList'])->name('leaveList');

    Route::post('leave/updateStatus', [UserEmployeeController::class, 'updateLeaveStatus'])->name('leave.updateStatus');

    Route::delete('leave/{id}/delete', [UserEmployeeController::class, 'deleteLeave'])->name('leave.delete');

    Route::get('defaultGraceSetting', [SalarytypeController::class, 'defaultGraceSetting'])->name('defaultGraceSetting');

    Route::get('get-employee-salary/{employeeId}', [SalarytypeController::class, 'getEmployeeSalary'])->name('getEmployeeSalary');
    Route::get('generate-all-salary', [SalarytypeController::class, 'generateAllSalary'])->name('generateAllSalary');
    Route::resource('salarytype', SalarytypeController::class);
    Route::get('generate-salary', [SalarytypeController::class, 'generateSalary'])->name('generateSalary');
    Route::post('save-employee-salary', [SalarytypeController::class, 'saveEmployeeSalary'])->name('saveEmployeeSalary');
    Route::get('monthly-salary-list', [SalarytypeController::class, 'employeeSalaryList'])->name('employeeSalaryList');
    Route::get('employee-salary-details/{employeeId}', [SalarytypeController::class, 'employeeSalaryDetails'])->name('employeeSalaryDetails');

    Route::get('employee/salary-slip/download', [SalarytypeController::class, 'salaryPDF'])->name('salaryPDF');
    Route::get('salary/export', [SalarytypeController::class, 'export'])->name('salary-export');

    // Department routes
    Route::resource('departments', DepartmentController::class);
    Route::patch('departments/{id}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggleStatus');
    
    // Leavetypes routes
    Route::resource('leavetypes', LeavetypeController::class);
    Route::patch('leavetypes/{id}/toggle-status', [LeavetypeController::class, 'toggleStatus'])->name('leavetypes.toggleStatus');

    // Reimbursement routes
    Route::resource('reimbursements', ReimbursementController::class);
    Route::post('reimbursements/statuschange', [ReimbursementController::class, 'statuschange'])->name('reimbursements.statuschange');
   

    Route::resource('compoffleaves', CompoffLeaveController::class);
    Route::post('compoffleaves/statuschange', [CompoffLeaveController::class, 'statuschange'])->name('compoffleaves.statuschange');
   
    //  my team

    Route::resource('myteam', MyTeamController::class);
    Route::resource('shifts', ShiftController::class);
    Route::resource('attendance', UserAttendanceController::class);

    Route::get('attendances/export', [UserAttendanceController::class, 'export'])->name('user.attendances.export');
    Route::post('attendances/import', [UserAttendanceController::class, 'import'])->name('user.attendances.import');

    Route::resource('branche', UserBranchController::class);
    Route::resource('holiday', UserHolidayController::class);

    Route::resource('salary-package-type', SalryTypePackageController::class);
    Route::post('salarytype/get-package-salary-types', [SalarytypeController::class, 'getPackageSalaryTypes'])->name('salarytype.get-package-salary-types');
    Route::post('salary-package-type/{id}/toggle-status', [SalryTypePackageController::class, 'toggleStatus'])->name('salary-package-type.toggleStatus');


    // Display list of documents for an employee
    Route::get('employees/{employeeId}/documents', [EmployeeDocumentController::class, 'index'])->name('employee.documents.index');
    Route::get('employees/{employeeId}/documents/create', [EmployeeDocumentController::class, 'create'])->name('employee.documents.create');
    //employee document upload
    Route::post('employees/{employeeId}/documents', [EmployeeDocumentController::class, 'store'])->name('employee.documents.store');

    // Show the form for editing an existing document
    Route::get('employee/{employeeId}/documents/{documentId}/edit', [EmployeeDocumentController::class, 'edit'])
        ->name('employee.documents.edit');

    // Update the specified document
    Route::put('employee/{employeeId}/documents/{documentId}', [EmployeeDocumentController::class, 'update'])
        ->name('employee.documents.update');

    // Delete Employee Document
    Route::delete('documents/{documentId}', [EmployeeDocumentController::class, 'destroy'])->name('employee.documents.destroy');

    // comapny documents
    Route::resource('company-documents', CompanyDocumentController::class);
    Route::get('company/document-verification', [CompanyDocumentController::class, 'documentVerification'])->name('companydocumentVerification');
    Route::put('company-data-update', [CompanyDocumentController::class, 'companyDataUpdate'])->name('companyDataUpdate');

    Route::resource('company-packages', UserPackageController::class);
    Route::get('getpackage', [UserPackageController::class, 'getPackage'])->name('getPackage');
    
    Route::get('subscription-details', [UserPackageController::class, 'subscriptionDetails'])->name('subscriptionDetails');
    Route::resource('company-features', UserFeatureController::class);
    Route::resource('download-report', ReportController::class);
    Route::get('report/export-employee', [ReportController::class, 'exportEmployee'])->name('report.exportEmployee');
    Route::get('report/export-attendance', [ReportController::class, 'exportAttendance'])->name('report.exportAttendance');
    Route::get('report/export-leave', [ReportController::class, 'exportLeave'])->name('report.exportLeave');

    Route::get('performance-type-list', [PerformanceController::class, 'performancetypeList'])->name('performancetypeList');
    Route::get('performance-type-create', [PerformanceController::class, 'performancetypeCreate'])->name('performancetypeCreate');
    Route::post('performance-type-store', [PerformanceController::class, 'performancetypeStore'])->name('performancetypeStore');
    Route::get('performance-type-edit/{id}', [PerformanceController::class, 'performancetypeEdit'])->name('performancetypeEdit');
    Route::put('performance-type-update/{id}', [PerformanceController::class, 'performancetypeUpdate'])->name('performancetypeUpdate');
    Route::delete('performance-type-delete/{id}', [PerformanceController::class, 'performancetypeDelete'])->name('performancetypeDelete');
    Route::get('employee-performance-add/{employeeId}', [PerformanceController::class, 'employeePerformanceAdd'])->name('employeePerformanceAdd');
    Route::post('performance-type-store/{employeeId}', [PerformanceController::class, 'saveEmployeePerformance'])->name('saveEmployeePerformance');
    Route::get('employee-performance/{employeeId}', [PerformanceController::class, 'employeePerformance'])->name('employeePerformance');
    Route::get('rank-list', [PerformanceController::class, 'employeeRankList'])->name('employeeRankList');
    Route::get('rank-details', [PerformanceController::class, 'employeeRankDetails'])->name('employeeRankDetails');
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('save-chat', [ChatController::class, 'savechat'])->name('savechat');
    Route::get('get-chat', [ChatController::class, 'getChat'])->name('getChat');

    Route::get('lead-list', [CRMController::class, 'leadList'])->name('leadList');
    Route::get('lead-create', [CRMController::class, 'leadCreate'])->name('leadCreate');
    Route::post('lead-store', [CRMController::class, 'leadStore'])->name('leadStore');
    Route::get('lead-edit/{id}', [CRMController::class, 'leadEdit'])->name('leadEdit');
    Route::put('lead-update/{id}', [CRMController::class, 'leadUpdate'])->name('leadUpdate');
    Route::delete('lead-delete/{id}', [CRMController::class, 'destroy'])->name('leadDelete');
    Route::get('followup-list/{id}', [CRMController::class, 'leadFollowup'])->name('leadFollowup');
    Route::get('followup-create/{lead_id}', [CRMController::class, 'leadFollowupCreate'])->name('leadFollowupCreate');
    Route::post('followup-store/{lead_id}', [CRMController::class, 'leadFollowupStore'])->name('leadFollowupStore');
    Route::get('followup-edit/{id}', [CRMController::class, 'followupEdit'])->name('followupEdit');
    Route::put('followup-update/{id}', [CRMController::class, 'followupUpdate'])->name('followupUpdate');

    Route::get('device-list', [UserCompanyDetailController::class, 'deviceList'])->name('deviceList');
    Route::get('approve-device/{id}', [UserCompanyDetailController::class, 'approveDevice'])->name('approveDevice');
     Route::get('reject-device/{id}', [UserCompanyDetailController::class, 'rejectDevice'])->name('rejectDevice');

    Route::prefix('task')->group(function () {
        Route::get('/list', [TaskController::class, 'taskList'])->name('taskList');
        Route::get('/create', [TaskController::class, 'taskCreate'])->name('taskCreate');
        Route::post('/store', [TaskController::class, 'taskStore'])->name('taskStore');
        Route::get('/edit/{id}', [TaskController::class, 'taskEdit'])->name('taskEdit');
        Route::post('/update/{id}', [TaskController::class, 'taskUpdate'])->name('taskUpdate');
        Route::delete('/delete/{id}', [TaskController::class, 'destroy'])->name('taskDelete');
        Route::get('/comment-list/{id}', [TaskController::class, 'commentList'])->name('commentList');
        Route::get('/comment-create/{id}', [TaskController::class, 'commentCreate'])->name('commentCreate');
        Route::post('/comment-store/{id}', [TaskController::class, 'commentStore'])->name('commentStore');
        Route::get('/comment-edit/{id}', [TaskController::class, 'commentEdit'])->name('commentEdit');
        Route::put('/comment-update/{id}', [TaskController::class, 'commentUpdate'])->name('commentUpdate');
        Route::delete('/comment-delete/{id}', [TaskController::class, 'commentDelete'])->name('commentDelete');
        Route::delete('/file-delete/{id}', [TaskController::class, 'fileDelete'])->name('fileDelete');
    });

    Route::prefix('expense')->group(function () {
        Route::get('/list', [ExpenseController::class, 'expenseList'])->name('expenseList');
        Route::get('/create', [ExpenseController::class, 'expenseCreate'])->name('expenseCreate');
        Route::post('/store', [ExpenseController::class, 'expenseStore'])->name('expenseStore');
        Route::get('/edit/{id}', [ExpenseController::class, 'expenseEdit'])->name('expenseEdit');
        Route::post('/update/{id}', [ExpenseController::class, 'expenseUpdate'])->name('expenseUpdate');
        Route::delete('/expenseDelete/{id}', [ExpenseController::class, 'expenseDelete'])->name('expenseDelete');
        Route::delete('/expenseAttachmentDelete/{id}', [ExpenseController::class, 'expenseAttachmentDelete'])->name('expenseAttachmentDelete');
        Route::post('/status-change', [ExpenseController::class, 'expensestatuschange'])->name('expensestatuschange');
        Route::get('/details/{id}', [ExpenseController::class, 'expenseDetails'])->name('expenseDetails');
        
        Route::get('/expenseFormDisplay', [ExpenseController::class, 'expenseFormDisplay'])->name('expenseFormDisplay');
        Route::get('/formlist', [ExpenseController::class, 'expenseformList'])->name('expenseformList');
        Route::get('/formcreate', [ExpenseController::class, 'expenseformCreate'])->name('expenseformCreate');
        Route::post('/formstore', [ExpenseController::class, 'expenseformStore'])->name('expenseformStore');
        Route::get('/formedit/{id}', [ExpenseController::class, 'expenseformEdit'])->name('expenseformEdit');
        Route::post('/formupdate/{id}', [ExpenseController::class, 'expenseformUpdate'])->name('expenseformUpdate');
        Route::delete('/formdelete/{id}', [ExpenseController::class, 'expenseformDelete'])->name('expenseformDelete');
        Route::get('/formdetails/{id}', [ExpenseController::class, 'expenseformDetails'])->name('expenseformDetails');
    });

    Route::get('grace-settings', [GraceSettingController::class, 'index'])->name('grace_settings.index');
    Route::get('grace-settings/create', [GraceSettingController::class, 'create'])->name('grace_settings.create');
    Route::post('grace-settings', [GraceSettingController::class, 'store'])->name('grace_settings.store');
    Route::get('grace-settings/{id}/edit', [GraceSettingController::class, 'edit'])->name('grace_settings.edit');
    Route::put('grace-settings/{id}', [GraceSettingController::class, 'update'])->name('grace_settings.update');
    Route::delete('grace-settings/{id}', [GraceSettingController::class, 'destroy'])->name('grace_settings.destroy');

      // vehicles routes start
    Route::prefix('vehicles')->group(function () {
        Route::get('/list', [VehicleController::class, 'index'])->name('vehicles.index');    
        Route::get('/create', [VehicleController::class, 'create'])->name('vehicles.create');  
        Route::post('/store', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/edit/{id}', [VehicleController::class, 'edit'])->name('vehicles.edit');
        Route::put('/update/{id}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/destroy/{id}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
    });
        // vehicles routes end
        
        //trips routes start
    Route::prefix('trips')->group(function () {
        Route::get('/list', [TripController::class, 'index'])->name('trips.index');
        Route::get('/create', [TripController::class, 'create'])->name('trips.create');
        Route::post('/store', [TripController::class, 'store'])->name('trips.store');
        Route::get('/edit/{id}', [TripController::class, 'edit'])->name('trips.edit');
        Route::put('/update/{id}', [TripController::class, 'update'])->name('trips.update');
        Route::delete('/destroy/{id}', [TripController::class, 'destroy'])->name('trips.destroy');
        Route::get('/show/{id}', [TripController::class, 'show'])->name('trips.show'); // Optional: View trip details
        Route::put('/assign/{id}', [TripController::class, 'DriverAndVechicleAssign'])->name('trips.assign');
        Route::put('/trips/{id}/status', [TripController::class, 'updateStatus'])->name('trips.status.update');
        Route::get('/trips/{trip}/check-vehicle/{vehicle}', [TripController::class, 'checkVehicleAvailability'])->name('trips.checkVehicleAvailability');
        Route::get('/trips/{trip}/check-driver/{driver}', [TripController::class, 'checkDriverAvailability'])->name('trips.checkDriverAvailability');
        Route::get('/location/{id}', [TripController::class, 'location'])->name('trips.location');

    });
    //trips routes end
   
});


Route::group(['prefix' => 'admin', 'namespace' => 'App\Http\Controllers\admin'], function () {
    Route::get('/', ['as' => 'admin_login', 'uses' => 'LoginController@login']);
    Route::post('/', ['as' => 'do_admin_login', 'uses' => 'LoginController@dologin']);
});

Route::group(['prefix' => 'admin', 'namespace' => 'App\Http\Controllers\admin', 'middleware' => ['auth:admin']], function () {
    Route::get('/logout', ['as' => 'admin_logout', 'uses' => 'LoginController@logout']);
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('document', 'DocumentTypeController@index')->name('document_list');
    Route::get('document-add', 'DocumentTypeController@add')->name('document_add');
    Route::post('document-add', 'DocumentTypeController@store')->name('store_document');
    Route::get('document-edit/{id}', 'DocumentTypeController@edit')->name('document_edit');
    Route::post('document-edit', 'DocumentTypeController@update')->name('document_update');
    Route::get('document-delete/{id}', 'DocumentTypeController@delete')->name('document_delete');
    //Route::get('roles', 'RoleController@index')->name('role');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);


    Route::get('cms-list', [CmsController::class, 'index'])->name('cms.list');
    Route::get('cms-create', [CmsController::class, 'create'])->name('cms.create');
    Route::post('cms-store', [CmsController::class, 'store'])->name('cms.store');
    Route::get('cms-edit/{id}', [CmsController::class, 'edit'])->name('cms.edit');
    Route::put('cms-update/{id}', [CmsController::class, 'update'])->name('cms.update');
    Route::delete('cms-destroy/{id}', [CmsController::class, 'destroy'])->name('cms.destroy');

    Route::get('company', [CompanyTypeController::class, 'index'])->name('company.list');
    Route::get('company-create', [CompanyTypeController::class, 'add'])->name('company.add');
    Route::post('company-store', [CompanyTypeController::class, 'store'])->name('company.store');
    Route::get('company-edit/{id}', [CompanyTypeController::class, 'edit'])->name('company.edit');
    Route::post('company-update', [CompanyTypeController::class, 'update'])->name('company.update');
    Route::get('company-destroy/{id}', [CompanyTypeController::class, 'destroy'])->name('company.destroy');

    //employee
    Route::get('employees-list', [EmployeeController::class, 'index'])->name('employees.list');
    Route::get('employees-create', action: [EmployeeController::class, 'create'])->name('employees.create');
    Route::get('employees-show/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('employees-edit/{employee}', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::post('employees-store', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('employees-update/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('employees-delete/{employee}', [EmployeeController::class, 'destroy'])->name('employees.delete');

    //sites_settings
    // Route::get('site_settings.index', [SiteSettingController::class, 'index'])->name('site_settings.index');
    // Route::get('site_settings.create', action: [SiteSettingController::class, 'create'])->name('site_settings.create');
    // Route::get('site_settings.show/{setting}', [SiteSettingController::class, 'show'])->name('site_settings.show');
    // Route::get('site_settings.edit/{setting}', [SiteSettingController::class, 'edit'])->name('site_settings.edit');
    // Route::put('site_settings.update/{siteSetting}', [SiteSettingController::class, 'update'])->name('site_settings.update');
    // Route::post('site_settings.store/{setting}', [SiteSettingController::class, 'store'])->name('site_settings.store');
    // Route::delete('site_settings.destroy/{setting}', [SiteSettingController::class, 'destroy'])->name('site_settings.destroy');


    Route::get('site_settings', [SiteSettingController::class, 'index'])->name('site_settings.index');
    Route::get('site_settings/create', [SiteSettingController::class, 'create'])->name('site_settings.create');
    Route::post('site_settings', [SiteSettingController::class, 'store'])->name('site_settings.store');
    Route::get('site_settings/{id}/edit', [SiteSettingController::class, 'edit'])->name('site_settings.edit');
    Route::put('site_settings/{id}', [SiteSettingController::class, 'update'])->name('site_settings.update');
    Route::delete('site_settings/{id}', [SiteSettingController::class, 'destroy'])->name('site_settings.destroy');




    //Branches_Management
    Route::get('branches', [BranchController::class, 'index'])->name('branches.index'); // List all branches
    Route::get('branches/create', [BranchController::class, 'create'])->name('branches.create'); // Show the form to create a branch
    Route::post('branches', [BranchController::class, 'store'])->name('branches.store'); // Store a new branch
    Route::get('branches/{id}', [BranchController::class, 'show'])->name('branches.show'); // View branch
    Route::get('branches/{id}/edit', [BranchController::class, 'edit'])->name('branches.edit'); // Show the form to edit a branch
    Route::put('branches/{id}', [BranchController::class, 'update'])->name('branches.update'); // Update an existing branch
    Route::delete('branches/{id}', [BranchController::class, 'destroy'])->name('branches.destroy'); // Delete a branch

    Route::get('/branches/get-branches/{companyId}', [BranchController::class, 'getBranchesByCompany']);


    //Attendance_Management
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('attendances/{id}/show', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::get('attendances/{id}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('attendances/{id}', [AttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('attendances/{id}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

    Route::get('/attendances/export', [AttendanceController::class, 'export'])->name('attendances.export');
    Route::post('/attendances/import', [AttendanceController::class, 'import'])->name('attendances.import');

    //packages
    // Route::get('packages', [PackageController::class, 'index'])->name('packages.index');






    Route::get('proprietor-list', [ProprietorDetailController::class, 'index'])->name('proprietor.list');
    Route::get('proprietor-create', [ProprietorDetailController::class, 'create'])->name('proprietor.create');
    Route::post('proprietor-store', [ProprietorDetailController::class, 'store'])->name('proprietor.store');
    Route::get('proprietor-edit/{id}', [ProprietorDetailController::class, 'edit'])->name('proprietor.edit');
    Route::get('proprietor-show/{id}', [ProprietorDetailController::class, 'show'])->name('proprietor.show');
    Route::put('proprietor-update/{id}', [ProprietorDetailController::class, 'update'])->name('proprietor.update');
    Route::delete('proprietor-destroy/{id}', [ProprietorDetailController::class, 'destroy'])->name('proprietor.destroy');
    Route::post('/proprietor/status/{id}', [ProprietorDetailController::class, 'toggleStatus'])->name('proprietor.status.toggle');


    Route::get('/states/{countryId}', [LocationController::class, 'getStates'])->name('getStates');
    Route::get('/cities/{stateId}', [LocationController::class, 'getCities'])->name('getCities');

    Route::get('company-details-list', [CompanyDetailController::class, 'index'])->name('company.details.list');
    Route::get('company-details-create', [CompanyDetailController::class, 'create'])->name('company.details.create');
    Route::post('company-details-store', [CompanyDetailController::class, 'store'])->name('company.details.store');
    Route::get('company-details-edit/{id}', [CompanyDetailController::class, 'edit'])->name('company.details.edit');
    Route::get('company-details-show/{id}', [CompanyDetailController::class, 'show'])->name('company.details.show');
    Route::put('company-details-update/{id}', [CompanyDetailController::class, 'update'])->name('company.details.update');
    Route::delete('company-details-destroy/{id}', [CompanyDetailController::class, 'destroy'])->name('company.details.destroy');
    Route::post('delete-pin-check', [CompanyDetailController::class, 'pinchecking'])->name('pinchecking');
    
    Route::post('/company/status/{id}', [CompanyDetailController::class, 'toggleStatus'])->name('company.status.toggle');

    Route::get('company/referralcode', [CompanyDetailController::class, 'referralcode'])->name('referralcode');
    Route::get('company/referralcode-list/{id}', [CompanyDetailController::class, 'referralCodeList'])->name('referralCodeList');
    Route::get('company/referralcode-create/{id}', [CompanyDetailController::class, 'createReferralCode'])->name('createReferralCode');
    Route::post('company/referralcode-store', [CompanyDetailController::class, 'storeReferralCode'])->name('storeReferralCode');
    Route::delete('company/referralcode-delete/{id}', [CompanyDetailController::class, 'destroyReferralCode'])->name('destroyReferralCode');
    Route::post('/company/referralcode-status/{id}', [CompanyDetailController::class, 'referralcodeStatus'])->name('referralcodeStatus');

    Route::get('company/referralcode-edit/{id}', [CompanyDetailController::class, 'editReferralCode'])->name('editReferralCode');
    Route::put('company/referralcode-update/{id}', [CompanyDetailController::class, 'updateReferralCode'])->name('updateReferralCode');
    // Business type
    Route::resource('businessTypes', BusinessTypeController::class);

    Route::get('/contact-form-submissions', [ContactFormController::class, 'index'])->name('admin.contact_form_submissions');

    Route::delete('/contact-form-submissions/{id}', [ContactFormController::class, 'destroy'])
        ->name('admin.contact_form_submissions.delete');

    // CallbackRequest
    Route::get('/request-callback', [CallbackRequestController::class, 'index'])->name('request-callback');
    Route::delete('/callback-requests/{id}', [CallbackRequestController::class, 'destroy'])->name('admin.callback_request.delete');

    



    Route::resource('faq', FAQController::class);

    //feedback_Management
    Route::get('/feedback-list', [FeedbackController::class, 'index'])->name('admin.feedback_list');
    Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy'])->name('admin.feedback_delete');
    Route::post('/feedback-reply/{id}', [FeedbackController::class, 'reply'])->name('admin.feedback_reply');


    //tickets_Management
    Route::get('/tickets-list', [TicketController::class, 'index'])->name('admin.tickets_list');
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('admin.tickets_delete');
    Route::post('/tickets-reply/{id}', [TicketController::class, 'reply'])->name('admin.tickets_reply');

    // Show the help content page
    Route::get('help-contents', [HelpContentController::class, 'index'])->name('admin.help.index');
    Route::post('help-contents', [HelpContentController::class, 'store'])->name('admin.help.store');
    Route::get('help-contents/{helpContent}/edit', [HelpContentController::class, 'edit'])->name('admin.help.edit');
    Route::put('help-contents/{helpContent}', [HelpContentController::class, 'update'])->name('admin.help.update');
    Route::delete('help-contents/{helpContent}', [HelpContentController::class, 'destroy'])->name('admin.help.destroy');



    Route::resource('homebanner', HomebannerController::class);
    Route::resource('appbanner', AppbannerController::class);
    Route::resource('features', FeaturesController::class);
    Route::post('features/{id}/toggle-status', [FeaturesController::class, 'toggleStatus'])->name('features.toggleStatus');

    Route::resource('packages', PackageController::class);
    
    Route::get('chat/{id}', [AdminChatController::class, 'index'])->name('adminchat');
    Route::get('chat-list', [AdminChatController::class, 'chatList'])->name('chatList');
    Route::get('save-adminchat/{id}', [AdminChatController::class, 'savechat'])->name('saveadminchat');
    Route::get('get-adminchat/{id}', [AdminChatController::class, 'getadminChat'])->name('getadminChat');

    Route::get('notification/send', [NotificationController::class, 'create'])->name('notification.send');
    Route::post('notification/send', [NotificationController::class, 'send'])->name('notification_send');

    
    Route::get('get-employee-salary/{employeeId}', [SalaryController::class, 'getEmployeeSalary'])->name('admin.getEmployeeSalary');
    
    //Route::resource('salarytype', SalaryController::class);
    Route::get('generate-salary', [SalaryController::class, 'generateSalary'])->name('admin.generateSalary');
    Route::post('save-employee-salary', [SalaryController::class, 'saveEmployeeSalary'])->name('admin.saveEmployeeSalary');
    Route::get('monthly-salary-list', [SalaryController::class, 'employeeSalaryList'])->name('admin.employeeSalaryList');
    Route::get('employee-salary-details/{employeeId}', [SalaryController::class, 'employeeSalaryDetails'])->name('admin.employeeSalaryDetails');

    Route::get('employee/salary-slip/download', [SalaryController::class, 'salaryPDF'])->name('admin.salaryPDF');
    Route::get('get-employee/{companyId}', [SalaryController::class, 'getEmployee'])->name('admin.getEmployee');

    Route::get('lead', [AdminCRMController::class, 'lead'])->name('admin.lead');
    Route::get('lead-list/{company_id}', [AdminCRMController::class, 'leadList'])->name('admin.leadList');
    Route::get('lead-create/{company_id}', [AdminCRMController::class, 'leadCreate'])->name('admin.leadCreate');
    Route::post('lead-store', [AdminCRMController::class, 'leadStore'])->name('admin.leadStore');
    Route::get('lead-edit/{id}', [AdminCRMController::class, 'leadEdit'])->name('admin.leadEdit');
    Route::put('lead-update/{id}', [AdminCRMController::class, 'leadUpdate'])->name('admin.leadUpdate');
    Route::delete('lead-delete/{id}', [AdminCRMController::class, 'destroy'])->name('admin.leadDelete');
    Route::get('followup-list/{id}', [AdminCRMController::class, 'leadFollowup'])->name('admin.leadFollowup');
    Route::get('followup-create/{lead_id}', [AdminCRMController::class, 'leadFollowupCreate'])->name('admin.leadFollowupCreate');
    Route::post('followup-store/{lead_id}', [AdminCRMController::class, 'leadFollowupStore'])->name('admin.leadFollowupStore');
    Route::get('followup-edit/{id}', [AdminCRMController::class, 'followupEdit'])->name('admin.followupEdit');
    Route::put('followup-update/{id}', [AdminCRMController::class, 'followupUpdate'])->name('admin.followupUpdate');

    Route::prefix('task')->group(function () {
        Route::get('/list', [AdminTaskController::class, 'taskList'])->name('admin.taskList');
        Route::get('/create', [AdminTaskController::class, 'taskCreate'])->name('admin.taskCreate');
        Route::post('/store', [AdminTaskController::class, 'taskStore'])->name('admin.taskStore');
        Route::get('/edit/{id}', [AdminTaskController::class, 'taskEdit'])->name('admin.taskEdit');
        Route::post('/update/{id}', [AdminTaskController::class, 'taskUpdate'])->name('admin.taskUpdate');
        Route::delete('/delete/{id}', [AdminTaskController::class, 'destroy'])->name('admin.taskDelete');
        Route::get('/comment-list/{id}', [AdminTaskController::class, 'commentList'])->name('admin.commentList');
        Route::get('/comment-create/{id}', [AdminTaskController::class, 'commentCreate'])->name('admin.commentCreate');
        Route::post('/comment-store/{id}', [AdminTaskController::class, 'commentStore'])->name('admin.commentStore');
        Route::get('/comment-edit/{id}', [AdminTaskController::class, 'commentEdit'])->name('admin.commentEdit');
        Route::put('/comment-update/{id}', [AdminTaskController::class, 'commentUpdate'])->name('admin.commentUpdate');
        Route::delete('/comment-delete/{id}', [AdminTaskController::class, 'commentDelete'])->name('admin.commentDelete');
        Route::delete('/file-delete/{id}', [AdminTaskController::class, 'fileDelete'])->name('admin.fileDelete');
    });

    Route::prefix('expense')->group(function () {
        Route::get('/list', [AdminExpenseController::class, 'expenseList'])->name('admin.expenseList');
        Route::get('/create', [AdminExpenseController::class, 'expenseCreate'])->name('admin.expenseCreate');
        Route::post('/store', [AdminExpenseController::class, 'expenseStore'])->name('admin.expenseStore');
        Route::get('/edit/{id}', [AdminExpenseController::class, 'expenseEdit'])->name('admin.expenseEdit');
        Route::post('/update/{id}', [AdminExpenseController::class, 'expenseUpdate'])->name('admin.expenseUpdate');
        Route::delete('/expenseDelete/{id}', [AdminExpenseController::class, 'expenseDelete'])->name('admin.expenseDelete');
        Route::delete('/expenseAttachmentDelete/{id}', [AdminExpenseController::class, 'expenseAttachmentDelete'])->name('admin.expenseAttachmentDelete');
        Route::post('/status-change', [AdminExpenseController::class, 'expensestatuschange'])->name('admin.expensestatuschange');
        Route::get('/details/{id}', [AdminExpenseController::class, 'expenseDetails'])->name('admin.expenseDetails');
        
        Route::get('/expenseFormDisplay', [AdminExpenseController::class, 'expenseFormDisplay'])->name('admin.expenseFormDisplay');
        Route::get('/formlist', [AdminExpenseController::class, 'expenseformList'])->name('admin.expenseformList');
        Route::get('/formcreate', [AdminExpenseController::class, 'expenseformCreate'])->name('admin.expenseformCreate');
        Route::post('/formstore', [AdminExpenseController::class, 'expenseformStore'])->name('admin.expenseformStore');
        Route::get('/formedit/{id}', [AdminExpenseController::class, 'expenseformEdit'])->name('admin.expenseformEdit');
        Route::post('/formupdate/{id}', [AdminExpenseController::class, 'expenseformUpdate'])->name('admin.expenseformUpdate');
        Route::delete('/formdelete/{id}', [AdminExpenseController::class, 'expenseformDelete'])->name('admin.expenseformDelete');
        Route::get('/formdetails/{id}', [AdminExpenseController::class, 'expenseformDetails'])->name('admin.expenseformDetails');
    });

    Route::prefix('vehicles')->group(function () {
        Route::get('/list', [AdminVehicleController::class, 'index'])->name('admin.vehicles.index');    
        Route::get('/create', [AdminVehicleController::class, 'create'])->name('admin.vehicles.create');  
        Route::post('/store', [AdminVehicleController::class, 'store'])->name('admin.vehicles.store');
        Route::get('/edit/{id}', [AdminVehicleController::class, 'edit'])->name('admin.vehicles.edit');
        Route::put('/update/{id}', [AdminVehicleController::class, 'update'])->name('admin.vehicles.update');
        Route::delete('/destroy/{id}', [AdminVehicleController::class, 'destroy'])->name('admin.vehicles.destroy');
    });

    //trips routes start
    Route::prefix('trips')->group(function () {
        Route::get('/list', [AdminTripController::class, 'index'])->name('admin.trips.index');
        Route::get('/create', [AdminTripController::class, 'create'])->name('admin.trips.create');
        Route::post('/store', [AdminTripController::class, 'store'])->name('admin.trips.store');
        Route::get('/edit/{id}', [AdminTripController::class, 'edit'])->name('admin.trips.edit');
        Route::put('/update/{id}', [AdminTripController::class, 'update'])->name('admin.trips.update');
        Route::delete('/destroy/{id}', [AdminTripController::class, 'destroy'])->name('admin.trips.destroy');
        Route::get('/show/{id}', [AdminTripController::class, 'show'])->name('admin.trips.show'); // Optional: View trip details
        Route::put('/assign/{id}', [AdminTripController::class, 'DriverAndVechicleAssign'])->name('admin.trips.assign');
        Route::put('/trips/{id}/status', [AdminTripController::class, 'updateStatus'])->name('admin.trips.status.update');
        Route::get('/trips/{trip}/check-vehicle/{vehicle}', [AdminTripController::class, 'checkVehicleAvailability'])->name('admin.trips.checkVehicleAvailability');
        Route::get('/trips/{trip}/check-driver/{driver}', [AdminTripController::class, 'checkDriverAvailability'])->name('admin.trips.checkDriverAvailability');
    });
    //trips routes end

    Route::prefix('blog-categories')->group(function () {
        Route::get('/', [BlogCategoryController::class, 'index'])->name('admin.blog_categories.index');
        Route::get('/create', [BlogCategoryController::class, 'create'])->name('admin.blog_category.create');
        Route::post('/store', [BlogCategoryController::class, 'store'])->name('admin.blog_categories.store');
        Route::get('/{id}/edit', [BlogCategoryController::class, 'edit'])->name('admin.blog_category.edit');
        Route::put('/{id}', [BlogCategoryController::class, 'update'])->name('admin.blog_categories.update');
        Route::delete('/{id}', [BlogCategoryController::class, 'destroy'])->name('admin.blog_category.destroy');

    });

    Route::prefix('blog-tags')->group(function () {
        Route::get('/', [BlogTagController::class, 'index'])->name('admin.blog_tag.index');
        Route::get('/create', [BlogTagController::class, 'create'])->name('admin.blog_tag.create');
        Route::post('/store', [BlogTagController::class, 'store'])->name('admin.blog_tag.store');
        Route::get('/{id}/edit', [BlogTagController::class, 'edit'])->name('admin.blog_tag.edit');
        Route::put('/{id}', [BlogTagController::class, 'update'])->name('admin.blog_tag.update');
        Route::delete('/{id}', [BlogTagController::class, 'destroy'])->name('admin.blog_tag.destroy');

    });

    Route::prefix('blog')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('admin.blog.index');
        Route::get('/create', [BlogController::class, 'create'])->name('admin.blog.create');
        Route::post('/store', [BlogController::class, 'store'])->name('admin.blog.store');
        Route::get('/{id}/edit', [BlogController::class, 'edit'])->name('admin.blog.edit');
        Route::put('/{id}', [BlogController::class, 'update'])->name('admin.blog.update');
        Route::delete('/{id}', [BlogController::class, 'destroy'])->name('admin.blog.destroy');
        Route::get('/{id}/comments', [BlogController::class, 'blogcomments'])->name('admin.blogcomments');
        Route::delete('/comment/{id}', [BlogController::class, 'blogcommentdelete'])->name('admin.blogcommentdelete');
    });
});