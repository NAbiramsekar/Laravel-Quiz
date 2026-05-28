# AI Usage Report - Laravel Dynamic Quiz System

## Purpose

This document records how AI tools were used during development of this Laravel assignment project, what was generated, what was manually adjusted, and how outputs were validated.

## How AI Tools Were Used

AI was used as an engineering assistant for:

- UI improvement recommendations and Blade view refactoring
- Bug triage for inconsistent option-saving behavior
- Documentation generation (`README.md`, `ARCHITECTURE.md`, this file)
- Route and controller-level fixes
- Architecture review and maintainability recommendations

AI was not used as an autopilot. Proposed changes were reviewed, selectively applied, and verified in the local project context.

## Prompts Used (Representative)

Below are representative prompt themes used during the assignment:

- "Improve the Create Quiz UI and make it clean and neat."
- "Explain clearly how question creation and options work, with examples."
- "Options are sometimes working and sometimes not; check like a senior engineer and fix bugs."
- "Set root URL to open quizzes page directly instead of Laravel welcome page."
- "Generate professional project documentation (README/ARCHITECTURE)."
- "Review architecture and suggest practical improvements without enterprise complexity."

These prompts drove iterative improvement rather than one-shot generation.

## What Code Was Manually Modified

Manual edits and decisions were applied on top of AI-generated suggestions, including:

- Final structure/content acceptance for Blade UI updates
- Route behavior decision for `/` to redirect to quiz index
- Validation flow refinements for question option consistency
- Practical scope control (avoiding unnecessary abstraction layers during assignment stage)
- Documentation tone, section ordering, and project-specific assumptions

In short: AI assisted with draft implementation and analysis, while final project direction and acceptance were manually controlled.

## Debugging Assistance

AI supported debugging by:

- Tracing intermittent option correctness issues across Blade form + controller processing
- Identifying index/type mismatch edge cases for `correct_options[]`
- Recommending deterministic normalization of options and correctness indexes
- Suggesting type-aware validations to prevent invalid question configurations

Bug-fix process remained evidence-driven: inspect code, reproduce likely failure paths, patch, then run syntax checks.

## Architecture Discussions

AI was used to discuss and document architecture tradeoffs:

- Why a single `questions` table is pragmatic for this scope
- Why evaluation logic belongs in a service layer
- How to extend for new question types with controlled change points
- How to keep controllers thin and reduce coupling
- Practical scalability and security improvements for next iterations

The goal was maintainable Laravel architecture, not enterprise overengineering.

## Validation of Generated Code

Generated/edited code was validated through:

- Local file inspection before and after patching
- PHP syntax checks (`php -l`) on modified files
- Behavioral verification through expected UI flows
- Controller-level validation coverage checks for key form inputs

Known limitation: full automated test suite expansion is still pending.

## Learning Outcomes

Key takeaways from AI-assisted development:

- AI is most effective when used iteratively with narrow, testable tasks.
- Generated code still requires strong human review for edge cases and domain rules.
- Validation and deterministic input normalization are critical for dynamic forms.
- Architecture quality improves when business logic is centralized (service layer) and controllers stay orchestration-focused.
- Documentation quality can be significantly accelerated with AI, but must be curated for project truthfulness.

## Professional Integrity Note

This project used AI as a productivity and review assistant. Final responsibility for design decisions, correctness, and deliverables remains with the me.
