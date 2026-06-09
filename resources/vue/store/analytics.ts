import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Dashboard, Widget, ReportTemplate, WidgetData } from '../types'

const API = '/api/analytics'

async function api(method: string, path: string, body?: unknown) {
  const res = await fetch(`${API}${path}`, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-XSRF-TOKEN': document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '',
    },
    body: body ? JSON.stringify(body) : undefined,
    credentials: 'include',
  })
  if (!res.ok) throw new Error(`API error ${res.status}: ${await res.text()}`)
  return res.status === 204 ? null : res.json()
}

// ============================================================
// DASHBOARDS
// ============================================================
export const useDashboardStore = defineStore('dashboards', () => {
  const items       = ref<Dashboard[]>([])
  const current     = ref<Dashboard | null>(null)
  const loading     = ref(false)
  const widgetData  = ref<Record<number, WidgetData>>({})

  const isEmpty = computed(() => items.value.length === 0)

  async function fetchAll() {
    loading.value = true
    try {
      const res   = await api('GET', '/dashboards')
      items.value = res.data
    } finally {
      loading.value = false
    }
  }

  async function fetchOne(id: number) {
    loading.value = true
    try {
      const res     = await api('GET', `/dashboards/${id}`)
      current.value = res.data ?? res
    } finally {
      loading.value = false
    }
  }

  async function create(data: Partial<Dashboard>) {
    const res = await api('POST', '/dashboards', data)
    items.value.unshift(res.data ?? res)
    return res.data ?? res
  }

  async function update(id: number, data: Partial<Dashboard>) {
    const res = await api('PUT', `/dashboards/${id}`, data)
    const updated = res.data ?? res
    const idx = items.value.findIndex(d => d.id === id)
    if (idx !== -1) items.value[idx] = updated
    if (current.value?.id === id) current.value = updated
    return updated
  }

  async function remove(id: number) {
    await api('DELETE', `/dashboards/${id}`)
    items.value = items.value.filter(d => d.id !== id)
    if (current.value?.id === id) current.value = null
  }

  async function cloneDashboard(id: number, name: string) {
    const res = await api('POST', `/dashboards/${id}/clone`, { name })
    items.value.unshift(res.data ?? res)
    return res.data ?? res
  }

  async function saveLayout(id: number, layout: unknown[]) {
    await api('POST', `/dashboards/${id}/layout`, { layout })
  }

  async function share(id: number, expiryDays = 7) {
    return api('POST', `/dashboards/${id}/share`, { expiry_days: expiryDays })
  }

  async function unshare(id: number) {
    return api('DELETE', `/dashboards/${id}/share`)
  }

  async function loadWidgetData(dashboardId: number, widgetId: number, params = {}) {
    const res = await api('GET', `/dashboards/${dashboardId}/widgets/${widgetId}/data`)
    widgetData.value[widgetId] = res
    return res
  }

  async function refreshWidget(dashboardId: number, widgetId: number) {
    const res = await api('POST', `/dashboards/${dashboardId}/widgets/${widgetId}/refresh`)
    widgetData.value[widgetId] = res
    return res
  }

  return {
    items, current, loading, widgetData, isEmpty,
    fetchAll, fetchOne, create, update, remove,
    cloneDashboard, saveLayout, share, unshare,
    loadWidgetData, refreshWidget,
  }
})

// ============================================================
// REPORTS
// ============================================================
export const useReportStore = defineStore('reports', () => {
  const items   = ref<ReportTemplate[]>([])
  const current = ref<ReportTemplate | null>(null)
  const loading = ref(false)
  const result  = ref<unknown>(null)

  async function fetchAll(params = {}) {
    loading.value = true
    try {
      const qs  = new URLSearchParams(params as Record<string, string>).toString()
      const res = await api('GET', `/reports${qs ? '?' + qs : ''}`)
      items.value = res.data
    } finally {
      loading.value = false
    }
  }

  async function fetchOne(id: number) {
    const res     = await api('GET', `/reports/${id}`)
    current.value = res.data ?? res
    return current.value
  }

  async function create(data: Partial<ReportTemplate>) {
    const res = await api('POST', '/reports', data)
    items.value.unshift(res.data ?? res)
    return res.data ?? res
  }

  async function update(id: number, data: Partial<ReportTemplate>) {
    const res     = await api('PUT', `/reports/${id}`, data)
    const updated = res.data ?? res
    const idx     = items.value.findIndex(r => r.id === id)
    if (idx !== -1) items.value[idx] = updated
    return updated
  }

  async function remove(id: number) {
    await api('DELETE', `/reports/${id}`)
    items.value = items.value.filter(r => r.id !== id)
  }

  async function run(id: number, params = {}) {
    loading.value = true
    try {
      result.value = await api('POST', `/reports/${id}/run`, params)
      return result.value
    } finally {
      loading.value = false
    }
  }

  async function exportTemplate(id: number) {
    return api('GET', `/reports/${id}/export`)
  }

  async function importTemplate(definition: unknown) {
    const res = await api('POST', '/reports/import', { definition })
    items.value.unshift(res.data ?? res)
    return res.data ?? res
  }

  return {
    items, current, loading, result,
    fetchAll, fetchOne, create, update, remove,
    run, exportTemplate, importTemplate,
  }
})

// ============================================================
// ANALYTICS
// ============================================================
export const useAnalyticsStore = defineStore('analytics', () => {
  const modules = ref<unknown[]>([])
  const stats   = ref<Record<string, unknown>>({})

  async function fetchModules() {
    const res    = await api('GET', '/analytics/modules')
    modules.value = res.data
  }

  async function fetchStats(model: string) {
    const res         = await api('GET', `/analytics/stats?model=${model}`)
    stats.value[model] = res
    return res
  }

  async function fetchSummary(module: string) {
    return api('GET', `/analytics/summary/${module}`)
  }

  return { modules, stats, fetchModules, fetchStats, fetchSummary }
})
