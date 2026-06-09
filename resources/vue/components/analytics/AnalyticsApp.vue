<template>
  <div class="as-app min-h-screen bg-slate-50 dark:bg-slate-900" :class="{ dark: isDark }" :dir="isRtl ? 'rtl' : 'ltr'">
    <!-- Sidebar -->
    <aside v-if="!fullscreen"
      class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex flex-col transform transition-transform"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

      <!-- Logo -->
      <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          </div>
          <span class="font-bold text-slate-800 dark:text-white text-sm">Analytics Suite</span>
        </div>
      </div>

      <!-- Nav -->
      <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <button v-for="item in navItems" :key="item.id"
          @click="currentView = item.id; sidebarOpen = false"
          :class="['w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all',
            currentView === item.id
              ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300'
              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700']">
          <span class="text-lg">{{ item.icon }}</span>
          {{ item.label }}
        </button>
      </nav>

      <!-- Bottom tools -->
      <div class="px-3 py-4 border-t border-slate-200 dark:border-slate-700 space-y-1">
        <button @click="isDark = !isDark"
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">
          <span>{{ isDark ? '☀️' : '🌙' }}</span>
          {{ isDark ? 'Light Mode' : 'Dark Mode' }}
        </button>
        <button @click="isRtl = !isRtl"
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">
          <span>🔄</span> RTL / LTR
        </button>
      </div>
    </aside>

    <!-- Main content -->
    <div :class="['transition-all', !fullscreen ? 'lg:ml-64' : '']">
      <!-- Top bar (mobile) -->
      <header v-if="!fullscreen" class="lg:hidden flex items-center justify-between px-4 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-slate-100">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <span class="font-bold text-slate-800 dark:text-white text-sm">Analytics Suite</span>
        <div></div>
      </header>

      <!-- View router -->
      <div class="h-screen lg:h-screen overflow-hidden" :class="{ 'pt-14 lg:pt-0': !fullscreen }">
        <!-- Dashboard list -->
        <DashboardList
          v-if="currentView === 'dashboards' && !activeDashboardId"
          @open="openDashboard"
          @new="createDashboard"
        />

        <!-- Dashboard builder -->
        <DashboardBuilder
          v-else-if="currentView === 'dashboards' && activeDashboardId"
          :dashboard-id="activeDashboardId"
          @back="activeDashboardId = null"
        />

        <!-- Report builder -->
        <ReportBuilder
          v-else-if="currentView === 'reports' && !activeReportId"
          @back="currentView = 'reports'"
          @saved="r => { activeReportId = r.id }"
        />

        <ReportBuilder
          v-else-if="currentView === 'reports' && activeReportId"
          :report-id="activeReportId"
          @back="activeReportId = null"
        />

        <!-- Analytics overview -->
        <AnalyticsOverview v-else-if="currentView === 'analytics'" />

        <!-- Exports -->
        <ExportHistory v-else-if="currentView === 'exports'" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import DashboardList    from '../dashboard/DashboardList.vue'
import DashboardBuilder from '../dashboard/DashboardBuilder.vue'
import ReportBuilder    from '../reports/ReportBuilder.vue'
import AnalyticsOverview from './AnalyticsOverview.vue'
import ExportHistory    from './ExportHistory.vue'
import { createPinia } from 'pinia'

const isDark           = ref(false)
const isRtl            = ref(false)
const sidebarOpen      = ref(false)
const fullscreen       = ref(false)
const currentView      = ref('dashboards')
const activeDashboardId = ref<number | null>(null)
const activeReportId   = ref<number | null>(null)

const navItems = [
  { id: 'dashboards', icon: '📊', label: 'Dashboards' },
  { id: 'reports',    icon: '📋', label: 'Report Builder' },
  { id: 'analytics',  icon: '🔬', label: 'Analytics' },
  { id: 'exports',    icon: '📥', label: 'Exports' },
]

function openDashboard(id: number) {
  activeDashboardId.value = id
}

async function createDashboard() {
  const name = prompt('Dashboard name:')
  if (!name) return
  const res = await fetch('/api/analytics/dashboards', {
    method:  'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body:    JSON.stringify({ name }),
    credentials: 'include',
  })
  const data = await res.json()
  activeDashboardId.value = (data.data ?? data).id
}
</script>
