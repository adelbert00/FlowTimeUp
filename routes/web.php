<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeSessionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/home', function () {
    return Inertia::render('Home', ['title' => 'Home']);
})->middleware(['auth', 'verified'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('time-sessions', TimeSessionController::class);
    Route::resource('tags', TagController::class);

    Route::post('/tasks/{task}/tags', [TaskController::class, 'attachTags'])->name('tasks.attachTags');
    Route::delete('/tasks/{task}/tags/{tag}', [TaskController::class, 'detachTag'])->name('tasks.detachTag');

});

Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    return redirect('/login');
})->name('logout');

require __DIR__.'/auth.php';
