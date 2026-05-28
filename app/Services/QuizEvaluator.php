<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Collection;

class QuizEvaluator
{
    public function normalizeAnswer(Question $question, array $answerData): array
    {
        $validOptionIds = $question->options->pluck('id')->all();

        return match ($question->type) {
            'true_false', 'single_choice' => $this->normalizeSingleOptionAnswer($answerData, $validOptionIds),
            'multiple_choice' => $this->normalizeMultipleOptionAnswer($answerData, $validOptionIds),
            'number' => $this->normalizeNumberAnswer($answerData),
            'text' => $this->normalizeTextAnswer($answerData),
            default => [
                'answer_text' => null,
                'selected_options' => null,
            ],
        };
    }

    public function evaluateQuestion(Question $question, array $normalizedAnswer): int
    {
        return match ($question->type) {
            'true_false', 'single_choice' => $this->evaluateSingleChoice($question, $normalizedAnswer),
            'multiple_choice' => $this->evaluateMultipleChoice($question, $normalizedAnswer),
            'number' => $this->evaluateNumber($question, $normalizedAnswer),
            'text' => $this->evaluateText($question, $normalizedAnswer),
            default => 0,
        };
    }

    public function evaluateTotal(Collection $questions, array $answersByQuestionId): int
    {
        $total = 0;

        foreach ($answersByQuestionId as $questionId => $answerData) {
            $question = $questions->get((int) $questionId);
            if (! $question || ! is_array($answerData)) {
                continue;
            }

            $normalized = $this->normalizeAnswer($question, $answerData);
            $total += $this->evaluateQuestion($question, $normalized);
        }

        return $total;
    }

    private function normalizeSingleOptionAnswer(array $answerData, array $validOptionIds): array
    {
        $optionId = isset($answerData['option_id']) ? (int) $answerData['option_id'] : null;
        $selected = in_array($optionId, $validOptionIds, true) ? [$optionId] : null;

        return [
            'answer_text' => null,
            'selected_options' => $selected,
        ];
    }

    private function normalizeMultipleOptionAnswer(array $answerData, array $validOptionIds): array
    {
        $selected = collect($answerData['option_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => in_array($id, $validOptionIds, true))
            ->unique()
            ->values()
            ->all();

        return [
            'answer_text' => null,
            'selected_options' => $selected ?: null,
        ];
    }

    private function normalizeNumberAnswer(array $answerData): array
    {
        $raw = isset($answerData['answer_text']) ? trim((string) $answerData['answer_text']) : null;
        $value = ($raw !== '' && is_numeric($raw)) ? (string) +$raw : null;

        return [
            'answer_text' => $value,
            'selected_options' => null,
        ];
    }

    private function normalizeTextAnswer(array $answerData): array
    {
        $raw = isset($answerData['answer_text']) ? trim((string) $answerData['answer_text']) : null;

        return [
            'answer_text' => $raw !== '' ? $raw : null,
            'selected_options' => null,
        ];
    }

    private function evaluateSingleChoice(Question $question, array $normalizedAnswer): int
    {
        $selected = collect($normalizedAnswer['selected_options'] ?? [])->sort()->values()->all();
        $correct = $this->getCorrectOptionIds($question);

        return $selected === $correct ? (int) $question->marks : 0;
    }

    private function evaluateMultipleChoice(Question $question, array $normalizedAnswer): int
    {
        $selected = collect($normalizedAnswer['selected_options'] ?? [])->sort()->values()->all();
        $correct = $this->getCorrectOptionIds($question);

        return $selected === $correct ? (int) $question->marks : 0;
    }

    private function evaluateNumber(Question $question, array $normalizedAnswer): int
    {
        $submitted = $normalizedAnswer['answer_text'] ?? null;
        $expected = $this->getCorrectTextAnswer($question);

        if ($submitted === null || $expected === null || ! is_numeric($submitted) || ! is_numeric($expected)) {
            return 0;
        }

        return abs((float) $submitted - (float) $expected) < 0.000001
            ? (int) $question->marks
            : 0;
    }

    private function evaluateText(Question $question, array $normalizedAnswer): int
    {
        $submitted = $normalizedAnswer['answer_text'] ?? null;
        $expected = $this->getCorrectTextAnswer($question);

        if ($submitted === null || $expected === null) {
            return 0;
        }

        return trim($submitted) === trim($expected)
            ? (int) $question->marks
            : 0;
    }

    private function getCorrectOptionIds(Question $question): array
    {
        return $question->options
            ->where('is_correct', true)
            ->pluck('id')
            ->sort()
            ->values()
            ->all();
    }

    private function getCorrectTextAnswer(Question $question): ?string
    {
        $correctOption = $question->options->firstWhere('is_correct', true);

        if (! $correctOption || $correctOption->option_text === null) {
            return null;
        }

        $answer = trim((string) $correctOption->option_text);

        return $answer !== '' ? $answer : null;
    }
}
