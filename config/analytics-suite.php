<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Package Identity
    |--------------------------------------------------------------------------
    */
    'name'    => 'Enterprise Analytics Suite',
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'prefix'     => 'api/analytics',
        'middleware' => ['api', 'auth:sanctum'],
        'name'       => 'analytics.',
    ],

    'ui_routes' => [
        'prefix'     => 'analytics',
        'middleware' => ['web', 'auth'],
        'name'       => 'analytics.ui.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */
    'database' => [
        'connection' => env('ANALYTICS_DB_CONNECTION', 'mysql'),
        'prefix'     => 'as_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'driver'       => env('ANALYTICS_CACHE_DRIVER', 'redis'),
        'ttl'          => [
            'dashboard' => env('ANALYTICS_CACHE_DASHBOARD_TTL', 300),
            'widget'    => env('ANALYTICS_CACHE_WIDGET_TTL', 180),
            'report'    => env('ANALYTICS_CACHE_REPORT_TTL', 600),
            'stats'     => env('ANALYTICS_CACHE_STATS_TTL', 120),
            'schema'    => env('ANALYTICS_CACHE_SCHEMA_TTL', 3600),
        ],
        'prefix'       => env('ANALYTICS_CACHE_PREFIX', 'analytics_suite'),
        'enabled'      => env('ANALYTICS_CACHE_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    'security' => [
        'enforce_permissions'    => env('ANALYTICS_ENFORCE_PERMISSIONS', true),
        'row_level_security'     => env('ANALYTICS_RLS_ENABLED', true),
        'tenant_isolation'       => env('ANALYTICS_TENANT_ISOLATION', false),
        'tenant_column'          => env('ANALYTICS_TENANT_COLUMN', 'tenant_id'),
        'branch_column'          => env('ANALYTICS_BRANCH_COLUMN', 'branch_id'),
        'department_column'      => env('ANALYTICS_DEPARTMENT_COLUMN', 'department_id'),
        'max_export_rows'        => env('ANALYTICS_MAX_EXPORT_ROWS', 100000),
        'max_query_rows'         => env('ANALYTICS_MAX_QUERY_ROWS', 50000),
        'public_share_enabled'   => env('ANALYTICS_PUBLIC_SHARE', true),
        'public_share_expiry'    => env('ANALYTICS_PUBLIC_SHARE_EXPIRY', 7), // days
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy
    |--------------------------------------------------------------------------
    */
    'multi_tenancy' => [
        'enabled'    => env('ANALYTICS_MULTI_TENANCY', false),
        'strategy'   => env('ANALYTICS_TENANT_STRATEGY', 'single_db'), // single_db | multi_db
        'resolver'   => null, // Custom tenant resolver class
    ],

    /*
    |--------------------------------------------------------------------------
    | Reporting Engine Integration
    |--------------------------------------------------------------------------
    */
    'reporting_engine' => [
        'default_data_source' => env('ANALYTICS_DEFAULT_SOURCE', 'mysql'),
        'mongo_enabled'       => env('ANALYTICS_MONGO_ENABLED', false),
        'mongo_connection'    => env('ANALYTICS_MONGO_CONNECTION', 'mongodb'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Detection Engine
    |--------------------------------------------------------------------------
    */
    'detection' => [
        'scan_paths' => [
            app_path('Models'),
        ],
        'module_paths' => [
            base_path('Modules'),
        ],
        'excluded_models' => [
            'Migration',
            'PersonalAccessToken',
        ],
        'auto_generate_widgets'    => env('ANALYTICS_AUTO_WIDGETS', true),
        'auto_generate_dashboards' => env('ANALYTICS_AUTO_DASHBOARDS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Builder
    |--------------------------------------------------------------------------
    */
    'dashboard' => [
        'default_layout'       => 'grid',   // grid | masonry | fixed
        'grid_columns'         => 12,
        'default_refresh_rate' => 300,       // seconds; 0 = manual
        'max_widgets_per_dash' => 50,
        'allow_public'         => true,
        'snapshot_enabled'     => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Engine
    |--------------------------------------------------------------------------
    */
    'widgets' => [
        'default_refresh_rate' => 300,
        'types'                => [
            'kpi_card', 'stats_card', 'data_table',
            'bar_chart', 'pie_chart', 'donut_chart',
            'line_chart', 'area_chart', 'gauge_chart',
            'progress', 'comparison', 'growth',
            'trend', 'leaderboard', 'custom',
        ],
        'marketplace_enabled'  => true,
        'registry'             => [], // Custom widget classes registered via AnalyticsSuite::registerWidget()
    ],

    /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */
    'export' => [
        'disk'       => env('ANALYTICS_EXPORT_DISK', 'local'),
        'path'       => 'analytics/exports',
        'formats'    => ['pdf', 'excel', 'csv', 'json'],
        'rtl'        => env('ANALYTICS_RTL', false),
        'locale'     => env('ANALYTICS_LOCALE', 'en'),
        'queue'      => env('ANALYTICS_EXPORT_QUEUE', 'default'),
        'chunk_size' => env('ANALYTICS_EXPORT_CHUNK', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduling
    |--------------------------------------------------------------------------
    */
    'scheduling' => [
        'enabled'          => env('ANALYTICS_SCHEDULING_ENABLED', true),
        'delivery_methods' => ['email', 'notification', 'webhook'],
        'queue'            => env('ANALYTICS_SCHEDULE_QUEUE', 'default'),
        'from_email'       => env('ANALYTICS_FROM_EMAIL', env('MAIL_FROM_ADDRESS')),
        'from_name'        => env('ANALYTICS_FROM_NAME', env('MAIL_FROM_NAME', 'Analytics Suite')),
    ],

    /*
    |--------------------------------------------------------------------------
    | UI / Frontend
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'theme'           => env('ANALYTICS_THEME', 'light'), // light | dark | auto
        'locale'          => env('ANALYTICS_LOCALE', 'en'),
        'rtl'             => env('ANALYTICS_RTL', false),
        'brand_name'      => env('ANALYTICS_BRAND', 'Analytics Suite'),
        'logo_url'        => env('ANALYTICS_LOGO_URL', null),
        'primary_color'   => env('ANALYTICS_PRIMARY_COLOR', '#6366f1'),
        'sidebar_enabled' => true,
    ],

];
