<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class QuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        return view('questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'type' => ['required', 'in:true_false,single_choice,multiple_choice,number,text'],
            'marks' => ['required', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'max:2048'],
            'video_url' => ['nullable', 'url'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string'],
            'correct_options' => ['nullable', 'array'],
            'correct_options.*' => ['integer'],
        ]);

        $rawOptions = $validated['options'] ?? [];
        $trimmedOptions = [];

        foreach ($rawOptions as $index => $optionText) {
            if (! is_string($optionText)) {
                continue;
            }

            $normalizedText = trim($optionText);
            if ($normalizedText === '') {
                continue;
            }

            $trimmedOptions[(int) $index] = $normalizedText;
        }

        $correctOptionIndexes = collect($validated['correct_options'] ?? [])
            ->map(static fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();

        $correctNonEmptyCount = collect(array_keys($trimmedOptions))
            ->filter(fn ($index) => in_array((int) $index, $correctOptionIndexes, true))
            ->count();

        $type = $validated['type'];

        if (in_array($type, ['true_false', 'single_choice', 'multiple_choice'], true)) {
            if (count($trimmedOptions) < 2) {
                return back()->withInput()->withErrors([
                    'options' => 'Please add at least 2 non-empty options.',
                ]);
            }

            if (in_array($type, ['true_false', 'single_choice'], true) && $correctNonEmptyCount !== 1) {
                return back()->withInput()->withErrors([
                    'correct_options' => 'Select exactly 1 correct option for True/False and Single Choice.',
                ]);
            }

            if ($type === 'multiple_choice' && $correctNonEmptyCount < 1) {
                return back()->withInput()->withErrors([
                    'correct_options' => 'Select at least 1 correct option for Multiple Choice.',
                ]);
            }
        }

        if (in_array($type, ['number', 'text'], true) && $correctNonEmptyCount !== 1) {
            return back()->withInput()->withErrors([
                'correct_options' => 'For Number/Text questions, add one answer in Options and mark it as Correct.',
            ]);
        }

        try {
            DB::transaction(function () use ($request, $quiz, $validated, $trimmedOptions, $correctOptionIndexes) {
                $imagePath = null;

                if ($request->hasFile('image')) {
                    $imagePath = $request
                        ->file('image')
                        ->store('questions', 'public');
                }

                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $validated['question_text'],
                    'type' => $validated['type'],
                    'marks' => $validated['marks'],
                    'image' => $imagePath,
                    'video_url' => $validated['video_url'] ?? null,
                ]);

                foreach ($trimmedOptions as $index => $optionText) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => in_array((int) $index, $correctOptionIndexes, true),
                    ]);
                }
            });

            return back()->with('success', 'Question added successfully.');
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to save question. Please try again.');
        }
    }
}
