<?php

use App\Http\Controllers\NpsAnswerController;
use App\Http\Controllers\NpsController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckUserCookie;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('nps.index');
});

Route::resources([
    'nps' => NpsController::class,
    'nps_answers' => NpsAnswerController::class
]);

Route::get('/why', [NpsController::class, 'why'])->name('why');
Route::get('/justify', [NpsController::class, 'justify'])->name('justify');
Route::get('/finish', [NpsController::class, 'finish'])->name('finish');
Route::get('/login', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('app');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
