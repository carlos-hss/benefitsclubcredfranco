<?php

use Illuminate\Support\Facades\Route;

Route::get('/{id}', function (string $id) {
    return 'User '.$id;
});
