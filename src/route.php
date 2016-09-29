<?php

use Alicecore\Route;

Route::get('/', 'IndexController::index');
Route::get('/menu/{name}', ['_controller' => 'MenuController::index', '_before_middlewares' => 'menu::boot']);
Route::get('/articleshow/{id}', 'ArticleshowController::index');

Route::get('/tag/showall', 'TagController::showAll');
Route::get('/tag/{id}', 'TagController::show');

Route::get('/login', 'AdminController::index');
Route::post('/login', 'AdminController::login');

Route::get('/manage', ['_controller' => 'ManageController::index', '_before_middlewares' => 'auth::boot']);
Route::get('/manage/articles', ['_controller' => 'ArticleController::index', '_before_middlewares' => 'auth::boot']);

Route::post('/comment/add', 'CommentController::add');
Route::post('/search', 'SearchController::index');

#测试用 名称：GET_haha_name
#Route::get('/haha/{name}', ['_controller'=>'DefaultController::news']);
#Route::get('/test', 'DefaultController::index');
#Route::get('/mem', 'DefaultController::memcache');

