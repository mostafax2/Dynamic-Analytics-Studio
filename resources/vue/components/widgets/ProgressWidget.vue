<template>
  <div class="h-full flex flex-col justify-center gap-4">
    <div v-for="(item, i) in bars" :key="i">
      <div class="flex items-center justify-between mb-1">
        <span class="text-sm text-slate-700 dark:text-slate-300">{{ item.label }}</span>
        <span class="text-sm font-bold text-slate-800 dark:text-white">{{ item.pct }}%</span>
      </div>
      <div class="h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
        <div class="h-full rounded-full transition-all duration-700"
          :style="{ width: `${item.pct}%`, background: item.color }"></div>
      </div>
    </div>
    <div v-if="!bars.length" class="flex items-center justify-center text-slate-400 text-sm">No data</div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WidgetData, WidgetConfig } from '../../types'

const COLORS = ['#6366f1', '#22d3ee', '#f59e0b', '#ef4444', '#10b981']

const props = defineProps<{ data?: WidgetData | null; config: WidgetConfig }>()

const bars = computed(() => {
  const rows = (props.data?.data ?? []) as Record<string, unknown>[]
  const max  = Number(props.config.max ?? 100)
  const lk   = props.config.label_column as string | undefined
  const vk   = props.config.value_column as string | undefined

  return rows.map((r, i) => {
    const keys  = Object.keys(r)
    const label = String(r[lk ?? keys[0]] ?? `Item ${i + 1}`)
    const val   = Number(r[vk ?? keys[1]] ?? 0)
    return {
      label,
      pct:   Math.min(100, Math.round((val / max) * 100)),
      color: COLORS[i % COLORS.length],
    }
  })
})
</script>
