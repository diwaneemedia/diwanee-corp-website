<?php

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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::resource('articles', 'ArticleController', ['only' => ['index', 'show']]);

Route::post('sirtrevor/upload-image', 'ImagesController@uploadSirTrevorImage')->name('sirtrevor.upload.image');

//only admin can access
Route::group(['prefix' => 'admin', 'middleware' => 'admin', 'namespace' => 'Admin'], function() {
    Route::resource('users', 'UsersController');

});

Route::group(['prefix' => 'admin', 'middleware' => 'editor', 'namespace' => 'Admin'], function(){

    Route::get('/', 'DashboardController@index')->name('admin.home');

    Route::resource('tags', 'TagsController');
    Route::resource('articles', 'ArticlesController');

    Route::get('dashboard/log-chart', 'DashboardController@getLogChartData')->name('dashboard.log.chart');
    Route::get('dashboard/registration-chart', 'DashboardController@getRegistrationChartData')->name('dashboard.registration.chart');

});
    
Route::group(['prefix' => 'ajax'], function() {
    Route::get('/subcategories/{category_id?}', 'AjaxController@subcategories')->name('subcategories');
    Route::get('/tags/{type}', 'AjaxController@tagsByType')->name('tags.by.type');
});