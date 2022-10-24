<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\RepaymentController;
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

Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::post('/loan', [LoanController::class, 'createLoanRequest'])->middleware('auth:sanctum');

Route::get('/get-loan-list/{userId}', [LoanController::class, 'index'])->middleware('auth:sanctum');
Route::get('/approve-reject-loan-by-admin/{loanId}/{status}/{adminId}', [LoanController::class, 'approveRejectLoanByAdmin'])->middleware('auth:sanctum');

Route::post('/add-repayment', [RepaymentController::class, 'addRepayment'])->middleware('auth:sanctum');