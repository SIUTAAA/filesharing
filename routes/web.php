<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\BundleController;
use App\Http\Middleware\UploadAccess;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [WebController::class, 'login']);
Route::post('/login', [WebController::class, 'doLogin']);

Route::middleware(['can.upload'])->group(function() {
	Route::get('/', [WebController::class, 'homepage'])->name('homepage');
	Route::post('/new', [WebController::class, 'newBundle'])->name('bundle.new');


	Route::prefix('/upload/{bundle}')->controller(UploadController::class)->name('upload.')->group(function() {
    	Route::get('/', 'createBundle')->name('create.show');

		Route::middleware(['access.owner'])->group(function() {
    		Route::post('/', 'storeBundle')->name('create.store');
    		Route::get('/metadata', 'getMetadata')->name('metadata.get');
    		Route::post('/file', 'uploadFile')->name('file.store');
    		Route::delete('/file', 'deleteFile')->name('file.delete');
    		Route::post('/complete', 'completeBundle')->name('complete');
			Route::delete('/delete', 'deleteBundle')->name('bundle.delete');
		});
	});

});

Route::middleware(['access.guest'])->prefix('/bundle/{bundle}')->controller(BundleController::class)->name('bundle.')->group(function() {
    Route::get('/preview', 'previewBundle')->name('preview');
    Route::post('/zip', 'prepareZip')->name('zip.make');
    Route::get('/download', 'downloadZip')->name('zip.download');
});
