<template>
  <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
    <form class="form-card rounded-2xl p-6" @submit.prevent="submit">
      <h2 class="mb-5 text-lg font-black" style="color: var(--text-heading)">{{ editing ? 'Edit Menu' : 'Tambah Menu' }}</h2>
      <div class="grid gap-4">
        <label class="field">
          <span>Label *</span>
          <input v-model="form.label" class="input" required placeholder="Tentang Kami" />
        </label>
        <label class="field">
          <span>Sumber *</span>
          <select v-model="form.type" class="input" @change="resetReference">
            <option value="page">Page</option>
            <option value="post">Post/Berita</option>
            <option value="custom">Custom URL</option>
          </select>
          <small class="field-help">Untuk menambahkan route bawaan website seperti <code>/news</code> atau <code>/contact</code>, pilih <strong>Custom URL</strong> karena route tersebut bukan Pages.</small>
        </label>
        <label v-if="form.type === 'page'" class="field">
          <span>Pilih Page *</span>
          <select v-model.number="form.reference_id" class="input" required @change="fillLabelFromReference">
            <option :value="null">Pilih page</option>
            <option v-for="page in options.pages" :key="page.id" :value="page.id">{{ page.title }} ({{ page.status }})</option>
          </select>
        </label>
        <label v-else-if="form.type === 'post'" class="field">
          <span>Pilih Post *</span>
          <select v-model.number="form.reference_id" class="input" required @change="fillLabelFromReference">
            <option :value="null">Pilih post</option>
            <option v-for="post in options.posts" :key="post.id" :value="post.id">{{ post.title }} ({{ post.status }})</option>
          </select>
        </label>
        <label v-else class="field">
          <span>URL *</span>
          <input v-model="form.url" class="input" required placeholder="/#about atau https://contoh.com" />
          <small class="field-help">Contoh URL bawaan: <code>/news</code> untuk berita dan <code>/contact</code> untuk kontak.</small>
        </label>
        <label class="field">
          <span>Parent</span>
          <select v-model="form.parent_id" class="input">
            <option :value="null">Tidak ada</option>
            <option v-for="item in flatItems" :key="item.id" :value="item.id" :disabled="editing?.id === item.id">{{ item.depthLabel }}{{ item.label }}</option>
          </select>
        </label>
        <label class="field">
          <span>Target</span>
          <select v-model="form.target" class="input">
            <option value="_self">Tab yang sama</option>
            <option value="_blank">Tab baru</option>
          </select>
        </label>
        <label class="flex items-center justify-between rounded-xl px-4 py-3" style="background: var(--bg-input); border: 1px solid var(--border)">
          <span class="text-sm font-bold" style="color: var(--text-body)">Aktif</span>
          <input v-model="form.is_active" type="checkbox" class="h-5 w-5 accent-blue-600" />
        </label>
        <div v-if="error" class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">{{ error }}</div>
        <div class="flex gap-3">
          <button class="rounded-lg bg-accent px-5 py-2.5 text-sm font-black disabled:opacity-60" :disabled="saving" style="color: var(--text-btn)">
            {{ saving ? 'Menyimpan...' : (editing ? 'Update' : 'Simpan') }}
          </button>
          <button v-if="editing" type="button" class="rounded-lg px-5 py-2.5 text-sm font-bold" style="border: 1px solid var(--border); color: var(--text-body)" @click="resetForm">Batal</button>
        </div>
      </div>
    </form>

    <section class="rounded-2xl p-2" style="background: var(--bg-card); border: 1px solid var(--border)">
      <div class="flex items-center justify-between gap-4 p-4">
        <div>
          <h2 class="text-lg font-black" style="color: var(--text-heading)">Manajemen Menu Navbar</h2>
          <p class="text-sm" style="color: var(--text-muted)">Atur menu publik dari Pages, Posts, atau custom URL.</p>
        </div>
        <button class="rounded-lg px-4 py-2 text-sm font-bold" style="border: 1px solid var(--border); color: var(--text-body)" @click="fetchData">Refresh</button>
      </div>
      <div v-if="loading" class="flex justify-center py-12">
        <span class="material-symbols-outlined animate-spin text-4xl text-accent">progress_activity</span>
      </div>
      <div v-else class="grid gap-2 p-4">
        <p v-if="flatItems.length === 0" class="py-10 text-center text-sm" style="color: var(--text-muted)">Belum ada menu</p>
        <article
          v-for="item in flatItems"
          :key="item.id"
          class="menu-row"
          :class="{
            'menu-row--dragging': draggedItemId === item.id,
            'menu-row--drop-before': dragOverItemId === item.id && dragOverPosition === 'before',
            'menu-row--drop-after': dragOverItemId === item.id && dragOverPosition === 'after',
          }"
          draggable="true"
          :style="{ marginLeft: `${item.depth * 24}px` }"
          @dragstart="startDrag($event, item)"
          @dragover.prevent="dragOver($event, item)"
          @dragleave="leaveDrag(item)"
          @drop.prevent="dropItem(item)"
          @dragend="endDrag"
        >
          <span class="drag-handle material-symbols-outlined" title="Geser menu">drag_indicator</span>
          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <p class="font-black" style="color: var(--text-heading)">{{ item.label }}</p>
              <span class="app-badge" :class="item.is_active ? 'app-badge--success' : 'app-badge--muted'">{{ item.is_active ? 'Aktif' : 'Nonaktif' }}</span>
              <span class="app-badge app-badge--blue">{{ sourceLabel(item.type) }}</span>
            </div>
            <p class="mt-1 truncate text-xs" style="color: var(--text-muted)">{{ item.url }}</p>
          </div>
          <div class="flex items-center gap-1">
            <button class="icon-btn" title="Naik" @click="move(item, -1)"><span class="material-symbols-outlined text-[20px]">keyboard_arrow_up</span></button>
            <button class="icon-btn" title="Turun" @click="move(item, 1)"><span class="material-symbols-outlined text-[20px]">keyboard_arrow_down</span></button>
            <button class="icon-btn text-accent" title="Edit" @click="edit(item)"><span class="material-symbols-outlined text-[20px]">edit</span></button>
            <button class="icon-btn text-red-400" title="Hapus" @click="remove(item)"><span class="material-symbols-outlined text-[20px]">delete</span></button>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import api from '../../../axios'

const loading = ref(false)
const saving = ref(false)
const error = ref('')
const treeItems = ref([])
const editing = ref(null)
const draggedItemId = ref(null)
const dragOverItemId = ref(null)
const dragOverPosition = ref(null)
const options = reactive({ pages: [], posts: [] })
const form = ref(emptyForm())

const flatItems = computed(() => flatten(treeItems.value))

function emptyForm() {
  return { label: '', type: 'custom', reference_id: null, url: '', target: '_self', parent_id: null, is_active: true }
}

function flatten(items, depth = 0) {
  return (items || []).flatMap((item) => [
    { ...item, depth, depthLabel: depth ? `${'— '.repeat(depth)}` : '' },
    ...flatten(item.children || [], depth + 1),
  ])
}

async function fetchData() {
  loading.value = true
  try {
    const [{ data: items }, { data: optionData }] = await Promise.all([
      api.get('/menus/primary/items'),
      api.get('/menus/primary/options'),
    ])
    treeItems.value = items || []
    options.pages = optionData.pages || []
    options.posts = optionData.posts || []
  } finally {
    loading.value = false
  }
}

function resetReference() {
  form.value.reference_id = null
  form.value.url = ''
}

function fillLabelFromReference() {
  if (form.value.label) return
  const source = form.value.type === 'page' ? options.pages : options.posts
  const found = source.find((item) => Number(item.id) === Number(form.value.reference_id))
  if (found) form.value.label = found.title
}

function edit(item) {
  editing.value = item
  form.value = {
    label: item.label,
    type: item.type,
    reference_id: item.reference_id,
    url: item.url || '',
    target: item.target || '_self',
    parent_id: item.parent_id || null,
    is_active: Boolean(item.is_active),
  }
  error.value = ''
}

function resetForm() {
  editing.value = null
  form.value = emptyForm()
  error.value = ''
}

async function submit() {
  saving.value = true
  error.value = ''
  try {
    const payload = { ...form.value, parent_id: form.value.parent_id || null }
    if (editing.value) await api.put(`/menus/primary/items/${editing.value.id}`, payload)
    else await api.post('/menus/primary/items', payload)
    resetForm()
    await fetchData()
  } catch (e) {
    const errors = e.response?.data?.errors
    error.value = errors ? Object.values(errors).flat().join(' ') : (e.response?.data?.message || 'Gagal menyimpan menu.')
  } finally {
    saving.value = false
  }
}

async function remove(item) {
  if (!confirm(`Hapus menu "${item.label}"? Sub-menu juga akan terhapus.`)) return
  await api.delete(`/menus/primary/items/${item.id}`)
  await fetchData()
}

async function move(item, direction) {
  const siblings = flatItems.value.filter((candidate) => String(candidate.parent_id || '') === String(item.parent_id || ''))
  const currentIndex = siblings.findIndex((candidate) => candidate.id === item.id)
  const targetIndex = currentIndex + direction
  if (targetIndex < 0 || targetIndex >= siblings.length) return
  const reordered = [...siblings]
  const [removed] = reordered.splice(currentIndex, 1)
  reordered.splice(targetIndex, 0, removed)
  await saveSiblingOrder(reordered)
}

function startDrag(event, item) {
  draggedItemId.value = item.id
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/plain', String(item.id))
}

function dragOver(event, item) {
  if (!canDropOn(item)) {
    event.dataTransfer.dropEffect = 'none'
    clearDropTarget(item)
    return
  }

  const rect = event.currentTarget.getBoundingClientRect()
  dragOverItemId.value = item.id
  dragOverPosition.value = event.clientY < rect.top + rect.height / 2 ? 'before' : 'after'
  event.dataTransfer.dropEffect = 'move'
}

function leaveDrag(item) {
  clearDropTarget(item)
}

async function dropItem(targetItem) {
  if (!canDropOn(targetItem)) {
    endDrag()
    return
  }

  const draggedItem = flatItems.value.find((candidate) => candidate.id === draggedItemId.value)
  const siblings = flatItems.value.filter((candidate) => String(candidate.parent_id || '') === String(targetItem.parent_id || ''))
  const reordered = siblings.filter((candidate) => candidate.id !== draggedItem.id)
  const targetIndex = reordered.findIndex((candidate) => candidate.id === targetItem.id)
  const insertIndex = dragOverPosition.value === 'after' ? targetIndex + 1 : targetIndex
  reordered.splice(insertIndex, 0, draggedItem)
  await saveSiblingOrder(reordered)
  endDrag()
}

function canDropOn(item) {
  if (!draggedItemId.value || draggedItemId.value === item.id) return false

  const draggedItem = flatItems.value.find((candidate) => candidate.id === draggedItemId.value)
  return draggedItem && String(draggedItem.parent_id || '') === String(item.parent_id || '')
}

function clearDropTarget(item) {
  if (dragOverItemId.value !== item.id) return
  dragOverItemId.value = null
  dragOverPosition.value = null
}

function endDrag() {
  draggedItemId.value = null
  dragOverItemId.value = null
  dragOverPosition.value = null
}

async function saveSiblingOrder(items) {
  const payload = items.map((candidate, index) => ({ id: candidate.id, parent_id: candidate.parent_id || null, sort_order: index + 1 }))
  await api.post('/menus/primary/reorder', { items: payload })
  await fetchData()
}

function sourceLabel(type) {
  if (type === 'page') return 'Page'
  if (type === 'post') return 'Post'
  return 'Custom'
}

onMounted(fetchData)
</script>

<style scoped>
.form-card { background: var(--bg-card); border: 1px solid var(--border); box-shadow: var(--shadow-card); }
.field { display: flex; flex-direction: column; gap: 0.4rem; color: var(--text-body); font-size: 0.875rem; font-weight: 700; }
.field-help { color: var(--text-muted); font-size: 0.78rem; font-weight: 600; line-height: 1.45; }
.field-help code { border-radius: 0.35rem; background: var(--hover-nav-bg); color: var(--text-heading); padding: 0.08rem 0.28rem; font-size: 0.74rem; }
.input { border-radius: 0.85rem; background: var(--bg-input); border: 1px solid var(--border); color: var(--text-heading); padding: 0.7rem 0.9rem; outline: none; }
.input:focus { border-color: var(--color-accent); box-shadow: 0 0 12px rgba(37, 99, 235, 0.18); }
.menu-row { position: relative; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; border: 1px solid var(--border); border-radius: 0.9rem; background: var(--bg-input); padding: 0.9rem; cursor: grab; transition: border-color 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease; }
.menu-row:active { cursor: grabbing; }
.menu-row--dragging { opacity: 0.45; }
.menu-row--drop-before { border-top-color: var(--color-accent); box-shadow: 0 -3px 0 var(--color-accent); }
.menu-row--drop-after { border-bottom-color: var(--color-accent); box-shadow: 0 3px 0 var(--color-accent); }
.drag-handle { flex: 0 0 auto; color: var(--text-muted); font-size: 22px; cursor: grab; user-select: none; }
.menu-row:hover .drag-handle { color: var(--text-heading); }
.icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 2.1rem; height: 2.1rem; border-radius: 0.7rem; color: var(--text-muted); }
.icon-btn:hover { background: var(--hover-nav-bg); color: var(--text-heading); }
</style>
