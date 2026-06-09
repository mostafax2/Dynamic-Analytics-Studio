<template>
  <div class="p-6 overflow-auto h-full">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Dashboards</h1>
        <p class="text-slate-500 text-sm">{{ store.items.length }} dashboard{{ store.items.length !== 1 ? 's' : '' }}</p>
      </div>
      <button @click="$emit('new')" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Dashboard
      </button>
    </div>

    <!-- Search -->
    <div class="mb-4 relative">
      <svg class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input v-model="search" type="text" placeholder="Search dashboards..."
        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
    </div>

    <!-- Loading skeleton -->
    <div v-if="store.loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="h-40 bg-white dark:bg-slate-800 rounded-2xl animate-pulse"></div>
    </div>

    <!-- Empty state -->
    <div v-else-if="filtered.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
      <div class="w-20 h-20 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mb-4">
        <svg class="w-10 h-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      </div>
      <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-300">No dashboards yet</h3>
      <p class="text-slate-500 text-sm mt-1 mb-4">Create your first dashboard to start analyzing data</p>
      <button @click="$emit('new')" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700">
        Create Dashboard
      </button>
    </div>

    <!-- Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div v-for="dash in filtered" :key="dash.id"
        class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-700 transition-all cursor-pointer overflow-hidden"
        @click="$emit('open', dash.id)">

        <!-- Card header with color accent -->
        <div class="h-1.5 bg-gradient-to-r from-indigo-500 to-cyan-400"></div>

        <div class="p-5">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
              <h3 class="font-semibold text-slate-800 dark:text-white truncate group-hover:text-indigo-600 transition-colors">
                {{ dash.name }}
              </h3>
              <p v-if="dash.description" class="text-xs text-slate-500 mt-0.5 truncate">{{ dash.description }}</p>
            </div>
            <div class="flex items-center gap-1 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click.stop="cloneDash(dash)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400" title="Clone">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
              </button>
              <button @click.stop="deleteDash(dash.id)" class="p-1.5 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-500" title="Delete">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
          </div>

          <div class="flex items-center gap-4 text-xs text-slate-500">
            <span class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
              {{ dash.widget_count }} widgets
            </span>
            <span v-if="dash.is_public" class="flex items-center gap-1 text-emerald-500">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Public
            </span>
          </div>

          <div class="mt-3 text-[11px] text-slate-400">
            Updated {{ new Date(dash.updated_at).toLocaleDateString() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useDashboardStore } from '../../store/analytics'

const emit  = defineEmits(['open', 'new'])
const store = useDashboardStore()
const search = ref('')

const filtered = computed(() =>
  search.value
    ? store.items.filter(d => d.name.toLowerCase().includes(search.value.toLowerCase()))
    : store.items
)

onMounted(() => store.fetchAll())

async function cloneDash(dash: { id: number; name: string }) {
  const name = prompt('Name for the clone:', `Copy of ${dash.name}`)
  if (!name) return
  await store.cloneDashboard(dash.id, name)
}

async function deleteDash(id: number) {
  if (!confirm('Delete this dashboard?')) return
  await store.remove(id)
}
</script>
