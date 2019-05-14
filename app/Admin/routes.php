<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    //用户列表
    $router->get('users', 'UsersController@index')->name('admin.users');
    //商品列表
    $router->get('products', 'ProductsController@index')->name('admin.products');
    //添加商品
    $router->get('products/create', 'ProductsController@create')->name('admin.products.create');
    $router->post('products', 'ProductsController@store')->name('admin.products.store');
    //编辑商品
    $router->get('products/{product}/edit', 'ProductsController@edit')->name('admin.products.edit');
    $router->put('products/{product}', 'ProductsController@update')->name('admin.products.update');
    //商品详情
    $router->get('products/{product}', 'ProductsController@show')->name('admin.products.show');
});
