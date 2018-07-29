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

Route::get('/', 'TournamentsController@index')->name('tournaments.index');
Route::post('/tournaments/create', 'TournamentsController@create')->name('tournaments.create');
Route::get('/tournaments/{id}', 'TournamentsController@view')->name('tournaments.view');
Route::post('/tournaments/nextStep/{id}', 'FakeResultGeneratorController@nextStep')->name('tournaments.nextStep');
