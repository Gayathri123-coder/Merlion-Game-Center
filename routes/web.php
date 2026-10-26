<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MasterSorSettingsController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\PlaceController;

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
Route::get('/', function(){
   return Redirect::to('login');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');

Auth::routes();

Route::get('/logout', [LogoutController::class, 'doLogout'])->name('logout');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/contact', [PolicyController::class, 'contact'])->name('contact');
Route::get('/privacy', [PolicyController::class, 'privacy'])->name('privacy');
Route::get('/refund', [PolicyController::class, 'refund'])->name('refund');
Route::get('/terms_condition', [PolicyController::class, 'terms_condition'])->name('terms_condition');


/// Console Master Start /
Route::get('/console_master', [MasterController::class, 'index'])->name('console_master');
Route::post('/console_save', [MasterController::class, 'saveconsole'])->name('console_save');
Route::post('/console_edit', [MasterController::class, 'editconsole'])->name('console_edit');
Route::post('/console_delete',[MasterController::class,'deleteConsole'])->name('console_delete');
Route::get('/master_ajax_console_list', [MasterController::class, 'masterAjaxConsoleList'])->name('master_ajax_console_list');
/*Console Master End */
///  Place Start /
Route::get('/place_master', [PlaceController::class, 'index'])->name('place_master');
Route::post('/place_save', [PlaceController::class, 'saveplace'])->name('place_save');
Route::post('/place_edit', [PlaceController::class, 'editplace'])->name('place_edit');
Route::post('/place_delete',[PlaceController::class,'deletePlace'])->name('place_delete');
Route::get('/master_ajax_place_list', [PlaceController::class, 'masterAjaxPlaceList'])->name('master_ajax_place_list');
Route::post('/set-place', [PlaceController::class, 'setPlace'])
     ->name('set.place');

/// Reference Master Start /
Route::get('/reference_master', [MasterController::class, 'Referencemaster'])->name('reference_master');
Route::post('/reference_save', [MasterController::class, 'savereference'])->name('reference_save');
Route::post('/reference_edit', [MasterController::class, 'editreference'])->name('reference_edit');
Route::post('/reference_delete',[MasterController::class,'deletereference'])->name('console_delete');
Route::get('/master_ajax_reference_list', [MasterController::class, 'masterAjaxreferenceList'])->name('master_ajax_reference_list');
/*Reference Master End */

// Number of players start

Route::get('/players_list', [MasterController::class, 'Noofplayers'])->name('players_list');
Route::get('/master_ajax_noofplayers_list', [MasterController::class, 'masterAjaxplayersList'])->name('master_ajax_noofplayers_list');
Route::post('/Players_save', [MasterController::class, 'SavePlayers'])->name('Players_save');
Route::post('/edit_players', [MasterController::class, 'editPlayers'])->name('edit_players');

/// Hours  Master Start /
Route::get('/Hours_master', [MasterController::class, 'Hoursmaster'])->name('Hours_master');
Route::post('/hours_save', [MasterController::class, 'savehours'])->name('hours_save');
Route::post('/hours_edit', [MasterController::class, 'edithours'])->name('hours_edit');
Route::post('/hours_delete',[MasterController::class,'deletehours'])->name('hours_delete');
Route::get('/master_ajax_hours_list', [MasterController::class, 'masterAjaxhoursList'])->name('master_ajax_hours_list');
/*Hours Master End */

/// Payment type Master Start 
Route::get('/payment_type_master', [MasterController::class, 'paymenttypemaster'])->name('payment_type_master');
Route::post('/payment_type_save', [MasterController::class, 'savepaymenttype'])->name('payment_type_save');
Route::post('/payment_type_edit', [MasterController::class, 'editpaymenttype'])->name('payment_type_edit');
Route::post('/payment_type_delete',[MasterController::class,'deletepaymenttype'])->name('payment_type_delete');
Route::get('/master_ajax_payment_type_list', [MasterController::class, 'masterAjaxpaymenttypeList'])->name('master_ajax_payment_type_list');
/*Payment type Master End */

/// Price Master Start 
Route::get('/price_master', [MasterController::class, 'priceMaster'])->name('price_master');
Route::post('/price_save', [MasterController::class, 'saveprice'])->name('price_save');
Route::post('/price_edit', [MasterController::class, 'editpprice'])->name('price_edit');
Route::post('/price_delete',[MasterController::class,'deleteprice'])->name('price_delete');
Route::get('/master_ajax_price_list', [MasterController::class, 'masterAjaxprice'])->name('master_ajax_price_list');
/*Price Master End */

/* Users start*/
Route::get('/submission_list', [SubmissionController::class, 'index'])->name('submission_list');
Route::get('/submission_add', [SubmissionController::class, 'create'])->name('submission_add');
Route::post('/submission_save', [SubmissionController::class, 'SubmissionSave'])->name('submission_save');
Route::get('/ajax_submission_list', [SubmissionController::class, 'ajaxSubmissionList'])->name('ajax_submission_list');
Route::get('/submission_edit/{id?}', [SubmissionController::class, 'SubmissionEdit'])->name('submission_edit');
Route::post('/submission_update/{id}', [SubmissionController::class, 'updateSubmission'])->name('submission_update');
Route::post('/getpriceinfo', [SubmissionController::class, 'getpriceinfo'])->name('getpriceinfo');
Route::post('/submission_delete',[SubmissionController::class,'deletesubmission'])->name('submission_delete');

Route::get('/submission_report_list', [ReportController::class, 'index'])->name('submission_report_list');
Route::get('/ajax_submission_report_list', [ReportController::class, 'ajaxReportList'])->name('ajax_submission_report_list');

/// FandB Master Start 
Route::get('/fandb_master', [MasterController::class, 'fandbMaster'])->name('fandb_master');
Route::post('/fandb_save', [MasterController::class, 'savefandb'])->name('fandb_save');
Route::post('/fandb_edit', [MasterController::class, 'editfandb'])->name('fandb_edit');
Route::post('/fandb_delete',[MasterController::class,'deletefandb'])->name('fandb_delete');
Route::get('/master_ajax_fandb_list', [MasterController::class, 'masterAjaxfandb'])->name('master_ajax_fandb_list');
/*FandB Master End */
Route::post('/web/get-name-by-mobile', 
    [SubmissionController::class, 'getNameByMobile']
)->name('web.get.name.by.mobile');