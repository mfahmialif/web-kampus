<template>
  <header class="fixed inset-x-0 top-0 z-50 px-4 pt-4 sm:px-6 lg:pt-6">
    <div class="public-nav-shell mx-auto flex max-w-7xl items-center justify-between gap-4 rounded-[1.7rem] px-5 py-3 backdrop-blur-xl lg:px-7">
      <router-link to="/" class="logo-pill group">
        <span class="logo-icon">
          <span class="material-symbols-outlined text-[22px]">auto_stories</span>
        </span>
        <span class="leading-tight">
          <span class="block text-base font-black text-white">Sastra Arab</span>
          <span class="block text-[10px] font-black uppercase tracking-wide text-sky-300">Program Studi</span>
        </span>
      </router-link>

      <nav class="public-nav-links hidden items-center gap-2 text-[14px] font-bold md:flex">
        <div v-for="item in items" :key="item.href" class="nav-item-wrap">
          <a
            v-if="isExternal(item.href)"
            :href="item.href"
            :target="item.target || '_self'"
            :rel="item.target === '_blank' ? 'noopener noreferrer' : null"
            class="nav-link"
            :class="{ active: isActive(item) }"
          >
            <span>{{ item.label }}</span>
            <span v-if="item.children?.length" class="nav-branch-arrow material-symbols-outlined" aria-hidden="true">keyboard_arrow_down</span>
          </a>
          <router-link v-else :to="item.href" class="nav-link" :class="{ active: isActive(item) }">
            <span>{{ item.label }}</span>
            <span v-if="item.children?.length" class="nav-branch-arrow material-symbols-outlined" aria-hidden="true">keyboard_arrow_down</span>
          </router-link>
          <div v-if="item.children?.length" class="nav-dropdown">
            <NavbarDropdown :items="item.children" />
          </div>
        </div>
      </nav>

      <div class="hidden items-center gap-2 md:flex">
        <button
          class="public-theme-toggle"
          type="button"
          :title="theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode'"
          @click="$emit('toggle-theme')"
        >
          <span class="material-symbols-outlined text-[19px]">{{ theme === 'dark' ? 'light_mode' : 'dark_mode' }}</span>
        </button>
        <router-link :to="authLink.to" class="public-signin hidden h-9 items-center gap-2 rounded-full px-3.5 text-sm font-black transition md:inline-flex">
          <span class="material-symbols-outlined text-[19px]">{{ authLink.icon }}</span>
          {{ authLink.label }}
        </router-link>
      </div>

      <button @click="mobileOpen = !mobileOpen" class="public-mobile-menu-button flex size-10 items-center justify-center rounded-full md:hidden">
        <span class="material-symbols-outlined">{{ mobileOpen ? 'close' : 'menu' }}</span>
      </button>
    </div>

    <Transition name="mobile-menu">
      <div v-if="mobileOpen" class="public-mobile-panel mx-auto mt-3 max-w-7xl rounded-3xl p-4 shadow-2xl backdrop-blur-xl md:hidden">
        <div v-for="item in items" :key="item.href" class="mobile-menu-group">
          <a
            v-if="isExternal(item.href)"
            :href="item.href"
            :target="item.target || '_self'"
            :rel="item.target === '_blank' ? 'noopener noreferrer' : null"
            @click="mobileOpen = false"
            class="public-mobile-link flex items-center justify-between gap-3 rounded-2xl px-4 py-3 font-bold"
            :class="{ active: isActive(item) }"
          >
            <span>{{ item.label }}</span>
            <span v-if="item.children?.length" class="mobile-branch-arrow material-symbols-outlined" aria-hidden="true">keyboard_arrow_down</span>
          </a>
          <router-link v-else :to="item.href" @click="mobileOpen = false" class="public-mobile-link flex items-center justify-between gap-3 rounded-2xl px-4 py-3 font-bold" :class="{ active: isActive(item) }">
            <span>{{ item.label }}</span>
            <span v-if="item.children?.length" class="mobile-branch-arrow material-symbols-outlined" aria-hidden="true">keyboard_arrow_down</span>
          </router-link>
          <div v-if="item.children?.length" class="ml-4 border-l border-white/10 pl-3">
            <template v-for="child in flattenMenuChildren(item.children)" :key="`${child.depth}-${child.href}`">
              <a
                v-if="isExternal(child.href)"
                :href="child.href"
                :target="child.target || '_self'"
                :rel="child.target === '_blank' ? 'noopener noreferrer' : null"
                @click="mobileOpen = false"
                class="public-mobile-link mt-1 flex items-center justify-between gap-3 rounded-2xl px-4 py-2.5 text-sm font-bold"
                :style="{ marginLeft: `${child.depth * 0.75}rem` }"
              >
                <span>{{ child.label }}</span>
                <span v-if="child.children?.length" class="mobile-branch-arrow material-symbols-outlined" aria-hidden="true">keyboard_arrow_down</span>
              </a>
              <router-link v-else :to="child.href" @click="mobileOpen = false" class="public-mobile-link mt-1 flex items-center justify-between gap-3 rounded-2xl px-4 py-2.5 text-sm font-bold" :style="{ marginLeft: `${child.depth * 0.75}rem` }">
                <span>{{ child.label }}</span>
                <span v-if="child.children?.length" class="mobile-branch-arrow material-symbols-outlined" aria-hidden="true">keyboard_arrow_down</span>
              </router-link>
            </template>
          </div>
        </div>
        <button class="public-mobile-theme-toggle mt-2 flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3 font-black" type="button" @click="$emit('toggle-theme')">
          <span class="material-symbols-outlined text-[20px]">{{ theme === 'dark' ? 'light_mode' : 'dark_mode' }}</span>
          {{ theme === 'dark' ? 'Light Mode' : 'Dark Mode' }}
        </button>
        <router-link :to="authLink.to" class="public-mobile-signin mt-2 flex items-center justify-center gap-2 rounded-2xl px-4 py-3 font-black" @click="mobileOpen = false">
          <span class="material-symbols-outlined text-[20px]">{{ authLink.icon }}</span>
          {{ authLink.label }}
        </router-link>
      </div>
    </Transition>
  </header>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import NavbarDropdown from './NavbarDropdown.vue'

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
  activeSection: {
    type: String,
    default: 'home',
  },
  theme: {
    type: String,
    default: 'light',
  },
})

defineEmits(['toggle-theme'])

const mobileOpen = ref(false)
const authStore = useAuthStore()

const dashboardRoute = computed(() => {
  const roleName = authStore.user?.role?.name

  if (roleName === 'Penulis') {
    return { name: 'PenulisDashboard' }
  }

  if (roleName === 'Kepala Penulis') {
    return { name: 'KepalaPenulisDashboard' }
  }

  return { name: 'AdminDashboard' }
})

const authLink = computed(() => {
  if (authStore.isAuthenticated) {
    return {
      to: dashboardRoute.value,
      icon: 'dashboard',
      label: 'Dashboard',
    }
  }

  return {
    to: '/login',
    icon: 'login',
    label: 'Sign In',
  }
})

function isExternal(href = '') {
  return /^https?:\/\//i.test(href) || href.startsWith('mailto:') || href.startsWith('tel:')
}

function isActive(item) {
  return props.activeSection === item.id || props.activeSection === item.href
}

function flattenMenuChildren(items = [], depth = 0) {
  return items.flatMap((item) => [
    { ...item, depth },
    ...flattenMenuChildren(item.children || [], depth + 1),
  ])
}

onMounted(() => {
  if (!authStore.hasCheckedSession) {
    authStore.fetchUser()
  }
})
</script>
