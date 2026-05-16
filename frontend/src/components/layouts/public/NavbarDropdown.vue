<template>
  <template v-for="item in items" :key="item.href">
    <div class="nav-dropdown-item">
      <a
        v-if="isExternal(item.href)"
        :href="item.href"
        :target="item.target || '_self'"
        :rel="item.target === '_blank' ? 'noopener noreferrer' : null"
        class="nav-dropdown-link"
      >
        <span>{{ item.label }}</span>
        <span v-if="item.children?.length" class="nav-submenu-arrow material-symbols-outlined" aria-hidden="true">chevron_right</span>
      </a>
      <router-link v-else :to="item.href" class="nav-dropdown-link">
        <span>{{ item.label }}</span>
        <span v-if="item.children?.length" class="nav-submenu-arrow material-symbols-outlined" aria-hidden="true">chevron_right</span>
      </router-link>

      <div v-if="item.children?.length" class="nav-submenu">
        <NavbarDropdown :items="item.children" />
      </div>
    </div>
  </template>
</template>

<script setup>
defineOptions({
  name: 'NavbarDropdown',
})

defineProps({
  items: {
    type: Array,
    required: true,
  },
})

function isExternal(href = '') {
  return /^https?:\/\//i.test(href) || href.startsWith('mailto:') || href.startsWith('tel:')
}
</script>
