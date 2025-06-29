<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Auth::routes(['verify' => true]);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Job Routes
Route::resource('jobs', JobController::class);
Route::get('/my-jobs', [JobController::class, 'myJobs'])
    ->middleware('auth')
    ->name('jobs.my-jobs');

// Service Routes
Route::resource('services', ServiceController::class);
Route::get('/my-services', [ServiceController::class, 'myServices'])
    ->middleware('auth')
    ->name('services.my-services');

// Proposal Routes
Route::middleware('auth')->group(function () {
    Route::post('/jobs/{job}/proposals', [ProposalController::class, 'store'])->name('proposals.store');
    Route::get('/proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');
    Route::post('/proposals/{proposal}/accept', [ProposalController::class, 'accept'])->name('proposals.accept');
    Route::post('/proposals/{proposal}/reject', [ProposalController::class, 'reject'])->name('proposals.reject');
    Route::post('/proposals/{proposal}/withdraw', [ProposalController::class, 'withdraw'])->name('proposals.withdraw');
    Route::get('/my-proposals', [ProposalController::class, 'myProposals'])->name('proposals.my-proposals');
});

// Review Routes
Route::middleware('auth')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
});

// Payment Routes
Route::middleware('auth')->group(function () {
    Route::post('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::get('/payments/{payment}/success', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('users', AdminUserController::class);
    Route::resource('jobs', AdminJobController::class);
    Route::resource('services', AdminServiceController::class);
    Route::resource('categories', AdminCategoryController::class);
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
