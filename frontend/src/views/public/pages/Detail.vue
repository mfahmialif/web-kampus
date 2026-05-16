<template>
  <main class="public-page-detail pt-32">
    <section v-if="loading" class="mx-auto max-w-4xl px-5 py-24 text-center lg:px-8">
      <div class="mx-auto size-14 animate-spin rounded-full border-4 border-sky-100 border-t-sky-600"></div>
      <p class="mt-5 font-bold text-slate-500">Memuat halaman...</p>
    </section>

    <section v-else-if="error" class="mx-auto max-w-3xl px-5 py-24 text-center lg:px-8">
      <span class="material-symbols-outlined text-[64px] text-red-500">error</span>
      <h1 class="mt-4 text-3xl font-black text-[#101418]">Halaman tidak ditemukan</h1>
      <p class="mt-3 text-slate-600">{{ error }}</p>
      <router-link to="/" class="cta-primary mt-7">Kembali ke Home</router-link>
    </section>

    <article v-else class="mx-auto max-w-4xl px-5 pb-24 lg:px-8">
      <p class="news-kicker compact">
        <span class="size-2.5 rounded-full bg-sky-600"></span>
        Sastra Arab
      </p>
      <h1 class="mt-4 text-4xl font-black leading-tight text-[#101418] md:text-5xl">{{ page.title }}</h1>
      <div class="article-body article-body--story mt-10" v-html="bodyHtml"></div>
    </article>
  </main>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '../../../axios'
import { fixHtmlAssetUrls } from '../../../utils/asset'
import '../news/styles.css'

const route = useRoute()
const loading = ref(true)
const error = ref('')
const page = ref(null)
const bodyHtml = computed(() => fixHtmlAssetUrls(page.value?.body || '<p>Konten halaman belum tersedia.</p>'))

async function loadPage() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get(`/public/pages/${route.params.slug}`)
    page.value = data
    document.title = `${data.title} - Sastra Arab`
  } catch (err) {
    page.value = null
    error.value = err.response?.status === 404 ? 'Halaman tidak ditemukan atau belum dipublikasikan.' : 'Terjadi kesalahan saat memuat halaman.'
  } finally {
    loading.value = false
  }
}

onMounted(loadPage)

watch(() => route.params.slug, () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
  loadPage()
})
</script>
