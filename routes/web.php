<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('employee.index');
});

Route::post('/employee/upload', [EmployeeController::class, 'uploadPhoto'])
    ->name('employee.upload');
Route::get('employee/list', [EmployeeController::class, 'list'])->name('employee.list');
Route::resource('employee', EmployeeController::class);
