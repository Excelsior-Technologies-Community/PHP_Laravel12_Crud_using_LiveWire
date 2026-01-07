<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('posts'); // loads resources/views/posts.blade.php
});

