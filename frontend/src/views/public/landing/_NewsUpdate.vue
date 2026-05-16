<template>
  <section id="news" class="news-update-section bg-white py-20">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
      <div class="section-head" data-aos="fade-up">
        <div>
          <p class="section-kicker">Sastra Arab Update</p>
          <h2 class="section-title mt-4">Berita, kegiatan, dan informasi prodi.</h2>
          <p class="mt-5 max-w-2xl leading-8 text-slate-600">
            Kanal update dipakai untuk mengabarkan agenda akademik, kegiatan mahasiswa, informasi layanan, dan pengumuman prodi.
          </p>
        </div>

        <div class="news-update-actions">
          <button class="news-swiper-button news-swiper-prev" type="button" aria-label="Berita sebelumnya">
            <span class="material-symbols-outlined">arrow_back</span>
          </button>
          <button class="news-swiper-button news-swiper-next" type="button" aria-label="Berita berikutnya">
            <span class="material-symbols-outlined">arrow_forward</span>
          </button>
          <router-link to="/news" class="cta-primary">
            Semua News
            <span class="material-symbols-outlined text-[20px]">newspaper</span>
          </router-link>
        </div>
      </div>

      <div class="news-swiper-wrap" data-aos="fade-up" data-aos-delay="120">
        <Swiper
          :modules="swiperModules"
          :slides-per-view="1"
          :space-between="18"
          :loop="updates.length > 3"
          :speed="750"
          :autoplay="{ delay: 4200, disableOnInteraction: false, pauseOnMouseEnter: true }"
          :pagination="{ clickable: true, el: '.news-swiper-pagination' }"
          :navigation="{ nextEl: '.news-swiper-next', prevEl: '.news-swiper-prev' }"
          :breakpoints="swiperBreakpoints"
          class="news-swiper"
        >
          <SwiperSlide v-for="(item, index) in updates" :key="item.id || item.title">
            <article class="news-update-card group">
              <router-link :to="item.href || (item.id ? `/news/${item.id}` : '/news')" class="block">
                <div class="news-update-image">
                  <img :src="item.image || fallbackImage(index)" :alt="item.title" />
                  <div class="news-update-shade"></div>
                  <div class="date-badge">
                    <span>{{ item.day }}</span>
                    <small>{{ item.month }}</small>
                  </div>
                  <span class="news-update-category">{{ item.category }}</span>
                </div>
                <div class="news-update-content">
                  <p v-if="item.author" class="news-update-author">{{ item.author }}</p>
                  <h3>{{ item.title }}</h3>
                  <p>{{ item.body }}</p>
                  <span class="news-update-link">
                    Lihat selengkapnya
                    <span class="material-symbols-outlined text-[18px] transition group-hover:translate-x-1">arrow_forward</span>
                  </span>
                </div>
              </router-link>
            </article>
          </SwiperSlide>
        </Swiper>

        <div class="news-swiper-pagination"></div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Autoplay, Navigation, Pagination } from 'swiper/modules'
import { Swiper, SwiperSlide } from 'swiper/vue'

defineProps({
  updates: {
    type: Array,
    required: true,
  },
})

const swiperModules = [Autoplay, Navigation, Pagination]
const fallbackImages = ['/img/news/news1.jpg', '/img/news/news2.jpg', '/img/news/news3.jpg', '/img/news/news4.jpg']
const swiperBreakpoints = {
  640: {
    slidesPerView: 1.25,
    spaceBetween: 20,
  },
  768: {
    slidesPerView: 2,
    spaceBetween: 22,
  },
  1024: {
    slidesPerView: 3,
    spaceBetween: 24,
  },
}

function fallbackImage(index) {
  return fallbackImages[index % fallbackImages.length]
}
</script>
