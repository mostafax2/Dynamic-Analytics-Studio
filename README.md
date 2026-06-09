# Enterprise Analytics Suite

> **Power BI + Grafana + Metabase + Tableau — built for Laravel Enterprise Applications**

A complete analytics platform for Laravel 12+ that sits on top of the [Dynamic Hybrid Reporting Engine](https://packagist.org/packages/mostafax/dynamic-hybrid-reporting-engine).

**Author:** Mostafa Elbayyar  
**Repository:** https://github.com/mostafax2/Dynamic-Analytics-Studio  
**License:** MIT

---

## Features

| Layer | Capabilities |
|---|---|
| **Dashboard Builder** | Drag & drop, resize, public share links, clone, import/export |
| **Widget Engine** | 15 widget types, custom marketplace, per-widget cache & refresh |
| **Report Builder** | Visual DSL editor, all filter ops, aggregations, joins |
| **Analytics Engine** | Auto-stats for any detected model, trend computation |
| **Detection Engine** | Auto-discovers App\Models + Laravel Modules |
| **Security** | 14 permissions, RLS, branch/tenant/department scoping, Sanctum |
| **Export** | PDF, Excel, CSV, JSON — RTL/Arabic support |
| **Scheduling** | Daily→Yearly, Email/Webhook/Notification delivery |
| **Cache** | Redis-backed, pattern invalidation, TTL per resource |
| **Multi-Tenant** | Single-DB / Multi-DB ready, isolation flag |

---

## Requirements

- PHP 8.3+
- Laravel 11 or 12
- Redis (for cache layer)
- `mostafax/dynamic-hybrid-reporting-engine` ^1.0
- `laravel/sanctum` ^4.0

**Optional:**
- `phpoffice/phpspreadsheet` — Excel export
- `barryvdh/laravel-dompdf` — PDF export
- `spatie/laravel-permission` — Role-based permissions
- `mongodb/laravel-mongodb` — MongoDB data source

---

## Installation

```bash
composer require mostafax/enterprise-analytics-suite
```

Run the one-command installer:

```bash
php artisan analytics-suite:install
```

This automatically:
- Publishes config (`config/analytics-suite.php`)
- Publishes Vue components and CSS assets
- Runs all migrations (11 tables, `as_` prefixed)
- Seeds 14 enterprise permissions
- Detects existing models and modules
- Generates default dashboards and widgets
- Configures Redis cache layer
- Verifies Sanctum integration

---

## Configuration

```php
// config/analytics-suite.php
return [
    'routes' => [
        'prefix'     => 'api/analytics',
        'middleware' => ['api', 'auth:sanctum'],
    ],
    'cache' => [
        'driver'  => 'redis',
        'enabled' => true,
        'ttl'     => ['dashboard' => 300, 'widget' => 180, 'report' => 600],
    ],
    'security' => [
        'enforce_permissions' => true,
        'row_level_security'  => true,
        'tenant_isolation'    => false,
    ],
];
```

---

## API Reference

### Dashboards
```
GET    /api/analytics/dashboards
POST   /api/analytics/dashboards
GET    /api/analytics/dashboards/{id}
PUT    /api/analytics/dashboards/{id}
DELETE /api/analytics/dashboards/{id}
POST   /api/analytics/dashboards/{id}/clone
POST   /api/analytics/dashboards/{id}/layout
POST   /api/analytics/dashboards/{id}/share
DELETE /api/analytics/dashboards/{id}/share
GET    /api/analytics/dashboards/{id}/export
POST   /api/analytics/dashboards/import
GET    /api/analytics/public/{token}          (no auth)
```

### Widgets
```
GET    /api/analytics/dashboards/{id}/widgets
POST   /api/analytics/dashboards/{id}/widgets
GET    /api/analytics/dashboards/{id}/widgets/{wid}
PUT    /api/analytics/dashboards/{id}/widgets/{wid}
DELETE /api/analytics/dashboards/{id}/widgets/{wid}
GET    /api/analytics/dashboards/{id}/widgets/{wid}/data
POST   /api/analytics/dashboards/{id}/widgets/{wid}/refresh
GET    /api/analytics/widget-types
```

### Reports
```
GET    /api/analytics/reports
POST   /api/analytics/reports
GET    /api/analytics/reports/{id}
PUT    /api/analytics/reports/{id}
DELETE /api/analytics/reports/{id}
POST   /api/analytics/reports/{id}/run
POST   /api/analytics/reports/{id}/clone
GET    /api/analytics/reports/{id}/export
POST   /api/analytics/reports/import
```

### Analytics & Exports
```
GET    /api/analytics/analytics/modules
GET    /api/analytics/analytics/stats?model=App\Models\User
GET    /api/analytics/analytics/summary/{module}
POST   /api/analytics/exports/queue
GET    /api/analytics/exports/status/{jobId}
GET    /api/analytics/exports/history
```

### Scheduling
```
GET    /api/analytics/schedules
POST   /api/analytics/schedules
PUT    /api/analytics/schedules/{id}
DELETE /api/analytics/schedules/{id}
POST   /api/analytics/schedules/{id}/pause
POST   /api/analytics/schedules/{id}/resume
```

---

## Widget Marketplace

Register custom widgets in your `AppServiceProvider`:

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

AnalyticsSuite::registerWidget(SalesComparisonWidget::class);
AnalyticsSuite::registerWidget(RevenueGrowthWidget::class);
```

Your widget class must define `const TYPE = 'my_type';`.

---

## Vue Frontend

The package ships with a complete Vue 3 SPA:

```js
// resources/js/app.js
import { createApp }   from 'vue'
import { createPinia } from 'pinia'
import AnalyticsApp    from './analytics-suite/components/analytics/AnalyticsApp.vue'

const app = createApp(AnalyticsApp)
app.use(createPinia())
app.mount('#analytics-app')
```

**External CDN dependencies** (add to your layout):
```html
<script src="https://cdn.jsdelivr.net/npm/gridstack/dist/gridstack-all.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
```

---

## Artisan Commands

```bash
# Full installation (zero config required)
php artisan analytics-suite:install

# Scan models and optionally generate widgets/dashboards
php artisan analytics-suite:detect-models --generate-widgets --generate-dashboards

# Sync permissions (integrates with Spatie Permission)
php artisan analytics-suite:sync-permissions --role=admin
```

---

## Security Permissions

| Permission | Description |
|---|---|
| `view_reports` | View and run reports |
| `create_reports` | Create report templates |
| `edit_reports` | Modify existing reports |
| `delete_reports` | Delete reports |
| `export_reports` | Export to PDF/Excel/CSV/JSON |
| `schedule_reports` | Create scheduled report deliveries |
| `view_dashboards` | View dashboards and widget data |
| `create_dashboards` | Create new dashboards |
| `edit_dashboards` | Modify dashboards and layouts |
| `delete_dashboards` | Delete dashboards |
| `create_widgets` | Add widgets to dashboards |
| `edit_widgets` | Configure existing widgets |
| `delete_widgets` | Remove widgets |
| `manage_analytics` | Admin: detection, cache, system |

---

## Testing

```bash
cd packages/mostafax/analytics-suite
composer install
vendor/bin/pest
```

---

## Architecture

```
MySQL/MongoDB
     ↓
Dynamic Hybrid Reporting Engine (DSL → Query → Result)
     ↓
Analytics Engine (widget execution, stats, trends)
     ↓  ← Redis Cache Layer
Dashboard Builder  ←→  Widget Engine
     ↓
Vue 3 Frontend (Pinia + ApexCharts + GridStack)
```

---

## License

MIT © 2026 Mostafa Elbayyar
