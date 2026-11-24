<?php

// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', function () {
    return view('pages.home');
});
