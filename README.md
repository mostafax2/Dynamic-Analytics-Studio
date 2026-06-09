<div align="center">

<img src="https://raw.githubusercontent.com/mostafax2/Dynamic-Analytics-Studio/main/.github/banner.png" alt="Enterprise Analytics Suite" width="100%" />

# Enterprise Analytics Suite

**منصة تحليلات متكاملة للمشاريع Laravel Enterprise**

[![Laravel](https://img.shields.io/badge/Laravel-12+-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php)](https://php.net)
[![Vue](https://img.shields.io/badge/Vue-3.x-4FC08D?style=flat-square&logo=vue.js)](https://vuejs.org)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)
[![Version](https://img.shields.io/badge/Version-1.0.0-blue?style=flat-square)](CHANGELOG.md)

*Power BI · Grafana · Metabase · Tableau — مُصمَّم خصيصًا لـ Laravel*

**المؤلف:** Mostafa Elbayyar — [mostafa.m.elbiar2@gmail.com](mailto:mostafa.m.elbiar2@gmail.com)  
**GitHub:** [github.com/mostafax2/Dynamic-Analytics-Studio](https://github.com/mostafax2/Dynamic-Analytics-Studio)

</div>

---

## جدول المحتويات

- [نظرة عامة](#-نظرة-عامة)
- [المتطلبات](#-المتطلبات)
- [التثبيت السريع](#-التثبيت-السريع)
- [الإعداد التفصيلي](#-الإعداد-التفصيلي)
- [الاستخدام — Dashboard Builder](#-استخدام-dashboard-builder)
- [الاستخدام — Report Builder](#-استخدام-report-builder)
- [الاستخدام — Widget Engine](#-استخدام-widget-engine)
- [الاستخدام — Analytics Engine](#-استخدام-analytics-engine)
- [نظام الأمان](#-نظام-الأمان)
- [نظام التصدير](#-نظام-التصدير)
- [الجدولة الزمنية للتقارير](#-جدولة-التقارير)
- [الـ Facade — AnalyticsSuite](#-the-facade)
- [API Reference](#-api-reference-كامل)
- [الـ Vue Frontend](#-vue-3-frontend)
- [إضافة Widget مخصص](#-إضافة-widget-مخصص)
- [Multi-Tenancy](#-multi-tenancy)
- [الاختبارات](#-الاختبارات)
- [الأوامر المتاحة](#-أوامر-artisan)
- [هيكل المجلدات](#-هيكل-المجلدات)
- [مرجع الـ Config](#-مرجع-config-كامل)

---

## 🌟 نظرة عامة

**Enterprise Analytics Suite** هو حزمة Laravel متكاملة تحوّل أي تطبيق Laravel إلى منصة BI (Business Intelligence) كاملة في خطوة واحدة.

### ما الذي يميزه؟

| الميزة | الوصف |
|---|---|
| 🤖 **Auto-Detection** | يكتشف تلقائيًا جميع نماذج التطبيق (`App\Models` + `Modules/*`) ويولّد لوحات ووِدجات افتراضية |
| 📊 **Dashboard Builder** | واجهة drag & drop كاملة مدعومة بـ GridStack |
| 📋 **Report Builder** | بنّاء تقارير بصري كامل بدون كتابة SQL |
| 🔢 **15 Widget Type** | KPI، مخططات، جداول، مقاييس، ترتيب، تقدم، وأكثر |
| 🔌 **Widget Marketplace** | أضف أي widget مخصص بسطر واحد |
| 🔒 **Enterprise Security** | 14 صلاحية، Row-Level Security، Sanctum، عزل المستأجرين |
| ⚡ **Redis Cache** | تخزين مؤقت ذكي بإبطال تلقائي لكل طبقة |
| 📥 **Export System** | PDF، Excel، CSV، JSON مع دعم RTL والعربية |
| ⏰ **Report Scheduling** | جدولة يومية/أسبوعية/شهرية مع تسليم بالبريد أو Webhook |
| 🌐 **Multi-Tenant** | جاهز للمستأجرين المتعددين |
| 🎨 **Vue 3 SPA** | واجهة حديثة، Dark Mode، RTL كامل |

### المعمارية

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

## 📋 المتطلبات

### إلزامية

| الحزمة | الإصدار |
|---|---|
| PHP | `^8.3` |
| Laravel | `^11.0` أو `^12.0` |
| Redis | أي إصدار حديث |
| `mostafax/dynamic-hybrid-reporting-engine` | `^1.0` |
| `laravel/sanctum` | `^4.0` |

### اختيارية (تُفعَّل حسب الحاجة)

| الحزمة | الغرض |
|---|---|
| `phpoffice/phpspreadsheet` | تصدير Excel |
| `barryvdh/laravel-dompdf` | تصدير PDF |
| `league/csv` | تدفق CSV محسَّن |
| `spatie/laravel-permission` | إدارة الأدوار والصلاحيات |
| `mongodb/laravel-mongodb` | مصدر بيانات MongoDB |

---

## ⚡ التثبيت السريع

```bash
# 1. تثبيت الحزمة
composer require mostafax/enterprise-analytics-suite

# 2. تشغيل المثبِّت الذكي (يفعل كل شيء تلقائيًا)
php artisan analytics-suite:install

# 3. تشغيل queue worker (للتصدير غير المتزامن)
php artisan queue:work
```

**انتهى!** — الـ API متاحة الآن على `/api/analytics/...`

---

## 🔧 الإعداد التفصيلي

### الخطوة 1 — تثبيت الحزمة

```bash
composer require mostafax/enterprise-analytics-suite
```

إذا كنت تستخدم **Local Path Repository** (للتطوير المحلي):

```json
// composer.json للمشروع الرئيسي
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

ثم:
```bash
composer update mostafax/enterprise-analytics-suite
```

---

### الخطوة 2 — نشر الإعدادات

```bash
# نشر ملف الإعداد فقط
php artisan vendor:publish --tag=analytics-suite-config

# نشر الـ Migrations فقط
php artisan vendor:publish --tag=analytics-suite-migrations

# نشر الـ Vue assets
php artisan vendor:publish --tag=analytics-suite-assets

# أو نشر كل شيء دفعة واحدة
php artisan analytics-suite:install
```

---

### الخطوة 3 — إعداد قاعدة البيانات

```bash
php artisan migrate
```

الجداول المنشأة (كلها بالبادئة `as_`):

| الجدول | الوصف |
|---|---|
| `as_dashboards` | لوحات التحكم |
| `as_widgets` | الوِدجات |
| `as_report_templates` | قوالب التقارير |
| `as_scheduled_reports` | التقارير المجدولة |
| `as_export_jobs` | مهام التصدير |
| `as_permissions` | الصلاحيات |
| `as_rls_policies` | سياسات Row-Level Security |
| `as_dashboard_shares` | مشاركات اللوحات |
| `as_widget_snapshots` | لقطات بيانات الوِدجات |
| `as_detected_models` | النماذج المكتشفة |
| `as_analytics_events` | سجل الأحداث |

---

### الخطوة 4 — إعداد متغيرات البيئة

أضف هذه المتغيرات إلى ملف `.env`:

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

# MongoDB (اختياري)
ANALYTICS_MONGO_ENABLED=false
ANALYTICS_MONGO_CONNECTION=mongodb
```

---

### الخطوة 5 — Sanctum

تأكد من تثبيت Laravel Sanctum وإعداده:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

في `app/Http/Kernel.php` أو `bootstrap/app.php`:

```php
// Laravel 12 — bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->statefulApi();
})
```

---

### الخطوة 6 — إعداد Queue

للتصدير غير المتزامن والجدولة:

```env
QUEUE_CONNECTION=redis
```

```bash
php artisan queue:work --queue=default
```

أو مع Supervisor:
```ini
[program:analytics-queue]
command=php /var/www/html/artisan queue:work redis --queue=default --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
```

---

## 📊 استخدام Dashboard Builder

### عبر API

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

// إنشاء لوحة تحكم
$dashboard = AnalyticsSuite::dashboards()->create([
    'name'        => 'Sales Dashboard',
    'description' => 'لوحة مبيعات رئيسية',
    'settings'    => ['theme' => 'dark'],
], auth()->id());

// استرجاع لوحة
$dashboard = AnalyticsSuite::dashboards()->find(1);

// تحديث التخطيط (بعد drag & drop)
AnalyticsSuite::dashboards()->updateLayout(1, [
    ['widget_id' => 5, 'x' => 0, 'y' => 0, 'w' => 6, 'h' => 3],
    ['widget_id' => 6, 'x' => 6, 'y' => 0, 'w' => 6, 'h' => 3],
]);

// تفعيل الرابط العام
$dashboard = AnalyticsSuite::dashboards()->enablePublicShare(1, expiryDays: 30);
echo $dashboard->publicToken; // رمز الوصول العام
```

### عبر HTTP

```bash
# إنشاء لوحة
curl -X POST /api/analytics/dashboards \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "My Dashboard", "description": "وصف اللوحة"}'

# استرجاع قائمة اللوحات
curl -X GET /api/analytics/dashboards \
  -H "Authorization: Bearer {token}"

# نسخ لوحة
curl -X POST /api/analytics/dashboards/1/clone \
  -H "Authorization: Bearer {token}" \
  -d '{"name": "نسخة من Dashboard"}'

# تحديث التخطيط
curl -X POST /api/analytics/dashboards/1/layout \
  -H "Authorization: Bearer {token}" \
  -d '{
    "layout": [
      {"widget_id": 5, "x": 0, "y": 0, "w": 6, "h": 3},
      {"widget_id": 6, "x": 6, "y": 0, "w": 6, "h": 3}
    ]
  }'

# مشاركة عامة
curl -X POST /api/analytics/dashboards/1/share \
  -H "Authorization: Bearer {token}" \
  -d '{"expiry_days": 7}'
```

### استيراد / تصدير

```bash
# تصدير لوحة (JSON definition)
curl -X GET /api/analytics/dashboards/1/export \
  -H "Authorization: Bearer {token}"

# استيراد لوحة
curl -X POST /api/analytics/dashboards/import \
  -H "Authorization: Bearer {token}" \
  -d '{"definition": {...}}'
```

---

## 📋 استخدام Report Builder

### بناء تقرير برمجيًا

```php
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\ReportTemplateModel;

$template = ReportTemplateModel::create([
    'name'         => 'تقرير المبيعات الشهري',
    'description'  => 'إجمالي المبيعات مجمَّعة شهريًا',
    'data_source'  => 'orders',          // اسم الجدول
    'source_type'  => 'mysql',
    'columns'      => [
        ['field' => 'id'],
        ['field' => 'total', 'alias' => 'amount'],
        ['field' => 'created_at', 'alias' => 'date'],
    ],
    'filters'      => [
        ['column' => 'status', 'operator' => 'eq', 'value' => 'completed'],
        ['column' => 'total',  'operator' => 'gte', 'value' => 100],
    ],
    'aggregations' => [
        ['function' => 'sum',   'column' => 'total',  'alias' => 'revenue'],
        ['function' => 'count', 'column' => 'id',     'alias' => 'orders_count'],
    ],
    'group_by'    => ['DATE_FORMAT(created_at, "%Y-%m")'],
    'order_by'    => [['column' => 'created_at', 'direction' => 'desc']],
    'is_template' => true,
    'category'    => 'sales',
    'tags'        => ['monthly', 'revenue'],
    'created_by'  => auth()->id(),
]);
```

### تشغيل تقرير

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

### عبر HTTP

```bash
# تشغيل تقرير مع فلاتر إضافية
curl -X POST /api/analytics/reports/1/run \
  -H "Authorization: Bearer {token}" \
  -d '{
    "filters": [
      {"column": "created_at", "operator": "gte", "value": "2026-01-01"}
    ],
    "pagination": {"page": 1, "per_page": 50}
  }'
```

### الفلاتر المتاحة

| المشغّل | الوصف | مثال |
|---|---|---|
| `eq` | يساوي | `"operator": "eq", "value": "active"` |
| `neq` | لا يساوي | `"operator": "neq", "value": "deleted"` |
| `gt` | أكبر من | `"operator": "gt", "value": 100` |
| `gte` | أكبر من أو يساوي | `"operator": "gte", "value": 0` |
| `lt` | أصغر من | `"operator": "lt", "value": 1000` |
| `lte` | أصغر من أو يساوي | `"operator": "lte", "value": 50` |
| `between` | بين قيمتين | `"operator": "between", "value": 10, "value2": 100` |
| `in` | ضمن قائمة | `"operator": "in", "value": ["a","b","c"]` |
| `not_in` | خارج قائمة | `"operator": "not_in", "value": ["x","y"]` |
| `like` | يحتوي (LIKE) | `"operator": "like", "value": "%ahmed%"` |
| `starts_with` | يبدأ بـ | `"operator": "starts_with", "value": "EMP"` |
| `ends_with` | ينتهي بـ | `"operator": "ends_with", "value": "@gmail.com"` |
| `contains` | يحتوي | `"operator": "contains", "value": "cairo"` |
| `null` | فارغ | `"operator": "null"` |
| `not_null` | غير فارغ | `"operator": "not_null"` |

### دوال التجميع

| الدالة | الوصف |
|---|---|
| `count` | عدد السجلات |
| `sum` | مجموع القيم |
| `avg` | المتوسط |
| `min` | أصغر قيمة |
| `max` | أكبر قيمة |
| `count_distinct` | عدد القيم الفريدة |

---

## 🔢 استخدام Widget Engine

### إضافة وِدجت برمجيًا

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

$widget = AnalyticsSuite::widgets()->create([
    'dashboard_id'     => 1,
    'type'             => 'kpi_card',
    'title'            => 'إجمالي المستخدمين',
    'config'           => [
        'data_source' => 'users',
        'aggregation' => 'count',
        'column'      => 'id',
    ],
    'position'         => ['x' => 0, 'y' => 0, 'w' => 3, 'h' => 2],
    'refresh_interval' => 300,     // ثانية — 0 يعني يدوي
    'cache_enabled'    => true,
    'cache_ttl'        => 180,
], auth()->id());
```

### أنواع الوِدجات

#### 1. `kpi_card` — بطاقة مؤشر رئيسي
```json
{
    "type": "kpi_card",
    "config": {
        "data_source": "orders",
        "aggregation": "sum",
        "column": "total",
        "label": "إجمالي الإيرادات",
        "show_trend": true
    }
}
```

#### 2. `stats_card` — بطاقة إحصاءات متعددة
```json
{
    "type": "stats_card",
    "config": {
        "data_source": "users",
        "columns": ["id", "email", "created_at"]
    }
}
```

#### 3. `data_table` — جدول بيانات
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

#### 4. `bar_chart` — مخطط أعمدة
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

#### 5. `line_chart` — مخطط خطي
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

#### 6. `pie_chart` / `donut_chart` — مخطط دائري
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

#### 7. `gauge_chart` — مقياس
```json
{
    "type": "gauge_chart",
    "config": {
        "data_source": "kpis",
        "aggregation": "sum",
        "column": "value",
        "min": 0,
        "max": 1000000,
        "label": "المبيعات المستهدفة"
    }
}
```

#### 8. `progress` — شريط تقدم
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

#### 9. `leaderboard` — ترتيب أفضل العناصر
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

### تحديث بيانات وِدجت

```php
// تحديث مباشر
AnalyticsSuite::engine()->executeWidget(widgetId: 5);

// إبطال كاش وإعادة جلب
AnalyticsSuite::engine()->refreshCache(widgetId: 5);
```

```bash
# عبر API
curl -X POST /api/analytics/dashboards/1/widgets/5/refresh \
  -H "Authorization: Bearer {token}"

# جلب بيانات وِدجت مع فلاتر ديناميكية
curl -X GET /api/analytics/dashboards/1/widgets/5/data \
  -H "Authorization: Bearer {token}" \
  -d '{"filters": [{"column": "year", "operator": "eq", "value": 2026}]}'
```

---

## 🔬 استخدام Analytics Engine

### اكتشاف النماذج تلقائيًا

```php
// اكتشاف جميع النماذج
$models = AnalyticsSuite::detectModels();

foreach ($models as $model) {
    echo $model->name;          // User
    echo $model->table;         // users
    echo $model->module;        // App
    echo $model->primaryKey;    // id
    print_r($model->fillable);  // ['name', 'email', ...]
    print_r($model->columns);   // ['id', 'name', 'email', 'created_at', ...]
    print_r($model->relationships); // [['method'=>'posts', 'type'=>'hasMany', ...]]
}
```

### حساب إحصاءات نموذج

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

### توليد إحصاءات لوحة كاملة

```php
$allWidgetData = AnalyticsSuite::engine()->executeDashboard(dashboardId: 1, params: [
    'filters' => [['column' => 'branch_id', 'operator' => 'eq', 'value' => 3]],
]);

foreach ($allWidgetData as $widgetData) {
    echo "Widget {$widgetData->widgetId}: " . count($widgetData->data) . " rows";
}
```

---

## 🔒 نظام الأمان

### الصلاحيات المتاحة (14 صلاحية)

```php
// التقارير
'view_reports'     // عرض وتشغيل التقارير
'create_reports'   // إنشاء قوالب تقارير
'edit_reports'     // تعديل التقارير
'delete_reports'   // حذف التقارير
'export_reports'   // تصدير التقارير
'schedule_reports' // جدولة التقارير

// اللوحات
'view_dashboards'    // عرض اللوحات وبيانات الوِدجات
'create_dashboards'  // إنشاء لوحات
'edit_dashboards'    // تعديل اللوحات والتخطيطات
'delete_dashboards'  // حذف اللوحات

// الوِدجات
'create_widgets'  // إضافة وِدجات
'edit_widgets'    // تعديل الوِدجات
'delete_widgets'  // حذف الوِدجات

// الإدارة
'manage_analytics'  // صلاحية المسؤول الكاملة
```

### ربط مع Spatie Permission

```bash
# إنشاء الصلاحيات وتعيينها لدور معين
php artisan analytics-suite:sync-permissions --role=admin

# تعيين لدور محدد مع guard مختلف
php artisan analytics-suite:sync-permissions --role=manager --guard=api
```

```php
// في AppServiceProvider أو بشكل برمجي
use Spatie\Permission\Models\Role;
use Mostafax\AnalyticsSuite\Security\SecurityManager;

$role = Role::findByName('analyst');
foreach (SecurityManager::PERMISSIONS as $permission) {
    $role->givePermissionTo($permission);
}
```

### فحص الصلاحيات يدويًا

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

$security = AnalyticsSuite::security();

if ($security->can(auth()->user(), 'export_reports')) {
    // مسموح بالتصدير
}

// التحقق من صلاحية واحدة على الأقل
$security->canAny(auth()->user(), ['edit_reports', 'create_reports']);

// إلقاء 403 تلقائيًا إذا لم يكن مسموحًا
$security->authorizeOr403(auth()->user(), 'delete_dashboards');
```

### Row-Level Security

```php
// إضافة سياسة RLS برمجيًا
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\RlsPolicyModel;

RlsPolicyModel::create([
    'name'         => 'Branch Isolation',
    'model'        => \App\Models\Order::class,
    'column'       => 'branch_id',
    'scope'        => 'branch',
    'operator'     => '=',
    'value_source' => 'auth_user',   // يأخذ القيمة من الـ user المسجل
    'value_key'    => 'branch_id',   // خاصية المستخدم
    'is_active'    => true,
]);
```

الآن عند تنفيذ أي تقرير على `orders` — سيُضاف `WHERE branch_id = {auth()->user()->branch_id}` تلقائيًا.

---

## 📥 نظام التصدير

### تصدير فوري

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

$exporter = AnalyticsSuite::exporter();

// تصدير تقرير مباشرة
$result = $exporter->exportReport(reportId: 1, format: 'pdf', params: [
    'title'  => 'تقرير المبيعات',
    'rtl'    => true,
    'locale' => 'ar',
]);

echo $result->downloadUrl; // رابط تنزيل مؤقت
echo $result->rows;        // 1500
echo $result->sizeBytes;   // 245760
```

### تصدير غير متزامن (موصى به للبيانات الكبيرة)

```php
// إرسال مهمة للـ Queue
$jobId = $exporter->queueExport(
    type:        'report',
    id:          1,
    format:      'excel',
    params:      ['title' => 'Sales Report'],
    notifyEmail: 'mostafa@example.com'
);

echo "Job ID: {$jobId}";
```

```bash
# تتبع حالة المهمة
curl -X GET /api/analytics/exports/status/42 \
  -H "Authorization: Bearer {token}"

# الاستجابة
{
    "id": 42,
    "status": "done",
    "filename": "sales-report_2026-06-09_120000.xlsx",
    "rows": 15430,
    "size": 1245678,
    "completed": "2026-06-09T12:00:45.000Z"
}
```

### دعم RTL والعربية

```php
// في config/analytics-suite.php
'export' => [
    'rtl'    => true,
    'locale' => 'ar',
],

// أو عند كل تصدير
$result = $exporter->exportReport(1, 'pdf', [
    'title'  => 'تقرير مالي شهري',
    'rtl'    => true,
    'locale' => 'ar',
]);
```

---

## ⏰ جدولة التقارير

### إنشاء تقرير مجدول

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

$scheduled = AnalyticsSuite::scheduler()->schedule([
    'report_id'        => 1,
    'name'             => 'تقرير المبيعات اليومي',
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

### تسليم عبر Webhook

```php
$scheduled = AnalyticsSuite::scheduler()->schedule([
    'report_id'        => 3,
    'name'             => 'إشعار إنتهاء التقرير',
    'frequency'        => 'weekly',
    'format'           => 'json',
    'delivery_methods' => ['webhook'],
    'webhook_url'      => 'https://your-app.com/webhooks/report-ready',
]);
```

Payload المُرسَل للـ Webhook:
```json
{
    "scheduled_report_id": 3,
    "report_name": "إشعار إنتهاء التقرير",
    "format": "json",
    "download_url": "https://...",
    "rows": 2340,
    "generated_at": "2026-06-09T08:00:00Z"
}
```

### إيقاف / استئناف / إلغاء

```php
$scheduler = AnalyticsSuite::scheduler();

$scheduler->pause(id: 3);    // إيقاف مؤقت
$scheduler->resume(id: 3);   // استئناف
$scheduler->cancel(id: 3);   // إلغاء نهائي
```

```bash
# عبر API
curl -X POST /api/analytics/schedules/3/pause  -H "Authorization: Bearer {token}"
curl -X POST /api/analytics/schedules/3/resume -H "Authorization: Bearer {token}"
curl -X DELETE /api/analytics/schedules/3      -H "Authorization: Bearer {token}"
```

---

## 🎭 The Facade

```php
use Mostafax\AnalyticsSuite\Support\Facades\AnalyticsSuite;

// الخدمات الرئيسية
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

// اكتشاف النماذج
AnalyticsSuite::detectModels(); // Collection<DetectedModelDTO>

// معلومات الحزمة
AnalyticsSuite::version(); // "1.0.0"
```

---

## 🌐 API Reference كامل

### Authentication

جميع المسارات تتطلب `Bearer Token` من Laravel Sanctum:

```bash
# الحصول على token
curl -X POST /sanctum/token \
  -d '{"email":"user@example.com","password":"secret","device_name":"api"}'
```

---

### Dashboards API

| Method | Endpoint | الوصف | الصلاحية |
|---|---|---|---|
| `GET` | `/api/analytics/dashboards` | قائمة اللوحات | `view_dashboards` |
| `POST` | `/api/analytics/dashboards` | إنشاء لوحة | `create_dashboards` |
| `GET` | `/api/analytics/dashboards/{id}` | تفاصيل لوحة | `view_dashboards` |
| `PUT` | `/api/analytics/dashboards/{id}` | تحديث لوحة | `edit_dashboards` |
| `DELETE` | `/api/analytics/dashboards/{id}` | حذف لوحة | `delete_dashboards` |
| `POST` | `/api/analytics/dashboards/{id}/clone` | نسخ لوحة | `create_dashboards` |
| `POST` | `/api/analytics/dashboards/{id}/layout` | تحديث التخطيط | `edit_dashboards` |
| `POST` | `/api/analytics/dashboards/{id}/share` | تفعيل رابط عام | `edit_dashboards` |
| `DELETE` | `/api/analytics/dashboards/{id}/share` | إلغاء الرابط العام | `edit_dashboards` |
| `GET` | `/api/analytics/dashboards/{id}/export` | تصدير تعريف اللوحة | `export_reports` |
| `POST` | `/api/analytics/dashboards/import` | استيراد لوحة | `create_dashboards` |
| `GET` | `/api/analytics/public/{token}` | عرض لوحة عامة | لا يتطلب auth |

**POST /api/analytics/dashboards** — Body:
```json
{
    "name": "Sales Overview",
    "description": "نظرة عامة على المبيعات",
    "settings": {"theme": "dark", "refresh_rate": 60},
    "is_public": false,
    "is_default": false
}
```

---

### Widgets API

| Method | Endpoint | الوصف | الصلاحية |
|---|---|---|---|
| `GET` | `/api/analytics/dashboards/{id}/widgets` | قائمة الوِدجات | `view_dashboards` |
| `POST` | `/api/analytics/dashboards/{id}/widgets` | إضافة وِدجت | `create_widgets` |
| `GET` | `/api/analytics/dashboards/{id}/widgets/{wid}` | تفاصيل وِدجت | `view_dashboards` |
| `PUT` | `/api/analytics/dashboards/{id}/widgets/{wid}` | تحديث وِدجت | `edit_widgets` |
| `DELETE` | `/api/analytics/dashboards/{id}/widgets/{wid}` | حذف وِدجت | `delete_widgets` |
| `GET` | `/api/analytics/dashboards/{id}/widgets/{wid}/data` | جلب بيانات | `view_dashboards` |
| `POST` | `/api/analytics/dashboards/{id}/widgets/{wid}/refresh` | تحديث كاش | `view_dashboards` |
| `GET` | `/api/analytics/widget-types` | أنواع الوِدجات المتاحة | — |

**POST /api/analytics/dashboards/1/widgets** — Body:
```json
{
    "type": "bar_chart",
    "title": "المبيعات الشهرية",
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

| Method | Endpoint | الوصف | الصلاحية |
|---|---|---|---|
| `GET` | `/api/analytics/reports` | قائمة التقارير | `view_reports` |
| `POST` | `/api/analytics/reports` | إنشاء تقرير | `create_reports` |
| `GET` | `/api/analytics/reports/{id}` | تفاصيل تقرير | `view_reports` |
| `PUT` | `/api/analytics/reports/{id}` | تحديث تقرير | `edit_reports` |
| `DELETE` | `/api/analytics/reports/{id}` | حذف تقرير | `delete_reports` |
| `POST` | `/api/analytics/reports/{id}/run` | تشغيل تقرير | `view_reports` |
| `POST` | `/api/analytics/reports/{id}/clone` | نسخ تقرير | `create_reports` |
| `GET` | `/api/analytics/reports/{id}/export` | تصدير تعريف JSON | `export_reports` |
| `POST` | `/api/analytics/reports/import` | استيراد تقرير | `create_reports` |

**Query Parameters لـ GET /api/analytics/reports:**
```
?search=مبيعات         البحث بالاسم
?category=sales        فلترة بالتصنيف
?templates_only=true   القوالب فقط
?per_page=20           عدد النتائج
```

---

### Analytics API

| Method | Endpoint | الوصف |
|---|---|---|
| `GET` | `/api/analytics/analytics/modules` | النماذج المكتشفة |
| `GET` | `/api/analytics/analytics/stats?model=App\Models\User` | إحصاءات نموذج |
| `GET` | `/api/analytics/analytics/summary/{module}` | ملخص وحدة |
| `GET` | `/api/analytics/analytics/dashboard/{id}/data` | بيانات كل وِدجات لوحة |
| `POST` | `/api/analytics/analytics/cache/invalidate` | إبطال كاش |

---

### Export API

| Method | Endpoint | الوصف |
|---|---|---|
| `POST` | `/api/analytics/exports/queue` | إضافة مهمة تصدير للقائمة |
| `GET` | `/api/analytics/exports/status/{id}` | حالة مهمة التصدير |
| `GET` | `/api/analytics/exports/formats` | صيغ التصدير المتاحة |
| `GET` | `/api/analytics/exports/history` | سجل التصديرات |

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

| Method | Endpoint | الوصف |
|---|---|---|
| `GET` | `/api/analytics/schedules` | قائمة الجداول |
| `POST` | `/api/analytics/schedules` | إنشاء جدول |
| `GET` | `/api/analytics/schedules/{id}` | تفاصيل جدول |
| `PUT` | `/api/analytics/schedules/{id}` | تحديث جدول |
| `DELETE` | `/api/analytics/schedules/{id}` | إلغاء جدول |
| `POST` | `/api/analytics/schedules/{id}/pause` | إيقاف مؤقت |
| `POST` | `/api/analytics/schedules/{id}/resume` | استئناف |

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
    "params": {"rtl": true, "title": "تقرير أسبوعي"}
}
```

---

## 🎨 Vue 3 Frontend

### التثبيت السريع

أضف لـ `resources/js/app.js`:

```javascript
import { createApp }   from 'vue'
import { createPinia } from 'pinia'
import AnalyticsApp    from './analytics-suite/components/analytics/AnalyticsApp.vue'

const app = createApp(AnalyticsApp)
app.use(createPinia())
app.mount('#analytics-app')
```

أضف لـ Blade view:
```html
<!DOCTYPE html>
<html lang="ar" dir="rtl">
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

### استخدام Component منفرد

```vue
<script setup>
import DashboardBuilder from './analytics-suite/components/dashboard/DashboardBuilder.vue'
import { createPinia }  from 'pinia'

// يجب تهيئة Pinia في جذر التطبيق
</script>

<template>
    <DashboardBuilder :dashboard-id="1" @back="goBack" />
</template>
```

### Pinia Store — استخدام مباشر

```typescript
import { useDashboardStore } from './analytics-suite/store/analytics'
import { useReportStore }    from './analytics-suite/store/analytics'
import { useAnalyticsStore } from './analytics-suite/store/analytics'

// في setup()
const dashStore     = useDashboardStore()
const reportStore   = useReportStore()
const analyticsStore = useAnalyticsStore()

// جلب اللوحات
await dashStore.fetchAll()
console.log(dashStore.items)       // Dashboard[]

// تشغيل تقرير
const result = await reportStore.run(1, { filters: [] })
console.log(result.rows)           // Record[]

// اكتشاف النماذج
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
// تفعيل Dark Mode
document.documentElement.classList.add('dark')

// تفعيل RTL
document.documentElement.setAttribute('dir', 'rtl')
document.documentElement.setAttribute('lang', 'ar')
```

---

## 🔌 إضافة Widget مخصص

### 1. إنشاء الـ Widget Class

```php
// app/Analytics/Widgets/SalesComparisonWidget.php
namespace App\Analytics\Widgets;

class SalesComparisonWidget
{
    const TYPE = 'sales_comparison';

    public static function label(): string
    {
        return 'مقارنة المبيعات';
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
            'period_1' => ['type' => 'select', 'label' => 'الفترة الأولى'],
            'period_2' => ['type' => 'select', 'label' => 'الفترة الثانية'],
            'metric'   => ['type' => 'string',  'label' => 'المؤشر'],
        ];
    }
}
```

### 2. تسجيله في AppServiceProvider

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

### 3. Vue Component للـ Widget

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

ثم أضف تسجيله في `WidgetRenderer.vue` المنشور في مشروعك.

---

## 🏢 Multi-Tenancy

### Single Database

```env
ANALYTICS_TENANT_ISOLATION=true
ANALYTICS_TENANT_COLUMN=tenant_id
```

كل سجل في جداول الحزمة سيُربط بـ `tenant_id`. النظام يضيف الفلتر تلقائيًا.

### إعداد محدد المستأجر

```php
// في middleware الخاص بك
app()->bind('current_tenant_id', fn () => auth()->user()->tenant_id);
```

### Row-Level Security للمستأجرين

```php
RlsPolicyModel::create([
    'name'         => 'Tenant Isolation',
    'model'        => '*',             // يطبق على كل النماذج
    'column'       => 'tenant_id',
    'scope'        => 'tenant',
    'value_source' => 'auth_user',
    'value_key'    => 'tenant_id',
    'is_active'    => true,
]);
```

---

## 🧪 الاختبارات

```bash
cd packages/mostafax/analytics-suite

# تثبيت dependencies
composer install

# تشغيل جميع الاختبارات
vendor/bin/pest

# تشغيل Unit Tests فقط
vendor/bin/pest tests/Unit

# تشغيل Feature Tests فقط
vendor/bin/pest tests/Feature

# مع تغطية الكود
vendor/bin/pest --coverage --min=80

# اختبار محدد
vendor/bin/pest tests/Unit/Services/DashboardServiceTest.php
```

### كتابة اختبار للحزمة

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
        // اختبارك هنا
    }
}
```

---

## 🛠 أوامر Artisan

### `analytics-suite:install`

```bash
php artisan analytics-suite:install [options]

Options:
  --force              استبدال الملفات الموجودة
  --skip-migrations    تخطي تشغيل الـ migrations
  --skip-detection     تخطي اكتشاف النماذج
  --skip-defaults      تخطي توليد اللوحات والوِدجات الافتراضية
```

```bash
# تثبيت كامل
php artisan analytics-suite:install

# تثبيت بدون توليد افتراضي
php artisan analytics-suite:install --skip-defaults

# إعادة التثبيت وتجاوز الملفات الموجودة
php artisan analytics-suite:install --force
```

### `analytics-suite:detect-models`

```bash
php artisan analytics-suite:detect-models [options]

Options:
  --generate-widgets     توليد تعريفات وِدجات تلقائية
  --generate-dashboards  توليد لوحات افتراضية
```

```bash
# عرض النماذج المكتشفة فقط
php artisan analytics-suite:detect-models

# عرض + توليد وِدجات
php artisan analytics-suite:detect-models --generate-widgets

# عرض + توليد وِدجات + لوحات
php artisan analytics-suite:detect-models --generate-widgets --generate-dashboards
```

الناتج:
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
  --role=   تعيين كل الصلاحيات لهذا الدور (يتطلب Spatie Permission)
  --guard=  الـ guard (افتراضي: web)
```

```bash
# مزامنة الصلاحيات فقط
php artisan analytics-suite:sync-permissions

# مزامنة وتعيين لدور admin
php artisan analytics-suite:sync-permissions --role=admin

# مع guard مختلف
php artisan analytics-suite:sync-permissions --role=manager --guard=api
```

---

## 📁 هيكل المجلدات

```
packages/mostafax/analytics-suite/
├── composer.json
├── README.md
├── CHANGELOG.md
├── .gitignore
│
├── config/
│   └── analytics-suite.php          # 150+ إعداد
│
├── routes/
│   └── api.php                      # 30+ مسار API
│
├── database/
│   └── migrations/                  # 11 migration
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
│           │   ├── AnalyticsOverview.vue    # نظرة عامة
│           │   └── ExportHistory.vue        # سجل التصديرات
│           ├── dashboard/
│           │   ├── DashboardBuilder.vue     # البنّاء الرئيسي
│           │   ├── DashboardList.vue        # قائمة اللوحات
│           │   ├── WidgetPickerDialog.vue   # حوار إضافة وِدجت
│           │   └── ShareDialog.vue          # مشاركة اللوحة
│           ├── reports/
│           │   └── ReportBuilder.vue        # بنّاء التقارير
│           └── widgets/
│               ├── WidgetRenderer.vue       # موزّع الوِدجات
│               ├── KpiCard.vue
│               ├── StatsCard.vue
│               ├── DataTable.vue
│               ├── ApexChart.vue            # Bar/Line/Area/Pie/Donut
│               ├── GaugeWidget.vue
│               ├── LeaderboardWidget.vue
│               └── ProgressWidget.vue
│
├── src/
│   ├── AnalyticsSuiteManager.php            # نقطة الدخول المركزية
│   │
│   ├── Analytics/
│   │   └── AnalyticsEngine.php             # محرك التحليلات
│   │
│   ├── Cache/
│   │   └── AnalyticsCacheManager.php       # إدارة Redis Cache
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
│   │   └── ModelDetectionEngine.php        # اكتشاف النماذج
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

## ⚙️ مرجع Config كامل

```php
// config/analytics-suite.php
return [

    // ========== Route Configuration ==========
    'routes' => [
        'prefix'     => 'api/analytics',          // بادئة المسارات
        'middleware' => ['api', 'auth:sanctum'],   // Middleware افتراضي
        'name'       => 'analytics.',
    ],

    // ========== Database ==========
    'database' => [
        'connection' => env('ANALYTICS_DB_CONNECTION', 'mysql'),
        'prefix'     => 'as_',                    // بادئة الجداول
    ],

    // ========== Cache ==========
    'cache' => [
        'driver'   => env('ANALYTICS_CACHE_DRIVER', 'redis'),
        'enabled'  => env('ANALYTICS_CACHE_ENABLED', true),
        'prefix'   => env('ANALYTICS_CACHE_PREFIX', 'analytics_suite'),
        'ttl'      => [
            'dashboard' => 300,   // 5 دقائق
            'widget'    => 180,   // 3 دقائق
            'report'    => 600,   // 10 دقائق
            'stats'     => 120,   // دقيقتان
            'schema'    => 3600,  // ساعة
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
        'public_share_expiry'  => 7,         // أيام
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
        'registry'             => [],           // Custom widgets
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

## ❓ الأسئلة الشائعة

**س: كيف أغير prefix المسارات؟**
```php
// config/analytics-suite.php
'routes' => ['prefix' => 'api/v1/analytics']
```

**س: كيف أتعطيل صلاحيات معينة في التطوير؟**
```env
ANALYTICS_ENFORCE_PERMISSIONS=false
```

**س: كيف أضيف مسارات الـ Vue لـ vite.config.js؟**
```javascript
// vite.config.js
resolve: {
    alias: {
        '@analytics': '/resources/js/analytics-suite'
    }
}
```

**س: كيف أُخصص middleware للـ API؟**
```php
// config/analytics-suite.php
'routes' => [
    'middleware' => ['api', 'auth:sanctum', 'verified', 'throttle:60,1'],
]
```

**س: هل يعمل مع Laravel Modules (nwidart/laravel-modules)?**
نعم. أضف مسار الـ Modules في:
```php
'detection' => [
    'module_paths' => [base_path('Modules')],
]
```

---

## 📄 الترخيص

MIT License © 2026 [Mostafa Elbayyar](mailto:mostafa.m.elbiar2@gmail.com)

---

<div align="center">

**صُنع بـ ❤️ لمجتمع Laravel**

[⭐ Star on GitHub](https://github.com/mostafax2/Dynamic-Analytics-Studio) · [🐛 Report Bug](https://github.com/mostafax2/Dynamic-Analytics-Studio/issues) · [💡 Request Feature](https://github.com/mostafax2/Dynamic-Analytics-Studio/issues)

</div>
