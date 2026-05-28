<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Attempt;
use App\Models\Quiz;
use App\Services\QuizEvaluator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quizzes = Quiz::latest()->get();

        return view('quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('quizzes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('quizzes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function attempt(Quiz $quiz)
    {
        $quiz->load([
            'questions' => fn ($query) => $query->with('options')->orderBy('id'),
        ]);

        return view('quizzes.attempt', compact('quiz'));
    }

    public function submitAttempt(Request $request, Quiz $quiz, QuizEvaluator $quizEvaluator)
    {
        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'array'],
            'answers.*.option_id' => ['nullable', 'integer'],
            'answers.*.option_ids' => ['nullable', 'array'],
            'answers.*.option_ids.*' => ['integer'],
            'answers.*.answer_text' => ['nullable', 'string'],
        ]);

        $questions = $quiz->questions()
            ->with('options:id,question_id,is_correct')
            ->get()
            ->keyBy('id');

        $attempt = DB::transaction(function () use ($quiz, $questions, $validated, $quizEvaluator) {
            $attempt = Attempt::create([
                'quiz_id' => $quiz->id,
                'submitted_at' => now(),
                'total_score' => 0,
            ]);

            $totalScore = 0;

            foreach ($validated['answers'] as $questionId => $answerData) {
                $question = $questions->get((int) $questionId);
                if (! $question) {
                    continue;
                }

                $normalized = $quizEvaluator->normalizeAnswer($question, $answerData);

                Answer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'answer_text' => $normalized['answer_text'],
                    'selected_options' => $normalized['selected_options'],
                ]);

                $totalScore += $quizEvaluator->evaluateQuestion($question, $normalized);
            }

            $attempt->update(['total_score' => $totalScore]);

            return $attempt;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Quiz submitted successfully.',
                'attempt_id' => $attempt->id,
                'total_score' => $attempt->total_score,
            ], 201);
        }

        return redirect()
            ->route('quiz.result', $attempt)
            ->with('success', "Quiz submitted successfully. Score: {$attempt->total_score}");
    }

    public function result(Attempt $attempt, QuizEvaluator $quizEvaluator)
    {
        $attempt->load([
            'quiz.questions.options',
            'answers.question.options',
        ]);

        $questions = $attempt->quiz->questions;
        $answersByQuestionId = $attempt->answers->keyBy('question_id');

        $reviewItems = $questions->map(function ($question) use ($answersByQuestionId, $quizEvaluator) {
            $answer = $answersByQuestionId->get($question->id);

            $normalized = [
                'answer_text' => $answer?->answer_text,
                'selected_options' => $answer?->selected_options,
            ];

            $isCorrect = $quizEvaluator->evaluateQuestion($question, $normalized) > 0;
            $correctOptionIds = $question->options
                ->where('is_correct', true)
                ->pluck('id')
                ->values()
                ->all();

            return [
                'question' => $question,
                'answer' => $answer,
                'is_correct' => $isCorrect,
                'correct_option_ids' => $correctOptionIds,
                'selected_option_ids' => $answer?->selected_options ?? [],
            ];
        });

        $totalQuestions = $questions->count();
        $correctAnswers = $reviewItems->where('is_correct', true)->count();
        $wrongAnswers = $totalQuestions - $correctAnswers;

        return view('quizzes.result', [
            'attempt' => $attempt,
            'quiz' => $attempt->quiz,
            'reviewItems' => $reviewItems,
            'totalQuestions' => $totalQuestions,
            'totalScore' => $attempt->total_score,
            'correctAnswers' => $correctAnswers,
            'wrongAnswers' => $wrongAnswers,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
