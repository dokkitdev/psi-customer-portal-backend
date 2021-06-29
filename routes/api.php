<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupSimproSiteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobAttachmentController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\QuoteDeclineReasonController;
use App\Http\Controllers\QuoteRerequestReasonController;
use App\Http\Controllers\SimproCustomerController;
use App\Http\Controllers\SimproSiteController;
use App\Http\Controllers\SimproWebhookController;
use App\Http\Controllers\SiteContactController;
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
    Route::post('/users/{id}/resend-invitation', ['uses' => UserController::class . '@resendInvitation']);
    Route::post('/users', ['uses' => UserController::class . '@create']);
    Route::put('/users/{id}', ['uses' => UserController::class . '@update']);
    Route::delete('/users/{id}', ['uses' => UserController::class . '@delete']);
    Route::get('/users/{id}', ['uses' => UserController::class . '@get']);
    Route::get('/users', ['uses' => UserController::class . '@search']);
    Route::get('/profile', ['uses' => UserController::class . '@profile']);
    Route::put('/profile', ['uses' => UserController::class . '@updateProfile']);
    Route::get('/dashboard', ['uses' => UserController::class . '@dashboard']);

    Route::post('/media', ['uses' => MediaController::class . '@create']);
    Route::delete('/media/{id}', ['uses' => MediaController::class . '@delete']);
    Route::get('/media/{id}/download', ['uses' => MediaController::class . '@download']);
    Route::get('/media/{id}/view', ['uses' => MediaController::class . '@view']);
    Route::get('/media', ['uses' => MediaController::class . '@search']);

    Route::get('/settings/project-tags', ['uses' => SettingController::class . '@getProjectTags']);
    Route::get('/settings/project-custom-fields', ['uses' => SettingController::class . '@getProjectCustomFields']);
    Route::put('/settings/defaults', ['uses' => SettingController::class . '@updateDefaults']);
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
    Route::put('/groups/{id}/change-sites-visibility', ['uses' => GroupController::class . '@changeSitesVisibility']);
    Route::delete('/groups/{id}', ['uses' => GroupController::class . '@delete']);
    Route::get('/groups/{id}', ['uses' => GroupController::class . '@get']);
    Route::get('/groups', ['uses' => GroupController::class . '@search']);

    Route::get('/simpro-customers/{id}', ['uses' => SimproCustomerController::class . '@get']);
    Route::get('/simpro-customers', ['uses' => SimproCustomerController::class . '@search']);

    Route::put('/simpro-sites/{id}', ['uses' => SimproSiteController::class . '@update']);
    Route::get('/simpro-sites/{id}', ['uses' => SimproSiteController::class . '@get']);
    Route::get('/simpro-sites', ['uses' => SimproSiteController::class . '@search']);

    Route::put('/group-simpro-sites/{id}', ['uses' => GroupSimproSiteController::class . '@update']);

    Route::post('/site-contacts', ['uses' => SiteContactController::class . '@create']);
    Route::put('/site-contacts/{id}', ['uses' => SiteContactController::class . '@update']);
    Route::delete('/site-contacts/{id}', ['uses' => SiteContactController::class . '@delete']);
    Route::get('/site-contacts/{id}', ['uses' => SiteContactController::class . '@get']);

    Route::post('/documents', ['uses' => DocumentController::class . '@create']);
    Route::put('/documents/{id}', ['uses' => DocumentController::class . '@update']);
    Route::delete('/documents/{id}', ['uses' => DocumentController::class . '@delete']);
    Route::get('/documents/{id}', ['uses' => DocumentController::class . '@get']);
    Route::get('/documents', ['uses' => DocumentController::class . '@search']);

    Route::post('/jobs/create-in-simpro', ['uses' => JobController::class . '@createInSimpro']);
    Route::get('/jobs/response-times', ['uses' => JobController::class . '@getResponseTimes']);
    Route::get('/jobs/cost-centers', ['uses' => JobController::class . '@getCostCenters']);
    Route::get('/jobs/business-groups', ['uses' => JobController::class . '@getBusinessGroups']);
    Route::get('/jobs/{id}', ['uses' => JobController::class . '@get']);
    Route::get('/jobs', ['uses' => JobController::class . '@search']);

    Route::get('/job-attachments/download/{id}', ['uses' => JobAttachmentController::class . '@download']);

    Route::post('/quotes/create-in-simpro', ['uses' => QuoteController::class . '@createInSimpro']);
    Route::get('/quotes/{id}/download', ['uses' => QuoteController::class . '@download']);
    Route::put('/quotes/{id}/approve', ['uses' => QuoteController::class . '@approve']);
    Route::put('/quotes/{id}/decline', ['uses' => QuoteController::class . '@decline']);
    Route::put('/quotes/{id}/re-request', ['uses' => QuoteController::class . '@reRequest']);
    Route::get('/quotes', ['uses' => QuoteController::class . '@search']);

    Route::get('/invoices', ['uses' => InvoiceController::class . '@search']);

    Route::get('/assets/service-levels', ['uses' => AssetController::class . '@getServiceLevels']);
    Route::get('/assets/{id}', ['uses' => AssetController::class . '@get']);
    Route::get('/assets', ['uses' => AssetController::class . '@search']);

    Route::get('/asset-attachments/{id}/download', ['uses' => AssetController::class . '@download']);
});

Route::group(['middleware' => 'guest'], function () {
    Route::post('/login', ['uses' => AuthController::class . '@login']);
    Route::get('/auth/refresh', ['uses' => AuthController::class . '@refreshToken'])
        ->middleware(['jwt.refresh']);
    Route::post('/auth/forgot-password', ['uses' => AuthController::class . '@forgotPassword']);
    Route::post('/auth/restore-password', ['uses' => AuthController::class . '@restorePassword']);
    Route::post('/auth/token/check', ['uses' => AuthController::class . '@checkRestoreToken']);

    Route::get('/status', ['uses' => StatusController::class . '@status']);

    Route::post('/simpro-webhook', ['uses' => SimproWebhookController::class . '@process']);
});