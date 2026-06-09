<template>
  <div class="h-full flex flex-col justify-between">
    <div v-for="(stat, i) in stats" :key="i"
      class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700 last:border-0">
      <span class="text-sm text-slate-600 dark:text-slate-400">{{ stat.label }}</span>
      <span class="text-sm font-bold text-slate-800 dark:text-white">{{ stat.value }}</span>
    </div>
    <div v-if="!stats.length" class="flex-1 flex items-center justify-center text-slate-400 text-sm">No data</div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WidgetData, WidgetConfig } from '../../types'

const props = defineProps<{ data?: WidgetData | null; config: WidgetConfig }>()

const stats = computed(() => {
  const rows = (props.data?.data ?? []) as Record<string, unknown>[]
  if (!rows.length) return []
  const row = rows[0]
  return Object.entries(row).map(([k, v]) => ({
    label: k.replace(/_/g, ' '),
    value: typeof v === 'number' ? v.toLocaleString() : String(v ?? '—'),
  }))
})
</script>
