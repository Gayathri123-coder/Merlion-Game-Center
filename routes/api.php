<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\SubmissiondataController;
use App\Http\Controllers\Api\BookingDateController;
use App\Http\Controllers\Api\BookingDurationController;
use App\Http\Controllers\Api\BookingStartTimeController;
use App\Http\Controllers\Api\BookingPlayerController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\CreateConsoleController;
use App\Http\Controllers\Api\ConsoleController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BookingHistoryController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FandBController;
use App\Http\Controllers\Api\PlaceController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no token)
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

   // User
    Route::post('/logout', [LoginController::class, 'logout']);

    // Booking related endpoints
    Route::get('/bookingdates', [BookingDateController::class, 'index']);
    Route::get('/bookingduration', [BookingDurationController::class, 'index']);
    Route::get('/bookingstart-times', [BookingStartTimeController::class, 'index']);
    Route::get('/bookingplayers', [BookingPlayerController::class, 'index']);
    Route::get('/session-time', [SessionController::class, 'getSessionTime']);
    Route::post('/booking-submit', [CreateConsoleController::class, 'submitBooking']);
    Route::post('/get-price', [CreateConsoleController::class, 'getPrice']);


    // My bookings & history
    Route::get('/my-bookings', [BookingController::class,'myBookings']);
    Route::get('/booking/summary/{id}', [BookingController::class, 'bookingSummary']);
    Route::get('booking-history', [BookingHistoryController::class, 'history']);
    Route::get('booking/show/{id}', [BookingHistoryController::class, 'show']);

    // Console management
    Route::get('/consoles', [ConsoleController::class, 'index']);
    Route::get('/consoles/{consoleName}', [ConsoleController::class, 'show']);
    Route::post('/consoles/update/{id}', [ConsoleController::class, 'update']);

    Route::post('/profile/update-image', [ProfileController::class, 'updateProfileImage']);

    // Payments
  Route::post('/create-order', [PaymentController::class, 'createOrder']);
  Route::post('/verify-payment', [PaymentController::class, 'verifyPayment']);
//Food 
  Route::get('fandb/list', [FandBController::class, 'list']);
  Route::post('/fandb/{id}/upload-image', [FandBController::class, 'updateImage']);

Route::get('/places', [PlaceController::class, 'index']);