<?php
/**
 * Created by PhpStorm.
 * User: Curator
 * Date: 2019/7/10
 * Time: 15:40
 */

use Illuminate\Support\Facades\Route;

Route::get('coder', '\CuratorC\Coder\Index@index')->name('curatorc.coder.index');

Route::post('coder', '\CuratorC\Coder\Index@store')->name('curatorc.coder.store');