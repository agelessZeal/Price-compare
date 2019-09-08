<?php

Route::post('login', 'Auth\AuthController@login');
Route::post('login/social', 'Auth\SocialLoginController@index');
Route::post('logout', 'Auth\AuthController@logout');

if (settings('reg_enabled')) {
    Route::post('register', 'Auth\RegistrationController@index');
    Route::post('register/verify-email/{token}', 'Auth\RegistrationController@verifyEmail');
}

if (settings('forgot_password')) {
    Route::post('password/remind', 'Auth\Password\RemindController@index');
    Route::post('password/reset', 'Auth\Password\ResetController@index');
}

Route::get('stats', 'StatsController@index');

Route::get('me', 'Profile\DetailsController@index');
Route::patch('me/details', 'Profile\DetailsController@update');
Route::put('me/avatar', 'Profile\AvatarController@update');
Route::delete('me/avatar', 'Profile\AvatarController@destroy');
Route::put('me/avatar/external', 'Profile\AvatarController@updateExternal');
Route::get('me/sessions', 'Profile\SessionsController@index');

if (settings('2fa.enabled')) {
    Route::put('me/2fa', 'Profile\TwoFactorController@update');
    Route::delete('me/2fa', 'Profile\TwoFactorController@destroy');
}

Route::resource('users', 'Users\UsersController', [
    'except' => 'create'
]);

Route::put('users/{user}/avatar', 'Users\AvatarController@update');
Route::put('users/{user}/avatar/external', 'Users\AvatarController@updateExternal');
Route::delete('users/{user}/avatar', 'Users\AvatarController@destroy');

if (settings('2fa.enabled')) {
    Route::put('users/{user}/2fa', 'Users\TwoFactorController@update');
    Route::delete('users/{user}/2fa', 'Users\TwoFactorController@destroy');
}

Route::get('users/{user}/activity', 'Users\ActivityController@index');
Route::get('users/{user}/sessions', 'Users\SessionsController@index');

Route::get('/sessions/{session}', 'SessionsController@show');
Route::delete('/sessions/{session}', 'SessionsController@destroy');

Route::get('/activity', 'ActivityController@index');

Route::resource('roles', 'Authorization\RolesController', [
    'except' => 'create'
]);
Route::get("roles/{role}/permissions", 'Authorization\RolePermissionsController@show');
Route::put("roles/{role}/permissions", 'Authorization\RolePermissionsController@update');

Route::resource('permissions', 'Authorization\PermissionsController', [
    'except' => 'create'
]);

Route::get('/settings', 'SettingsController@index');

Route::get('/countries', 'CountriesController@index');
