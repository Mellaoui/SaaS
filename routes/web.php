<?php

use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\TasksController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/tokens/create', function (Request $request) {
        $token = $request->user()->createToken(Hash::make($request->user()->name));
        return ['token' => $token->plainTextToken];
    });

    Route::resource('/companies', CompaniesController::class);
    Route::get('/companies/{company}/invite/{user}', [CompaniesController::class, 'inviteUser'])->name('companies.invite');
    Route::get('/companies/{company}/accept', [CompaniesController::class, 'acceptInvite'])->name('companies.accept');
    Route::get('/companies/{company}/add/{user}', [CompaniesController::class, 'addEmployee'])->name('companies.add');
    Route::resource('/companies/{company}/branches', BranchesController::class);
    Route::resource('/branches/{branch}/tasks', TasksController::class);
    Route::get('/tasks/{task}/assign/{user}', [TasksController::class, 'assignToUser'])->name('tasks.assign');
    Route::resource('/tasks/{task}/schedules', SchedulesController::class);
});

require __DIR__ . '/auth.php';
