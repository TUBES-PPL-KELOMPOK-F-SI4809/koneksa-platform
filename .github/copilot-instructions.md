# GitHub Copilot Instructions — Koneksa Platform

All Copilot suggestions and interactions must strictly adhere to the project constitution in `AGENTS.md`:

1. **Pre-flight Reading**: Consult `docs/` and skills in `.agents/skills/` and `.antigravity/skills/` before proposing code changes:
   - Business Logic: `docs/PRD/PRD-KONEKSA-PLATFORM.md`
   - ERD: `docs/analysis/RANCANGAN_ERD_KONEKSA.md`
   - Architecture: `docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md`
   - Clean Code: `docs/guidelines/CLEAN-CODE.md`
   - Design: `docs/design-system/BASELINE-DESIGN.md` and `docs/design-system/DESIGN-TOKENS.md`

2. **Architecture**: Thin controllers, dedicated form requests in `app/Http/Requests/{Feature}/`, and business logic in `app/Services/{Feature}/`.

3. **No Over-Engineering (KISS & YAGNI)**: Implement the simplest, cleanest solution. Do not create unnecessary abstractions (no generic repository patterns over Eloquent, no single-implementation interfaces, no speculative future-proofing).

4. **Check Installed Dependencies First**: Check `composer.json` and `package.json` before writing custom functions or adding packages. Leverage Laravel's built-in tools (`Str`, `Arr`, `Carbon`, `Collection`) and Vue/Inertia helpers (`useForm`).

5. **Database Integrity**: Never modify existing migration files. Always create new migrations for schema adjustments.

6. **Testing Mandatory**: Write comprehensive unit/feature tests for every feature or logic update. Tests must pass locally via `php artisan test`.

7. **UI Components**: Check `shadcn-ui` before creating custom components from scratch.

