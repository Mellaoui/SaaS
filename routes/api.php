<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\TasksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/companies', CompaniesController::class);
    Route::apiResource('/branches/{branch}/tasks', TasksController::class);
    Route::apiResource('/companies/{company}/branches', BranchesController::class);
    Route::get('/tasks/{task}/assign/{user}', [TasksController::class, 'assignToUser'])->name('tasks.assign');
    Route::get('/companies/{company}/invite/{user}', [CompaniesController::class, 'inviteUser'])->name('companies.invite');
    Route::get('/companies/{company}/add/{user}', [CompaniesController::class, 'addEmployee'])->name('companies.add');
    Route::get('/companies/{company}/accept', [CompaniesController::class, 'acceptInvite'])->name('companies.accept');
});
