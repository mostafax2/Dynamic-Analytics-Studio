<template>
  <div class="h-full flex flex-col items-center justify-center text-center">
    <div class="text-4xl font-bold tracking-tight" :class="valueColor">
      {{ formattedValue }}
    </div>
    <div v-if="trend !== null" class="flex items-center gap-1 mt-2">
      <svg v-if="trend >= 0" class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
      </svg>
      <svg v-else class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>
      </svg>
      <span class="text-sm font-medium" :class="trend >= 0 ? 'text-emerald-600' : 'text-rose-500'">
        {{ Math.abs(trend) }}% vs last period
      </span>
    </div>
    <div class="mt-1 text-xs text-slate-500">{{ config.label ?? 'Total' }}</div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WidgetData, WidgetConfig } from '../../types'

const props = defineProps<{ data?: WidgetData | null; config: WidgetConfig }>()

const value = computed(() => {
  if (!props.data?.data) return 0
  const rows = props.data.data as unknown[]
  if (Array.isArray(rows) && rows.length > 0) {
    const row = rows[0] as Record<string, unknown>
    return row.value ?? row.count ?? row[Object.keys(row)[0]] ?? 0
  }
  return props.data.data
})

const formattedValue = computed(() => {
  const v = Number(value.value)
  if (isNaN(v)) return value.value
  if (v >= 1_000_000) return (v / 1_000_000).toFixed(1) + 'M'
  if (v >= 1_000)     return (v / 1_000).toFixed(1) + 'K'
  return v.toLocaleString()
})

const trend      = computed(() => props.config.show_trend ? (props.data?.meta as Record<string, unknown>)?.trend as number ?? null : null)
const valueColor = computed(() => 'text-indigo-600 dark:text-indigo-400')
</script>
