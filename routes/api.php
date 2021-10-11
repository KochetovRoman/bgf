<?php

use App\Http\Controllers\Data\IndustriesController;
use App\Http\Controllers\Data\RegionsController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\FilesUploadController;
use App\Http\Controllers\Filters\FiltersController;
use App\Http\Controllers\Data\DiagnosisController;
use App\Http\Controllers\Data\StatisticsController;

Route::prefix('files')->group(function(){
    Route::post('upload', [FilesUploadController::class, 'upload']);
});

Route::prefix('import')->group(function(){
    Route::post('statistics', [ImportController::class, 'importStatistics']);
    Route::post('diagnoses', [ImportController::class, 'importDiagnoses']);
    Route::post('industries', [ImportController::class, 'importIndustries']);
    Route::post('regions', [ImportController::class, 'importRegionsToMap']);
});

Route::prefix('data')->group(function(){
    //graph
    Route::prefix('filters')->group(function(){
        Route::get('getFilters', [FiltersController::class, 'getFiltersStatistics']);
        Route::get('getFiltersIndustries', [FiltersController::class, 'getFiltersIndustries']);
    });
    Route::prefix('diagrams')->group(function(){
        Route::get('getDiagramsData', [DiagnosisController::class, 'getDiagramsData']);
    });
    Route::prefix('graph')->group(function(){
        Route::get('getGraphData', [StatisticsController::class, 'getGraphData']);
        Route::post('getFilteredGraphData', [StatisticsController::class, 'getFilteredGraphData']);
    });

    Route::prefix('industries')->group(function(){
        Route::post('getFilteredIndustriesData', [IndustriesController::class, 'getFilteredIndustriesData']);
    });

    Route::prefix('map')->group(function(){
        Route::get('getRegionsToMap', [RegionsController::class, 'getRegionsToMap']);
    });

    Route::post('export', [ExportController::class, 'export']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});



