<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MatkulController;
use App\Http\Controllers\AuthController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
});


Route::resource('matkul', MatkulController::class);
Route::get('/matkul/{kode_matkul}/unduh', [MatkulController::class, 'unduhMatkul'])->name('matkul.cetak');

Route::resource('mahasiswa', MahasiswaController::class);


