# FlowTimeUp Multi-Agent System (MAS) Configuration

## 👥 Agent Roles & Models

| Role | Name | Primary Model | Responsibility |
| :--- | :--- | :--- | :--- |
| **Orchestrator** | **Claw (Manager)** | `google/gemini-3-flash-preview` | Task delegation, MAS coordination, Final reviews. |
| **Frontend Expert** | **Agent-Vue** | `claude-3.5-sonnet` | Vue 3, Inertia.js, TailwindCSS, Shadcn-Vue, TypeScript alignment. |
| **Backend Architect** | **Agent-Laravel** | `gpt-4o` | Laravel 11, Eloquent, Controllers, API Design, Security, Migrations. |
| **Quality Guardian** | **Agent-QA** | `claude-3-opus` | Pest Tests, Bug Hunting, Edge-case validation, Documentation review. |

## 🛠 Project Stack Context (V1.2 Audit)
- **Backend:** Laravel 11.31 + PHP 8.2+
- **Frontend:** Vue 3.4 (Inertia.js 2.0) + TypeScript + Shadcn-Vue
- **Styling:** TailwindCSS
- **State:** Pinia + VueUse
- **Testing:** Pest PHP
- **Infrastructure:** Docker/Sail support detected.

## 📈 Active Tasks (Current Focus)
1. **[PENDING]** Finalize Report Generation logic (Backend).
2. **[PENDING]** Update Report UI (Frontend) with Shadcn-Vue components.
3. **[PENDING]** Full Test Coverage for TimeSessionController (QA).

## 🚦 Workflow Policy
- **Branching:** `feature/agent-<role>-<task-name>`
- **Handoffs:** Use `sessions_yield` to pass results back to Manager.
- **Reviews:** Manager MUST ASK user for approval before any merge to `main`.
- **Mergeless by default:** Agents are strictly forbidden from merging to `main` without explicit human-in-the-loop (HITL) confirmation.
- **Production Safety:** `main` branch is considered PRODUCTION. No direct pushes or automated merges.

## 🛡️ Anti-Guessing Policy (Mandatory)
- **ZERO GUESSING:** Agents are strictly forbidden from assuming database column names, variable names, or file structures.
- **VERIFY FIRST:** Before writing any migration, seeder, or controller logic, the agent MUST run `grep`, `cat` on migrations, or `php artisan db:show` to confirm the exact schema.
- **PLAN FIRST:** Agents must provide a short text-based plan (listing confirmed fields/files) before executing code changes.
- **CROSS-DB SAFETY:** Always use raw SQL subqueries for data updates in migrations to ensure compatibility across MySQL, PostgreSQL, and SQLite.
