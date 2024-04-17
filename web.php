<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppUserController;
use App\Http\Controllers\FeederSettingsController;
use App\Http\Controllers\FeederTimerController;
use App\Http\Controllers\StorageLevelController;

Route::get('/', function () { return view('login'); });  
Route::get('/home', function () {return view('home');});
Route::get('/login', function () { return view('login'); }); 
Route::get('/register', function () { return view('register'); }); 
Route::get('/schedules', function () { return view('schedule'); }); 


Route::post('/registerUser', [AppUserController::class, 'register_user']);
Route::post('/loginUser', [AppUserController::class, 'login_user']);

Route::get('/decreaseFood', [FeederSettingsController::class, 'decrease_food_per_meal']);
Route::get('/increaseFood', [FeederSettingsController::class, 'increase_food_per_meal']); 
Route::get('/amountOfFood', [FeederSettingsController::class, 'get_amount_of_food']); 

Route::post('/createTimer', [FeederTimerController::class, 'create_new_timer']);
Route::get('/deleteTimer', [FeederTimerController::class, 'delete_timer']);
Route::get('/updateTimer', [FeederTimerController::class, 'update_timer']);
Route::get('/updateTimerActive', [FeederTimerController::class, 'update_timer_active']);
Route::get('/getTimers', [FeederTimerController::class, 'get_timers']);
Route::get('/checkTimers', [FeederTimerController::class, 'check_timers']);

Route::get('/setStorageLevel', [StorageLevelController::class, 'set_storage_level']);
Route::get('/getStorageLevel', [StorageLevelController::class, 'get_storage_level']);
Route::get('/setFeedNow', [StorageLevelController::class, 'set_feed_now']);
Route::get('/getFeedNow', [StorageLevelController::class, 'get_feed_now']);