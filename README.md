<div align="center">

<img src="https://raw.githubusercontent.com/mostafax2/Dynamic-Analytics-Studio/main/.github/banner.png" alt="Enterprise Analytics Suite" width="100%" />

# Enterprise Analytics Suite

**A full-stack Business Intelligence platform built as a Laravel package**

[![Laravel](https://img.shields.io/badge/Laravel-12+-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php)](https://php.net)
[![Vue](https://img.shields.io/badge/Vue-3.x-4FC08D?style=flat-square&logo=vue.js)](https://vuejs.org)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)
[![Version](https://img.shields.io/badge/Version-1.0.0-blue?style=flat-square)](CHANGELOG.md)

*Power BI · Grafana · Metabase · Tableau — purpose-built for Laravel*

**Author:** Mostafa Elbayyar — [mostafa.m.elbiar2@gmail.com](mailto:mostafa.m.elbiar2@gmail.com)  
**GitHub:** [github.com/mostafax2/Dynamic-Analytics-Studio](https://github.com/mostafax2/Dynamic-Analytics-Studio)

</div>

---

## Table of Contents

- [Overview](#-overview)
- [Requirements](#-requirements)
- [Quick Install](#-quick-install)
- [Detailed Setup](#-detailed-setup)
- [Dashboard Builder](#-dashboard-builder)
- [Report Builder](#-report-builder)
- [Widget Engine](#-widget-engine)
- [Analytics Engine](#-analytics-engine)
- [Security](#-security)
- [Export System](#-export-system)
- [Report Scheduling](#-report-scheduling)
- [The Facade](#-the-facade)
- [API Reference](#-api-reference)
- [Vue 3 Frontend](#-vue-3-frontend)
- [Custom Widgets](#-custom-widgets)
- [Multi-Tenancy](#-multi-tenancy)
- [Testing](#-testing)
- [Artisan Commands](#-artisan-commands)
- [Folder Structure](#-folder-structure)
- [Config Reference](#-config-reference)
- [FAQ](#-faq)

---

## 🌟 Overview

**Enterprise Analytics Suite** is a complete Laravel package that turns any Laravel application into a full BI (Business Intelligence) platform in a single step.

### What makes it stand out?

| Feature | Description |
|---|---|
| 🤖 **Auto-Detection** | Automatically discovers all application models (`App\Models` + `Modules/*`) and generates default dashboards and widgets |
| 📊 **Dashboard Builder** | Full drag-and-drop interface powered by GridStack |
| 📋 **Report Builder** | Visual no-code report builder — no SQL required |
| 🔢 **15 Widget Types** | KPI cards, charts, tables, gauges, leaderboards, progress bars, and more |
| 🔌 **Widget Marketplace** | Register any custom widget with a single line |
| 🔒 **Enterprise Security** | 14 permissions, Row-Level Security, Sanctum integration, tenant isolation |
| ⚡ **Redis Cache** | Smart caching with automatic invalidation at every layer |
| 📥 **Export System** | PDF, Excel, CSV, JSON — with full RTL and Arabic support |
| ⏰ **Report Scheduling** | Daily/weekly/monthly delivery via Email or Webhook |
| 🌐 **Multi-Tenant** | Ready for multi-tenant applications |
| 🎨 **Vue 3 SPA** | Modern UI with Dark Mode and full RTL support |

### Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│                   Enterprise Analytics Suite                      │
│                                                                    │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐              │
│  │  Dashboard  │  │   Report    │  │   Widget    │              │
│  │   Builder   │  │   Builder   │  │   Engine    │              │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘              │
│         │                │                │                      │
│  ┌──────▼────────────────▼────────────────▼──────┐              │
│  │              Analytics Engine                  │              │
│  │         (Security · Cache · Events)            │              │
│  └──────────────────────┬─────────────────────────┘              │
│                         │                                        │
│  ┌──────────────────────▼─────────────────────────┐              │
│  │   Dynamic Hybrid Reporting Engine (DSL Layer)  │              │
│  └──────────────────────┬─────────────────────────┘              │
│                         │                                        │
│        ┌────────────────┼────────────────┐                       │
│        ▼                ▼                ▼                       │
│     MySQL           MongoDB           Redis                      │
└──────────────────────────────────────────────────────────────────┘
```

---

## 📋 Requirements

### Required

| Package | Version |
|---|---|
| PHP | `^8.3` |
| Laravel | `^11.0` or `^12.0` |
| Redis | Any recent version |
| `mostafax/dynamic-hybrid-reporting-engine` | `^1.0` |
| `laravel/sanctum` | `^4.0` |

### Optional (enable as needed)

| Package | Purpose |
|---|---|
| `phpoffice/phpspreadsheet` | Excel export |
| `barryvdh/laravel-dompdf` | PDF export |
| `league/csv` | Enhanced CSV streaming |
| `spatie/laravel-permission` | Role and permission management |
| `mongodb/laravel-mongodb` | MongoDB data source |

---

## ⚡ Quick Install

```bash
# 1. Install the package
composer require mostafax/enterprise-analytics-suite

# 2. Run the smart installer (does everything automatically)
php artisan analytics-suite:install

# 3. Start the queue worker (for async exports)
php artisan queue:work
```

**Done!** — The API is now available at `/api/analytics/...`

---

## 🔧 Detailed Setup

### Step 1 — Install the Package

```bash
composer require mostafax/enterprise-analytics-suite
```

If you are using a **Local Path Repository** (for local development):

```json
// project's composer.json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/mostafax/analytics-suite"
        }
    ],
    "require": {
        "mostafax/enterprise-analytics-suite": "@dev"
    }
}
```

Then:
```bash
composer update mostafax/enterprise-analytics-suite
```

---

### Step 2 — Publish Assets

```bash
# Publish only the config file
php artisan vendor:publish --tag=analytics-suite-config

# Publish only the migrations
php artisan vendor:publish --tag=analytics-suite-migrations

# Publish Vue assets
php artisan vendor:publish --tag=analytics-suite-assets

# Or publish everything at once
php artisan analytics-suite:install
```

---

### Step 3 — Database Setup

```bash
php artisan migrate
```

Tables created (all prefixed with `as_`):

| Table | Purpose |
|---|---|
| `as_dashboards` | Dashboard definitions |
| `as_widgets` | Widget definitions |
| `as_report_templates` | Report templates |
| `as_scheduled_reports` | Scheduled report jobs |
| `as_export_jobs` | Export job queue |
| `as_permissions` | Permission records |
| `as_rls_policies` | Row-Level Security policies |
| `as_dashboard_shares` | Dashboard sharing grants |
| `as_widget_snapshots` | Cached widget data snapshots |
| `as_detected_models` | Auto-detected model registry |
| `as_analytics_events` | Event audit log |

---

### Step 4 — Environment Variables

Add the following to your `.env` file:

```env
# Cache
ANALYTICS_CACHE_DRIVER=redis
ANALYTICS_CACHE_ENABLED=true
ANALYTICS_CACHE_DASHBOARD_TTL=300
ANALYTICS_CACHE_WIDGET_TTL=180
ANALYTICS_CACHE_REPORT_TTL=600

# Security
ANALYTICS_ENFORCE_PERMISSIONS=true
ANALYTICS_RLS_ENABLED=true
ANALYTICS_TENANT_ISOLATION=false

# Export
ANALYTICS_EXPORT_DISK=local
ANALYTICS_MAX_EXPORT_ROWS=100000

# UI
ANALYTICS_THEME=light
ANALYTICS_RTL=false
ANALYTICS_LOCALE=en

# Scheduling
ANALYTICS_SCHEDULING_ENABLED=true
ANALYTICS_FROM_EMAIL=noreply@yourdomain.com
ANALYTICS_FROM_NAME="Analytics Suite"

# MongoDB (optional)
ANALYTICS_MONGO_ENABLED=false
ANALYTICS_MONGO_CONNECTION=mongodb
```

---

### Step 5 — Sanctum Setup

Ensure Laravel Sanctum is installed and configured:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

In `bootstrap/app.php` (Laravel 12):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->statefulApi();
})
```

---

### Step 6 — Queue Setup

For async exports and scheduling:

```env
QUEUE_CONNECTION=redis
```

```bash
php artisan queue:work --queue=default
```

Or with Supervisor:
```ini
[program:analytics-queue]
command=php /var/www/html/artisan queue:work redis --queue=default --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
```

---

## 📊 Dashboard Builder

### Via Facade

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

// Create a dashboard
$dashboard = AnalyticsSuite::dashboards()->create([
    'name'        => 'Sales Dashboard',
    'description' => 'Main sales overview dashboard',
    'settings'    => ['theme' => 'dark'],
], auth()->id());

// Retrieve a dashboard
$dashboard = AnalyticsSuite::dashboards()->find(1);

// Update layout (after drag & drop)
AnalyticsSuite::dashboards()->updateLayout(1, [
    ['widget_id' => 5, 'x' => 0, 'y' => 0, 'w' => 6, 'h' => 3],
    ['widget_id' => 6, 'x' => 6, 'y' => 0, 'w' => 6, 'h' => 3],
]);

// Enable public sharing
$dashboard = AnalyticsSuite::dashboards()->enablePublicShare(1, expiryDays: 30);
echo $dashboard->publicToken; // public access token
```

### Via HTTP

```bash
# Create a dashboard
curl -X POST /api/analytics/dashboards \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "My Dashboard", "description": "Overview dashboard"}'

# List dashboards
curl -X GET /api/analytics/dashboards \
  -H "Authorization: Bearer {token}"

# Clone a dashboard
curl -X POST /api/analytics/dashboards/1/clone \
  -H "Authorization: Bearer {token}" \
  -d '{"name": "Copy of Dashboard"}'

# Update layout
curl -X POST /api/analytics/dashboards/1/layout \
  -H "Authorization: Bearer {token}" \
  -d '{
    "layout": [
      {"widget_id": 5, "x": 0, "y": 0, "w": 6, "h": 3},
      {"widget_id": 6, "x": 6, "y": 0, "w": 6, "h": 3}
    ]
  }'

# Enable public share
curl -X POST /api/analytics/dashboards/1/share \
  -H "Authorization: Bearer {token}" \
  -d '{"expiry_days": 7}'
```

### Import / Export

```bash
# Export dashboard definition (JSON)
curl -X GET /api/analytics/dashboards/1/export \
  -H "Authorization: Bearer {token}"

# Import a dashboard
curl -X POST /api/analytics/dashboards/import \
  -H "Authorization: Bearer {token}" \
  -d '{"definition": {...}}'
```

---

## 📋 Report Builder

### Build a Report Programmatically

```php
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\ReportTemplateModel;

$template = ReportTemplateModel::create([
    'name'         => 'Monthly Sales Report',
    'description'  => 'Total sales aggregated by month',
    'data_source'  => 'orders',
    'source_type'  => 'mysql',
    'columns'      => [
        ['field' => 'id'],
        ['field' => 'total', 'alias' => 'amount'],
        ['field' => 'created_at', 'alias' => 'date'],
    ],
    'filters'      => [
        ['column' => 'status', 'operator' => 'eq',  'value' => 'completed'],
        ['column' => 'total',  'operator' => 'gte', 'value' => 100],
    ],
    'aggregations' => [
        ['function' => 'sum',   'column' => 'total', 'alias' => 'revenue'],
        ['function' => 'count', 'column' => 'id',    'alias' => 'orders_count'],
    ],
    'group_by'    => ['DATE_FORMAT(created_at, "%Y-%m")'],
    'order_by'    => [['column' => 'created_at', 'direction' => 'desc']],
    'is_template' => true,
    'category'    => 'sales',
    'tags'        => ['monthly', 'revenue'],
    'created_by'  => auth()->id(),
]);
```

### Run a Report

```php
use Mostafax\ReportingEngine\Core\Engine\ReportEngine;

$engine   = app(ReportEngine::class);
$template = ReportTemplateModel::find(1);

$result = $engine->run(
    $template->toDslDefinition(),
    auth()->user()->roles ?? []
);

foreach ($result->rows as $row) {
    echo $row['revenue'] . ' | ' . $row['orders_count'];
}
```

### Via HTTP

```bash
# Run a report with additional filters
curl -X POST /api/analytics/reports/1/run \
  -H "Authorization: Bearer {token}" \
  -d '{
    "filters": [
      {"column": "created_at", "operator": "gte", "value": "2026-01-01"}
    ],
    "pagination": {"page": 1, "per_page": 50}
  }'
```

### Available Filter Operators

| Operator | Description | Example |
|---|---|---|
| `eq` | Equal | `"operator": "eq", "value": "active"` |
| `neq` | Not equal | `"operator": "neq", "value": "deleted"` |
| `gt` | Greater than | `"operator": "gt", "value": 100` |
| `gte` | Greater than or equal | `"operator": "gte", "value": 0` |
| `lt` | Less than | `"operator": "lt", "value": 1000` |
| `lte` | Less than or equal | `"operator": "lte", "value": 50` |
| `between` | Between two values | `"operator": "between", "value": 10, "value2": 100` |
| `in` | In a list | `"operator": "in", "value": ["a","b","c"]` |
| `not_in` | Not in a list | `"operator": "not_in", "value": ["x","y"]` |
| `like` | Contains (LIKE) | `"operator": "like", "value": "%search%"` |
| `starts_with` | Starts with | `"operator": "starts_with", "value": "EMP"` |
| `ends_with` | Ends with | `"operator": "ends_with", "value": "@gmail.com"` |
| `contains` | Contains | `"operator": "contains", "value": "cairo"` |
| `null` | Is null | `"operator": "null"` |
| `not_null` | Is not null | `"operator": "not_null"` |

### Aggregation Functions

| Function | Description |
|---|---|
| `count` | Row count |
| `sum` | Sum of values |
| `avg` | Average |
| `min` | Minimum value |
| `max` | Maximum value |
| `count_distinct` | Count of distinct values |

---

## 🔢 Widget Engine

### Create a Widget Programmatically

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

$widget = AnalyticsSuite::widgets()->create([
    'dashboard_id'     => 1,
    'type'             => 'kpi_card',
    'title'            => 'Total Users',
    'config'           => [
        'data_source' => 'users',
        'aggregation' => 'count',
        'column'      => 'id',
    ],
    'position'         => ['x' => 0, 'y' => 0, 'w' => 3, 'h' => 2],
    'refresh_interval' => 300,   // seconds — 0 means manual
    'cache_enabled'    => true,
    'cache_ttl'        => 180,
], auth()->id());
```

### Widget Types

#### 1. `kpi_card` — KPI Card
```json
{
    "type": "kpi_card",
    "config": {
        "data_source": "orders",
        "aggregation": "sum",
        "column": "total",
        "label": "Total Revenue",
        "show_trend": true
    }
}
```

#### 2. `stats_card` — Multi-Stat Card
```json
{
    "type": "stats_card",
    "config": {
        "data_source": "users",
        "columns": ["id", "email", "created_at"]
    }
}
```

#### 3. `data_table` — Data Table
```json
{
    "type": "data_table",
    "config": {
        "data_source": "employees",
        "columns": ["name", "department", "salary", "hire_date"],
        "order_by": [{"column": "salary", "direction": "desc"}],
        "limit": 20
    }
}
```

#### 4. `bar_chart` — Bar Chart
```json
{
    "type": "bar_chart",
    "config": {
        "data_source": "orders",
        "label_column": "month",
        "value_column": "revenue",
        "aggregation": "sum",
        "column": "total",
        "group_by": "DATE_FORMAT(created_at, '%Y-%m')"
    }
}
```

#### 5. `line_chart` — Line Chart
```json
{
    "type": "line_chart",
    "config": {
        "data_source": "users",
        "label_column": "period",
        "value_column": "count",
        "aggregation": "count",
        "column": "id",
        "group_by": "created_at",
        "interval": "month"
    }
}
```

#### 6. `pie_chart` / `donut_chart` — Pie or Donut Chart
```json
{
    "type": "pie_chart",
    "config": {
        "data_source": "orders",
        "label_column": "status",
        "value_column": "count",
        "aggregation": "count",
        "column": "id"
    }
}
```

#### 7. `gauge_chart` — Gauge
```json
{
    "type": "gauge_chart",
    "config": {
        "data_source": "kpis",
        "aggregation": "sum",
        "column": "value",
        "min": 0,
        "max": 1000000,
        "label": "Sales Target"
    }
}
```

#### 8. `progress` — Progress Bar
```json
{
    "type": "progress",
    "config": {
        "data_source": "targets",
        "label_column": "target_name",
        "value_column": "achieved",
        "max": 100
    }
}
```

#### 9. `leaderboard` — Top Items Leaderboard
```json
{
    "type": "leaderboard",
    "config": {
        "data_source": "employees",
        "label_column": "name",
        "value_column": "sales_count"
    }
}
```

#### 10. `area_chart` — Area Chart
```json
{
    "type": "area_chart",
    "config": {
        "data_source": "orders",
        "label_column": "date",
        "value_column": "revenue",
        "aggregation": "sum",
        "column": "total",
        "interval": "day"
    }
}
```

#### 11. `scatter_chart` — Scatter Plot
```json
{
    "type": "scatter_chart",
    "config": {
        "data_source": "products",
        "x_column": "price",
        "y_column": "units_sold"
    }
}
```

#### 12. `heatmap` — Heatmap
```json
{
    "type": "heatmap",
    "config": {
        "data_source": "events",
        "x_column": "hour",
        "y_column": "day_of_week",
        "value_column": "count"
    }
}
```

#### 13. `funnel` — Funnel Chart
```json
{
    "type": "funnel",
    "config": {
        "data_source": "pipeline",
        "label_column": "stage",
        "value_column": "count"
    }
}
```

#### 14. `timeline` — Timeline
```json
{
    "type": "timeline",
    "config": {
        "data_source": "events",
        "date_column": "occurred_at",
        "label_column": "description",
        "limit": 10
    }
}
```

#### 15. `metric_comparison` — Period Comparison
```json
{
    "type": "metric_comparison",
    "config": {
        "data_source": "orders",
        "aggregation": "sum",
        "column": "total",
        "period_1": "current_month",
        "period_2": "last_month"
    }
}
```

### Refresh Widget Data

```php
// Direct refresh
AnalyticsSuite::engine()->executeWidget(widgetId: 5);

// Invalidate cache and re-fetch
AnalyticsSuite::engine()->refreshCache(widgetId: 5);
```

```bash
# Via API
curl -X POST /api/analytics/dashboards/1/widgets/5/refresh \
  -H "Authorization: Bearer {token}"

# Fetch widget data with dynamic filters
curl -X GET /api/analytics/dashboards/1/widgets/5/data \
  -H "Authorization: Bearer {token}" \
  -d '{"filters": [{"column": "year", "operator": "eq", "value": 2026}]}'
```

---

## 🔬 Analytics Engine

### Auto-Detect Models

```php
// Discover all models
$models = AnalyticsSuite::detectModels();

foreach ($models as $model) {
    echo $model->name;           // User
    echo $model->table;          // users
    echo $model->module;         // App
    echo $model->primaryKey;     // id
    print_r($model->fillable);   // ['name', 'email', ...]
    print_r($model->columns);    // ['id', 'name', 'email', 'created_at', ...]
    print_r($model->relationships); // [['method'=>'posts', 'type'=>'hasMany', ...]]
}
```

### Compute Model Statistics

```php
$result = AnalyticsSuite::engine()->computeStats(\App\Models\User::class, [
    'date_column' => 'created_at',
]);

echo $result->stats['total'];  // 15430
echo $result->stats['today'];  // 23
print_r($result->trends);      // [['period' => '2026-01', 'count' => 1200], ...]
echo $result->fromCache;       // true
echo $result->executionMs;     // 12.5
```

### Execute All Dashboard Widgets

```php
$allWidgetData = AnalyticsSuite::engine()->executeDashboard(dashboardId: 1, params: [
    'filters' => [['column' => 'branch_id', 'operator' => 'eq', 'value' => 3]],
]);

foreach ($allWidgetData as $widgetData) {
    echo "Widget {$widgetData->widgetId}: " . count($widgetData->data) . " rows";
}
```

---

## 🔒 Security

### Available Permissions (14 total)

```php
// Reports
'view_reports'     // View and run reports
'create_reports'   // Create report templates
'edit_reports'     // Edit reports
'delete_reports'   // Delete reports
'export_reports'   // Export reports
'schedule_reports' // Schedule reports

// Dashboards
'view_dashboards'    // View dashboards and widget data
'create_dashboards'  // Create dashboards
'edit_dashboards'    // Edit dashboards and layouts
'delete_dashboards'  // Delete dashboards

// Widgets
'create_widgets'  // Add widgets
'edit_widgets'    // Edit widgets
'delete_widgets'  // Delete widgets

// Administration
'manage_analytics'  // Full admin permission
```

### Integration with Spatie Permission

```bash
# Create permissions and assign to a role
php artisan analytics-suite:sync-permissions --role=admin

# Assign to a specific role with a different guard
php artisan analytics-suite:sync-permissions --role=manager --guard=api
```

```php
use Spatie\Permission\Models\Role;
use Mostafax\AnalyticsSuite\Security\SecurityManager;

$role = Role::findByName('analyst');
foreach (SecurityManager::PERMISSIONS as $permission) {
    $role->givePermissionTo($permission);
}
```

### Manual Permission Checks

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

$security = AnalyticsSuite::security();

if ($security->can(auth()->user(), 'export_reports')) {
    // Export is allowed
}

// Check if the user has at least one permission
$security->canAny(auth()->user(), ['edit_reports', 'create_reports']);

// Automatically throw 403 if not permitted
$security->authorizeOr403(auth()->user(), 'delete_dashboards');
```

### Row-Level Security

```php
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\RlsPolicyModel;

RlsPolicyModel::create([
    'name'         => 'Branch Isolation',
    'model'        => \App\Models\Order::class,
    'column'       => 'branch_id',
    'scope'        => 'branch',
    'operator'     => '=',
    'value_source' => 'auth_user',   // reads value from the authenticated user
    'value_key'    => 'branch_id',   // user property to read
    'is_active'    => true,
]);
```

Now any report query on `orders` will automatically have `WHERE branch_id = {auth()->user()->branch_id}` appended.

---

## 📥 Export System

### Immediate Export

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

$exporter = AnalyticsSuite::exporter();

$result = $exporter->exportReport(reportId: 1, format: 'pdf', params: [
    'title'  => 'Sales Report',
    'rtl'    => false,
    'locale' => 'en',
]);

echo $result->downloadUrl; // temporary download URL
echo $result->rows;        // 1500
echo $result->sizeBytes;   // 245760
```

### Async Export (recommended for large data)

```php
$jobId = $exporter->queueExport(
    type:        'report',
    id:          1,
    format:      'excel',
    params:      ['title' => 'Sales Report'],
    notifyEmail: 'user@example.com'
);

echo "Job ID: {$jobId}";
```

```bash
# Track job status
curl -X GET /api/analytics/exports/status/42 \
  -H "Authorization: Bearer {token}"

# Response
{
    "id": 42,
    "status": "done",
    "filename": "sales-report_2026-06-09_120000.xlsx",
    "rows": 15430,
    "size": 1245678,
    "completed": "2026-06-09T12:00:45.000Z"
}
```

### RTL & Arabic Support

```php
// In config/analytics-suite.php
'export' => [
    'rtl'    => true,
    'locale' => 'ar',
],

// Or per-export
$result = $exporter->exportReport(1, 'pdf', [
    'title'  => 'Monthly Financial Report',
    'rtl'    => true,
    'locale' => 'ar',
]);
```

---

## ⏰ Report Scheduling

### Create a Scheduled Report

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

$scheduled = AnalyticsSuite::scheduler()->schedule([
    'report_id'        => 1,
    'name'             => 'Daily Sales Report',
    'frequency'        => 'daily',         // daily|weekly|monthly|quarterly|yearly
    'format'           => 'pdf',
    'delivery_methods' => ['email'],
    'recipients'       => [
        'manager@company.com',
        'cfo@company.com',
    ],
    'params' => [
        'title' => 'Sales Daily Report',
        'rtl'   => false,
    ],
]);

echo $scheduled->nextRunAt; // 2026-06-10 08:00:00
```

### Webhook Delivery

```php
$scheduled = AnalyticsSuite::scheduler()->schedule([
    'report_id'        => 3,
    'name'             => 'Weekly Report Notification',
    'frequency'        => 'weekly',
    'format'           => 'json',
    'delivery_methods' => ['webhook'],
    'webhook_url'      => 'https://your-app.com/webhooks/report-ready',
]);
```

Webhook payload:
```json
{
    "scheduled_report_id": 3,
    "report_name": "Weekly Report Notification",
    "format": "json",
    "download_url": "https://...",
    "rows": 2340,
    "generated_at": "2026-06-09T08:00:00Z"
}
```

### Pause / Resume / Cancel

```php
$scheduler = AnalyticsSuite::scheduler();

$scheduler->pause(id: 3);    // pause
$scheduler->resume(id: 3);   // resume
$scheduler->cancel(id: 3);   // permanent cancel
```

```bash
# Via API
curl -X POST   /api/analytics/schedules/3/pause  -H "Authorization: Bearer {token}"
curl -X POST   /api/analytics/schedules/3/resume -H "Authorization: Bearer {token}"
curl -X DELETE /api/analytics/schedules/3        -H "Authorization: Bearer {token}"
```

---

## 🎭 The Facade

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

// Core services
AnalyticsSuite::dashboards()   // DashboardService
AnalyticsSuite::widgets()      // WidgetService
AnalyticsSuite::engine()       // AnalyticsEngine
AnalyticsSuite::scheduler()    // ReportScheduler
AnalyticsSuite::exporter()     // ExportManager
AnalyticsSuite::detector()     // ModelDetectionEngine
AnalyticsSuite::security()     // SecurityManager
AnalyticsSuite::cache()        // AnalyticsCacheManager

// Widget Marketplace
AnalyticsSuite::registerWidget(MyCustomWidget::class);
AnalyticsSuite::getRegisteredWidgets(); // ['MyCustomWidget', ...]

// Model detection
AnalyticsSuite::detectModels(); // Collection<DetectedModelDTO>

// Package info
AnalyticsSuite::version(); // "1.0.0"
```

---

## 🌐 API Reference

### Authentication

All routes require a `Bearer Token` from Laravel Sanctum:

```bash
curl -X POST /sanctum/token \
  -d '{"email":"user@example.com","password":"secret","device_name":"api"}'
```

---

### Dashboards API

| Method | Endpoint | Description | Permission |
|---|---|---|---|
| `GET` | `/api/analytics/dashboards` | List dashboards | `view_dashboards` |
| `POST` | `/api/analytics/dashboards` | Create dashboard | `create_dashboards` |
| `GET` | `/api/analytics/dashboards/{id}` | Dashboard details | `view_dashboards` |
| `PUT` | `/api/analytics/dashboards/{id}` | Update dashboard | `edit_dashboards` |
| `DELETE` | `/api/analytics/dashboards/{id}` | Delete dashboard | `delete_dashboards` |
| `POST` | `/api/analytics/dashboards/{id}/clone` | Clone dashboard | `create_dashboards` |
| `POST` | `/api/analytics/dashboards/{id}/layout` | Update layout | `edit_dashboards` |
| `POST` | `/api/analytics/dashboards/{id}/share` | Enable public link | `edit_dashboards` |
| `DELETE` | `/api/analytics/dashboards/{id}/share` | Disable public link | `edit_dashboards` |
| `GET` | `/api/analytics/dashboards/{id}/export` | Export definition | `export_reports` |
| `POST` | `/api/analytics/dashboards/import` | Import dashboard | `create_dashboards` |
| `GET` | `/api/analytics/public/{token}` | View public dashboard | No auth required |

**POST /api/analytics/dashboards** — Body:
```json
{
    "name": "Sales Overview",
    "description": "Main sales dashboard",
    "settings": {"theme": "dark", "refresh_rate": 60},
    "is_public": false,
    "is_default": false
}
```

---

### Widgets API

| Method | Endpoint | Description | Permission |
|---|---|---|---|
| `GET` | `/api/analytics/dashboards/{id}/widgets` | List widgets | `view_dashboards` |
| `POST` | `/api/analytics/dashboards/{id}/widgets` | Add widget | `create_widgets` |
| `GET` | `/api/analytics/dashboards/{id}/widgets/{wid}` | Widget details | `view_dashboards` |
| `PUT` | `/api/analytics/dashboards/{id}/widgets/{wid}` | Update widget | `edit_widgets` |
| `DELETE` | `/api/analytics/dashboards/{id}/widgets/{wid}` | Delete widget | `delete_widgets` |
| `GET` | `/api/analytics/dashboards/{id}/widgets/{wid}/data` | Fetch data | `view_dashboards` |
| `POST` | `/api/analytics/dashboards/{id}/widgets/{wid}/refresh` | Refresh cache | `view_dashboards` |
| `GET` | `/api/analytics/widget-types` | Available widget types | — |

**POST /api/analytics/dashboards/1/widgets** — Body:
```json
{
    "type": "bar_chart",
    "title": "Monthly Sales",
    "config": {
        "data_source": "orders",
        "aggregation": "sum",
        "column": "total",
        "group_by": "created_at",
        "interval": "month",
        "label_column": "period",
        "value_column": "y"
    },
    "position": {"x": 0, "y": 0, "w": 6, "h": 4},
    "refresh_interval": 300,
    "cache_enabled": true,
    "cache_ttl": 180
}
```

---

### Reports API

| Method | Endpoint | Description | Permission |
|---|---|---|---|
| `GET` | `/api/analytics/reports` | List reports | `view_reports` |
| `POST` | `/api/analytics/reports` | Create report | `create_reports` |
| `GET` | `/api/analytics/reports/{id}` | Report details | `view_reports` |
| `PUT` | `/api/analytics/reports/{id}` | Update report | `edit_reports` |
| `DELETE` | `/api/analytics/reports/{id}` | Delete report | `delete_reports` |
| `POST` | `/api/analytics/reports/{id}/run` | Run report | `view_reports` |
| `POST` | `/api/analytics/reports/{id}/clone` | Clone report | `create_reports` |
| `GET` | `/api/analytics/reports/{id}/export` | Export JSON definition | `export_reports` |
| `POST` | `/api/analytics/reports/import` | Import report | `create_reports` |

**Query parameters for GET /api/analytics/reports:**
```
?search=sales          Search by name
?category=sales        Filter by category
?templates_only=true   Templates only
?per_page=20           Results per page
```

---

### Analytics API

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/analytics/analytics/modules` | Detected models |
| `GET` | `/api/analytics/analytics/stats?model=App\Models\User` | Model statistics |
| `GET` | `/api/analytics/analytics/summary/{module}` | Module summary |
| `GET` | `/api/analytics/analytics/dashboard/{id}/data` | All widget data for a dashboard |
| `POST` | `/api/analytics/analytics/cache/invalidate` | Invalidate cache |

---

### Export API

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/analytics/exports/queue` | Queue an export job |
| `GET` | `/api/analytics/exports/status/{id}` | Export job status |
| `GET` | `/api/analytics/exports/formats` | Available export formats |
| `GET` | `/api/analytics/exports/history` | Export history |

**POST /api/analytics/exports/queue** — Body:
```json
{
    "type": "report",
    "resource_id": 1,
    "format": "excel",
    "params": {
        "title": "Monthly Sales",
        "rtl": false
    },
    "notify_email": "user@example.com"
}
```

---

### Schedule API

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/analytics/schedules` | List schedules |
| `POST` | `/api/analytics/schedules` | Create schedule |
| `GET` | `/api/analytics/schedules/{id}` | Schedule details |
| `PUT` | `/api/analytics/schedules/{id}` | Update schedule |
| `DELETE` | `/api/analytics/schedules/{id}` | Cancel schedule |
| `POST` | `/api/analytics/schedules/{id}/pause` | Pause schedule |
| `POST` | `/api/analytics/schedules/{id}/resume` | Resume schedule |

**POST /api/analytics/schedules** — Body:
```json
{
    "report_id": 1,
    "name": "Weekly Sales Report",
    "frequency": "weekly",
    "format": "pdf",
    "delivery_methods": ["email", "webhook"],
    "recipients": ["manager@company.com"],
    "webhook_url": "https://your-app.com/webhooks/reports",
    "params": {"rtl": false, "title": "Weekly Report"}
}
```

---

## 🎨 Vue 3 Frontend

### Quick Setup

Add to `resources/js/app.js`:

```javascript
import { createApp }   from 'vue'
import { createPinia } from 'pinia'
import AnalyticsApp    from './analytics-suite/components/analytics/AnalyticsApp.vue'

const app = createApp(AnalyticsApp)
app.use(createPinia())
app.mount('#analytics-app')
```

Add to your Blade view:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- GridStack -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack/dist/gridstack.min.css">
    <script src="https://cdn.jsdelivr.net/npm/gridstack/dist/gridstack-all.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-slate-50">
    <div id="analytics-app"></div>
</body>
</html>
```

### Using a Single Component

```vue
<script setup>
import DashboardBuilder from './analytics-suite/components/dashboard/DashboardBuilder.vue'
// Pinia must be initialized at the application root
</script>

<template>
    <DashboardBuilder :dashboard-id="1" @back="goBack" />
</template>
```

### Pinia Store — Direct Usage

```typescript
import { useDashboardStore } from './analytics-suite/store/analytics'
import { useReportStore }    from './analytics-suite/store/analytics'
import { useAnalyticsStore } from './analytics-suite/store/analytics'

const dashStore      = useDashboardStore()
const reportStore    = useReportStore()
const analyticsStore = useAnalyticsStore()

// Fetch dashboards
await dashStore.fetchAll()
console.log(dashStore.items)        // Dashboard[]

// Run a report
const result = await reportStore.run(1, { filters: [] })
console.log(result.rows)            // Record[]

// Detect models
await analyticsStore.fetchModules()
console.log(analyticsStore.modules) // DetectedModel[]
```

### TypeScript Types

```typescript
import type {
    Dashboard,
    Widget,
    WidgetType,
    WidgetConfig,
    ReportTemplate,
    FilterCondition,
    AggregationDef,
    DetectedModel,
    WidgetData,
    ScheduledReport,
    ExportJob,
} from './analytics-suite/types'
```

### Dark Mode & RTL

```typescript
// Enable Dark Mode
document.documentElement.classList.add('dark')

// Enable RTL
document.documentElement.setAttribute('dir', 'rtl')
document.documentElement.setAttribute('lang', 'ar')
```

---

## 🔌 Custom Widgets

### 1. Create the Widget Class

```php
// app/Analytics/Widgets/SalesComparisonWidget.php
namespace App\Analytics\Widgets;

class SalesComparisonWidget
{
    const TYPE = 'sales_comparison';

    public static function label(): string
    {
        return 'Sales Comparison';
    }

    public static function defaultConfig(): array
    {
        return [
            'period_1' => 'current_month',
            'period_2' => 'last_month',
            'metric'   => 'revenue',
        ];
    }

    public static function configSchema(): array
    {
        return [
            'period_1' => ['type' => 'select', 'label' => 'Period 1'],
            'period_2' => ['type' => 'select', 'label' => 'Period 2'],
            'metric'   => ['type' => 'string',  'label' => 'Metric'],
        ];
    }
}
```

### 2. Register in AppServiceProvider

```php
// app/Providers/AppServiceProvider.php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;
use App\Analytics\Widgets\SalesComparisonWidget;
use App\Analytics\Widgets\RevenueGrowthWidget;
use App\Analytics\Widgets\AttendanceWidget;

public function boot(): void
{
    AnalyticsSuite::registerWidget(SalesComparisonWidget::class);
    AnalyticsSuite::registerWidget(RevenueGrowthWidget::class);
    AnalyticsSuite::registerWidget(AttendanceWidget::class);
}
```

### 3. Vue Component for the Widget

```vue
<!-- resources/js/analytics-suite/components/widgets/SalesComparison.vue -->
<template>
    <div class="h-full flex items-center justify-around">
        <div class="text-center">
            <p class="text-xs text-slate-500">{{ config.period_1 }}</p>
            <p class="text-3xl font-bold text-indigo-600">{{ period1Value }}</p>
        </div>
        <div class="text-2xl text-slate-400">VS</div>
        <div class="text-center">
            <p class="text-xs text-slate-500">{{ config.period_2 }}</p>
            <p class="text-3xl font-bold text-emerald-600">{{ period2Value }}</p>
        </div>
    </div>
</template>
```

Then register it in the published `WidgetRenderer.vue` in your project.

---

## 🏢 Multi-Tenancy

### Single Database

```env
ANALYTICS_TENANT_ISOLATION=true
ANALYTICS_TENANT_COLUMN=tenant_id
```

Every record in package tables will be bound to a `tenant_id`. The system adds the filter automatically.

### Tenant Resolver

```php
// In your middleware
app()->bind('current_tenant_id', fn () => auth()->user()->tenant_id);
```

### RLS for Tenant Isolation

```php
RlsPolicyModel::create([
    'name'         => 'Tenant Isolation',
    'model'        => '*',              // applies to all models
    'column'       => 'tenant_id',
    'scope'        => 'tenant',
    'value_source' => 'auth_user',
    'value_key'    => 'tenant_id',
    'is_active'    => true,
]);
```

---

## 🧪 Testing

```bash
cd packages/mostafax/analytics-suite

# Install dependencies
composer install

# Run all tests
vendor/bin/pest

# Run only Unit Tests
vendor/bin/pest tests/Unit

# Run only Feature Tests
vendor/bin/pest tests/Feature

# With coverage
vendor/bin/pest --coverage --min=80

# Run a specific test file
vendor/bin/pest tests/Unit/Services/DashboardServiceTest.php
```

### Writing a Package Test

```php
namespace Tests\Feature;

use Mostafax\AnalyticsSuite\Providers\AnalyticsSuiteServiceProvider;
use Orchestra\Testbench\TestCase;

class MyTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [AnalyticsSuiteServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
        $app['config']->set('analytics-suite.security.enforce_permissions', false);
    }

    public function test_something(): void
    {
        // your test here
    }
}
```

---

## 🛠 Artisan Commands

### `analytics-suite:install`

```bash
php artisan analytics-suite:install [options]

Options:
  --force              Overwrite existing files
  --skip-migrations    Skip running migrations
  --skip-detection     Skip model detection
  --skip-defaults      Skip generating default dashboards and widgets
```

```bash
# Full install
php artisan analytics-suite:install

# Install without generating defaults
php artisan analytics-suite:install --skip-defaults

# Re-install, overwriting existing files
php artisan analytics-suite:install --force
```

### `analytics-suite:detect-models`

```bash
php artisan analytics-suite:detect-models [options]

Options:
  --generate-widgets     Generate automatic widget definitions
  --generate-dashboards  Generate default dashboards
```

```bash
# Show detected models only
php artisan analytics-suite:detect-models

# Show + generate widgets
php artisan analytics-suite:detect-models --generate-widgets

# Show + generate widgets + dashboards
php artisan analytics-suite:detect-models --generate-widgets --generate-dashboards
```

Output:
```
+--------------------+-----------+----------+--------+--------------+-----------+
| Class              | Name      | Table    | Module | Soft Deletes | Relations |
+--------------------+-----------+----------+--------+--------------+-----------+
| App\Models\User    | User      | users    | App    | No           | 3         |
| App\Models\Order   | Order     | orders   | App    | Yes          | 2         |
| Modules\HR\Models\ | Employee  | employees| HR     | Yes          | 5         |
+--------------------+-----------+----------+--------+--------------+-----------+
Detection complete. Found 3 model(s).
```

### `analytics-suite:sync-permissions`

```bash
php artisan analytics-suite:sync-permissions [options]

Options:
  --role=   Assign all permissions to this role (requires Spatie Permission)
  --guard=  Guard to use (default: web)
```

```bash
# Sync permissions only
php artisan analytics-suite:sync-permissions

# Sync and assign to admin role
php artisan analytics-suite:sync-permissions --role=admin

# With a different guard
php artisan analytics-suite:sync-permissions --role=manager --guard=api
```

---

## 📁 Folder Structure

```
packages/mostafax/analytics-suite/
├── composer.json
├── README.md
├── CHANGELOG.md
├── .gitignore
│
├── config/
│   └── analytics-suite.php          # 150+ configuration keys
│
├── routes/
│   └── api.php                      # 30+ API routes
│
├── database/
│   └── migrations/                  # 11 migrations
│
├── resources/
│   ├── css/
│   │   └── analytics-suite.css      # TailwindCSS + GridStack
│   └── vue/
│       ├── types/index.ts           # TypeScript types
│       ├── store/analytics.ts       # Pinia stores
│       └── components/
│           ├── analytics/
│           │   ├── AnalyticsApp.vue         # Root SPA
│           │   ├── AnalyticsOverview.vue    # Overview
│           │   └── ExportHistory.vue        # Export history
│           ├── dashboard/
│           │   ├── DashboardBuilder.vue     # Main builder
│           │   ├── DashboardList.vue        # Dashboard list
│           │   ├── WidgetPickerDialog.vue   # Add widget dialog
│           │   └── ShareDialog.vue          # Share dashboard
│           ├── reports/
│           │   └── ReportBuilder.vue        # Report builder
│           └── widgets/
│               ├── WidgetRenderer.vue       # Widget dispatcher
│               ├── KpiCard.vue
│               ├── StatsCard.vue
│               ├── DataTable.vue
│               ├── ApexChart.vue            # Bar/Line/Area/Pie/Donut
│               ├── GaugeWidget.vue
│               ├── LeaderboardWidget.vue
│               └── ProgressWidget.vue
│
├── src/
│   ├── AnalyticsSuiteManager.php            # Central entry point
│   │
│   ├── Analytics/
│   │   └── AnalyticsEngine.php             # Analytics engine
│   │
│   ├── Cache/
│   │   └── AnalyticsCacheManager.php       # Redis cache manager
│   │
│   ├── Commands/
│   │   ├── InstallCommand.php
│   │   ├── SyncPermissionsCommand.php
│   │   └── DetectModelsCommand.php
│   │
│   ├── Contracts/                          # 7 Interfaces
│   ├── DTOs/                               # 8 Data Transfer Objects
│   │
│   ├── Detection/
│   │   └── ModelDetectionEngine.php        # Model auto-detection
│   │
│   ├── Events/                             # 6 Events
│   ├── Export/                             # PDF, Excel, CSV, JSON
│   ├── Http/
│   │   ├── Controllers/                    # 6 Controllers
│   │   ├── Requests/                       # Form Requests
│   │   └── Resources/                      # API Resources
│   │
│   ├── Infrastructure/
│   │   └── Persistence/
│   │       ├── Models/                     # 8 Eloquent Models
│   │       └── Repositories/               # 2 Repositories
│   │
│   ├── Jobs/                               # 3 Queue Jobs
│   ├── Providers/
│   │   └── AnalyticsSuiteServiceProvider.php
│   ├── Scheduling/
│   │   └── ReportScheduler.php
│   ├── Security/
│   │   └── SecurityManager.php
│   ├── Services/                           # DashboardService, WidgetService
│   └── Support/
│       └── Facades/
│           └── AnalyticsSuite.php
│
├── tests/
│   ├── Unit/
│   │   ├── Services/DashboardServiceTest.php
│   │   └── Detection/ModelDetectionEngineTest.php
│   ├── Feature/
│   │   ├── Api/DashboardApiTest.php
│   │   └── Commands/InstallCommandTest.php
│   └── pest.php
│
└── docs/
    ├── 01-installation.md
    ├── 02-configuration.md
    ├── 03-dashboard-builder.md
    ├── 04-report-builder.md
    ├── 05-widget-engine.md
    ├── 06-security.md
    ├── 07-export-scheduling.md
    ├── 08-api-reference.md
    └── 09-frontend-vue.md
```

---

## ⚙️ Config Reference

```php
// config/analytics-suite.php
return [

    // ========== Route Configuration ==========
    'routes' => [
        'prefix'     => 'api/analytics',          // route prefix
        'middleware' => ['api', 'auth:sanctum'],   // default middleware
        'name'       => 'analytics.',
    ],

    // ========== Database ==========
    'database' => [
        'connection' => env('ANALYTICS_DB_CONNECTION', 'mysql'),
        'prefix'     => 'as_',                    // table prefix
    ],

    // ========== Cache ==========
    'cache' => [
        'driver'   => env('ANALYTICS_CACHE_DRIVER', 'redis'),
        'enabled'  => env('ANALYTICS_CACHE_ENABLED', true),
        'prefix'   => env('ANALYTICS_CACHE_PREFIX', 'analytics_suite'),
        'ttl'      => [
            'dashboard' => 300,   // 5 minutes
            'widget'    => 180,   // 3 minutes
            'report'    => 600,   // 10 minutes
            'stats'     => 120,   // 2 minutes
            'schema'    => 3600,  // 1 hour
        ],
    ],

    // ========== Security ==========
    'security' => [
        'enforce_permissions'  => true,
        'row_level_security'   => true,
        'tenant_isolation'     => false,
        'tenant_column'        => 'tenant_id',
        'branch_column'        => 'branch_id',
        'department_column'    => 'department_id',
        'max_export_rows'      => 100000,
        'max_query_rows'       => 50000,
        'public_share_enabled' => true,
        'public_share_expiry'  => 7,         // days
    ],

    // ========== Detection Engine ==========
    'detection' => [
        'scan_paths'    => [app_path('Models')],
        'module_paths'  => [base_path('Modules')],
        'excluded_models'          => ['Migration', 'PersonalAccessToken'],
        'auto_generate_widgets'    => true,
        'auto_generate_dashboards' => true,
    ],

    // ========== Dashboard ==========
    'dashboard' => [
        'default_layout'       => 'grid',     // grid|masonry|fixed
        'grid_columns'         => 12,
        'default_refresh_rate' => 300,
        'max_widgets_per_dash' => 50,
    ],

    // ========== Widget ==========
    'widgets' => [
        'default_refresh_rate' => 300,
        'marketplace_enabled'  => true,
        'registry'             => [],           // custom widgets
    ],

    // ========== Export ==========
    'export' => [
        'disk'       => env('ANALYTICS_EXPORT_DISK', 'local'),
        'path'       => 'analytics/exports',
        'formats'    => ['pdf', 'excel', 'csv', 'json'],
        'rtl'        => false,
        'locale'     => 'en',
        'queue'      => 'default',
        'chunk_size' => 1000,
    ],

    // ========== Scheduling ==========
    'scheduling' => [
        'enabled'          => true,
        'delivery_methods' => ['email', 'notification', 'webhook'],
        'queue'            => 'default',
        'from_email'       => env('ANALYTICS_FROM_EMAIL'),
        'from_name'        => env('ANALYTICS_FROM_NAME', 'Analytics Suite'),
    ],

    // ========== UI ==========
    'ui' => [
        'theme'         => 'light',   // light|dark|auto
        'locale'        => 'en',
        'rtl'           => false,
        'brand_name'    => 'Analytics Suite',
        'primary_color' => '#6366f1',
    ],
];
```

---

## ❓ FAQ

**Q: How do I change the route prefix?**
```php
// config/analytics-suite.php
'routes' => ['prefix' => 'api/v1/analytics']
```

**Q: How do I disable permissions in development?**
```env
ANALYTICS_ENFORCE_PERMISSIONS=false
```

**Q: How do I add the Vue paths to vite.config.js?**
```javascript
// vite.config.js
resolve: {
    alias: {
        '@analytics': '/resources/js/analytics-suite'
    }
}
```

**Q: How do I customize the API middleware?**
```php
// config/analytics-suite.php
'routes' => [
    'middleware' => ['api', 'auth:sanctum', 'verified', 'throttle:60,1'],
]
```

**Q: Does it work with Laravel Modules (nwidart/laravel-modules)?**
Yes. Add the Modules path in:
```php
'detection' => [
    'module_paths' => [base_path('Modules')],
]
```

---

## 📄 License

MIT License © 2026 [Mostafa Elbayyar](mailto:mostafa.m.elbiar2@gmail.com)

---

<div align="center">

**Built with ❤️ for the Laravel community**

[⭐ Star on GitHub](https://github.com/mostafax2/Dynamic-Analytics-Studio) · [🐛 Report Bug](https://github.com/mostafax2/Dynamic-Analytics-Studio/issues) · [💡 Request Feature](https://github.com/mostafax2/Dynamic-Analytics-Studio/issues)

</div>
