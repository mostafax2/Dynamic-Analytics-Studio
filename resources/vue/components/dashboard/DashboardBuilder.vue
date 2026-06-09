<template>
  <div class="as-dashboard-builder h-full flex flex-col bg-slate-50 dark:bg-slate-900">
    <!-- Header toolbar -->
    <header class="as-toolbar flex items-center justify-between px-6 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm">
      <div class="flex items-center gap-3">
        <button @click="$emit('back')" class="as-btn-ghost">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </button>
        <div>
          <input v-if="editingName" v-model="localName" @blur="saveName" @keyup.enter="saveName"
            class="text-xl font-bold bg-transparent border-b-2 border-indigo-500 outline-none text-slate-800 dark:text-white" />
          <h1 v-else @click="editingName = true"
            class="text-xl font-bold text-slate-800 dark:text-white cursor-pointer hover:text-indigo-600 transition-colors">
            {{ dashboard?.name || 'Untitled Dashboard' }}
          </h1>
          <p class="text-xs text-slate-500">{{ widgetCount }} widgets · last saved {{ lastSaved }}</p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <!-- Theme toggle -->
        <button @click="toggleTheme" class="as-btn-icon" title="Toggle theme">
          <svg v-if="theme === 'light'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
          <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>

        <!-- RTL toggle -->
        <button @click="rtl = !rtl" class="as-btn-icon" :class="{ 'bg-indigo-100': rtl }" title="RTL mode">
          RTL
        </button>

        <!-- Share -->
        <button @click="shareDialog = true" class="as-btn-secondary">
          <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
          Share
        </button>

        <!-- Add widget -->
        <button @click="addWidgetDialog = true" class="as-btn-primary">
          <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add Widget
        </button>

        <!-- Save -->
        <button @click="save" :disabled="saving" class="as-btn-success">
          <span v-if="saving">Saving...</span>
          <span v-else>Save</span>
        </button>
      </div>
    </header>

    <!-- Grid canvas -->
    <main class="flex-1 overflow-auto p-6" :dir="rtl ? 'rtl' : 'ltr'">
      <div v-if="widgets.length === 0" class="flex flex-col items-center justify-center h-96 text-center">
        <div class="w-24 h-24 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mb-4">
          <svg class="w-12 h-12 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-300">Empty Dashboard</h3>
        <p class="text-slate-500 mt-1 mb-4">Add widgets to start building your analytics view</p>
        <button @click="addWidgetDialog = true" class="as-btn-primary">Add your first widget</button>
      </div>

      <!-- GridStack container -->
      <div v-else ref="gridEl" class="grid-stack">
        <div v-for="widget in widgets" :key="widget.id"
          :gs-id="widget.id"
          :gs-x="widget.position.x"
          :gs-y="widget.position.y"
          :gs-w="widget.position.w"
          :gs-h="widget.position.h"
          class="grid-stack-item">
          <div class="grid-stack-item-content rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 overflow-hidden">
            <WidgetRenderer
              :widget="widget"
              :data="widgetDataMap[widget.id]"
              @edit="editWidget(widget)"
              @delete="deleteWidget(widget.id)"
              @refresh="refreshWidget(widget.id)"
            />
          </div>
        </div>
      </div>
    </main>

    <!-- Add Widget Dialog -->
    <WidgetPickerDialog
      v-if="addWidgetDialog"
      :dashboard-id="dashboard?.id"
      @close="addWidgetDialog = false"
      @created="onWidgetCreated"
    />

    <!-- Share Dialog -->
    <ShareDialog
      v-if="shareDialog"
      :dashboard="dashboard"
      @close="shareDialog = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import type { Dashboard, Widget } from '../../types'
import { useDashboardStore } from '../../store/analytics'
import WidgetRenderer from '../widgets/WidgetRenderer.vue'
import WidgetPickerDialog from './WidgetPickerDialog.vue'
import ShareDialog from './ShareDialog.vue'

const props = defineProps<{ dashboardId: number }>()
const emit  = defineEmits(['back'])

const store          = useDashboardStore()
const dashboard      = computed(() => store.current)
const widgets        = computed(() => dashboard.value?.widgets ?? [])
const widgetDataMap  = computed(() => store.widgetData)
const widgetCount    = computed(() => widgets.value.length)

const gridEl         = ref<HTMLElement | null>(null)
const editingName    = ref(false)
const localName      = ref('')
const saving         = ref(false)
const lastSaved      = ref('never')
const addWidgetDialog = ref(false)
const shareDialog    = ref(false)
const theme          = ref<'light' | 'dark'>('light')
const rtl            = ref(false)

onMounted(async () => {
  await store.fetchOne(props.dashboardId)
  localName.value = dashboard.value?.name ?? ''
  // Load widget data
  for (const w of widgets.value) {
    store.loadWidgetData(props.dashboardId, w.id)
  }
  initGridStack()
})

function initGridStack() {
  // GridStack initialisation happens client-side via CDN or bundled import
  // @ts-ignore
  if (typeof GridStack === 'undefined') return
  // @ts-ignore
  const grid = GridStack.init({ float: false, cellHeight: 80, margin: 8 }, gridEl.value)
  grid.on('change', (_: Event, items: unknown[]) => {
    const layout = (items as any[]).map(i => ({
      widget_id: Number(i.id),
      x: i.x, y: i.y, w: i.w, h: i.h,
    }))
    store.saveLayout(props.dashboardId, layout)
  })
}

async function saveName() {
  editingName.value = false
  if (localName.value && dashboard.value) {
    await store.update(dashboard.value.id, { name: localName.value })
  }
}

async function save() {
  saving.value = true
  try {
    if (dashboard.value) {
      await store.update(dashboard.value.id, { name: localName.value })
      lastSaved.value = new Date().toLocaleTimeString()
    }
  } finally {
    saving.value = false
  }
}

function editWidget(widget: Widget) {
  console.log('edit', widget)
}

async function deleteWidget(widgetId: number) {
  if (!confirm('Delete this widget?')) return
  // handled via WidgetStore in real implementation
}

async function refreshWidget(widgetId: number) {
  await store.refreshWidget(props.dashboardId, widgetId)
}

function onWidgetCreated(widget: Widget) {
  addWidgetDialog.value = false
  store.fetchOne(props.dashboardId)
}

function toggleTheme() {
  theme.value = theme.value === 'light' ? 'dark' : 'light'
  document.documentElement.classList.toggle('dark', theme.value === 'dark')
}
</script>

<style scoped>
@reference "tailwindcss";
.as-btn-primary  { @apply inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors disabled:opacity-50; }
.as-btn-secondary{ @apply inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors; }
.as-btn-success  { @apply inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors disabled:opacity-50; }
.as-btn-ghost    { @apply p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors; }
.as-btn-icon     { @apply p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-xs font-medium; }
</style>
