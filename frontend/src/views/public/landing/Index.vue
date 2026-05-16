<template>
  <main>
    <Hero />
    <Services :services="services" />
    <Supporter :supporters="supporters" />
    <NewsUpdate :updates="latestNews" />
    <About :stats="stats" />
    <PublishCta />
    <Strengths :strengths="strengths" />
    <Flow :publishing-flow="publishingFlow" />
    <Testimonials :testimonials="testimonials" />
    <Contact />
  </main>
</template>

<script setup>
import { nextTick, onMounted, ref } from 'vue'
import AOS from 'aos'
import 'aos/dist/aos.css'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import './styles.css'
import api from '../../../axios'
import About from './_About.vue'
import Contact from './_Contact.vue'
import Flow from './_Flow.vue'
import Hero from './_Hero.vue'
import NewsUpdate from './_NewsUpdate.vue'
import PublishCta from './_PublishCta.vue'
import Services from './_Services.vue'
import Strengths from './_Strengths.vue'
import Supporter from './_Supporter.vue'
import Testimonials from './_Testimonials.vue'
import {
  publishingFlow,
  services,
  stats,
  strengths,
  supporters,
  testimonials,
  updates,
} from './data'
import { normalizeNews } from '../news/utils'

const latestNews = ref(updates)

function formatNewsDate(dateStr) {
  if (!dateStr) return { day: '', month: '' }
  const date = new Date(dateStr)
  if (Number.isNaN(date.getTime())) return { day: '', month: '' }

  return {
    day: new Intl.DateTimeFormat('id-ID', { day: '2-digit' }).format(date),
    month: new Intl.DateTimeFormat('id-ID', { month: 'short' }).format(date),
  }
}

async function fetchLatestNews() {
  try {
    const { data } = await api.get('/news', {
      params: {
        status: 'Published',
        per_page: 6,
        sort_by: 'created_at',
        sort_dir: 'desc',
      },
    })

    const items = (data.data || []).map((item) => {
      const normalized = normalizeNews(item)
      const date = formatNewsDate(normalized.created_at)

      return {
        id: normalized.id,
        href: `/news/${normalized.slug || normalized.id}`,
        day: date.day,
        month: date.month,
        category: normalized.categoryName,
        title: normalized.title,
        author: normalized.authorName,
        body: normalized.excerpt,
        image: normalized.image,
      }
    })

    latestNews.value = items.length ? items : updates
  } catch {
    latestNews.value = updates
  }
}

onMounted(async () => {
  AOS.init({
    duration: 850,
    easing: 'ease-out-cubic',
    once: true,
    offset: 90,
  })
  await fetchLatestNews()
  await nextTick()
  AOS.refreshHard()
})
</script>
