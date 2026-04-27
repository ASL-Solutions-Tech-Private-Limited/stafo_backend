<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\api\ReportController;
use App\Http\Controllers\Api\SalaryController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\HolidayController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\CompanyTypeController;
use App\Http\Controllers\Api\LeavePolicyController;
use App\Http\Controllers\Api\LeaveReportController;
use App\Http\Controllers\Api\BusinessTypeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\EmployeeReportController;
use App\Http\Controllers\Api\HelpAndSupportController;
use App\Http\Controllers\Api\AttendaceReportController;
use App\Http\Controllers\Api\companyDashboardController;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\BbpsCategoryController;
use App\Http\Controllers\Api\BbpsSubCategoryController;
use App\Http\Controllers\Api\BbpsOperatorMasterController;
use App\Http\Controllers\Api\ApiProviderController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\TaskController;

use App\Http\Controllers\Api\PerformanceController;

use App\Http\Controllers\Api\VehicleApiController;
use App\Http\Controllers\Api\TripAPiController;
use App\Http\Controllers\Api\TripGeoLocationController;
use App\Http\Controllers\Api\TripApiExpenseController;

use App\Http\Controllers\Api\ExpensetypeController;
use App\Http\Controllers\Api\ExpenseformController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\LeavetypeController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('login', 'App\Http\Controllers\Api\ApiLoginController@login')->name('login');

Route::post('register', 'App\Http\Controllers\Api\ApiLoginController@register')->name('register');

Route::post('send-otp', 'App\Http\Controllers\Api\ApiLoginController@sendOtp')->name('sendOtp');
Route::post('verify-otp', 'App\Http\Controllers\Api\ApiLoginController@loginWithOtp')->name('loginWithOtp');

Route::get('punchReminder', 'App\Http\Controllers\Api\EmployeeController@punchReminder')->name('punchReminder');
Route::post('change-device', 'App\Http\Controllers\Api\ApiLoginController@changeDevice')->name('changeDevice');

Route::get('package', 'App\Http\Controllers\Api\PackageController@package')->name('package');
Route::get('package-feature/{package_id}', 'App\Http\Controllers\Api\PackageController@packageFeature')->name('packageFeature');
Route::post('hasgenerate', 'App\Http\Controllers\Api\PackageController@hasgenerate')->name('hasgenerate');
Route::post('success', 'App\Http\Controllers\Api\PackageController@success')->name('success');
Route::post('failure', 'App\Http\Controllers\Api\PackageController@failure')->name('failure');
Route::post('paymentUpdate', 'App\Http\Controllers\Api\PackageController@paymentUpdate')->name('paymentUpdate');


Route::middleware('auth:sanctum')->group(function () {

    Route::get('subscription-info', 'App\Http\Controllers\Api\PackageController@subscriptionInfo')->name('subscriptionInfo');

    Route::post('getcustomerlist', 'App\Http\Controllers\Api\ApiCustomerController@getCustomerList')->name('getCustomerList');

    Route::post('company/approve-device', 'App\Http\Controllers\Api\ApiLoginController@approveDeviceRequest');
    Route::post('device-requests-list', 'App\Http\Controllers\Api\ApiLoginController@listDeviceRequests');

    // branch api
    Route::prefix('branch')->group(function () {
        Route::get('/list', [BranchController::class, 'index']);
        Route::post('/create', [BranchController::class, 'store']);
        Route::post('/update/{id}', [BranchController::class, 'update']);
        Route::delete('/delete/{id}', [BranchController::class, 'destroy']);
    });

    //   comapny Dashboard api
    Route::post('company-dashboard', [companyDashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('employeesOnLeave', [companyDashboardController::class, 'employeesOnLeave'])->name('employeesOnLeave');


    // Route for updating a company
    Route::post('company/update', [CompanyController::class, 'updateCompany'])->name('updateCompany');
    Route::post('company/delete', [CompanyController::class, 'deleteCompany'])->name('deleteCompany');
    Route::get('company/profile', [CompanyController::class, 'showCompanyProfile'])->name('showCompanyProfile');
    Route::post('generate-qrcode', [CompanyController::class, 'generateQrCode'])->name('generateQrCode');
    Route::post('feedback', [CompanyController::class, 'feedback'])->name('feedback');
    Route::post('feedback-list', [CompanyController::class, 'feedbackList'])->name('feedbackList');

    // employee-create
    Route::post('employees-create', [EmployeeController::class, 'store'])->name('employees.store');

    // Route::post('employee/punch-in', [EmployeeController::class, 'punchIn']);
    // Route::post('employee/punch-out', [EmployeeController::class, 'punchOut']);
    Route::post('employee-dashboard', [EmployeeController::class, 'employeeDashboard'])->name('employeeDashboard');

    Route::post('employee/punch', [EmployeeController::class, 'punch']);
    Route::post('employee/punch-list', [EmployeeController::class, 'punchList']);
    Route::post('employee/selfie-attendance', [EmployeeController::class, 'selfieAttendance']);
    Route::post('employee/qr-attendance', [EmployeeController::class, 'qrAttendance']);
    Route::post('employee/selfie-image-upload', [EmployeeController::class, 'selfieImageUpload']);
    Route::post('employee/selfie-image-remove', [EmployeeController::class, 'selfieImageRemove']);
    // employee-update
    Route::post('employees-update/{id?}', [EmployeeController::class, 'update'])->name('employees.update');

    // employee-list
    Route::post('employees-list', [EmployeeController::class, 'index'])->name('employees-list.index');

    // employee-details
    Route::get('employee-details/{id}', [EmployeeController::class, 'show'])->name('employee.details');

    // employee list by compnay_id
    Route::post('employees-list/by-company-id', [EmployeeController::class, 'listByCompany'])->name('ListByCompany');

    Route::post('employeetype-list', [EmployeeController::class, 'employeetypeList'])->name('employeetypeList');
    Route::post('employees-status-change', [EmployeeController::class, 'employeeStatusChange'])->name('employeeStatusChange');

    Route::post('employee/assign-branch', [EmployeeController::class, 'assignBranch']);
    Route::post('employee/assign-department', [EmployeeController::class, 'assignDepartment']);

    Route::post('jobtitle-list', [EmployeeController::class, 'jobtitleList'])->name('jobtitleList');
    // Employee leave
    Route::post('leave-request', [EmployeeController::class, 'leaveRequest'])->name('leaveRequest');

    Route::post('pending-leave-request', [EmployeeController::class, 'pendingLeaveRequest'])->name('pendingLeaveRequest');

    Route::post('leave-request-status-change', [EmployeeController::class, 'leaveRequestStatusChange'])->name('leaveRequestStatusChange');

    Route::post('leave-list', [EmployeeController::class, 'leaveList'])->name('leaveList');

    Route::post('update-geo-status', [EmployeeController::class, 'updateGeoStatus'])->name('updateGeoStatus');
    Route::post('store-geo-location', [EmployeeController::class, 'storeGeoLocation'])->name('storeGeoLocation');
    Route::post('get-geo-location', [EmployeeController::class, 'getGeoLocation'])->name('getGeoLocation');
    Route::post('set-attendance-type', [EmployeeController::class, 'setAttendanceType'])->name('setAttendanceType');
    Route::post('employee-branch-info', [EmployeeController::class, 'employeeBranchInfo'])->name('employeeBranchInfo');
    Route::post('devicelog-store', [EmployeeController::class, 'devicelogStore'])->name('devicelogStore');
    // document upload controller
    Route::Post('upload-document', [DocumentController::class, 'uploadDocument'])->name('uploadDocument');

    Route::post('update-document/{document_id}', [DocumentController::class, 'updateDocument']);

    Route::get('/company/{company_id}/documents', [DocumentController::class, 'getDocumentsByCompany'])->name('getDocumentsByCompany');

    // Route to list all holidays
    Route::get('holidays', [HolidayController::class, 'index']);
    // Route to create a holiday
    Route::post('holidays-create', [HolidayController::class, 'store'])->name('holidays.create');
    // holiday get by company_id
    Route::get('holidays/by-company', [HolidayController::class, 'holidayGetById']);
    // Route to edit a holiday
    Route::put('holidays-update/{id}', [HolidayController::class, 'update']);
    // Route to delete a holiday (using holiday ID)
    Route::delete('holidays-delete/{id}', [HolidayController::class, 'destroy']);

    Route::post('policy-create', [PolicyController::class, 'store']);
    Route::get('policy', [PolicyController::class, 'index']);

    Route::get('leave-policy', [LeavePolicyController::class, 'index']);
    Route::post('leavepolicy-create', [LeavePolicyController::class, 'store'])->name('leavepolicy.create');
    Route::put('leavepolicy-update/{id}', [LeavePolicyController::class, 'update']);
    Route::delete('leavepolicy-delete/{id}', [LeavePolicyController::class, 'destroy']);

    // get employee bank details
    Route::get('bank-accounts', [App\Http\Controllers\Api\BankAccountController::class, 'index'])->name('bank-accounts.index');

    Route::get('bank-accounts/employee/{employee_id}', [App\Http\Controllers\Api\BankAccountController::class, 'showByEmployeeId'])->name('bank-accounts.show-by-employee');


    Route::prefix('employee-documents')->group(function () {
        Route::post('list', [EmployeeDocumentController::class, 'index']);
        Route::post('create', [EmployeeDocumentController::class, 'store']);
        Route::get('show/{id}', [EmployeeDocumentController::class, 'show']);
        Route::put('update/{id}', [EmployeeDocumentController::class, 'update']);
        Route::delete('delete/{id}', [EmployeeDocumentController::class, 'destroy']);
    });


    // Route for adding attendance
    Route::post('attendance', [AttendanceController::class, 'store']);
    // Route for updating attendance
    Route::post('attendance/update/{id}', [AttendanceController::class, 'update']);
    // Route for listing attendance
    Route::get('attendance-list', [AttendanceController::class, 'index']);

    // Route for adding attendance
    Route::post('attendance-request', [AttendanceController::class, 'attendance_request_store']);
    // Route for updating attendance
    Route::post('attendance-request-status-update/{id}', [AttendanceController::class, 'attendance_request_status_update']);
    // Route for listing attendance
    Route::get('attendance-request-list', [AttendanceController::class, 'attendance_request_list']);



    Route::prefix('help-and-support')->group(function () {
        Route::post('/create', [HelpAndSupportController::class, 'store']);
        Route::get('/list', [HelpAndSupportController::class, 'index']);
        Route::get('/details/{id}', [HelpAndSupportController::class, 'show']);
        Route::put('/update/{id}', [HelpAndSupportController::class, 'update']);
        Route::delete('/delete/{id}', [HelpAndSupportController::class, 'destroy']);
    });

    // shift api
    Route::get('shifts', [ShiftController::class, 'index']);
    Route::post('shifts', [ShiftController::class, 'store']);
    Route::get('shifts/{id}', [ShiftController::class, 'show']);
    Route::put('shifts/{id}', [ShiftController::class, 'update']);
    Route::delete('shifts/{id}', [ShiftController::class, 'destroy']);

    Route::post('/employees/assign-shift', [ShiftController::class, 'assignShift']);

    // department api
    Route::get('departments', [DepartmentController::class, 'index']);
    Route::post('departments', [DepartmentController::class, 'store']);
    Route::get('departments/{id}', [DepartmentController::class, 'show']);
    Route::put('departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('departments/{id}', [DepartmentController::class, 'destroy']);

    // Send notification to employee
    Route::post('send-notification', [NotificationController::class, 'sendNotification']);
    Route::post('notifications', [NotificationController::class, 'getNotifications']);
    // Mark notification as read
    Route::post('notifications/read', [NotificationController::class, 'markAsRead']);
    Route::post('upgradeInterested', [CompanyController::class, 'upgradeInterested']);

    Route::post('performancetype/list', [PerformanceController::class, 'list']);
    Route::post('performancetype/add', [PerformanceController::class, 'store']);
    Route::post('performancetype/update/{id}', [PerformanceController::class, 'update']);
    Route::delete('performancetype/delete/{id}', [PerformanceController::class, 'delete']);
    Route::post('performance/save', [PerformanceController::class, 'saveEmployeePerformance']);
    Route::post('performance/rank-list', [PerformanceController::class, 'rankList']);
    Route::post('performance/rank-details', [PerformanceController::class, 'rankDetails']);
    Route::post('referral-list', [companyDashboardController::class, 'referralList']);

    Route::prefix('lead')->group(function () {
        Route::get('/dashboard', [LeadController::class, 'dashboard']);
        Route::get('/list', [LeadController::class, 'index']);
        Route::post('/create', [LeadController::class, 'store']);
        Route::post('/update/{id}', [LeadController::class, 'update']);
        Route::delete('/delete/{id}', [LeadController::class, 'destroy']);
        Route::get('/followup-list', [LeadController::class, 'followupList']);
        Route::post('/followup-create', [LeadController::class, 'followupStore']);
    });

    Route::prefix('task')->group(function () {
        Route::get('/list', [TaskController::class, 'list']);
        Route::post('/create', [TaskController::class, 'store']);
        Route::post('/update/{id}', [TaskController::class, 'update']);
        Route::delete('/delete/{id}', [TaskController::class, 'destroy']);
        Route::get('/comment-list', [TaskController::class, 'commentList']);
        Route::post('/comment-create', [TaskController::class, 'commentStore']);
        Route::delete('/comment-delete/{id}', [TaskController::class, 'commentDelete']);
        Route::delete('/file-delete/{id}', [TaskController::class, 'fileDelete']);
        Route::post('/file-uploads', [TaskController::class, 'fileUploads']);
        Route::post('/status-change/{id}', [TaskController::class, 'statusChange']);
    });

    // Expense api
    Route::prefix('expensetype')->group(function () {
        Route::get('/list', [ExpensetypeController::class, 'index']);
        Route::post('/create', [ExpensetypeController::class, 'store']);
        Route::post('/update/{id}', [ExpensetypeController::class, 'update']);
        Route::delete('/delete/{id}', [ExpensetypeController::class, 'destroy']);
    });

   
    Route::prefix('expenseform')->group(function () {
        Route::get('/list', [ExpenseformController::class, 'index']);
        Route::post('/create', [ExpenseformController::class, 'store']);
        Route::post('/update/{id}', [ExpenseformController::class, 'update']);
        Route::delete('/delete/{id}', [ExpenseformController::class, 'destroy']);
    });

    Route::prefix('expense')->group(function () {
        Route::get('/list', [ExpenseController::class, 'index']);
        Route::post('/create', [ExpenseController::class, 'store']);
        Route::get('/details', [ExpenseController::class, 'expense_details']);
        Route::post('/update/{id}', [ExpenseController::class, 'update']);
        Route::delete('/delete/{id}', [ExpenseController::class, 'destroy']);
        Route::delete('/attachment-delete/{id}', [ExpenseController::class, 'attachmentDelete']);
        Route::post('status-change', [ExpenseController::class, 'statusChange'])->name('expenseStatusChange');
    });

    // Expense api
    Route::prefix('leavetype')->group(function () {
        Route::get('/list', [LeavetypeController::class, 'index']);
        Route::post('/create', [LeavetypeController::class, 'store']);
        Route::post('/update/{id}', [LeavetypeController::class, 'update']);
        Route::delete('/delete/{id}', [LeavetypeController::class, 'destroy']);
    });

    // vehicles routes start
    Route::prefix('vehicles')->group(function () {
        Route::get('/list', [VehicleApiController::class, 'index']);   
        Route::post('/create', [VehicleApiController::class, 'store']);
        Route::post('/update/{id}', [VehicleApiController::class, 'update']);
        Route::post('/details', [VehicleApiController::class, 'show']);
        Route::post('/delete', [VehicleApiController::class, 'destroy']);
        Route::post('/status-change', [VehicleApiController::class, 'statusChange'])->name('vehicleStatusChange');
    });
    // vehicles routes end

        //trips routes start
    Route::prefix('trips')->group(function () {
        Route::get('/list', [TripAPiController::class, 'index']);
        Route::post('/create', [TripAPiController::class, 'store']);
        Route::get('/edit/{id}', [TripAPiController::class, 'edit']);
        Route::post('/update/{id}', [TripAPiController::class, 'update']);
        Route::post('/delete', [TripAPiController::class, 'destroy']);
        Route::post('/details', [TripAPiController::class, 'show']); 
        Route::post('/drivers/list', [TripAPiController::class, 'driverList']); 
        Route::post('/dashboard', [TripAPiController::class, 'dashboard']); 
        Route::post('/assign-driver', [TripAPiController::class, 'assignDriver']);
        Route::post('/assign-vehicle', [TripAPiController::class, 'assignVehicle']);
        Route::post('/check-vehicle-availability', [TripAPiController::class, 'checkVehicleAvailability']);
        Route::post('/check-driver-availability', [TripAPiController::class, 'checkDriverAvailability']);
        Route::post('/trip-start-end', [TripAPiController::class, 'tripAction']);
    });
    //trips routes end



    //trips geo-location routes start
    Route::prefix('trips-geolocation')->group(function () {
        Route::post('create', [TripGeoLocationController::class, 'storeTripGeoLocation']);
        Route::post('get', [TripGeoLocationController::class, 'getTripGeoLocation']);

    });
        //trips geo-location routes end


    //trips expense routes start
    Route::prefix('trips-expense')->group(function () {
        Route::post('create', [TripApiExpenseController::class, 'store']);
        Route::post('list', [TripApiExpenseController::class, 'index']);
        Route::post('update', [TripApiExpenseController::class, 'update']);
        Route::post('delete', [TripApiExpenseController::class, 'destroy']);
    });
    //trips expense routes end

});

//location country,state,city
Route::get('countries', [LocationController::class, 'getCountries']);
Route::get('countries/{countryId}/states', [LocationController::class, 'getStates']);
Route::get('states/{stateId}/cities', [LocationController::class, 'getCities']);


//company type api
Route::get('company-types', [CompanyTypeController::class, 'index']);
Route::post('company-types', [CompanyTypeController::class, 'store']);
//business type api
Route::get('business-types', [BusinessTypeController::class, 'index']);
Route::post('business-types', [BusinessTypeController::class, 'store']);

Route::post('/cron-job/mark-absent', [EmployeeController::class, 'runMarkAbsentJob']);

Route::post('/document-verify', [CompanyController::class, 'documentVerify']);
Route::get('app-banner', [companyDashboardController::class, 'appbanner']);


// download report for employee
Route::post('report/export-employee', [EmployeeReportController::class, 'exportEmployee'])->name('report.exportEmployee');

// employee report list
Route::get('employee/reports/list', [EmployeeReportController::class, 'getAllEmployeeReports']);


// download report for attendance
Route::post('report/export-attendace', [AttendaceReportController::class, 'exportAttendance'])->name('report.exportAttendance');

//  attendance report list
Route::get('attendace/reports/list', [AttendaceReportController::class, 'getAllAttendanceReports']);


// download report for leave
Route::post('report/export-leave', [LeaveReportController::class, 'exportLeave']);

// leave report list
Route::get('leave/reports/list', [LeaveReportController::class, 'getAllLeaveReports']);


// add salary type
Route::post('salarytype/store', [SalaryController::class, 'storeSalaryType']);
// add salary type list
Route::post('salarytype/list', [SalaryController::class, 'listSalaryType']);
// add salary type delete
Route::post('salarytype/delete', [SalaryController::class, 'deleteSalaryType']);

// salary generate
Route::post('salary/preview', [SalaryController::class, 'generateEmployeeSalaryPreview']);
// Final Save
Route::post('salary/save', [SalaryController::class, 'saveGeneratedEmployeeSalary']);

Route::post('employee/salary-slip/download', [SalaryController::class, 'salaryPDF']);
Route::get('gracesetting-list', [SalaryController::class, 'gracesettingList']);
Route::post('gracesetting-update/{id}', [SalaryController::class, 'gracesettingUpdate']);
Route::post('generate-all-salary', [SalaryController::class, 'generateAllSalary']);

// bbps category
Route::prefix('bbps')->group(function () {
    Route::get('categories', [BbpsCategoryController::class, 'index']);
    Route::post('categories', [BbpsCategoryController::class, 'store']);
    Route::post('categories-update/{id}', [BbpsCategoryController::class, 'update']);
    Route::delete('categories-delete/{id}', [BbpsCategoryController::class, 'destroy']);
});


// bbps sub category
Route::prefix('bbps-sub-category')->group(function () {
    Route::get('list', [BbpsSubCategoryController::class, 'index']);
    Route::post('create', [BbpsSubCategoryController::class, 'store']);
    Route::post('update/{id}', [BbpsSubCategoryController::class, 'update']);
    Route::delete('delete/{id}', [BbpsSubCategoryController::class, 'destroy']);
});

// bbps sub operators
Route::prefix('bbps-operators')->group(function () {
    Route::post('list', [BbpsOperatorMasterController::class, 'index']);
    Route::post('create', [BbpsOperatorMasterController::class, 'store']);
    // Route::get('details/{id}', [BbpsOperatorMasterController::class, 'show']);
    Route::get('details/{operator_code}', [BbpsOperatorMasterController::class, 'show']);
    Route::post('update/{id}', [BbpsOperatorMasterController::class, 'update']);
    Route::delete('delete/{id}', [BbpsOperatorMasterController::class, 'destroy']);
});


// api-providers
Route::prefix('api-providers')->group(function () {
    Route::get('list', [ApiProviderController::class, 'index']);
    Route::post('create', [ApiProviderController::class, 'store']);
    Route::get('details/{id}', [ApiProviderController::class, 'show']);
    Route::post('update/{id}', [ApiProviderController::class, 'update']);
    Route::delete('delete/{id}', [ApiProviderController::class, 'destroy']);
});

Route::prefix('chat')->group(function () {
    Route::post('send', [ChatController::class, 'sendchat']);
    Route::post('get', [ChatController::class, 'getChat']);
});
//Route::post('chat/send', [SendController::class, 'sendchat']);

