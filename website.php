<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

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

// Route::view('/', 'website.index')->name('website.index');
// Route::view('/services_consultation', 'website.services_consultation')->name('website.services_consultation');
// Route::view('/services_compilance', 'website.services_compilance')->name('website.services_compilance');
// Route::view('/services_solutions', 'website.services_solutions')->name('website.services_solutions');
// Route::view('/cybermode', 'website.cybermode')->name('website.cybermode');
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
