<template>
  <div class="h-full flex flex-col items-center justify-center">
    <div class="relative w-32 h-16 overflow-hidden">
      <svg viewBox="0 0 100 50" class="w-full">
        <!-- Background arc -->
        <path d="M10,50 A40,40 0 0,1 90,50" fill="none" stroke="#e2e8f0" stroke-width="8" stroke-linecap="round"/>
        <!-- Value arc -->
        <path :d="arcPath" fill="none" :stroke="arcColor" stroke-width="8" stroke-linecap="round"
          class="transition-all duration-700"/>
      </svg>
      <div class="absolute inset-x-0 bottom-0 flex flex-col items-center">
        <span class="text-2xl font-bold" :class="textColor">{{ formattedValue }}</span>
      </div>
    </div>
    <div class="flex items-center justify-between w-32 mt-1 text-[10px] text-slate-400">
      <span>{{ config.min ?? 0 }}</span>
      <span>{{ config.max ?? 100 }}</span>
    </div>
    <div class="mt-1 text-xs text-slate-500">{{ config.label ?? 'Value' }}</div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WidgetData, WidgetConfig } from '../../types'

const props = defineProps<{ data?: WidgetData | null; config: WidgetConfig }>()

const min = computed(() => Number(props.config.min ?? 0))
const max = computed(() => Number(props.config.max ?? 100))

const rawValue = computed(() => {
  const rows = (props.data?.data ?? []) as Record<string, unknown>[]
  if (!rows.length) return 0
  const row = rows[0]
  return Number(row.value ?? row.count ?? Object.values(row)[0] ?? 0)
})

const pct = computed(() => Math.min(1, Math.max(0, (rawValue.value - min.value) / (max.value - min.value))))

const formattedValue = computed(() => rawValue.value.toLocaleString())

// SVG arc computation (semi-circle from 180° to 0°)
const arcPath = computed(() => {
  const angle = Math.PI * (1 - pct.value)  // from 180° going right
  const x = 50 + 40 * Math.cos(angle)
  const y = 50 - 40 * Math.sin(angle)
  const largeArc = pct.value > 0.5 ? 0 : 1
  return `M10,50 A40,40 0 ${largeArc},1 ${x.toFixed(2)},${y.toFixed(2)}`
})

const arcColor = computed(() => {
  if (pct.value < 0.4) return '#10b981'
  if (pct.value < 0.75) return '#f59e0b'
  return '#ef4444'
})

const textColor = computed(() => {
  if (pct.value < 0.4) return 'text-emerald-600'
  if (pct.value < 0.75) return 'text-amber-600'
  return 'text-rose-600'
})
</script>
