<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupSimproSiteController;
use App\Http\Controllers\QuoteDeclineReasonController;
use App\Http\Controllers\QuoteRerequestReasonController;
use App\Http\Controllers\SimproCustomerController;
use App\Http\Controllers\SimproSiteController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth'], function () {
    Route::post('/users', ['uses' => UserController::class . '@create']);
    Route::put('/users/{id}', ['uses' => UserController::class . '@update']);
    Route::delete('/users/{id}', ['uses' => UserController::class . '@delete']);
    Route::get('/users/{id}', ['uses' => UserController::class . '@get']);
    Route::get('/users', ['uses' => UserController::class . '@search']);
    Route::get('/profile', ['uses' => UserController::class . '@profile']);
    Route::put('/profile', ['uses' => UserController::class . '@updateProfile']);

    Route::post('/media', ['uses' => MediaController::class . '@create']);
    Route::delete('/media/{id}', ['uses' => MediaController::class . '@delete']);
    Route::get('/media', ['uses' => MediaController::class . '@search']);

    Route::get('/settings/project-tags', ['uses' => SettingController::class . '@getProjectTags']);
    Route::get('/settings/project-custom-fields', ['uses' => SettingController::class . '@getProjectCustomFields']);
    Route::put('/settings/{name}', ['uses' => SettingController::class . '@update']);
    Route::get('/settings/{name}', ['uses' => SettingController::class . '@get']);
    Route::get('/settings', ['uses' => SettingController::class . '@search']);

    Route::post('/quote-decline-reasons', ['uses' => QuoteDeclineReasonController::class . '@create']);
    Route::delete('/quote-decline-reasons/{id}', ['uses' => QuoteDeclineReasonController::class . '@delete']);
    Route::get('/quote-decline-reasons', ['uses' => QuoteDeclineReasonController::class . '@search']);

    Route::post('/quote-rerequest-reasons', ['uses' => QuoteRerequestReasonController::class . '@create']);
    Route::delete('/quote-rerequest-reasons/{id}', ['uses' => QuoteRerequestReasonController::class . '@delete']);
    Route::get('/quote-rerequest-reasons', ['uses' => QuoteRerequestReasonController::class . '@search']);

    Route::post('/groups', ['uses' => GroupController::class . '@create']);
    Route::put('/groups/{id}', ['uses' => GroupController::class . '@update']);
    Route::delete('/groups/{id}', ['uses' => GroupController::class . '@delete']);
    Route::get('/groups/{id}', ['uses' => GroupController::class . '@get']);
    Route::get('/groups', ['uses' => GroupController::class . '@search']);

    Route::get('/simpro-customers', ['uses' => SimproCustomerController::class . '@search']);

    Route::get('/simpro-sites', ['uses' => SimproSiteController::class . '@search']);

    Route::put('/group-simpro-sites/{id}', ['uses' => GroupSimproSiteController::class . '@update']);
});

Route::group(['middleware' => 'guest'], function () {
    Route::post('/login', ['uses' => AuthController::class . '@login']);
    Route::get('/auth/refresh', ['uses' => AuthController::class . '@refreshToken'])
        ->middleware(['jwt.refresh']);
    Route::post('/auth/forgot-password', ['uses' => AuthController::class . '@forgotPassword']);
    Route::post('/auth/restore-password', ['uses' => AuthController::class . '@restorePassword']);
    Route::post('/auth/token/check', ['uses' => AuthController::class . '@checkRestoreToken']);

    Route::get('/status', ['uses' => StatusController::class . '@status']);
});