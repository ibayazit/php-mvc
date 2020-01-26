<?php
    Router::group(['prefix' => 'group1/group1', 'middleware' => 'Api'], function(){
        Router::group(['prefix' => 'group2', 'middleware' => ['Web']], function(){
            Router::group(['prefix' => 'group3'], function(){
                Router::get('/index', function () {
                    echo 'GroupRouter::works...';
                });
            });
        });
    });
    // Router::group(['prefix' => '/group-test', 'middleware' => ['Api']], null);

    Router::get('/test', function () {
        echo 'Router::test';
    });

    Router::get('/', function () {
        echo 'Hello World!. Route with callback function <br>';
    });
    Router::get('/param-test/{id}/{name}/{surname}', function ($id, $name) {
        printJSON(request());
    });
    Router::get('/param/{param1}/{param2}/{id}', function () {
        // echo $x;
        printJSON(request());
    });
    Router::get('/home', 'HomeController@index');
    Router::post('/post-test', 'HomeController@post_test');
    Router::get('/users', 'HomeController@user');

    Router::get('/namespace-test', 'Admin\NameController@index');
    Router::get('/namespace-test2', 'NameController@index');
    Router::group(['namespace' => 'Admin\User'], function(){
        Router::get('/namespace-test3', 'NameController@index');
    });
?>