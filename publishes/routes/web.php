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

// auth 中间件代表需要登录，verified中间件代表需要经过邮箱验证
Route::group([
    // 'middleware' => ['web', 'wechat.oauth'],
    'namespace' => 'Admin',
], function () {
    // 首页
    // Route::get('/', 'IndexController@wechatCheck')->name('wechatCheck');
    Route::get('/', 'IndexController@home')->name('home');
    Route::get('home', 'IndexController@home')->name('home');
    Route::get('test', 'IndexController@test')->name('index.test');

    Route::get('console', 'IndexController@console')->name('console');

    // 用户
    // 用户列表
    Route::get('users/list', 'UsersController@list')->name('users.list');
    // 用户状态
    Route::put('users/{user}/status/{status}', 'UsersController@status')->name('users.status');
    // 用户备注
    Route::put('users/{user}/remark', 'UsersController@remark')->name('users.remark');
    // 用户基础路由
    Route::resource('users', 'UsersController', ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']);

});

Route::group([
    'middleware' => ['web', 'wechat.oauth'],
    'namespace' => 'Admin',
], function () {
    Route::get('/wechat', 'IndexController@wechatHome')->name('wechat.home');

});
// 用户身份验证相关的路由
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
// 登出补充
Route::get('logout', 'Auth\LoginController@logout');

// 密码重置相关路由
Route::get('password/edit', 'Admin\IndexController@editPassword')->name('password.edit');
Route::post('password/update', 'Admin\IndexController@updatePassword')->name('passwords.update');
