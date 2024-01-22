<?php


use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('auth')->group(
    function (){
        Route::post('get_token',[\App\Http\Controllers\Api\AuthController::class,'get_token']);
    }
);
Route::prefix('category')->group(function (){
    Route::get('/',[\App\Http\Controllers\Api\CategoryController::class,'index']);
    Route::get('/{category_id}',[\App\Http\Controllers\Api\CategoryController::class,'show'])->where('category_id', '[0-9]+');
    Route::post('/',[\App\Http\Controllers\Api\CategoryController::class,'create']);
    Route::put('/{category_id}',[\App\Http\Controllers\Api\CategoryController::class,'update']);
    Route::delete('/{category_id}',[\App\Http\Controllers\Api\CategoryController::class,'destroy']);
});
Route::prefix('system')->group(function (){
    Route::get('/',[\App\Http\Controllers\Api\SystemController::class,'index']);
    Route::get('/{system_id}',[\App\Http\Controllers\Api\SystemController::class,'show'])->where('system_id', '[0-9]+');
    Route::post('/',[\App\Http\Controllers\Api\SystemController::class,'create']);
    Route::put('/{system_id}',[\App\Http\Controllers\Api\SystemController::class,'update']);
    Route::delete('/{system_id}',[\App\Http\Controllers\Api\SystemController::class,'destroy']);
});
Route::prefix('poll')->group(function (){
    Route::get('/',[\App\Http\Controllers\Api\PollController::class,'index']);
    Route::get('/{poll_id}',[\App\Http\Controllers\Api\PollController::class,'show'])->where('poll_id', '[0-9]+');
    Route::post('/',[\App\Http\Controllers\Api\PollController::class,'create']);
    Route::put('/{poll_id}',[\App\Http\Controllers\Api\PollController::class,'update']);
    Route::delete('/{poll_id}',[\App\Http\Controllers\Api\PollController::class,'destroy']);
    Route::post('/{poll_id}/question',[\App\Http\Controllers\Api\PollQuestionController::class,'create']);
    Route::get('/{poll_id}/question',[\App\Http\Controllers\Api\PollQuestionController::class,'index']);
    Route::get('/{poll_id}/question/{question_id}',[\App\Http\Controllers\Api\PollQuestionController::class,'show']);
    Route::delete('/{poll_id}/question/{question_id}',[\App\Http\Controllers\Api\PollQuestionController::class,'destroy']);
});
Route::prefix('system_categoty_poll')->group(function (){
    Route::post('/',[\App\Http\Controllers\Api\SystemCategoryPollController::class,'create']);
});
Route::prefix('question')->group(function (){
    Route::get('/',[\App\Http\Controllers\Api\QuestionController::class,'index']);
    Route::get('/{question_id}',[\App\Http\Controllers\Api\QuestionController::class,'show'])->where('category_id', '[0-9]+');
    Route::post('/',[\App\Http\Controllers\Api\QuestionController::class,'create']);
    Route::put('/{question_id}',[\App\Http\Controllers\Api\QuestionController::class,'update']);
    Route::delete('/{question_id}',[\App\Http\Controllers\Api\QuestionController::class,'destroy']);
    Route::get('/{question_id}/condition',[\App\Http\Controllers\Api\QuestionConditionController::class,'index']);
    Route::post('/{question_id}/condition',[\App\Http\Controllers\Api\QuestionConditionController::class,'create']);
});
Route::prefix('comment')->group(function (){});
Route::prefix('answer')->group(function (){
    Route::get('system/{system_id}/category/{category_id}/poll/{poll_id}',[\App\Http\Controllers\Api\AnswerController::class,'showPollQuestions']);
    Route::post('/',[\App\Http\Controllers\Api\AnswerController::class,'create']);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
