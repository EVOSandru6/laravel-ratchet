<?php

use Illuminate\Http\Request;
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
    return view('welcome');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/order', function () {
    return view('order');
});

Route::get('/orders', function () {
    return view('orders');
});

Route::post('/order', function (Request $request) {
    // ifconfig | grep 192
    $client = new WebSocket\Client("ws://192.168.2.42:8080");

    $client->text(json_encode(['message' => 'new room', 'value' => 'one']));

    $data = [
        'message' => 'new order',
        'value' => [
            'name' => $request['name'],
            'product' => $request['product'],
        ]
    ];
    $client->text(json_encode($data));
    // $client->receive(); т.к. ничего не приходит
    $client->close();
    return response()->redirectTo('/order');
})->name('order.store');
