<?php

use App\Http\Controllers\User\Organization\BranchController;
use App\Http\Controllers\User\Organization\SubOrganizationController;
use App\Http\Controllers\WebsiteTenderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('get-suborganizations/{org_id}', [SubOrganizationController::class, 'getSubOrganizations']);
Route::get('get-branches/{sub_org_id}', [BranchController::class, 'getBranches']);
Route::post('upload-tender', [WebsiteTenderController::class, 'store']);
