<?php

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

use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('patients','PatientController');
Route::get('/patient/newreceipt', 'PatientController@getPatient')->middleware('auth');
Route::get('/patient/loadHistory', 'PatientController@loadHistory');

Route::resource('receipts','ReceiptController');
Route::post('submit_receipt',function(){
    if(Request::ajax()){
        return Response::json(Request::all());
    }
});

Route::post('receipts/store','ReceiptController@store');
Route::post('receipts/medicinesDone','ReceiptController@medicinesDone');
Route::post('receipts/checkupsDone','ReceiptController@checkupsDone');

Route::post('ajax/med_suggest','ReceiptController@med_suggest');
Route::post('ajax/get_med_id','ReceiptController@get_med_id');
Route::post('receipts/medIssued','ReceiptController@medIssued');

//Tech
