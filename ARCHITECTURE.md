# Architecture - Laravel Dynamic Quiz System

## Overview

This project follows a server-rendered Laravel architecture with Blade views, Eloquent models, and a focused service layer for quiz evaluation. The primary goal is maintainability: keep domain logic centralized, data model stable, and feature growth predictable.

## Database Design Decisions

The schema is organized around core quiz entities:

- `quizzes`: quiz metadata (`title`, `description`)
- `questions`: question prompt + type + media + marks
- `options`: possible answers and correctness flags
- `attempts`: one quiz submission event with aggregate score
- `answers`: per-question submitted answer for an attempt

Design principles applied:

- Normalize by domain entity (quiz, question, option, attempt, answer).
- Keep question metadata in one place (`questions`) for simpler querying.
- Store user submissions (`answers`) separately from question definitions to preserve historical attempt integrity.
- Use foreign keys to protect relational consistency.

## Why a Single `questions` Table Is Used

A single `questions` table supports all question types using a shared schema (`type`, `question_text`, `marks`, optional media fields). This is intentional because:

- Most fields are common across types.
- It avoids table explosion (`true_false_questions`, `number_questions`, etc.).
- Querying quiz content remains straightforward.
- Adding a new type is mostly behavioral (service + validation), not structural.

Type-specific behavior is handled in evaluation and validation, not via separate tables.

## Why a Service Layer Is Used

`App\Services\QuizEvaluator` encapsulates normalization and scoring logic. This keeps controllers thin and prevents business rules from being duplicated across HTTP endpoints.

Benefits:

- Single source of truth for grading behavior.
- Easier unit testing of domain rules.
- Lower risk of controller bloat.
- Safe extension point for new evaluation strategies.

## Evaluation Architecture

Evaluation is split into clear stages:

1. Input validation (`QuizController@submitAttempt`)
2. Answer normalization (`QuizEvaluator::normalizeAnswer`)
3. Type-based scoring (`QuizEvaluator::evaluateQuestion`)
4. Attempt + answers persistence in DB transaction
5. Result projection/review generation

This pattern ensures deterministic scoring and transactional consistency.

## Eloquent Relationships

Core relationships:

- `Quiz` hasMany `Question`
- `Question` belongsTo `Quiz`
- `Question` hasMany `Option`
- `Option` belongsTo `Question`
- `Attempt` belongsTo `Quiz`
- `Attempt` hasMany `Answer`
- `Answer` belongsTo `Attempt`
- `Answer` belongsTo `Question`

These relationships support eager loading for attempt and result views while keeping domain traversal explicit.

## Separation of Concerns

The architecture separates responsibilities by layer:

- **Controllers**: request validation, orchestration, HTTP responses
- **Models**: persistence and relationships
- **Services**: quiz domain rules (normalization + scoring)
- **Views (Blade)**: rendering/forms/UI concerns

This boundary design improves readability and makes refactoring safer.

## Extensibility Approach

The system is designed for additive change:

- Add a new question type by extending:
  - question type validation (`QuestionController`)
  - creation constraints for options/correctness
  - evaluator normalization + scoring match arms
  - attempt view input renderer for that type
- Existing schema can usually remain unchanged.

This minimizes migration risk and allows incremental evolution.

## Media Handling

Question media is optional and decoupled from scoring:

- Image uploads are stored via Laravel filesystem (`public` disk).
- Questions store path references, not binary payloads.
- `php artisan storage:link` exposes assets safely to public web path.
- Video is stored as URL metadata and rendered in view layer.

This keeps DB lean and enables switching storage backends later (local, S3, etc.).

## Scalability Considerations

Current architecture is suitable for small-to-medium workloads and can scale with targeted improvements:

- **Read scalability**
  - Eager loading avoids N+1 queries.
  - Add DB indexes on FK and lookup fields (`quiz_id`, `question_id`, `attempt_id`).
- **Write consistency**
  - Attempt submission uses DB transactions for atomic persistence.
- **Horizontal scaling readiness**
  - Stateless request handling fits load-balanced environments.
- **Future optimization opportunities**
  - Caching quiz payloads
  - Queued analytics calculations
  - Pagination for large quiz repositories
  - Archival strategy for old attempts

## Future Support for New Question Types

Recommended strategy for new types (example: ordering, matching, matrix):

1. Add new `type` enum value in validation.
2. Extend question authoring UI for type-specific input.
3. Represent answer key using `options` or dedicated metadata field if complexity grows.
4. Add normalization + evaluation methods in `QuizEvaluator`.
5. Add integration tests for scoring edge cases.

If future types require complex structures, introduce a dedicated `question_meta` JSON column or type-specific configuration table while keeping the core `questions` table intact.

## Maintainability Summary

The project favors clean architecture through:

- Stable, normalized core entities
- Thin controllers
- Centralized grading logic in a service
- Transactional write boundaries
- Clear separation between domain rules and presentation

This balance keeps implementation practical now while allowing disciplined growth over time.
