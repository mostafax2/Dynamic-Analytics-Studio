<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
          <h2 class="text-lg font-bold text-slate-800 dark:text-white">Share Dashboard</h2>
          <button @click="$emit('close')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <div class="px-6 py-4 space-y-4">
          <!-- Public link toggle -->
          <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
            <div>
              <p class="text-sm font-semibold text-slate-800 dark:text-white">Public link</p>
              <p class="text-xs text-slate-500">Anyone with the link can view</p>
            </div>
            <button @click="togglePublic"
              :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                dashboard?.is_public ? 'bg-indigo-600' : 'bg-slate-200']">
              <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                dashboard?.is_public ? 'translate-x-6' : 'translate-x-1']"/>
            </button>
          </div>

          <!-- Public URL -->
          <div v-if="dashboard?.is_public && dashboard?.public_token" class="space-y-2">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">Public URL</label>
            <div class="flex gap-2">
              <input :value="publicUrl" readonly
                class="flex-1 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm text-slate-700 dark:text-slate-300 select-all" />
              <button @click="copy" class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                {{ copied ? 'Copied!' : 'Copy' }}
              </button>
            </div>
            <p v-if="dashboard.public_expires_at" class="text-xs text-slate-500">
              Expires: {{ new Date(dashboard.public_expires_at).toLocaleDateString() }}
            </p>
          </div>

          <!-- Expiry -->
          <div v-if="dashboard?.is_public">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase mb-1 block">Link expires in</label>
            <select v-model="expiryDays" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-white">
              <option :value="7">7 days</option>
              <option :value="30">30 days</option>
              <option :value="90">90 days</option>
              <option :value="365">1 year</option>
            </select>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex justify-end">
          <button @click="$emit('close')" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium">
            Close
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { Dashboard } from '../../types'
import { useDashboardStore } from '../../store/analytics'

const props = defineProps<{ dashboard?: Dashboard | null }>()
const emit  = defineEmits(['close'])

const store     = useDashboardStore()
const copied    = ref(false)
const expiryDays = ref(7)

const publicUrl = computed(() =>
  props.dashboard?.public_token
    ? `${window.location.origin}/api/analytics/public/${props.dashboard.public_token}`
    : ''
)

async function togglePublic() {
  if (!props.dashboard) return
  if (props.dashboard.is_public) {
    await store.unshare(props.dashboard.id)
  } else {
    await store.share(props.dashboard.id, expiryDays.value)
  }
  await store.fetchOne(props.dashboard.id)
}

async function copy() {
  await navigator.clipboard.writeText(publicUrl.value)
  copied.value = true
  setTimeout(() => (copied.value = false), 2000)
}
</script>
