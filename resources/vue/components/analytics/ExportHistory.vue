<template>
  <div class="p-6 overflow-auto h-full">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Export History</h1>
    <p class="text-slate-500 text-sm mb-6">Recent export jobs and their status</p>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-700">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400">#</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400">Type</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400">Format</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400">Status</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400">Rows</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400">Created</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="6" class="px-4 py-8 text-center text-slate-400">Loading...</td>
          </tr>
          <tr v-else-if="!jobs.length">
            <td colspan="6" class="px-4 py-8 text-center text-slate-400">No exports yet</td>
          </tr>
          <tr v-else v-for="job in jobs" :key="job.id"
            class="border-t border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50">
            <td class="px-4 py-3 text-slate-500">{{ job.id }}</td>
            <td class="px-4 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                {{ job.type }}
              </span>
            </td>
            <td class="px-4 py-3 uppercase text-xs font-bold text-indigo-600">{{ job.format }}</td>
            <td class="px-4 py-3">
              <span :class="statusClass(job.status)" class="px-2 py-0.5 rounded-full text-xs font-medium">
                {{ job.status }}
              </span>
            </td>
            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ job.rows?.toLocaleString() ?? '—' }}</td>
            <td class="px-4 py-3 text-slate-500 text-xs">{{ new Date(job.created_at).toLocaleString() }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const loading = ref(false)
const jobs    = ref<Record<string, unknown>[]>([])

onMounted(async () => {
  loading.value = true
  try {
    const res = await fetch('/api/analytics/exports/history', { credentials: 'include' })
    const data = await res.json()
    jobs.value = data.data ?? []
  } finally {
    loading.value = false
  }
})

function statusClass(status: string) {
  return {
    pending:    'bg-amber-100 text-amber-700',
    processing: 'bg-blue-100 text-blue-700',
    done:       'bg-emerald-100 text-emerald-700',
    failed:     'bg-rose-100 text-rose-700',
  }[status] ?? 'bg-slate-100 text-slate-700'
}
</script>
