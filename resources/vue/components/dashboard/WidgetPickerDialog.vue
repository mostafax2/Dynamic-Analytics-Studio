<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
          <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Add Widget</h2>
            <p class="text-sm text-slate-500">Choose a widget type to add to your dashboard</p>
          </div>
          <button @click="$emit('close')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <div class="flex flex-1 overflow-hidden">
          <!-- Widget type grid -->
          <div class="w-72 border-r border-slate-200 dark:border-slate-700 p-4 overflow-y-auto">
            <div class="grid grid-cols-2 gap-3">
              <button v-for="type in widgetTypes" :key="type.id"
                @click="selectedType = type"
                :class="['flex flex-col items-center gap-2 p-3 rounded-xl border-2 text-center transition-all',
                  selectedType?.id === type.id
                    ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                    : 'border-slate-200 dark:border-slate-700 hover:border-indigo-300']">
                <span class="text-2xl">{{ type.icon }}</span>
                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ type.label }}</span>
              </button>
            </div>
          </div>

          <!-- Widget configurator -->
          <div class="flex-1 p-6 overflow-y-auto">
            <div v-if="!selectedType" class="flex flex-col items-center justify-center h-full text-slate-400">
              <p>Select a widget type from the left</p>
            </div>

            <div v-else>
              <h3 class="text-base font-semibold text-slate-800 dark:text-white mb-4">
                Configure: {{ selectedType.label }}
              </h3>

              <div class="space-y-4">
                <!-- Title -->
                <div>
                  <label class="as-label">Widget Title</label>
                  <input v-model="config.title" type="text" class="as-input" placeholder="My Widget" />
                </div>

                <!-- Data Source -->
                <div>
                  <label class="as-label">Data Source (Table)</label>
                  <select v-model="config.data_source" class="as-input">
                    <option value="">Select a model...</option>
                    <option v-for="m in models" :key="m.table" :value="m.table">
                      {{ m.name }} ({{ m.table }})
                    </option>
                  </select>
                </div>

                <!-- Report (optional) -->
                <div>
                  <label class="as-label">Or link to a Report Template</label>
                  <select v-model="config.report_id" class="as-input">
                    <option :value="undefined">None (use direct data source)</option>
                    <option v-for="r in reports" :key="r.id" :value="r.id">{{ r.name }}</option>
                  </select>
                </div>

                <!-- Aggregation (for KPI/chart widgets) -->
                <div v-if="isAggregatable">
                  <label class="as-label">Aggregation</label>
                  <select v-model="config.aggregation" class="as-input">
                    <option value="count">Count</option>
                    <option value="sum">Sum</option>
                    <option value="avg">Average</option>
                    <option value="min">Min</option>
                    <option value="max">Max</option>
                    <option value="count_distinct">Distinct Count</option>
                  </select>
                </div>

                <!-- Refresh interval -->
                <div>
                  <label class="as-label">Refresh Interval (seconds, 0 = manual)</label>
                  <input v-model.number="config.refresh_interval" type="number" min="0" class="as-input" />
                </div>

                <!-- Size preset -->
                <div>
                  <label class="as-label">Size</label>
                  <div class="grid grid-cols-3 gap-2">
                    <button v-for="sz in sizes" :key="sz.label"
                      @click="config.position = sz.position"
                      :class="['px-3 py-2 rounded-lg border text-sm font-medium transition-all',
                        JSON.stringify(config.position) === JSON.stringify(sz.position)
                          ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                          : 'border-slate-200 hover:border-indigo-300']">
                      {{ sz.label }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 dark:border-slate-700">
          <button @click="$emit('close')" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium">
            Cancel
          </button>
          <button @click="create" :disabled="!canCreate || creating"
            class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 text-sm font-medium">
            {{ creating ? 'Adding...' : 'Add Widget' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAnalyticsStore, useReportStore } from '../../store/analytics'

const props = defineProps<{ dashboardId: number }>()
const emit  = defineEmits(['close', 'created'])

const analyticsStore = useAnalyticsStore()
const reportStore    = useReportStore()

const models  = computed(() => analyticsStore.modules)
const reports = computed(() => reportStore.items)

const selectedType = ref<{ id: string; label: string; icon: string } | null>(null)
const creating     = ref(false)

const config = ref({
  title:           '',
  data_source:     '',
  report_id:       undefined as number | undefined,
  aggregation:     'count',
  refresh_interval: 300,
  position:        { x: 0, y: 0, w: 4, h: 3 },
})

const widgetTypes = [
  { id: 'kpi_card',    label: 'KPI Card',     icon: '📊' },
  { id: 'stats_card',  label: 'Stats Card',   icon: '🔢' },
  { id: 'data_table',  label: 'Data Table',   icon: '📋' },
  { id: 'bar_chart',   label: 'Bar Chart',    icon: '📊' },
  { id: 'line_chart',  label: 'Line Chart',   icon: '📈' },
  { id: 'area_chart',  label: 'Area Chart',   icon: '🏔️' },
  { id: 'pie_chart',   label: 'Pie Chart',    icon: '🥧' },
  { id: 'donut_chart', label: 'Donut Chart',  icon: '🍩' },
  { id: 'gauge_chart', label: 'Gauge',        icon: '🎯' },
  { id: 'progress',    label: 'Progress',     icon: '⬆️' },
  { id: 'leaderboard', label: 'Leaderboard',  icon: '🏆' },
  { id: 'trend',       label: 'Trend',        icon: '📉' },
]

const sizes = [
  { label: 'Small',  position: { x: 0, y: 0, w: 3, h: 2 } },
  { label: 'Medium', position: { x: 0, y: 0, w: 4, h: 3 } },
  { label: 'Large',  position: { x: 0, y: 0, w: 6, h: 4 } },
  { label: 'Wide',   position: { x: 0, y: 0, w: 12, h: 3 } },
  { label: 'Tall',   position: { x: 0, y: 0, w: 4, h: 6 } },
  { label: 'Full',   position: { x: 0, y: 0, w: 12, h: 5 } },
]

const isAggregatable = computed(() =>
  selectedType.value && ['kpi_card', 'stats_card', 'bar_chart', 'line_chart', 'area_chart', 'gauge_chart'].includes(selectedType.value.id)
)

const canCreate = computed(() =>
  selectedType.value && config.value.title && (config.value.data_source || config.value.report_id)
)

onMounted(async () => {
  await Promise.all([
    analyticsStore.fetchModules(),
    reportStore.fetchAll(),
  ])
})

async function create() {
  if (!canCreate.value || !selectedType.value) return
  creating.value = true

  const body = {
    type:             selectedType.value.id,
    title:            config.value.title,
    config:           {
      data_source:  config.value.data_source,
      aggregation:  config.value.aggregation,
    },
    position:         config.value.position,
    refresh_interval: config.value.refresh_interval,
    report_id:        config.value.report_id,
  }

  try {
    const res = await fetch(`/api/analytics/dashboards/${props.dashboardId}/widgets`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify(body),
      credentials: 'include',
    })
    const data = await res.json()
    emit('created', data.data ?? data)
  } finally {
    creating.value = false
  }
}
</script>

<style scoped>
@reference "tailwindcss";
.as-label { @apply block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1; }
.as-input  { @apply w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500; }
</style>
