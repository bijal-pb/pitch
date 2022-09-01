<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\PledgeController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\HomeController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/token/{id}', [HomeController::class, 'accessToken'])->name('authtoken');

Route::get('/privacy-policy',function () {
    return view("admin.privacy-policy");
}); 
Route::get('/terms',function () {
    return view("admin.terms");
});
Route::get('/', function () {
    return redirect("/admin");
});


Auth::routes();

Route::get('/home', function () {
    return redirect("/admin");
});

Route::get('/forgot/password', [UserController::class, 'forgot_password'])->name('admin.forgot');
Route::post('/forgot/password/mail', [UserController::class, 'password_mail'])->name('admin.forgot.mail');
Route::post('admin/login', [UserController::class, 'admin_login'])->name('admin.login');

Route::name('admin.')->namespace('Admin')->group(function () {
    Route::group(['prefix' => 'admin', 'middleware' => ['admin.check']], function () {
        Route::get('/', [AdminController::class, 'index'])->name('home');
       
        // users  route
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::get('/password', [UserController::class, 'password'])->name('password');
        Route::post('/password/change', [UserController::class, 'change_password'])->name('password.update');
        Route::post('/profile/update', [UserController::class, 'update_profile'])->name('profile.update');
        Route::get('/users', [UserController::class, 'index'])->name('user');
        Route::get('/users/list', [UserController::class, 'users'])->name('users.list');
        Route::get('/get/user', [UserController::class, 'getUser'])->name('user.get');
        Route::get('/user/status/change', [UserController::class, 'changeStatus'])->name('user.status.change');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
        Route::get('/user/detail/{user_id}', [UserController::class, 'user_detail'])->name('users.detail');

        //business user
        Route::get('/business', [BusinessController::class, 'index'])->name('business');
         Route::get('/business/get', [BusinessController::class, 'getBusiness'])->name('business.get');
         Route::post('/business/store', [BusinessController::class, 'store'])->name('business.store');
         Route::post('/business/delete', [BusinessController::class, 'delete'])->name('business.delete');
         Route::get('/business/list', [BusinessController::class, 'businesses'])->name('business.list');
         Route::get('/business/status/change/verified', [BusinessController::class, 'changeStatusVerified'])->name('business.changeStatusVerified');
         Route::get('/business/status/change/unverified', [BusinessController::class, 'changeStatusUnverified'])->name('business.changeStatusUnverified');
         Route::get('/business/detail/{user_id}', [BusinessController::class, 'business_detail'])->name('business.detail');

         //pladge route
         Route::get('/pledge', [PledgeController::class, 'index'])->name('pledge');
         Route::get('/pledge/get', [PledgeController::class, 'getPledge'])->name('pledge.get');
         Route::post('/pledge/store', [PledgeController::class, 'store'])->name('pledge.store');
         Route::post('/pledge/delete', [PledgeController::class, 'delete'])->name('pledge.delete');
         Route::get('/pledge/list', [PledgeController::class, 'pledges'])->name('pledge.list');
        //  Route::get('/pledge/status/change', [PledgeController::class, 'changeStatus'])->name('pledge.status.change');

        //refund route
        Route::get('/refund', [RefundController::class, 'index'])->name('refund');
        Route::get('/refund/get', [RefundController::class, 'getRefund'])->name('refund.get');
        Route::post('/refund/store', [RefundController::class, 'store'])->name('refund.store');
        Route::post('/refund/delete', [RefundController::class, 'delete'])->name('refund.delete');
        Route::get('/refund/list', [RefundController::class, 'refunds'])->name('refund.list');

        //Document route
        Route::get('/document', [DocumentController::class, 'index'])->name('document');
        Route::get('/document/get', [DocumentController::class, 'getDocument'])->name('document.get');
        Route::post('/document/store', [DocumentController::class, 'store'])->name('document.store');
        Route::post('/document/delete', [DocumentController::class, 'delete'])->name('document.delete');
        Route::get('/document/list', [DocumentController::class, 'documents'])->name('document.list');

        //report route
        Route::get('/report', [ReportController::class, 'index'])->name('report');
        Route::post('/report/get', [ReportController::class, 'getReport'])->name('report.get');
        Route::get('generate/report', [ReportController::class, 'generateReport'])->name('generate.report');

        // app setting
        Route::get('setting', [UserController::class, 'app_setting'])->name('setting');
        Route::post('setting/update', [UserController::class, 'setting_update'])->name('setting.update');


        //notification
        Route::get('notification', [NotificationController::class, 'app_notification'])->name('notification');
        Route::post('notification/send', [ NotificationController::class ,'send_notification'])->name('notification.send');
    });
});

Route::get('logout', [LoginController::class, 'logout'])->name('logout');
