<template>
  <section id="home" class="relative overflow-hidden pt-30">
    <div class="hero-glow"></div>
    <div class="mx-auto grid min-h-screen max-w-7xl items-center gap-12 px-5 pb-16 pt-8 lg:grid-cols-[1fr_0.95fr] lg:px-8 lg:pt-16">
      <div class="reveal-up">
        <p class="mb-7 inline-flex items-center gap-3 rounded-full bg-slate-200/70 px-5 py-2 text-sm font-black tracking-[0.12em] text-slate-600 shadow-sm">
          <span class="size-2.5 rounded-full bg-sky-700"></span>
          PROGRAM STUDI
        </p>
        <h1 class="max-w-4xl text-5xl font-black leading-[1.02] tracking-tight text-[#101418] sm:text-6xl lg:text-7xl">
          Sastra Arab
        </h1>
        <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-600">
          Website resmi Program Studi Sastra Arab untuk informasi akademik, berita, kegiatan mahasiswa, dan layanan komunikasi prodi.
        </p>
        <div class="mt-9 flex flex-wrap items-center gap-4">
          <a href="#services" class="cta-primary">
            Lihat Akademik
            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
          </a>
          <a href="#flow" class="cta-secondary">
            Kegiatan Prodi
            <span class="material-symbols-outlined text-[20px]">groups</span>
          </a>
        </div>
      </div>

      <div class="hero-card reveal-up delay-2">
        <div class="relative overflow-hidden rounded-[2rem] bg-[#0b1020] shadow-2xl shadow-slate-900/25">
          <img src="/img/hero-bg.jpg" alt="Kegiatan Program Studi Sastra Arab" class="h-[500px] w-full object-cover opacity-80" />
          <div class="absolute inset-0 bg-linear-to-t from-[#0b1020] via-[#0b1020]/30 to-transparent"></div>
          <div class="absolute bottom-0 left-0 right-0 p-7 text-white">
            <p class="text-sm font-black uppercase tracking-[0.22em] text-sky-300">Arabic Literature</p>
            <h2 class="mt-3 text-3xl font-black leading-tight">Menguatkan bahasa, sastra, dan tradisi keilmuan Arab.</h2>
          </div>
          <div class="floating-card">
            <span class="material-symbols-outlined text-sky-500">verified</span>
            <div>
              <p class="text-sm font-black text-slate-950">Akademik aktif</p>
              <p class="text-xs font-semibold text-slate-500">Informasi prodi, kegiatan, dan layanan mahasiswa</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
