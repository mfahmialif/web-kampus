<template>
  <div class="flex flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
      <router-link :to="{ name: 'AdminPagesCreate' }" class="flex h-10 items-center gap-2 rounded-lg bg-accent px-5 text-sm font-bold shadow-[0_0_15px_rgba(37,99,235,0.3)]" style="color: var(--text-btn)">
        <span class="material-symbols-outlined text-[20px]">add_circle</span>
        Tambah Page
      </router-link>
      <div class="flex gap-3">
        <input v-model="search" class="input w-64" placeholder="Cari page..." @input="debouncedFetch" />
        <select v-model="status" class="input w-40" @change="fetchData">
          <option value="all">Semua Status</option>
          <option value="Published">Published</option>
          <option value="Draft">Draft</option>
        </select>
      </div>
    </div>

    <div class="rounded-2xl p-2" style="background: var(--bg-card); border: 1px solid var(--border)">
      <div v-if="loading" class="flex justify-center py-12">
        <span class="material-symbols-outlined animate-spin text-4xl text-accent">progress_activity</span>
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr style="background: var(--bg-input)">
              <th class="px-4 py-4 text-sm font-bold" style="color: var(--text-heading)">Judul</th>
              <th class="px-4 py-4 text-sm font-bold" style="color: var(--text-heading)">Slug</th>
              <th class="px-4 py-4 text-sm font-bold" style="color: var(--text-heading)">Status</th>
              <th class="px-4 py-4 text-right text-sm font-bold" style="color: var(--text-heading)">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="items.length === 0">
              <td colspan="4" class="px-4 py-12 text-center text-sm" style="color: var(--text-muted)">Belum ada page</td>
            </tr>
            <tr v-for="item in items" :key="item.id" class="table-row">
              <td class="px-4 py-4">
                <p class="font-black" style="color: var(--text-heading)">{{ item.title }}</p>
                <p class="text-sm" style="color: var(--text-muted)">{{ item.creator?.name || 'Admin' }}</p>
              </td>
              <td class="px-4 py-4 text-sm" style="color: var(--text-muted)">/pages/{{ item.slug }}</td>
              <td class="px-4 py-4"><span :class="item.status === 'Published' ? 'app-badge app-badge--success' : 'app-badge app-badge--blue'">{{ item.status }}</span></td>
              <td class="px-4 py-4 text-right">
                <router-link :to="{ name: 'AdminPagesEdit', params: { id: item.id } }" class="rounded-lg p-2 text-accent hover:bg-white/5" title="Edit">
                  <span class="material-symbols-outlined text-[20px]">edit</span>
                </router-link>
                <button class="rounded-lg p-2 text-red-400 hover:bg-white/5" title="Hapus" @click="remove(item)">
                  <span class="material-symbols-outlined text-[20px]">delete</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '../../../axios'

const items = ref([])
const loading = ref(false)
const search = ref('')
const status = ref('all')
let timer = null

function debouncedFetch() {
  clearTimeout(timer)
  timer = setTimeout(fetchData, 300)
}

async function fetchData() {
  loading.value = true
  try {
    const { data } = await api.get('/pages', { params: { search: search.value, status: status.value, per_page: 100 } })
    items.value = data.data || data || []
  } finally {
    loading.value = false
  }
}

async function remove(item) {
  if (!confirm(`Hapus page "${item.title}"?`)) return
  await api.delete(`/pages/${item.id}`)
  await fetchData()
}

onMounted(fetchData)
</script>

<style scoped>
.input { border-radius: 0.85rem; background: var(--bg-input); border: 1px solid var(--border); color: var(--text-heading); padding: 0.7rem 0.9rem; outline: none; }
.input:focus { border-color: var(--color-accent); box-shadow: 0 0 12px rgba(37, 99, 235, 0.18); }
.table-row { border-top: 1px solid var(--border); transition: background 0.2s ease; }
.table-row:hover { background: var(--hover-nav-bg); }
</style>
