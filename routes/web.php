<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HalfDayController;
use App\Http\Controllers\Admin\AdminLeaveController;
use App\Http\Controllers\Admin\AdminHalfDayController;
use App\Http\Controllers\ManagementSettingsController;
use App\Http\Controllers\DetailsOfLeaveController;
use App\Http\Controllers\LeaveCardController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollDeductionController;
use App\Http\Controllers\PayrollRemittanceController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserLeaveCardController;

// ── Guest ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/',       [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login',  [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout',      [AuthController::class, 'logout'])->name('logout');
    Route::post('/switch-view', [AuthController::class, 'switchView'])->name('auth.switch-view');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

     // ── Profile (all authenticated users) ────────────────────────────────────
    Route::get('/profile',           [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile',           [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',  [ProfileController::class, 'updatePassword'])->name('profile.password');

    // ── Notifications ─────────────────────────────────────────────────────────
    Route::get('/notifications',              [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',    [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read',   [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.count');

    // ── Employee-facing leave ─────────────────────────────────────────────────
    Route::get('/application/leave',              [LeaveApplicationController::class, 'index'])->name('application.leave');
    Route::post('/application/leave',             [LeaveApplicationController::class, 'store'])->name('leave.store');
    Route::post('/application/leave/monetize',    [LeaveApplicationController::class, 'storeMonetization'])->name('leave.monetize');
    Route::post('/application/leave/{id}/cancel', [LeaveApplicationController::class, 'cancel'])->name('leave.cancel');
    Route::get('/application/leave/{id}/pdf',     [LeaveApplicationController::class, 'pdf'])->name('leave.pdf');

    // ── Employee-facing half day ──────────────────────────────────────────────
    Route::get('/application/halfday',              [HalfDayController::class, 'index'])->name('application.halfday');
    Route::post('/application/halfday',             [HalfDayController::class, 'store'])->name('halfday.store');
    Route::get('/application/halfday/{id}/pdf',     [HalfDayController::class, 'pdf'])->name('halfday.pdf');
    Route::post('/application/halfday/{id}/cancel', [HalfDayController::class, 'cancel'])->name('halfday.cancel');

    // ── Employee-facing payroll ───────────────────────────────────────────────
    Route::get('/payroll/payslip',              [PayrollController::class, 'payslip'])->name('payroll.payslip');
    Route::get('/payroll/{period}/payslip-pdf', [PayrollController::class, 'payslipPdf'])->name('payroll.payslip.pdf');

   // ── Employee-facing leavecard ───────────────────────────────────────────────
    Route::get('/leave-card',                 [UserLeaveCardController::class, 'index'])->name('leave.card');
    Route::get('/leave-card/data',            [UserLeaveCardController::class, 'show'])->name('leave-card.data');
    Route::get('/leave-card/print',           [UserLeaveCardController::class, 'print'])->name('leave-card.print');
    Route::get('/leave-card/old-balance-pdf', [UserLeaveCardController::class, 'oldBalancePdf'])->name('leave-card.old-balance-pdf');

    // ── Admin-only ────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        // Employees
        Route::get('/employees',                     [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/employees',                    [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{id}/show',           [EmployeeController::class, 'show'])->name('employees.show');
        Route::put('/employees/{id}',                [EmployeeController::class, 'update'])->name('employees.update');
        Route::post('/employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle');
        Route::post('employees/{id}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');

        // Admin leave management
        Route::get('/admin/leave',               [AdminLeaveController::class, 'index'])->name('admin.leave.index');
        Route::post('/admin/leave/{id}/approve', [AdminLeaveController::class, 'approve'])->name('admin.leave.approve');
        Route::post('/admin/leave/{id}/reject',  [AdminLeaveController::class, 'reject'])->name('admin.leave.reject');
        Route::post('/admin/leave/{id}/status',  [AdminLeaveController::class, 'updateStatus'])->name('admin.leave.status');
        Route::get('/admin/leave/{id}/pdf',      [AdminLeaveController::class, 'pdf'])->name('admin.leave.pdf');

        // ── Admin half day management ─────────────────────────────────────────
        Route::get('/admin/halfday',              [AdminHalfDayController::class, 'index'])->name('admin.halfday.index');
        Route::post('/admin/halfday/{id}/status', [AdminHalfDayController::class, 'updateStatus'])->name('admin.halfday.status');
        Route::get('/admin/halfday/{id}/cert',    [AdminHalfDayController::class, 'cert'])->name('admin.halfday.cert');

        // ── Admin payroll ─────────────────────────────────────────────────────
        Route::get('/payroll',                                    [PayrollController::class, 'index'])->name('payroll.index');
        
        // --- FIXED ROUTES TO MATCH CONTROLLER METHOD NAMES ---
        Route::get('/payroll/create',                             [PayrollController::class, 'create'])->name('payroll.create');
        Route::post('/payroll',                                   [PayrollController::class, 'store'])->name('payroll.store');
        Route::get('/payroll/manage',                             [PayrollController::class, 'manage'])->name('payroll.manage');
        Route::patch('/payroll/record/{id}',                      [PayrollController::class, 'updateRecord'])->name('payroll.record.update');
        Route::post('/payroll/{id}/finalize',                     [PayrollController::class, 'finalize'])->name('payroll.finalize');
        Route::get('/payroll/{id}/pdf',                           [PayrollController::class, 'pdf'])->name('payroll.pdf');
        Route::get('/payroll/{id}/payslip-all-pdf',               [PayrollController::class, 'payslipAllPdf'])->name('payroll.payslip-all-pdf');
        
        // Remittance routes merged back in so the Javascript doesn't crash
        Route::get('/payroll/remittances',                        [PayrollController::class, 'remittances'])->name('payroll.remittances');
        Route::patch('/payroll/remittance/record/{id}',           [PayrollRemittanceController::class, 'saveField'])->name('payroll.remittance.record.save');
        Route::patch('/payroll/remittance/record/{id}/hide',      [PayrollRemittanceController::class, 'hideRecord'])->name('payroll.remittance.record.hide');
        Route::get('/payroll/{period}/remittance/{type}/pdf',     [PayrollController::class, 'remittancePdf'])->name('payroll.remittance.pdf');

        // Logs (placeholder)
        Route::get('/logs', fn() => view('placeholder'))->name('logs.index');

        // ── Leave Card (Admin) ────────────────────────────────────────────────
        Route::prefix('admin/leave-card')->name('leave-card.')->group(function () {
            Route::get('/',                                    [LeaveCardController::class, 'index'])->name('index');
            Route::get('/print-all',                           [LeaveCardController::class, 'printAll'])->name('print-all');
            Route::get('/{employeeId}/{year}',                 [LeaveCardController::class, 'show'])->name('show');
            Route::get('/{employeeId}/{year}/print',           [LeaveCardController::class, 'print'])->name('print');
            Route::get('/{employeeId}/{year}/old-balance-pdf', [LeaveCardController::class, 'oldBalancePdf'])->name('old-balance-pdf');
            Route::post('/save',                               [LeaveCardController::class, 'save'])->name('save');
        });

        Route::prefix('payroll/deductions')->name('payroll.deductions.')->group(function () {
            Route::get('/',              [PayrollDeductionController::class, 'index'])->name('index');
            Route::post('/',             [PayrollDeductionController::class, 'store'])->name('store');
            Route::put('/{id}',          [PayrollDeductionController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle', [PayrollDeductionController::class, 'toggle'])->name('toggle');
            Route::delete('/{id}',       [PayrollDeductionController::class, 'destroy'])->name('destroy');
            Route::post('/reorder',      [PayrollDeductionController::class, 'reorder'])->name('reorder');
        });

        // ── Management Settings ───────────────────────────────────────────────
        Route::prefix('settings')->name('settings.')->group(function () {

            Route::get('/', [ManagementSettingsController::class, 'index'])->name('index');

            // Leave Type
            Route::get('/leave-type',               [ManagementSettingsController::class, 'leaveType'])->name('leaveType');
            Route::post('/leave-type',              [ManagementSettingsController::class, 'storeLeaveType'])->name('leaveType.store');
            Route::put('/leave-type/{id}',          [ManagementSettingsController::class, 'updateLeaveType'])->name('leaveType.update');
            Route::patch('/leave-type/{id}/toggle', [ManagementSettingsController::class, 'toggleLeaveType'])->name('leaveType.toggle');
            Route::delete('/leave-type/{id}',       [ManagementSettingsController::class, 'destroyLeaveType'])->name('leaveType.destroy');

            // Department
            Route::get('/department',               [ManagementSettingsController::class, 'department'])->name('department');
            Route::post('/department',              [ManagementSettingsController::class, 'storeDepartment'])->name('department.store');
            Route::put('/department/{id}',          [ManagementSettingsController::class, 'updateDepartment'])->name('department.update');
            Route::patch('/department/{id}/toggle', [ManagementSettingsController::class, 'toggleDepartment'])->name('department.toggle');
            Route::delete('/department/{id}',       [ManagementSettingsController::class, 'destroyDepartment'])->name('department.destroy');

            // Position
            Route::get('/position',                 [ManagementSettingsController::class, 'position'])->name('position');
            Route::post('/position',                [ManagementSettingsController::class, 'storePosition'])->name('position.store');
            Route::put('/position/{id}',            [ManagementSettingsController::class, 'updatePosition'])->name('position.update');
            Route::patch('/position/{id}/toggle',   [ManagementSettingsController::class, 'togglePosition'])->name('position.toggle');
            Route::delete('/position/{id}',         [ManagementSettingsController::class, 'destroyPosition'])->name('position.destroy');

            // Details of Leave
            Route::get('/details-of-leave',                                 [DetailsOfLeaveController::class, 'index'])->name('detailsOfLeave');
            Route::post('/details-of-leave/groups',                         [DetailsOfLeaveController::class, 'storeGroup'])->name('detailsOfLeave.group.store');
            Route::put('/details-of-leave/groups/{id}',                     [DetailsOfLeaveController::class, 'updateGroup'])->name('detailsOfLeave.group.update');
            Route::delete('/details-of-leave/groups/{id}',                  [DetailsOfLeaveController::class, 'destroyGroup'])->name('detailsOfLeave.group.destroy');
            Route::post('/details-of-leave/groups/{groupId}/items',         [DetailsOfLeaveController::class, 'storeItem'])->name('detailsOfLeave.item.store');
            Route::post('/details-of-leave/groups/{groupId}/items/reorder', [DetailsOfLeaveController::class, 'reorderItems'])->name('detailsOfLeave.item.reorder');
            Route::put('/details-of-leave/items/{id}',                      [DetailsOfLeaveController::class, 'updateItem'])->name('detailsOfLeave.item.update');
            Route::delete('/details-of-leave/items/{id}',                   [DetailsOfLeaveController::class, 'destroyItem'])->name('detailsOfLeave.item.destroy');

            // Commutation
            Route::get('/commutation',              [ManagementSettingsController::class, 'commutation'])->name('commutation');
            Route::post('/commutation',             [ManagementSettingsController::class, 'storeCommutation'])->name('commutation.store');
            Route::put('/commutation/{id}',         [ManagementSettingsController::class, 'updateCommutation'])->name('commutation.update');
            Route::delete('/commutation/{id}',      [ManagementSettingsController::class, 'destroyCommutation'])->name('commutation.destroy');

            // Recommendation
            Route::get('/recommendation',           [ManagementSettingsController::class, 'recommendation'])->name('recommendation');
            Route::post('/recommendation',          [ManagementSettingsController::class, 'storeRecommendation'])->name('recommendation.store');
            Route::put('/recommendation/{id}',      [ManagementSettingsController::class, 'updateRecommendation'])->name('recommendation.update');
            Route::delete('/recommendation/{id}',   [ManagementSettingsController::class, 'destroyRecommendation'])->name('recommendation.destroy');

            // Signatory
            Route::get('/signatory',                [ManagementSettingsController::class, 'signatory'])->name('signatory');
            Route::post('/signatory',               [ManagementSettingsController::class, 'storeSignatory'])->name('signatory.store');
            Route::put('/signatory/{id}',           [ManagementSettingsController::class, 'updateSignatory'])->name('signatory.update');
            Route::delete('/signatory/{id}',        [ManagementSettingsController::class, 'destroySignatory'])->name('signatory.destroy');

            // Role
            Route::get('/role',                     [ManagementSettingsController::class, 'role'])->name('role');
            Route::post('/role',                    [ManagementSettingsController::class, 'storeRole'])->name('role.store');
            Route::put('/role/{id}',                [ManagementSettingsController::class, 'updateRole'])->name('role.update');
            Route::delete('/role/{id}',             [ManagementSettingsController::class, 'destroyRole'])->name('role.destroy');

        });

    }); // ← Admin middleware group ends here

}); // ← Auth middleware group ends here