<template>
  <div class="as-report-builder h-full flex flex-col bg-slate-50 dark:bg-slate-900">
    <!-- Toolbar -->
    <header class="flex items-center justify-between px-6 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm">
      <div class="flex items-center gap-3">
        <button @click="$emit('back')" class="as-btn-ghost">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </button>
        <div>
          <input v-model="form.name" class="text-lg font-bold bg-transparent border-b-2 border-indigo-500 outline-none text-slate-800 dark:text-white" placeholder="Report Name" />
          <p class="text-xs text-slate-500">Visual Report Builder · {{ form.source_type?.toUpperCase() }}</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <button @click="runReport" :disabled="running || !form.data_source" class="as-btn-secondary">
          <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          {{ running ? 'Running...' : 'Run' }}
        </button>
        <button @click="saveReport" :disabled="saving" class="as-btn-primary">
          {{ saving ? 'Saving...' : 'Save Report' }}
        </button>
        <div class="relative">
          <button @click="exportMenu = !exportMenu" class="as-btn-secondary">Export ▾</button>
          <div v-if="exportMenu" class="absolute right-0 mt-1 w-32 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden z-10">
            <button v-for="fmt in ['pdf','excel','csv','json']" :key="fmt"
              @click="queueExport(fmt)"
              class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200">
              {{ fmt.toUpperCase() }}
            </button>
          </div>
        </div>
      </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
      <!-- Left panel: Builder -->
      <aside class="w-80 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex flex-col overflow-y-auto">
        <div class="p-4 space-y-5">
          <!-- Data Source -->
          <section>
            <label class="as-label">Data Source</label>
            <select v-model="form.data_source" class="as-input" @change="onSourceChange">
              <option value="">Select model / table...</option>
              <option v-for="m in detectedModels" :key="m.table" :value="m.table">
                {{ m.name }} ({{ m.table }})
              </option>
            </select>
          </section>

          <!-- Source Type -->
          <section>
            <label class="as-label">Engine</label>
            <div class="flex gap-2">
              <button v-for="t in ['mysql','mongodb']" :key="t"
                @click="form.source_type = t"
                :class="['flex-1 py-1.5 text-sm font-medium rounded-lg border transition-all',
                  form.source_type === t
                    ? 'border-indigo-500 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
                    : 'border-slate-200 dark:border-slate-600 text-slate-600']">
                {{ t.toUpperCase() }}
              </button>
            </div>
          </section>

          <!-- Columns -->
          <section>
            <div class="flex items-center justify-between mb-2">
              <label class="as-label mb-0">Columns</label>
              <button @click="addColumn" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">+ Add</button>
            </div>
            <div class="space-y-2">
              <div v-for="(col, i) in form.columns" :key="i"
                class="flex items-center gap-2 p-2 bg-slate-50 dark:bg-slate-700 rounded-lg">
                <input v-model="form.columns[i].field" type="text" class="as-input-sm flex-1" placeholder="column" />
                <input v-model="form.columns[i].alias" type="text" class="as-input-sm w-24" placeholder="alias" />
                <button @click="form.columns.splice(i, 1)" class="text-rose-400 hover:text-rose-600">✕</button>
              </div>
              <div v-if="!form.columns.length" class="text-xs text-slate-400 text-center py-2">All columns (*)</div>
            </div>
          </section>

          <!-- Aggregations -->
          <section>
            <div class="flex items-center justify-between mb-2">
              <label class="as-label mb-0">Aggregations</label>
              <button @click="addAgg" class="text-xs text-indigo-600 font-medium">+ Add</button>
            </div>
            <div class="space-y-2">
              <div v-for="(agg, i) in form.aggregations" :key="i"
                class="flex items-center gap-1 p-2 bg-slate-50 dark:bg-slate-700 rounded-lg">
                <select v-model="form.aggregations[i].function" class="as-input-sm w-24">
                  <option v-for="fn in aggFunctions" :key="fn" :value="fn">{{ fn.toUpperCase() }}</option>
                </select>
                <input v-model="form.aggregations[i].column" class="as-input-sm flex-1" placeholder="column" />
                <input v-model="form.aggregations[i].alias" class="as-input-sm w-20" placeholder="alias" />
                <button @click="form.aggregations.splice(i, 1)" class="text-rose-400">✕</button>
              </div>
            </div>
          </section>

          <!-- Filters -->
          <section>
            <div class="flex items-center justify-between mb-2">
              <label class="as-label mb-0">Filters</label>
              <button @click="addFilter" class="text-xs text-indigo-600 font-medium">+ Add</button>
            </div>
            <div class="space-y-2">
              <div v-for="(f, i) in form.filters" :key="i"
                class="grid grid-cols-3 gap-1 p-2 bg-slate-50 dark:bg-slate-700 rounded-lg">
                <input v-model="form.filters[i].column" class="as-input-sm" placeholder="column" />
                <select v-model="form.filters[i].operator" class="as-input-sm">
                  <option v-for="op in filterOps" :key="op.value" :value="op.value">{{ op.label }}</option>
                </select>
                <div class="flex items-center gap-1">
                  <input v-model="form.filters[i].value" class="as-input-sm flex-1" placeholder="value" />
                  <button @click="form.filters.splice(i, 1)" class="text-rose-400">✕</button>
                </div>
              </div>
            </div>
          </section>

          <!-- Group By & Order By -->
          <section class="grid grid-cols-2 gap-3">
            <div>
              <label class="as-label">Group By</label>
              <input v-model="groupByInput" type="text" class="as-input" placeholder="col1, col2" />
            </div>
            <div>
              <label class="as-label">Order By</label>
              <input v-model="orderByInput" type="text" class="as-input" placeholder="col DESC" />
            </div>
          </section>
        </div>
      </aside>

      <!-- Right: Preview -->
      <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Result tabs -->
        <div class="px-4 pt-3 flex gap-2 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
          <button v-for="tab in ['table','json']" :key="tab"
            @click="activeTab = tab"
            :class="['px-4 py-2 text-sm font-medium border-b-2 transition-colors -mb-px',
              activeTab === tab ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500']">
            {{ tab.toUpperCase() }}
          </button>
          <div v-if="runResult" class="ml-auto text-xs text-slate-400 self-center">
            {{ runResult.total }} rows · {{ runResult.meta?.execution_ms ?? '—' }}ms
          </div>
        </div>

        <div class="flex-1 overflow-auto p-4">
          <div v-if="running" class="flex items-center justify-center h-full">
            <div class="text-center text-slate-400">
              <div class="w-8 h-8 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
              <p class="text-sm">Running query...</p>
            </div>
          </div>

          <div v-else-if="!runResult" class="flex flex-col items-center justify-center h-full text-slate-400 text-center">
            <svg class="w-16 h-16 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p>Configure your report and click <strong>Run</strong></p>
          </div>

          <!-- Table view -->
          <div v-else-if="activeTab === 'table'" class="overflow-auto">
            <table class="w-full text-sm border-collapse">
              <thead>
                <tr class="bg-slate-100 dark:bg-slate-700">
                  <th v-for="col in resultColumns" :key="col"
                    class="px-4 py-2 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                    {{ col }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, i) in runResult.rows" :key="i"
                  :class="i % 2 === 0 ? 'bg-white dark:bg-slate-800' : 'bg-slate-50 dark:bg-slate-750'">
                  <td v-for="col in resultColumns" :key="col"
                    class="px-4 py-2 text-xs text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    {{ row[col] ?? '—' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- JSON view -->
          <pre v-else class="text-xs text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-xl p-4 overflow-auto h-full">{{ JSON.stringify(runResult, null, 2) }}</pre>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { ReportTemplate } from '../../types'
import { useAnalyticsStore, useReportStore } from '../../store/analytics'

const props = defineProps<{ reportId?: number }>()
const emit  = defineEmits(['back', 'saved'])

const analyticsStore = useAnalyticsStore()
const reportStore    = useReportStore()

const detectedModels = computed(() => analyticsStore.modules)

const form = ref({
  name:         'New Report',
  data_source:  '',
  source_type:  'mysql',
  columns:      [] as { field: string; alias: string }[],
  filters:      [] as { column: string; operator: string; value: unknown }[],
  aggregations: [] as { function: string; column: string; alias: string }[],
  group_by:     [] as string[],
  order_by:     [] as { column: string; direction: string }[],
  settings:     {},
})

const groupByInput  = ref('')
const orderByInput  = ref('')
const running       = ref(false)
const saving        = ref(false)
const exportMenu    = ref(false)
const activeTab     = ref('table')
const runResult     = ref<{ rows: Record<string, unknown>[]; total: number; columns: string[]; meta: Record<string, unknown> } | null>(null)

const resultColumns = computed(() =>
  runResult.value?.columns?.length
    ? runResult.value.columns
    : runResult.value?.rows?.length ? Object.keys(runResult.value.rows[0]) : []
)

const aggFunctions = ['count', 'sum', 'avg', 'min', 'max', 'count_distinct']

const filterOps = [
  { value: 'eq',          label: '=' },
  { value: 'neq',         label: '≠' },
  { value: 'gt',          label: '>' },
  { value: 'lt',          label: '<' },
  { value: 'gte',         label: '≥' },
  { value: 'lte',         label: '≤' },
  { value: 'like',        label: 'LIKE' },
  { value: 'in',          label: 'IN' },
  { value: 'not_in',      label: 'NOT IN' },
  { value: 'between',     label: 'BETWEEN' },
  { value: 'null',        label: 'IS NULL' },
  { value: 'not_null',    label: 'IS NOT NULL' },
  { value: 'starts_with', label: 'STARTS WITH' },
  { value: 'ends_with',   label: 'ENDS WITH' },
  { value: 'contains',    label: 'CONTAINS' },
]

onMounted(async () => {
  await analyticsStore.fetchModules()
  if (props.reportId) {
    const r = await reportStore.fetchOne(props.reportId)
    if (r) Object.assign(form.value, r)
  }
})

function onSourceChange() {
  form.value.columns = []
  form.value.filters = []
}

function addColumn() { form.value.columns.push({ field: '', alias: '' }) }
function addAgg()    { form.value.aggregations.push({ function: 'count', column: '*', alias: 'count' }) }
function addFilter() { form.value.filters.push({ column: '', operator: 'eq', value: '' }) }

async function runReport() {
  running.value = true
  runResult.value = null
  try {
    const payload = {
      source:        form.value.source_type || 'mysql',
      table:         form.value.data_source,
      fields:        form.value.columns,
      filters:       form.value.filters?.length
                       ? { operator: 'AND', conditions: form.value.filters.map((f: Record<string,string>) => ({ field: f.column, operator: f.operator, value: f.value })) }
                       : undefined,
      aggregations:  form.value.aggregations,
      group_by:      groupByInput.value.split(',').map((s: string) => s.trim()).filter(Boolean),
      order_by:      orderByInput.value ? [{ column: orderByInput.value.replace(' DESC','').replace(' ASC','').trim(), direction: orderByInput.value.includes('DESC') ? 'desc' : 'asc' }] : [],
    }

    if (props.reportId) {
      runResult.value = await reportStore.run(props.reportId, payload)
    } else {
      // Inline run via reporting engine
      const res = await fetch('/api/analytics/reports/run-preview', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body:    JSON.stringify(payload),
        credentials: 'include',
      })
      runResult.value = await res.json()
    }
  } finally {
    running.value = false
  }
}

async function saveReport() {
  saving.value = true
  try {
    const data = {
      ...form.value,
      group_by: groupByInput.value.split(',').map(s => s.trim()).filter(Boolean),
      order_by: orderByInput.value ? [{ column: orderByInput.value.split(' ')[0], direction: orderByInput.value.includes('DESC') ? 'desc' : 'asc' }] : [],
    }
    if (props.reportId) {
      await reportStore.update(props.reportId, data)
    } else {
      const r = await reportStore.create(data)
      emit('saved', r)
    }
  } finally {
    saving.value = false
  }
}

async function queueExport(format: string) {
  exportMenu.value = false
  if (!props.reportId) { alert('Save the report first'); return }
  await fetch('/api/analytics/exports/queue', {
    method:  'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body:    JSON.stringify({ type: 'report', resource_id: props.reportId, format }),
    credentials: 'include',
  })
  alert(`${format.toUpperCase()} export queued. Check Export History.`)
}
</script>

<style scoped>
@reference "tailwindcss";
.as-label    { @apply block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wide; }
.as-input    { @apply w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500; }
.as-input-sm { @apply px-2 py-1 rounded-md border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500; }
.as-btn-primary  { @apply inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors disabled:opacity-50; }
.as-btn-secondary{ @apply inline-flex items-center px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors disabled:opacity-50; }
.as-btn-ghost    { @apply p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors; }
</style>
