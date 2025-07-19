<?php

use App\Http\Controllers\Resident\SubmissionController;
use App\Livewire\Resident\Dashboard as ResidentDashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Http\Controllers\Admin\ResidentController as AdminResidentController;
use App\Http\Controllers\Admin\LecturerController as AdminLecturerController;
use App\Http\Controllers\Admin\RequirementRuleController as AdminRequirementRuleController;
use App\Http\Controllers\Admin\TaskCategoryController as AdminTaskCategoryController;
use App\Http\Controllers\Admin\DivisionController as AdminDivisionController;
use App\Http\Controllers\Resident\ResidentController;
use App\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\LecturerController;

// Arahkan /home untuk Dosen
Route::get('/home', function () {
    $user = auth()->user();
    if ($user->hasRole('Admin')) { /* ... */
    }
    if ($user->hasRole('Dosen')) {
        return redirect()->route('lecturer.dashboard');
    }
    // ...
})->name('home');


Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        $user = auth()->user();
        if ($user->hasRole('Admin')) {
            // return view('admin.dashboard'); // Nanti kita buat view ini
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('Dosen')) {
            // return view('dosen.dashboard'); // Nanti kita buat view ini
            return redirect()->route('lecturer.dashboard');
        }
        if ($user->hasRole('Residen')) {
            return redirect()->route('resident.dashboard');
        }
        return 'Dashboard Umum';
    })->name('home');

    // Route khusus untuk dashboard residen
    Route::get('/resident/dashboard', ResidentDashboard::class)
        ->middleware('role:Residen')
        ->name('resident.dashboard');

    Route::get('/submissions/create', [SubmissionController::class, 'create'])
        ->middleware('role:Residen')
        ->name('submissions.create');

    Route::get('/submissions/history', [SubmissionController::class, 'history'])
        ->middleware('role:Residen')
        ->name('submissions.history');

    Route::get('/resident/summary', [ResidentController::class, 'summary'])
        ->middleware('role:Residen')
        ->name('resident.summary');

    Route::get('/resident/browse', [ResidentController::class, 'browse'])
        ->middleware('role:Residen')
        ->name('resident.browse');

    Route::get('/submissions/{submission}/edit', [SubmissionController::class, 'edit'])
        ->middleware('role:Residen')
        ->name('submissions.edit');
});

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/submissions/verify', [AdminSubmissionController::class, 'index'])->name('submissions.verify.index');

    Route::put('/submissions/{submission}/verify', [App\Http\Controllers\Admin\SubmissionController::class, 'verify'])->name('submissions.verify');
    Route::put('/submissions/{submission}/reject', [App\Http\Controllers\Admin\SubmissionController::class, 'reject'])->name('submissions.reject');

    Route::resource('lecturers', AdminLecturerController::class);
    Route::get('/lecturers/{lecturer}/advisees', [AdminLecturerController::class, 'advisees'])->name('lecturers.advisees');

    Route::resource('requirement-rules', AdminRequirementRuleController::class);

    Route::resource('task-categories', AdminTaskCategoryController::class);

    Route::resource('divisions', AdminDivisionController::class);
    Route::get('divisions/{division}/staff', [AdminDivisionController::class, 'staff'])->name('divisions.staff');
    Route::put('divisions/{division}/staff', [AdminDivisionController::class, 'updateStaff'])->name('divisions.staff.update');

    Route::get('promotions', [AdminPromotionController::class, 'index'])->name('promotions.index');
    Route::post('promotions/{resident}/promote', [AdminPromotionController::class, 'promote'])->name('promotions.promote');
});

// Buat grup baru untuk route dosen
Route::middleware(['auth', 'role:Dosen|Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/submissions/all', [AdminSubmissionController::class, 'all'])->name('submissions.all');
    Route::resource('residents', AdminResidentController::class);
    Route::get('/residents/{resident}/submissions', [AdminResidentController::class, 'submissions'])->name('residents.submissions');
});

Route::middleware(['auth', 'role:Dosen'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('dashboard');
    Route::get('/advisees', [LecturerController::class, 'advisees'])->name('advisees');
    Route::get('/submissions', [LecturerController::class, 'submissions'])->name('submissions');
    Route::get('/divisions', [LecturerController::class, 'divisions'])->name('divisions');
    Route::get('/submissions/all', [AdminSubmissionController::class, 'all'])->name('submissions.all');
});
