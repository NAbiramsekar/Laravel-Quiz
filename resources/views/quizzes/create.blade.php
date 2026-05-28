<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
    <style>
        :root {
            --bg-start: #f4f8ff;
            --bg-end: #e9f2ff;
            --card-bg: #ffffff;
            --text-main: #172033;
            --text-muted: #5d6b85;
            --primary: #2563eb;
            --primary-hover: #1e4fc2;
            --border: #d7e2f0;
            --danger-bg: #fef2f2;
            --danger-text: #b91c1c;
            --danger-border: #fecaca;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(160deg, var(--bg-start), var(--bg-end));
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .quiz-create-card {
            width: min(100%, 720px);
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 14px 34px rgba(13, 42, 87, 0.08);
            padding: 28px;
        }

        .quiz-create-title {
            margin: 0 0 8px;
            font-size: 28px;
            line-height: 1.2;
            font-weight: 700;
        }

        .quiz-create-subtitle {
            margin: 0 0 24px;
            color: var(--text-muted);
            font-size: 15px;
        }

        .alert {
            margin: 0 0 18px;
            padding: 12px 14px;
            border: 1px solid var(--danger-border);
            background: var(--danger-bg);
            color: var(--danger-text);
            border-radius: 10px;
            font-size: 14px;
        }

        .alert ul {
            margin: 8px 0 0;
            padding-left: 18px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: inline-block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .input,
        .textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--text-main);
            font: inherit;
            font-size: 15px;
            padding: 12px 14px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .textarea {
            min-height: 130px;
            resize: vertical;
            line-height: 1.5;
        }

        .input:focus,
        .textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .actions {
            margin-top: 6px;
            display: flex;
            justify-content: flex-end;
        }

        .btn-primary {
            border: none;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            padding: 11px 20px;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.06s ease;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-primary:active {
            transform: translateY(1px);
        }

        @media (max-width: 640px) {
            .quiz-create-card {
                padding: 20px;
                border-radius: 14px;
            }

            .quiz-create-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="quiz-create-card">
        <h1 class="quiz-create-title">Create a New Quiz</h1>
        <p class="quiz-create-subtitle">Add a title and description to set up your quiz.</p>

        @if ($errors->any())
            <div class="alert">
                Please fix the following issues:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('quizzes.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="title">Quiz Title</label>
                <input
                    class="input"
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Enter quiz title"
                    required
                >
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea
                    class="textarea"
                    id="description"
                    name="description"
                    placeholder="Write a short description for this quiz"
                >{{ old('description') }}</textarea>
            </div>

            <div class="actions">
                <button class="btn-primary" type="submit">Save Quiz</button>
            </div>
        </form>
    </div>
</body>
</html>
