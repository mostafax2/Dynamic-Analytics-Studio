# Changelog — Enterprise Analytics Suite

All notable changes to this package are documented in this file.
Format follows [Keep a Changelog](https://keepachangelog.com/).

---

## [1.0.0] — 2026-06-09

### Added
- **Auto Install Command** (`php artisan analytics-suite:install`) — zero-configuration setup
- **Model Detection Engine** — auto-discovers `App\Models`, `Modules/*`, `Packages/*`
- **Dashboard Builder** — drag & drop, resize, GridStack-powered, responsive
- **Widget Engine** — 15 widget types: KPI Card, Stats Card, Data Table, Bar/Line/Area/Pie/Donut/Gauge Charts, Progress, Leaderboard, Comparison, Growth, Trend, Custom
- **Widget Marketplace** — `AnalyticsSuite::registerWidget(MyWidget::class)`
- **Report Builder** — visual DSL builder with full filter/aggregation/join support
- **Analytics Engine** — integrates `dynamic-hybrid-reporting-engine` as core execution layer
- **Security Layer** — 14 enterprise permissions, RLS policies, branch/department/tenant scoping, Sanctum integration
- **Cache Layer** — Redis-backed with pattern invalidation, configurable TTL per resource type
- **Export System** — PDF (DomPDF), Excel (PhpSpreadsheet), CSV, JSON; RTL + Arabic support
- **Scheduling System** — Daily/Weekly/Monthly/Quarterly/Yearly; Email/Webhook/Notification delivery
- **Vue 3 Frontend** — Pinia stores, TypeScript types, ApexCharts, dark mode, RTL support
- **Multi-Tenant Ready** — single-db and multi-db strategies, tenant isolation flag
- **11 Database Migrations** — all `as_` prefixed, zero collision with host app
- **3 Artisan Commands** — `install`, `sync-permissions`, `detect-models`
- **Full Test Suite** — Unit + Feature + Integration with Orchestra Testbench + Pest

### Security
- All API routes protected by `auth:sanctum` middleware
- Permission enforcement configurable via `analytics-suite.security.enforce_permissions`
- Row-Level Security policies per model, scoped by user attributes
- Public dashboard share tokens with configurable expiry

---

## [Unreleased]
- MongoDB widget data source direct queries
- Real-time widget updates via Laravel Broadcasting / Echo
- Dashboard snapshot comparison (time-travel)
- AI-generated widget suggestions from detected models
- Embedded iframe widget support
