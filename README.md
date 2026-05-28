# Laravel Dynamic Quiz System

A web-based quiz management and assessment platform built with Laravel. This project allows admins/instructors to create quizzes with dynamic question types, and allows users to attempt quizzes and view scored results with answer review.

## Project Overview

The Laravel Dynamic Quiz System is designed to support quiz creation, question authoring, and automated evaluation. It supports multiple question formats, optional media attachments (image/video), and a clean user flow from quiz setup to result review.

## Features

- Create and manage quizzes
- Add dynamic questions per quiz
- Add options and mark correct answer(s)
- Support optional question image uploads
- Support optional YouTube video URL per question
- Attempt quizzes with type-specific answer input
- Auto-evaluate answers and calculate score
- View result summary and per-question review

## Installation Steps

1. Clone the repository:
```bash
git clone <your-repo-url>
cd quiz-system
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

## Database Setup

1. Create a MySQL database (example: `laravel_quiz`).
2. Update `.env` with your DB credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_quiz
DB_USERNAME=root
DB_PASSWORD=
```

## Migration Commands

Run all migrations:

```bash
php artisan migrate
```

If you need a fresh start:

```bash
php artisan migrate:fresh
```

## Storage Link Command

Create symbolic link for public access to uploaded question images:

```bash
php artisan storage:link
```

## Running Project

Start Laravel development server:

```bash
php artisan serve
```

Open in browser:

```text
http://127.0.0.1:8000
```

## Supported Question Types

- `true_false`
- `single_choice`
- `multiple_choice`
- `number`
- `text`

## Folder Structure

```text
quiz-system/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── Services/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   └── views/
│       ├── questions/
│       └── quizzes/
├── routes/
│   └── web.php
├── storage/
└── .env
```

## Technologies Used

- Laravel
- Blade
- Bootstrap
- MySQL

## Assumptions

- This project currently uses server-rendered Blade views (no SPA frontend).
- Authentication/authorization is minimal or not fully implemented for role-based access.
- Quiz creation and attempt are intended for local/dev usage unless production hardening is added.
- Uploaded media files are stored using Laravel `public` disk.

## Future Improvements

- Add authentication and role-based access (Admin/Instructor/Student)
- Add quiz categories, difficulty levels, and tags
- Add timer support and attempt limits
- Add pagination and filters for large quiz sets
- Add richer validation and UX hints for question creation
- Add analytics dashboard for quiz performance
- Add API endpoints and automated test coverage expansion
- Add import/export for quizzes (CSV/JSON)

