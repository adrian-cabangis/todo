<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/about', function (){
    return Inertia::render('About');
});

Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::post('/tasks', [TaskController::class, 'store'])->name('task.store');
Route::get('/tasks/{user}', [TaskController::class, 'userTask'])->name('tasks.userTask');
Route::post('/tasks/{user}', [TaskController::class, 'userStore'])->name('tasks.userStore');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

require __DIR__.'/settings.php';
