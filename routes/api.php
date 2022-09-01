<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\PledgeController;
use App\Http\Controllers\API\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::middleware('apilogs')->post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('username/check', [UserController::class, 'check_username']);
Route::post('forgot/password', [UserController::class,'forgot_password']);
Route::post('update/password', [UserController::class,'update_password']);
Route::get('brings', [UserController::class, 'bring_list']);
Route::get('eligibles', [UserController::class, 'eligible_list']);
Route::get('industries', [UserController::class, 'industry_list']);
Route::post('team/request', [UserController::class, 'team_request_data']);

Route::get('support',[UserController::class, 'support']);

Route::group(['middleware' => ['apilogs','auth:api']], function () {

    // profile
    Route::get('profile', [UserController::class, 'me']);
    Route::post('profile/update', [UserController::class, 'edit_profile']);
    Route::post('change/password', [UserController::class, 'change_password']);
    Route::post('profile/edit/image', [UserController::class, 'edit_profile_image']);

    // video routes
    Route::post('video/add', [VideoController::class, 'video_add']);
    Route::post('video/save',[VideoController::class, 'video_save']);
    Route::post('video/like',[VideoController::class, 'video_like']);
    Route::post('video/view',[VideoController::class, 'video_view']);
    Route::get('video/save/list',[VideoController::class, 'video_save_list']);

    // business
    Route::get('business/home', [HomeController::class, 'business_home']);
    Route::get('chart', [HomeController::class, 'chart']);
    Route::get('company/profile', [HomeController::class, 'company_profile']);

    // pledge user routes
    Route::get('user/home', [HomeController::class, 'user_home']);
    Route::get('company/list', [HomeController::class, 'company_list']);
    Route::get('company/detail', [HomeController::class, 'company_detail']);
    Route::post('company/save', [HomeController::class, 'company_save']);

    Route::get('company/save/list',[HomeController::class, 'company_save_list']);
    Route::post('team/add', [UserController::class, 'teamAdd']);



    // Pledge Routes
    Route::get('pledge/list', [PledgeController::class, 'pledge_list']);
    Route::get('pledge/user/detail', [PledgeController::class, 'pledge_user_detail']);
    Route::get('pledge/history', [PledgeController::class, 'pledge_history']);
    Route::post('pledge/add', [PledgeController::class, 'pledge_add']);
    Route::post('pledge/refund', [PledgeController::class, 'pledge_refund']);
    Route::get('pledge/business/detail', [PledgeController::class, 'pledge_business_detail']);
    Route::post('pledge/transaction',[PledgeController::class,'pledge_transaction']);

    // Following Routes
    Route::post('following', [HomeController::class, 'following']);
    Route::get('following/list', [HomeController::class, 'following_list']);

    
    //stripe
    Route::post('stripe/card/add', [UserController::class, 'stripe_card_add']);
    Route::get('stripe/express/url', [UserController::class, 'stripe_express_url']);
    Route::get('stripe/account/status', [UserController::class, 'retrive_acc']);

    //notification
    Route::get('notification/enable', [NotificationController::class,'notification_enable']);
    Route::get('notifications', [NotificationController::class,'notifications']);
    Route::get('read/notifications', [NotificationController::class,'read_notifications']);

    //logout
    Route::get('logout', [UserController::class, 'logout']);

});
