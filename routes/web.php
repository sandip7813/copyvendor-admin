<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    //return view('welcome');
    return Redirect::to('login');
});

Route::namespace('Admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

    Route::resource('category', 'CategoryController')->except(['store', 'show', 'update', 'destroy']);
    Route::post('add-category-submit', 'CategoryController@addCategorySubmit')->name('add-category-submit');
    Route::post('update-category-submit', 'CategoryController@updateCategorySubmit')->name('update-category-submit');
    Route::post('change-category-status', 'CategoryController@changeCategoryStatus')->name('change-category-status');
    Route::post('delete-category', 'CategoryController@deleteCategory')->name('delete-category');
    Route::post('regenerate-slug', 'CategoryController@regenerateSlug')->name('regenerate-slug');
});
Route::group(['middleware'=>['auth','role:editor']],function(){
    Route::get('role',function(){
        dd('hi');
    });
});






require __DIR__.'/auth.php';