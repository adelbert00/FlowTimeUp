<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeSessionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\TaskTemplateController;

// Landing page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

// Dashboard (authenticated)
Route::get('/home', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home');

// All authenticated and verified routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Projects
    Route::resource('projects', ProjectController::class);
    
    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->name('tasks.toggleComplete');
    Route::post('/tasks/{task}/tags', [TaskController::class, 'attachTags'])->name('tasks.attachTags');
    Route::delete('/tasks/{task}/tags/{tag}', [TaskController::class, 'detachTag'])->name('tasks.detachTag');
    
    // Time Sessions
    Route::resource('time-sessions', TimeSessionController::class);
    
    // Tags
    Route::resource('tags', TagController::class);
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/data', [ReportController::class, 'data'])->name('reports.data');
    
    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    
    // Task Templates
    Route::resource('task-templates', TaskTemplateController::class);
    Route::post('/task-templates/{taskTemplate}/use', [TaskTemplateController::class, 'useTemplate'])->name('task-templates.use');
});

require __DIR__.'/auth.php';
