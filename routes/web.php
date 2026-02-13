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

Route::middleware('auth')->group(function() {
    Route::get('/tasks', [TaskController::class, 'index'])->middleware('role:admin')->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->middleware('role:admin')->name('task.store');
    Route::put('/tasks/{task}/admin', [TaskController::class, 'update'])->middleware('role:admin')->name('task.update');

    Route::get('/tasks/{user}', [TaskController::class, 'userTask'])->name('tasks.userTask');
    Route::post('/tasks/{user}', [TaskController::class, 'userStore'])->name('tasks.userStore');
    Route::put('/tasks/{task}', [TaskController::class, 'userUpdate'])->name('tasks.userUpdate');
});


require __DIR__.'/settings.php';
