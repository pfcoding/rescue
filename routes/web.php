<?php

Route::get('/', 'FileUploadsController@create');
Route::get('files', 'FileUploadsController@index');
Route::get('files/create', 'FileUploadsController@create');
Route::post('files/upload-file', 'FileUploadsController@uploadFile');

