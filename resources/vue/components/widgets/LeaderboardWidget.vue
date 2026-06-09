<template>
  <div class="h-full overflow-auto space-y-2">
    <div v-for="(item, i) in rows" :key="i"
      class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
      <!-- Rank badge -->
      <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
        :class="rankClass(i)">
        {{ i + 1 }}
      </div>
      <!-- Label -->
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">{{ item.label }}</p>
      </div>
      <!-- Bar + value -->
      <div class="flex items-center gap-2 flex-shrink-0">
        <div class="w-24 h-1.5 bg-slate-200 dark:bg-slate-600 rounded-full overflow-hidden">
          <div class="h-full bg-indigo-500 rounded-full transition-all duration-700"
            :style="{ width: `${pct(item.value)}%` }"></div>
        </div>
        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 w-10 text-right">
          {{ formatNum(item.value) }}
        </span>
      </div>
    </div>
    <div v-if="!rows.length" class="flex items-center justify-center h-full text-slate-400 text-sm">No data</div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WidgetData, WidgetConfig } from '../../types'

const props = defineProps<{ data?: WidgetData | null; config: WidgetConfig }>()

interface LeaderboardRow { label: string; value: number }

const rows = computed<LeaderboardRow[]>(() => {
  const raw = (props.data?.data ?? []) as Record<string, unknown>[]
  const lk  = props.config.label_column as string | undefined
  const vk  = props.config.value_column as string | undefined
  return raw.map(r => {
    const keys   = Object.keys(r)
    const label  = String(r[lk ?? keys[0]] ?? '')
    const value  = Number(r[vk ?? keys[1]] ?? 0)
    return { label, value }
  }).sort((a, b) => b.value - a.value)
})

const maxVal = computed(() => Math.max(...rows.value.map(r => r.value), 1))

function pct(v: number) { return (v / maxVal.value) * 100 }
function formatNum(v: number) { return v >= 1000 ? (v / 1000).toFixed(1) + 'k' : v.toLocaleString() }
function rankClass(i: number) {
  if (i === 0) return 'bg-amber-400 text-white'
  if (i === 1) return 'bg-slate-300 text-slate-800'
  if (i === 2) return 'bg-amber-600 text-white'
  return 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400'
}
</script>
