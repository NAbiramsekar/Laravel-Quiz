<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;

Route::resource('quizzes', QuizController::class);
Route::get('/', function () {
    return redirect()->route('quizzes.index');
});

Route::get('/quizzes/{quiz}/questions/create',
    [QuestionController::class, 'create'])
    ->name('questions.create');

Route::post('/quizzes/{quiz}/questions',
    [QuestionController::class, 'store'])
    ->name('questions.store');

Route::get('/quizzes/{quiz}/attempt', [QuizController::class, 'attempt'])
    ->name('quiz.attempt');
Route::post('/quizzes/{quiz}/attempt', [QuizController::class, 'submitAttempt'])
    ->name('quiz.attempt.submit');
Route::get('/attempts/{attempt}/result', [QuizController::class, 'result'])
    ->name('quiz.result');
