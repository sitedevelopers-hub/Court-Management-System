
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\CaseController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\CourtroomController;
use App\Http\Controllers\CourtEventController;
use App\Http\Controllers\CaseDocumentController;
use App\Http\Controllers\CaseFeeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BailBondController;
use App\Http\Controllers\FinancialAdjustmentController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WorkTemplateController;

// Root URL — send visitors to the dashboard (or login if not authenticated)
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

// Public login routes
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.submit');

// Public registration routes
Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])
    ->name('register');

Route::post('/register', [RegistrationController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register.submit');

// Email verification routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/dashboard')
        ->with('success', 'Email verified successfully.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('success', 'A fresh verification link has been sent.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Logout requires an authenticated session
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Protect all court-system pages and records
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('cases', CaseController::class);

    Route::resource('participants', ParticipantController::class);
    Route::resource('hearings', HearingController::class);
    Route::resource('courtrooms', CourtroomController::class);
    Route::resource('court-events', CourtEventController::class);

    Route::resource('case-documents', CaseDocumentController::class);

    Route::get('/case-documents/{case_document}/download', [
        CaseDocumentController::class,
        'download'
    ])->name('case-documents.download');

    Route::resource('case-fees', CaseFeeController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('bail-bonds', BailBondController::class);
    Route::resource('financial-adjustments', FinancialAdjustmentController::class);

    Route::get('/financial-reports', [
        FinancialReportController::class,
        'index'
    ])->name('financial-reports.index');

    // The controller signatures type-hint User $user, so the resource must
    // bind its {id} parameter as "user" — otherwise Laravel injects an EMPTY
    // User model (the delete button silently did nothing).
    Route::resource('user-management', UserManagementController::class)
        ->parameter('user-management', 'user');

    // Reports: generate, list, download, share
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
    Route::post('/reports/{report}/share', [ReportController::class, 'share'])->name('reports.share');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

    // Work templates: type, save as Word doc, share, download
    Route::get('/work-templates', [WorkTemplateController::class, 'index'])->name('work-templates.index');
    Route::get('/work-templates/create', [WorkTemplateController::class, 'create'])->name('work-templates.create');
    Route::post('/work-templates', [WorkTemplateController::class, 'store'])->name('work-templates.store');
    Route::get('/work-templates/{template}', [WorkTemplateController::class, 'show'])->name('work-templates.show');
    Route::get('/work-templates/{template}/edit', [WorkTemplateController::class, 'edit'])->name('work-templates.edit');
    Route::put('/work-templates/{template}', [WorkTemplateController::class, 'update'])->name('work-templates.update');
    Route::get('/work-templates/{template}/download', [WorkTemplateController::class, 'download'])->name('work-templates.download');
    Route::post('/work-templates/{template}/share', [WorkTemplateController::class, 'share'])->name('work-templates.share');
    Route::delete('/work-templates/{template}', [WorkTemplateController::class, 'destroy'])->name('work-templates.destroy');
});
