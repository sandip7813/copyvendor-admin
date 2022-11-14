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
    return Redirect::to('login');
});

Route::namespace('Admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

    //+++++++++++++++++++++++++ CATEGORIES :: Start +++++++++++++++++++++++++//
    Route::resource('category', 'CategoryController')->except(['index', 'store', 'show', 'update', 'destroy']);
    Route::get('categories', 'CategoryController@index')->name('category.index');
    Route::post('category/add-submit', 'CategoryController@addCategorySubmit')->name('category.add-submit');
    Route::post('category/{uuid}/update-submit', 'CategoryController@updateCategorySubmit')->name('category.update-submit');
    Route::post('category/change-status', 'CategoryController@changeCategoryStatus')->name('category.change-status');
    Route::post('category/delete-item', 'CategoryController@deleteCategory')->name('category.delete-item');
    Route::post('category/regenerate-slug', 'CategoryController@regenerateSlug')->name('category.regenerate-slug');
    //+++++++++++++++++++++++++ CATEGORIES :: End +++++++++++++++++++++++++//

    //+++++++++++++++++++++++++ BLOGS :: Start +++++++++++++++++++++++++//
    Route::get('blogs', 'BlogController@index')->name('blog.index');
    Route::get('blog/create', 'BlogController@create')->name('blog.create');
    Route::post('blog/submit', 'BlogController@blogSubmit')->name('blog.submit');
    Route::post('blog/change-status', 'BlogController@changeStatus')->name('blog.change-status');
    Route::post('blog/delete-item', 'BlogController@deleteBlog')->name('blog.delete-item');
    Route::post('blog/change-banner', 'BlogController@changeBanner')->name('blog.change-banner');
    Route::get('blog/{uuid}/edit', 'BlogController@edit')->name('blog.edit');
    Route::post('blog/regenerate-slug', 'BlogController@regenerateSlug')->name('blog.regenerate-slug');
    Route::post('blog/update-submit', 'BlogController@updateBlogSubmit')->name('blog.update-submit');
    //+++++++++++++++++++++++++ BLOGS :: End +++++++++++++++++++++++++//

    //+++++++++++++++++++++++++ SERVICES :: Start +++++++++++++++++++++++++//
    Route::get('services', 'ServiceController@index')->name('service.index');
    Route::get('service/create', 'ServiceController@create')->name('service.create');
    Route::post('service/submit', 'ServiceController@serviceSubmit')->name('service.submit');
    Route::post('service/change-status', 'ServiceController@changeStatus')->name('service.change-status');
    Route::post('service/delete-item', 'ServiceController@deleteService')->name('service.delete-item');
    Route::get('service/{uuid}/edit', 'ServiceController@edit')->name('service.edit');
    Route::post('service/regenerate-slug', 'ServiceController@regenerateSlug')->name('service.regenerate-slug');
    Route::post('service/{uuid}/update-submit', 'ServiceController@updateServiceSubmit')->name('service.update-submit');
    //+++++++++++++++++++++++++ SERVICES :: End +++++++++++++++++++++++++//
});

Route::get('/phpinfo', function() {
    return phpinfo();
});

Route::group(['middleware'=>['auth','role:editor']],function(){
    Route::get('role',function(){
        dd('hi');
    });
});

require __DIR__.'/auth.php';