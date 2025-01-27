<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimeSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return redirect('/login'); 
});

Route::get('/home', function () {
    return Inertia::render('Home', [
        'title' => 'Home',
    ]);
})->middleware(['auth', 'verified'])->name('home');

Route::get('/tasks', function () {
    return Inertia::render('Tasks', [
        'title' => 'Tasks',
    ]);
})->middleware(['auth', 'verified'])->name('tasks');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('time-sessions', TimeSessionController::class);
});

Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    return redirect('/login');
})->name('logout');

require __DIR__.'/auth.php';
