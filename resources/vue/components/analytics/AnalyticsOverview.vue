<template>
  <div class="p-6 overflow-auto h-full">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Analytics Overview</h1>
    <p class="text-slate-500 text-sm mb-6">Detected modules and auto-generated statistics</p>

    <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div v-for="i in 4" :key="i" class="h-24 bg-white dark:bg-slate-800 rounded-2xl animate-pulse"></div>
    </div>

    <div v-else-if="modules.length === 0" class="text-center py-16 text-slate-400">
      <p>No modules detected. Run <code class="bg-slate-100 px-1.5 py-0.5 rounded">php artisan analytics-suite:detect-models</code></p>
    </div>

    <div v-else>
      <!-- Module cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <div v-for="mod in modules" :key="mod.class"
          class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
          <div class="flex items-center justify-between mb-3">
            <div>
              <h3 class="font-semibold text-slate-800 dark:text-white">{{ mod.name }}</h3>
              <p class="text-xs text-slate-500">{{ mod.table }} · {{ mod.module }}</p>
            </div>
            <button @click="loadStats(mod.class)"
              class="px-3 py-1.5 text-xs rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 font-medium hover:bg-indigo-100 transition-colors">
              Load Stats
            </button>
          </div>

          <!-- Stats if loaded -->
          <div v-if="statsMap[mod.class]" class="space-y-2">
            <div class="flex items-center justify-between text-sm">
              <span class="text-slate-600 dark:text-slate-400">Total Records</span>
              <span class="font-bold text-slate-800 dark:text-white">{{ statsMap[mod.class].stats.total?.toLocaleString() }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-slate-600 dark:text-slate-400">Added Today</span>
              <span class="font-bold text-emerald-600">{{ statsMap[mod.class].stats.today?.toLocaleString() }}</span>
            </div>
            <div class="mt-1 text-[10px] text-slate-400">
              {{ statsMap[mod.class].execution_ms }}ms · {{ statsMap[mod.class].from_cache ? 'cached' : 'live' }}
            </div>
          </div>

          <div v-else class="text-xs text-slate-400 text-center py-3">Click "Load Stats" to fetch data</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAnalyticsStore } from '../../store/analytics'

const store    = useAnalyticsStore()
const loading  = ref(false)
const statsMap = ref<Record<string, unknown>>({})

const modules = computed(() => store.modules)

onMounted(async () => {
  loading.value = true
  try {
    await store.fetchModules()
  } finally {
    loading.value = false
  }
})

async function loadStats(modelClass: string) {
  const result = await store.fetchStats(modelClass)
  statsMap.value[modelClass] = result
}
</script>
