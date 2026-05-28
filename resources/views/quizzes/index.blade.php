<!DOCTYPE html>
<html>
<head>

    <title>Quizzes</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">

</head>

<body>

<div class="container mt-5">

    <h2>Quiz List</h2>

    <a href="{{ route('quizzes.create') }}"
       class="btn btn-primary mb-3">

        Create Quiz

    </a>

    <table class="table table-bordered">

        <tr>
            <th>Title</th>
            <th>Actions</th>
        </tr>

        @foreach($quizzes as $quiz)

        <tr>

            <td>{{ $quiz->title }}</td>

            <td>

                <a href="{{ route('questions.create', $quiz->id) }}"
                   class="btn btn-success">

                    Add Questions

                </a>

                <a href="{{ route('quiz.attempt', $quiz->id) }}"
                   class="btn btn-warning">

                    Attempt Quiz

                </a>

            </td>

        </tr>

        @endforeach

    </table>

</div>

</body>
</html>