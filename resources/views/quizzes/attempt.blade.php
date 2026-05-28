<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attempt Quiz - {{ $quiz->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ $quiz->title }}</h1>
                    @if($quiz->description)
                        <p class="text-muted mb-0">{{ $quiz->description }}</p>
                    @endif
                </div>
                <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">Back to Quizzes</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('quiz.attempt.submit', $quiz) }}" method="POST">
                @csrf

                @forelse($quiz->questions as $index => $question)
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Q{{ $index + 1 }}. {{ $question->question_text }}</h2>

                            @if($question->image)
                                <div class="mb-3">
                                    <img
                                        src="{{ asset('storage/' . $question->image) }}"
                                        alt="Question image"
                                        class="img-fluid rounded"
                                        style="max-height: 320px; object-fit: contain;"
                                    >
                                </div>
                            @endif

                            @if($question->video_url)
                                @php
                                    $youtubeId = null;
                                    $host = parse_url($question->video_url, PHP_URL_HOST);

                                    if (str_contains((string) $host, 'youtu.be')) {
                                        $youtubeId = ltrim((string) parse_url($question->video_url, PHP_URL_PATH), '/');
                                    } elseif (str_contains((string) $host, 'youtube.com')) {
                                        parse_str((string) parse_url($question->video_url, PHP_URL_QUERY), $queryParams);
                                        $youtubeId = $queryParams['v'] ?? null;
                                    }
                                @endphp

                                @if($youtubeId)
                                    <div class="ratio ratio-16x9 mb-3">
                                        <iframe
                                            src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                            title="Question video"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen
                                        ></iframe>
                                    </div>
                                @endif
                            @endif

                            @if(in_array($question->type, ['true_false', 'single_choice']))
                                @foreach($question->options as $option)
                                    <div class="form-check mb-2">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="answers[{{ $question->id }}][option_id]"
                                            id="q{{ $question->id }}_o{{ $option->id }}"
                                            value="{{ $option->id }}"
                                        >
                                        <label class="form-check-label" for="q{{ $question->id }}_o{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                @endforeach
                            @elseif($question->type === 'multiple_choice')
                                @foreach($question->options as $option)
                                    <div class="form-check mb-2">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="answers[{{ $question->id }}][option_ids][]"
                                            id="q{{ $question->id }}_o{{ $option->id }}"
                                            value="{{ $option->id }}"
                                        >
                                        <label class="form-check-label" for="q{{ $question->id }}_o{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                @endforeach
                            @elseif($question->type === 'number')
                                <input
                                    type="number"
                                    step="any"
                                    class="form-control"
                                    name="answers[{{ $question->id }}][answer_text]"
                                    placeholder="Enter your numeric answer"
                                >
                            @elseif($question->type === 'text')
                                <textarea
                                    class="form-control"
                                    name="answers[{{ $question->id }}][answer_text]"
                                    rows="4"
                                    placeholder="Write your answer"
                                ></textarea>
                            @else
                                <p class="text-danger mb-0">Unsupported question type: {{ $question->type }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning">No questions available for this quiz.</div>
                @endforelse

                @if($quiz->questions->isNotEmpty())
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Submit Quiz</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
</body>
</html>
