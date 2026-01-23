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
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\PageController;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

Route::get('/robots.txt', function () {
    return response(file_get_contents(public_path('robots.txt')), 200)
        ->header('Content-Type', 'text/plain');
});

// Static pages
Route::get('/documentation', [PageController::class, 'documentation'])->name('pages.documentation');
Route::get('/api-reference', [PageController::class, 'apiReference'])->name('pages.api-reference');
Route::get('/help-center', [PageController::class, 'helpCenter'])->name('pages.help-center');
Route::get('/blog', [PageController::class, 'blog'])->name('pages.blog');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('pages.privacy-policy');
Route::get('/terms-of-service', [PageController::class, 'termsOfService'])->name('pages.terms-of-service');
Route::get('/cookie-policy', [PageController::class, 'cookiePolicy'])->name('pages.cookie-policy');
Route::get('/gdpr', [PageController::class, 'gdpr'])->name('pages.gdpr');
Route::get('/support', [PageController::class, 'support'])->name('pages.support');

Route::get('/home', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('projects', ProjectController::class);
    
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->name('tasks.toggleComplete');
    Route::post('/tasks/{task}/tags', [TaskController::class, 'attachTags'])->name('tasks.attachTags');
    Route::delete('/tasks/{task}/tags/{tag}', [TaskController::class, 'detachTag'])->name('tasks.detachTag');
    
    Route::resource('time-sessions', TimeSessionController::class);
    
    Route::resource('tags', TagController::class);
    Route::post('/tags/{tag}/archive', [TagController::class, 'archive'])->name('tags.archive');
    Route::post('/tags/{tag}/restore', [TagController::class, 'restore'])->name('tags.restore');
    
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/data', [ReportController::class, 'data'])->name('reports.data');
    
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    
    Route::resource('task-templates', TaskTemplateController::class);
    Route::post('/task-templates/{taskTemplate}/use', [TaskTemplateController::class, 'useTemplate'])->name('task-templates.use');
});

require __DIR__.'/auth.php';
