<?php

// to deal with invalid SSL certs when Terranet retrieves gravatar image
stream_context_set_default( [
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
]);

$pattern = '[a-z0-9\_\=]+';
$idpattern = '[0-9]+';

Route::group([
    'prefix'    => 'admin',
    'namespace' => 'Terranet\Administrator',
    'middleware'=> ['Terranet\Administrator\Middleware\Web'],
], function () use ($pattern, $idpattern) {
    /*
    |-------------------------------------------------------
    | Authentication
    |-------------------------------------------------------
    */
    Route::get('login', [
        'as'   => 'scaffold.login',
        'uses' => 'AuthController@getLogin',
    ]);
    Route::post('login', 'AuthController@postLogin');
    Route::get('logout', [
        'as'   => 'scaffold.logout',
        'uses' => 'AuthController@getLogout',
    ]);

    /*
    |-------------------------------------------------------
    | Main Scaffolding routes
    |-------------------------------------------------------
    */
    Route::group([], function () use ($pattern, $idpattern) {
        /*
        |-------------------------------------------------------
        | Custom routes
        |-------------------------------------------------------
        |
        | Controllers that shouldn't be handled by Scaffolding controller
        | goes here.
        |
        */
        //        Route::controllers([
        //            'test' => 'App\Http\Controllers\Admin\TestController'
        //        ]);

        /*
        |-------------------------------------------------------
        | Scaffolding routes
        |-------------------------------------------------------
        */
        // Dashboard
        Route::get('/', function() {
            return \Redirect::to('/admin/artworks');
        });
        // Route::get('/', [
        //     'as'   => 'scaffold.dashboard',
        //     'uses' => 'DashboardController@index',
        // ]);

        // Index
        Route::get('{module}', [
            'as'   => 'scaffold.index',
            'uses' => 'Controller@index',
        ])->where('module', $pattern);

        // Create new Item
        Route::get('{module}/create', [
            'as'   => 'scaffold.create',
            'uses' => 'Controller@create',
        ])->where('module', $pattern);

        // Save new item
        Route::post('{module}/create', 'Controller@store')->where('module', $pattern);

        // View Item
        Route::get('{module}/{id}', [
            'as'   => 'scaffold.view',
            'uses' => 'Controller@view',
        ])->where('module', $pattern)->where('id', $idpattern);

        // Edit Item
        Route::get('{module}/{id?}/edit', [
            'as'   => 'scaffold.edit',
            'uses' => 'Controller@edit',
        ])->where('module', $pattern)->where('id', $idpattern);

        // Save Item
        Route::post('{module}/{id?}/edit', [
            'as'   => 'scaffold.update',
            'uses' => 'Controller@update',
        ])->where('module', $pattern)->where('id', $idpattern);

        // Delete Item
        Route::get('{module}/{id}/delete', [
            'as'   => 'scaffold.delete',
            'uses' => 'Controller@delete',
        ])->where('module', $pattern)->where('id', $idpattern);

        // Delete attachment
        Route::get('{module}/{id}/delete/attachment/{attachment}', [
            'as'   => 'scaffold.delete_attachment',
            'uses' => 'Controller@deleteAttachment',
        ])->where('module', $pattern)->where('id', $idpattern);

        // Custom method
        Route::get('{module}/{id}/{action}', [
            'as'   => 'scaffold.action',
            'uses' => 'Controller@action',
        ])->where('module', $pattern)->where('id', $idpattern);

        // Custom batch method
        Route::post('{module}/batch-action', [
            'as'   => 'scaffold.batch',
            'uses' => 'BatchController@batch',
        ])->where('module', $pattern);

        // Export collection url
        Route::get('{module}.{format}', [
            'as'   => 'scaffold.export',
            'uses' => 'BatchController@export',
        ])->where('module', $pattern);
    });
});
