<!DOCTYPE html>
<html>
<head>
    <title>Create Question</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Add Question</h2>
        <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ $errors->first() }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <form method="POST"
          action="{{ route('questions.store', $quiz->id) }}"
          enctype="multipart/form-data">

        @csrf

        <div class="mb-3">
            <label>Question</label>

            <textarea name="question_text"
                      class="form-control"
                      required>{{ old('question_text') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Question Type</label>

            <select name="type"
                    id="question_type"
                    class="form-control">

                <option value="true_false" @selected(old('type') === 'true_false')>True False</option>
                <option value="single_choice" @selected(old('type') === 'single_choice')>Single Choice</option>
                <option value="multiple_choice" @selected(old('type') === 'multiple_choice')>Multiple Choice</option>
                <option value="number" @selected(old('type') === 'number')>Number</option>
                <option value="text" @selected(old('type') === 'text')>Text</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Marks</label>

            <input type="number"
                   name="marks"
                   class="form-control"
                   value="{{ old('marks', 1) }}">
        </div>

        <div class="mb-3">
            <label>Image</label>

            <input type="file"
                   name="image"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Video URL</label>

            <input type="text"
                   name="video_url"
                   class="form-control"
                   value="{{ old('video_url') }}">
        </div>

        @php
            $oldOptions = old('options', ['']);
            if (! is_array($oldOptions) || $oldOptions === []) {
                $oldOptions = [''];
            }

            $oldCorrectOptions = collect(old('correct_options', []))
                ->map(static fn ($value) => (int) $value)
                ->all();
        @endphp

        <div id="options_section">
            <h4>Options</h4>

            @foreach($oldOptions as $index => $oldOption)
                <div class="option-item mb-2" data-index="{{ $index }}">
                    <input type="text"
                           name="options[]"
                           class="form-control mb-2"
                           placeholder="Option"
                           value="{{ $oldOption }}">

                    <label>
                        <input type="checkbox"
                               name="correct_options[]"
                               value="{{ $index }}"
                               @checked(in_array((int) $index, $oldCorrectOptions, true))>
                        Correct
                    </label>
                </div>
            @endforeach
        </div>

        <button type="button"
                class="btn btn-secondary mb-3"
                onclick="addOption()">
            Add Option
        </button>

        <br>

        <button type="submit"
                class="btn btn-primary">
            Save Question
        </button>

    </form>

</div>

<script>
let optionIndex = {{ count($oldOptions) }};

function addOption()
{
    const html = `
        <div class="option-item mb-2" data-index="${optionIndex}">
            <input type="text"
                   name="options[]"
                   class="form-control mb-2"
                   placeholder="Option">

            <label>
                <input type="checkbox"
                       name="correct_options[]"
                       value="${optionIndex}">
                Correct
            </label>
        </div>
    `;

    document
        .getElementById('options_section')
        .insertAdjacentHTML('beforeend', html);

    optionIndex++;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
