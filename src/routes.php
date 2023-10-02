<?php

use Illuminate\Support\Facades\Route;
use Sbx\Frontcrm\Http\Controllers\CRMController;

Route::get('sbxGetSetting', [CRMController::class, 'getSetting'])->name('sbx.getSetting');
Route::post('sbxSetSetting', [CRMController::class, 'setSetting'])->name('sbx.setSetting');