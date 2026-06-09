<template>
  <div class="h-full overflow-auto">
    <table v-if="rows.length" class="w-full text-xs text-left">
      <thead class="sticky top-0 bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
        <tr>
          <th v-for="col in columns" :key="col"
            @click="sortBy(col)"
            class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-400 cursor-pointer hover:text-indigo-600 select-none whitespace-nowrap">
            {{ col }}
            <span v-if="sortCol === col">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, i) in sortedRows" :key="i"
          class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
          <td v-for="col in columns" :key="col" class="px-3 py-2 text-slate-700 dark:text-slate-300">
            {{ formatCell(row[col]) }}
          </td>
        </tr>
      </tbody>
    </table>
    <div v-else class="flex items-center justify-center h-full text-slate-400 text-sm">No data</div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { WidgetData, WidgetConfig } from '../../types'

const props = defineProps<{ data?: WidgetData | null; config: WidgetConfig }>()

const sortCol = ref<string | null>(null)
const sortDir = ref<'asc' | 'desc'>('asc')

const rows    = computed(() => (props.data?.data ?? []) as Record<string, unknown>[])
const columns = computed(() =>
  props.config.columns?.length
    ? props.config.columns as string[]
    : rows.value.length ? Object.keys(rows.value[0]) : []
)

const sortedRows = computed(() => {
  if (!sortCol.value) return rows.value
  return [...rows.value].sort((a, b) => {
    const va = a[sortCol.value!]
    const vb = b[sortCol.value!]
    const cmp = String(va).localeCompare(String(vb), undefined, { numeric: true })
    return sortDir.value === 'asc' ? cmp : -cmp
  })
})

function sortBy(col: string) {
  if (sortCol.value === col) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortCol.value = col
    sortDir.value = 'asc'
  }
}

function formatCell(v: unknown): string {
  if (v === null || v === undefined) return '—'
  if (typeof v === 'number') return v.toLocaleString()
  return String(v)
}
</script>
