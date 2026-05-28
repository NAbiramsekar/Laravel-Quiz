<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result - {{ $quiz->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h1 class="h3 mb-1">Quiz Result</h1>
                    <p class="text-muted mb-0">{{ $quiz->title }}</p>
                </div>
                <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">Back to Quizzes</a>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total Questions</p>
                            <h2 class="h4 mb-0">{{ $totalQuestions }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total Score</p>
                            <h2 class="h4 mb-0">{{ $totalScore }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-muted mb-1">Correct Answers</p>
                            <h2 class="h4 mb-0 text-success">{{ $correctAnswers }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-muted mb-1">Wrong Answers</p>
                            <h2 class="h4 mb-0 text-danger">{{ $wrongAnswers }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Question Review</h2>
                </div>
                <div class="card-body">
                    @forelse($reviewItems as $index => $item)
                        @php
                            $question = $item['question'];
                            $answer = $item['answer'];
                            $isCorrect = $item['is_correct'];
                            $selectedOptionIds = $item['selected_option_ids'];
                            $correctOptionIds = $item['correct_option_ids'];
                        @endphp

                        <div class="border rounded p-3 mb-3 {{ $isCorrect ? 'border-success bg-success-subtle' : 'border-danger bg-danger-subtle' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2 gap-3">
                                <h3 class="h6 mb-0">Q{{ $index + 1 }}. {{ $question->question_text }}</h3>
                                <span class="badge {{ $isCorrect ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ $isCorrect ? 'Correct' : 'Wrong' }}
                                </span>
                            </div>

                            @if(in_array($question->type, ['true_false', 'single_choice', 'multiple_choice'], true))
                                <div class="list-group">
                                    @foreach($question->options as $option)
                                        @php
                                            $isSelected = in_array($option->id, $selectedOptionIds, true);
                                            $isCorrectOption = in_array($option->id, $correctOptionIds, true);
                                            $itemClass = 'list-group-item d-flex justify-content-between align-items-center';

                                            if ($isCorrectOption) {
                                                $itemClass .= ' list-group-item-success';
                                            } elseif ($isSelected && ! $isCorrectOption) {
                                                $itemClass .= ' list-group-item-danger';
                                            }
                                        @endphp
                                        <div class="{{ $itemClass }}">
                                            <span>{{ $option->option_text }}</span>
                                            <span>
                                                @if($isCorrectOption)
                                                    <span class="badge text-bg-success">Correct</span>
                                                @endif
                                                @if($isSelected)
                                                    <span class="badge text-bg-primary">Your Choice</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="p-2 rounded bg-white border">
                                            <small class="text-muted d-block">Your Answer</small>
                                            <strong>{{ $answer?->answer_text ?? 'Not Answered' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @php
                                            $correctOption = $question->options->firstWhere('is_correct', true);
                                        @endphp
                                        <div class="p-2 rounded bg-white border">
                                            <small class="text-muted d-block">Correct Answer</small>
                                            <strong>{{ $correctOption?->option_text ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No review data available for this attempt.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
