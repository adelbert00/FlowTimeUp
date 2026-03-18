# FlowTimeUp Multi-Agent System (MAS) Configuration

## 👥 Agent Roles & Models (Updated for Rate-Limit Safety)

| Role | Name | Primary Model | Backup Model | Responsibility |
| :--- | :--- | :--- | :--- | :--- |
| **Orchestrator** | **Claw (Manager)** | `google/gemini-3-flash-preview` | `google/gemini-1.5-flash` | Delegation, Coordination, Final reviews. |
| **Frontend Expert** | **Agent-Vue** | `google/gemini-1.5-pro` | `google/gemini-1.5-flash` | Vue 3, Shadcn, TS alignment. |
| **Backend Architect** | **Agent-Laravel** | `google/gemini-3-flash-preview` | `google/gemini-1.5-flash` | Laravel 11, SQL, API, Migrations. |
| **Quality Guardian** | **Agent-QA** | `google/gemini-1.5-flash` | N/A | Pest Tests, Edge-cases (Low-cost). |

## 💰 Token & Rate-Limit Guardrails
1. **Flash-First Strategy:** Always use `flash` models for research, repetitive coding, or simple test writing.
2. **Pro-on-Demand:** Use `gemini-1.5-pro` ONLY for complex UI refactoring or debugging deep logic issues.
3. **Context Flushing:** Reset session context (`/compact` or branch switch) after every completed Sprint to avoid "bloated" history costs.
4. **Summary Requirement:** Agents must provide a concise summary before yielding, allowing the Manager to drop heavy intermediate logs.

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
