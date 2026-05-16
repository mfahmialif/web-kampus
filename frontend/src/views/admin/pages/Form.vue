<template>
  <div class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
      <router-link to="/administrator/pages" class="flex items-center gap-1 text-sm font-medium hover:text-accent" style="color: var(--text-muted)">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        Kembali
      </router-link>
      <h2 class="text-lg font-bold" style="color: var(--text-heading)">{{ isEdit ? 'Edit Page' : 'Tambah Page' }}</h2>
    </div>

    <div v-if="pageLoading" class="flex justify-center py-16">
      <span class="material-symbols-outlined animate-spin text-4xl text-accent">progress_activity</span>
    </div>

    <div v-else class="form-card rounded-2xl p-6">
      <div class="grid gap-5">
        <label class="field">
          <span>Judul Page *</span>
          <input v-model="form.title" class="input" placeholder="Tentang Program Studi" />
        </label>
        <label class="field">
          <span>Slug</span>
          <input v-model="form.slug" class="input" placeholder="tentang-program-studi" />
        </label>
        <label class="field">
          <span>Status</span>
          <select v-model="form.status" class="input">
            <option value="Published">Published</option>
            <option value="Draft">Draft</option>
          </select>
        </label>
        <div class="field">
          <span>Isi Page</span>
          <QuillEditor ref="quillRef" v-model:content="form.body" content-type="html" theme="snow" :toolbar="quillToolbar" :modules="quillModules" class="quill-dark" @ready="onQuillReady" />
        </div>
        <div v-if="error" class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">{{ error }}</div>
        <div class="flex justify-end gap-3">
          <router-link to="/administrator/pages" class="rounded-lg px-6 py-2.5 text-sm font-medium" style="color: var(--text-body); border: 1px solid var(--border)">Batal</router-link>
          <button class="rounded-lg bg-accent px-6 py-2.5 text-sm font-bold disabled:opacity-60" :disabled="saving" style="color: var(--text-btn)" @click="submit">
            {{ saving ? 'Menyimpan...' : (isEdit ? 'Update' : 'Simpan') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import QuillResizeImage from 'quill-resize-image'
import api from '../../../axios'

const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)
const pageLoading = ref(false)
const saving = ref(false)
const error = ref('')
const quillRef = ref(null)
const form = ref({ title: '', slug: '', body: '', status: 'Published' })
const quillToolbar = [[{ header: [1, 2, 3, false] }], ['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['link', 'image'], ['clean']]
const quillModules = [{ name: 'resize', module: QuillResizeImage, options: {} }]

function imageHandlerEditor() {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'
  input.click()
  input.addEventListener('change', () => {
    const file = input.files?.[0]
    if (!file) return
    const reader = new FileReader()
    reader.onload = () => {
      const q = quillRef.value?.getQuill()
      if (!q) return
      const range = q.getSelection(true)
      q.insertEmbed(range.index, 'image', reader.result)
      q.setSelection(range.index + 1)
    }
    reader.readAsDataURL(file)
  })
}

function onQuillReady() {
  quillRef.value?.getQuill()?.getModule('toolbar')?.addHandler('image', imageHandlerEditor)
}

async function uploadPendingEditorMedia() {
  const q = quillRef.value?.getQuill()
  let body = q ? q.root.innerHTML : (form.value.body || '')
  const imgRegex = /src="(data:image\/[^"]+)"/g
  const matches = []
  let match
  while ((match = imgRegex.exec(body)) !== null) matches.push(match[1])
  for (const dataUrl of matches) {
    const res = await fetch(dataUrl)
    const blob = await res.blob()
    const fd = new FormData()
    fd.append('file', blob, 'page-image.png')
    const { data } = await api.post('/upload-content-file', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    body = body.replace(dataUrl, data.url)
  }
  form.value.body = body
}

async function loadPage() {
  if (!isEdit.value) return
  pageLoading.value = true
  try {
    const { data } = await api.get(`/pages/${route.params.id}`)
    form.value = { title: data.title || '', slug: data.slug || '', body: data.body || '', status: data.status || 'Published' }
  } finally {
    pageLoading.value = false
  }
}

async function submit() {
  saving.value = true
  error.value = ''
  try {
    await uploadPendingEditorMedia()
    if (isEdit.value) await api.put(`/pages/${route.params.id}`, form.value)
    else await api.post('/pages', form.value)
    router.push({ name: 'AdminPages' })
  } catch (e) {
    const errors = e.response?.data?.errors
    error.value = errors ? Object.values(errors).flat().join(' ') : (e.response?.data?.message || 'Gagal menyimpan page.')
  } finally {
    saving.value = false
  }
}

onMounted(loadPage)
</script>

<style scoped>
.form-card { background: var(--bg-card); border: 1px solid var(--border); box-shadow: var(--shadow-card); }
.field { display: flex; flex-direction: column; gap: 0.45rem; color: var(--text-body); font-size: 0.875rem; font-weight: 700; }
.input { border-radius: 0.85rem; background: var(--bg-input); border: 1px solid var(--border); color: var(--text-heading); padding: 0.7rem 0.9rem; outline: none; }
.input:focus { border-color: var(--color-accent); box-shadow: 0 0 12px rgba(37, 99, 235, 0.18); }
.quill-dark :deep(.ql-toolbar) { background: var(--bg-input); border-color: var(--border) !important; border-radius: 12px 12px 0 0; }
.quill-dark :deep(.ql-container) { min-height: 260px; background: var(--bg-input); border-color: var(--border) !important; border-radius: 0 0 12px 12px; color: var(--text-heading); font-family: 'Inter', sans-serif; }
.quill-dark :deep(.ql-editor) { min-height: 260px; }
</style>
