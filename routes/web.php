<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/contato', function () {
    return view('welcome');
});

Route::get('/produtos/{id}', function () {
    return view('welcome');
})->whereNumber('id');
