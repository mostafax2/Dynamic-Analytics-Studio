<template>
  <div ref="chartEl" class="h-full w-full"></div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import type { WidgetData, WidgetConfig } from '../../types'

const props = defineProps<{
  type:    string
  data?:   WidgetData | null
  config:  WidgetConfig
}>()

const chartEl   = ref<HTMLElement | null>(null)
let   chartInst: unknown = null

onMounted(() => {
  if (props.data) renderChart()
})

watch(() => props.data, () => {
  if (props.data) renderChart()
})

function buildSeries() {
  const rows = (props.data?.data ?? []) as Record<string, unknown>[]

  if (['pie', 'donut'].includes(props.type)) {
    const labelCol = props.config.label_column ?? Object.keys(rows[0] ?? {})[0] ?? 'label'
    const valueCol = props.config.value_column ?? 'value'
    return {
      series: rows.map(r => Number(r[valueCol] ?? 0)),
      labels: rows.map(r => String(r[labelCol] ?? '')),
    }
  }

  const xCol = props.config.label_column ?? 'period'
  const yCol = props.config.value_column ?? 'y'

  return {
    series: [{ name: props.config.label ?? 'Value', data: rows.map(r => Number(r[yCol] ?? 0)) }],
    categories: rows.map(r => String(r[xCol] ?? '')),
  }
}

function renderChart() {
  // @ts-ignore — ApexCharts loaded globally
  if (typeof ApexCharts === 'undefined' || !chartEl.value) return

  const { series, labels, categories } = buildSeries() as Record<string, unknown>

  const options = {
    chart:     { type: props.type, height: '100%', toolbar: { show: false }, animations: { enabled: true } },
    series:    series,
    labels:    labels,
    xaxis:     categories ? { categories } : undefined,
    colors:    ['#6366f1', '#22d3ee', '#f59e0b', '#ef4444', '#10b981', '#3b82f6'],
    dataLabels:{ enabled: false },
    stroke:    props.type === 'area' ? { curve: 'smooth' } : undefined,
    fill:      props.type === 'area' ? { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0 } } : undefined,
    legend:    { position: 'bottom' },
    theme:     { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
  }

  if (chartInst) {
    // @ts-ignore
    chartInst.updateOptions(options)
  } else {
    // @ts-ignore
    chartInst = new ApexCharts(chartEl.value, options)
    // @ts-ignore
    chartInst.render()
  }
}
</script>
