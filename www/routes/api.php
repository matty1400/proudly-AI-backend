<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dummyApi;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// GET REQUESTS

Route::get('user/', [DeviceController::class, 'getUser']);



Route::get('company/filter/industry', [DeviceController::class, 'getIdByIndustry']);

Route::get('company/filter/headcount', [DeviceController::class, 'getIdByHeadcount']);
Route::get('company/filter/headquarters', [DeviceController::class, 'getIdByHeadquarters']);
Route::get('people/filter/seniority', [DeviceController::class, 'getIdBySeniority']);
Route::get('people/filter/function', [DeviceController::class, 'getIdByFunction']);
Route::get('company/searches', [DeviceController::class, 'getCompanySearchByUserId']);
Route::get('people/searches', [DeviceController::class, 'getPeopleSearchByUserId']);
Route::get('company/leads', [DeviceController::class, 'getCompanyLeadsBySearchId']);
Route::get('people/leads', [DeviceController::class, 'getPeopleLeadsBySearchId']);
Route::get('company/filter/allindustries', [DeviceController::class, 'getIndustryNames']);
Route::get('company/filter/allheadcounts', [DeviceController::class, 'getHeadcount']);
Route::get('company/filter/allheadquarters', [DeviceController::class, 'getHeadquarters']);



//POST REQUESTS
Route::post('user', [DeviceController::class, 'postUser']);
Route::post('people/leads', [DeviceController::class, 'newPeopleLeads']);
Route::post('company/leads', [DeviceController::class, 'newCompanyLeads']);
Route::post('people/searches', [DeviceController::class, 'newPeopleSearch']);
Route::post('company/searches', [DeviceController::class, 'newCompanySearch']);
Route::post('phantom/updateAndLaunch', [DeviceController::class, 'updateAndLaunch']);
Route::post('phantom/fetcher', [DeviceController::class, 'fetcher']);







// Apply the 'api' middleware group to the routes
Route::middleware('api')->group(function () {
    // Define your API routes here

    // Define the GET route
    Route::get('/webhook', function () {
        $webhookStatus = session('webhookStatus', 'not done');
        return $webhookStatus;
    });

    // Define the POST route
    Route::post('/webhook', function (Request $request) {
        $payload = $request->json()->all();

        if ($payload['exitMessage'] === 'finished') {
            session(['webhookStatus' => 'done']);
        }

        return response()->json(['message' => 'Webhook received']);
    });

    // Define the reset route
    Route::get('/webhook/reset', function () {
        session(['webhookStatus' => 'not done']);
        return response()->json(['message' => 'Webhook status reset']);
    });
});
