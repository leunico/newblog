<?php

use Alicecore\Route;

Route::get('/', 'IndexController::index');
Route::get('/menu/{name}', ['_controller' => 'MenuController::index', '_before_middlewares' => 'menu::boot']);
Route::get('/articleshow/{id}', 'ArticleshowController::index');

Route::get('/tag/showall', 'TagController::showAll');
Route::get('/tag/{id}', 'TagController::show');

Route::get('/login', 'AdminController::index');
Route::post('/login', 'AdminController::login');
Route::get('/manage/loginout', ['_controller' => 'AdminController::loginout']);

Route::get('/manage', ['_controller' => 'ManageController::index', '_before_middlewares' => 'auth::boot']);
Route::group('/manage/baidupush', ['_controller' => 'ManageController::push', '_before_middlewares' => 'auth::security']);
Route::get('/manage/memupdata', ['_controller' => 'ManageController::update', '_before_middlewares' => 'auth::security']);

Route::get('/manage/articles', ['_controller' => 'ArticleController::index', '_before_middlewares' => 'auth::boot']);
Route::get('/manage/article_add', ['_controller' => 'ArticleController::add', '_before_middlewares' => 'auth::boot']);
Route::get('/manage/article_my/{id}', ['_controller' => 'ArticleController::show', '_before_middlewares' => 'auth::boot']);
Route::post('/manage/article_save', ['_controller' => 'ArticleController::save', '_before_middlewares' => 'auth::boot']);
Route::group('/manage/article_edit/{id}', ['_controller' => 'ArticleController::edit', '_before_middlewares' => 'auth::security']);
Route::get('/manage/article_delete/{id}', ['_controller' => 'ArticleController::delete', '_before_middlewares' => 'auth::security']);

Route::get('/manage/users', ['_controller' => 'UserController::index', '_before_middlewares' => 'auth::security']);
Route::get('/manage/user_block/{id}', ['_controller' => 'UserController::block', '_before_middlewares' => 'auth::security']);
Route::get('/manage/user_delete/{id}', ['_controller' => 'UserController::delete', '_before_middlewares' => 'auth::security']);
Route::group('/manage/user_add', ['_controller' => 'UserController::add', '_before_middlewares' => 'auth::security']);
Route::group('/manage/user_edit', ['_controller' => 'UserController::edit', '_before_middlewares' => 'auth::security']);

Route::get('/manage/comments', ['_controller' => 'CommentController::index', '_before_middlewares' => 'auth::boot']);
Route::group('/manage/comment_edit/{id}', ['_controller' => 'CommentController::edit', '_before_middlewares' => 'auth::security']);
Route::get('/manage/comment_delete/{id}', ['_controller' => 'CommentController::delete', '_before_middlewares' => 'auth::security']);

Route::get('/manage/tags', ['_controller' => 'TagController::index', '_before_middlewares' => 'auth::boot']);
Route::group('/manage/tag_edit/{id}', ['_controller' => 'TagController::edit', '_before_middlewares' => 'auth::security']);
Route::get('/manage/tag_delete/{id}', ['_controller' => 'TagController::delete', '_before_middlewares' => 'auth::security']);

Route::group('/manage/pushs', ['_controller' => 'PushController::index', '_before_middlewares' => 'auth::security']);

Route::get('/manage/timewaits', ['_controller' => 'DiaryController::index', '_before_middlewares' => 'auth::boot']);
Route::get('/manage/timewait_delete/{id}', ['_controller' => 'DiaryController::delete', '_before_middlewares' => 'auth::security']);
Route::group('/manage/timewait_edit/{id}', ['_controller' => 'DiaryController::edit', '_before_middlewares' => 'auth::security']);
Route::group('/manage/timewait_add', ['_controller' => 'DiaryController::add', '_before_middlewares' => 'auth::security']);

Route::post('/comment/add', 'CommentController::add');
Route::post('/search', 'SearchController::index');

#测试用 名称：GET_haha_name
#Route::get('/haha/{name}', ['_controller'=>'DefaultController::news']);
#Route::get('/test', 'DefaultController::index');
#Route::get('/mem', 'DefaultController::memcache');

