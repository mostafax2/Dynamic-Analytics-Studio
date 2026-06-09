<template>
  <div class="as-widget h-full flex flex-col" :class="themeClass">
    <!-- Widget header -->
    <div class="as-widget-header flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700">
      <div class="flex items-center gap-2 min-w-0">
        <span class="text-base">{{ widgetIcon }}</span>
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ widget.title }}</h3>
        <span v-if="data?.from_cache" class="text-[10px] px-1.5 py-0.5 bg-blue-100 text-blue-600 rounded-full">cached</span>
      </div>
      <div class="flex items-center gap-1 flex-shrink-0">
        <button @click="$emit('refresh')" class="as-widget-btn" title="Refresh">
          <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': loading }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </button>
        <button @click="$emit('edit')" class="as-widget-btn" title="Edit">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </button>
        <button @click="$emit('delete')" class="as-widget-btn text-rose-400 hover:text-rose-600" title="Delete">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
      </div>
    </div>

    <!-- Widget body -->
    <div class="as-widget-body flex-1 p-4 overflow-hidden">
      <!-- Loading skeleton -->
      <div v-if="loading && !data" class="h-full flex items-center justify-center">
        <div class="space-y-2 w-full">
          <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
          <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded animate-pulse w-3/4"></div>
          <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded animate-pulse w-1/2"></div>
        </div>
      </div>

      <!-- Error state -->
      <div v-else-if="data?.error" class="h-full flex items-center justify-center">
        <div class="text-center text-rose-500">
          <svg class="w-8 h-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-xs">{{ data.error }}</p>
        </div>
      </div>

      <!-- KPI Card -->
      <KpiCard v-else-if="widget.type === 'kpi_card'" :data="data" :config="widget.config" />

      <!-- Stats Card -->
      <StatsCard v-else-if="widget.type === 'stats_card'" :data="data" :config="widget.config" />

      <!-- Data Table -->
      <DataTable v-else-if="widget.type === 'data_table'" :data="data" :config="widget.config" />

      <!-- Charts (ApexCharts) -->
      <ApexChart v-else-if="isChartType" :type="chartApexType" :data="data" :config="widget.config" />

      <!-- Gauge -->
      <GaugeWidget v-else-if="widget.type === 'gauge_chart'" :data="data" :config="widget.config" />

      <!-- Leaderboard -->
      <LeaderboardWidget v-else-if="widget.type === 'leaderboard'" :data="data" :config="widget.config" />

      <!-- Progress -->
      <ProgressWidget v-else-if="widget.type === 'progress'" :data="data" :config="widget.config" />

      <!-- Custom / unknown -->
      <div v-else class="h-full flex items-center justify-center text-slate-400 text-sm">
        Widget type <strong class="mx-1">{{ widget.type }}</strong> not rendered
      </div>
    </div>

    <!-- Footer meta -->
    <div v-if="data?.execution_ms" class="px-4 py-1.5 text-[10px] text-slate-400 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
      <span>{{ data.meta?.total ?? '' }} rows</span>
      <span>{{ data.execution_ms }}ms</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Widget, WidgetData } from '../../types'
import KpiCard        from './KpiCard.vue'
import StatsCard      from './StatsCard.vue'
import DataTable      from './DataTable.vue'
import ApexChart      from './ApexChart.vue'
import GaugeWidget    from './GaugeWidget.vue'
import LeaderboardWidget from './LeaderboardWidget.vue'
import ProgressWidget from './ProgressWidget.vue'

const props = defineProps<{
  widget:  Widget
  data?:   WidgetData | null
  loading?: boolean
}>()

defineEmits(['edit', 'delete', 'refresh'])

const ICONS: Record<string, string> = {
  kpi_card:    '📊', stats_card:  '🔢', data_table:  '📋',
  bar_chart:   '📊', line_chart:  '📈', area_chart:  '🏔️',
  pie_chart:   '🥧', donut_chart: '🍩', gauge_chart: '🎯',
  progress:    '⬆️', leaderboard: '🏆', trend:       '📉',
  comparison:  '⚖️', growth:      '🚀', custom:      '⚙️',
}

const CHART_TYPES: Record<string, string> = {
  bar_chart:   'bar',
  line_chart:  'line',
  area_chart:  'area',
  pie_chart:   'pie',
  donut_chart: 'donut',
}

const widgetIcon    = computed(() => ICONS[props.widget.type] ?? '📦')
const isChartType   = computed(() => props.widget.type in CHART_TYPES)
const chartApexType = computed(() => CHART_TYPES[props.widget.type] ?? 'bar')
const loading       = computed(() => !props.data)

const themeClass = computed(() =>
  'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100'
)
</script>

<style scoped>
@reference "tailwindcss";
.as-widget-btn { @apply p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 transition-colors; }
</style>
