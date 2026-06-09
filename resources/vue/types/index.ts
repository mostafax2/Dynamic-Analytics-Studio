export interface Dashboard {
  id: number
  name: string
  slug: string
  description?: string
  layout: LayoutItem[]
  settings: Record<string, unknown>
  is_public: boolean
  public_token?: string
  public_expires_at?: string
  is_default: boolean
  widget_count: number
  widgets: Widget[]
  created_at: string
  updated_at: string
}

export interface Widget {
  id: number
  dashboard_id: number
  type: WidgetType
  title: string
  description?: string
  config: WidgetConfig
  position: Position
  styling: Record<string, unknown>
  refresh_interval: number
  cache_enabled: boolean
  cache_ttl: number
  report_id?: number
  report_params: Record<string, unknown>
  created_at: string
  updated_at: string
}

export interface LayoutItem {
  widget_id: number
  x: number
  y: number
  w: number
  h: number
}

export interface Position {
  x: number
  y: number
  w: number
  h: number
}

export interface WidgetConfig {
  data_source?: string
  source_type?: 'mysql' | 'mongodb'
  columns?: string[]
  filters?: FilterCondition[]
  aggregation?: AggregationType
  column?: string
  group_by?: string
  order_by?: OrderBy[]
  limit?: number
  interval?: 'day' | 'week' | 'month' | 'year'
  label_column?: string
  value_column?: string
  [key: string]: unknown
}

export type WidgetType =
  | 'kpi_card'
  | 'stats_card'
  | 'data_table'
  | 'bar_chart'
  | 'pie_chart'
  | 'donut_chart'
  | 'line_chart'
  | 'area_chart'
  | 'gauge_chart'
  | 'progress'
  | 'comparison'
  | 'growth'
  | 'trend'
  | 'leaderboard'
  | 'custom'

export type AggregationType = 'count' | 'sum' | 'avg' | 'min' | 'max' | 'count_distinct'

export interface FilterCondition {
  column: string
  operator: FilterOperator
  value?: unknown
  value2?: unknown  // for 'between'
}

export type FilterOperator =
  | 'eq' | 'neq' | 'gt' | 'gte' | 'lt' | 'lte'
  | 'between' | 'in' | 'not_in' | 'like'
  | 'starts_with' | 'ends_with' | 'contains'
  | 'null' | 'not_null'

export interface OrderBy {
  column: string
  direction: 'asc' | 'desc'
}

export interface ReportTemplate {
  id: number
  name: string
  description?: string
  data_source: string
  source_type: 'mysql' | 'mongodb'
  columns: ReportColumn[]
  filters: FilterCondition[]
  group_by: string[]
  order_by: OrderBy[]
  aggregations: AggregationDef[]
  joins: JoinDef[]
  settings: Record<string, unknown>
  is_template: boolean
  category?: string
  tags: string[]
  created_by: number
  created_at: string
  updated_at: string
}

export interface ReportColumn {
  field: string
  alias?: string
  label?: string
}

export interface AggregationDef {
  function: AggregationType
  column: string
  alias: string
}

export interface JoinDef {
  table: string
  local_key: string
  foreign_key: string
  type: 'inner' | 'left' | 'right'
}

export interface WidgetData {
  widget_id: number
  type: WidgetType
  data: unknown
  meta: {
    total: number
    columns: string[]
    aggregations?: Record<string, number>
  }
  from_cache: boolean
  cached_at?: string
  execution_ms: number
  error?: string
}

export interface DetectedModel {
  class: string
  name: string
  table: string
  module: string
  fillable: string[]
  casts: Record<string, string>
  relationships: RelationshipDef[]
  columns: string[]
  has_soft_deletes: boolean
  primary_key: string
}

export interface RelationshipDef {
  method: string
  type: string
  related: string
}

export interface ScheduledReport {
  id: number
  report_id: number
  name: string
  frequency: 'daily' | 'weekly' | 'monthly' | 'quarterly' | 'yearly'
  cron_expression: string
  format: 'pdf' | 'excel' | 'csv'
  delivery_methods: string[]
  recipients: string[]
  webhook_url?: string
  is_active: boolean
  last_run_at?: string
  next_run_at?: string
}

export interface ExportJob {
  id: number
  status: 'pending' | 'processing' | 'done' | 'failed'
  filename?: string
  rows?: number
  size?: number
  error?: string
  completed?: string
}
