<?php

use App\Http\Controllers\NpsAnswerController;
use App\Http\Controllers\NpsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckUserCookie;
use App\Models\Entity;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


$entity_verify = Entity::where('id', 1)->first();
if (!$entity_verify) {
    Entity::create([
        'name' => "EtecSystems",
        'cnpj' => '52.545.261/0001-90',
        'token' => Str::uuid()->toString()
    ]);

}

Route::get('/', function () {
    return redirect()->route('nps.index');
});

Route::get('/entity/{entity_token}', function ($entity_token) {
    session()->put('entity_token', $entity_token);
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


Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => ['auth']], function () {

    // Listar todos os usuários
    Route::get('/', [UserController::class, 'index'])->name('index');

    // Exibir formulário para criar um novo usuário
    Route::get('create', [UserController::class, 'create'])->name('create');

    // Salvar um novo usuário
    Route::post('store', [UserController::class, 'store'])->name('store');

    // Exibir os detalhes de um usuário específico
    Route::get('{user}', [UserController::class, 'show'])->name('show');

    // Exibir formulário para editar um usuário
    Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');

    // Atualizar um usuário existente
    Route::put('{user}', [UserController::class, 'update'])->name('update');

    // Deletar um usuário
    Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/all', [NpsController::class, 'all'])->name('nps.all');
});

require __DIR__.'/auth.php';
