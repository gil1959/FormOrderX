<?php




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmbedSubmitController;
use App\Http\Controllers\Api\AbandonedSessionController;
use App\Http\Controllers\Api\EmbedNonceController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route di sini:
| - TIDAK pakai CSRF
| - Cocok untuk embed / external request
| - Prefix otomatis: /api
|
| Jadi route di sini:
| POST /submit/{token}  -> /api/submit/{token}
| POST /abandoned/{token}/touch -> /api/abandoned/{token}/touch
|--------------------------------------------------------------------------
*/

Route::get('/embed/{token}/nonce', [EmbedNonceController::class, 'issue'])
    ->middleware('throttle:embed-nonce');

Route::post('/submit/{token}', [EmbedSubmitController::class, 'submit'])
    ->middleware('throttle:embed-submit');

Route::post('/abandoned/{token}/touch', [AbandonedSessionController::class, 'touch']);
