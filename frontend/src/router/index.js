import { createRouter, createWebHistory } from 'vue-router'
import LandingIndex from '../views/public/landing/Index.vue'
import NewsPage from '../views/public/news/Index.vue'
import DetailPage from '../views/public/news/Detail.vue'
import PageDetail from '../views/public/pages/Detail.vue'
import ContactPage from '../views/public/contact/Index.vue'
import Login from '../views/public/login/Index.vue'
import Register from '../views/public/register/Index.vue'
import VerifyEmail from '../views/public/verify-email/Index.vue'
import PublicLayout from '../layouts/PublicLayout.vue'
import AdminLayout from '../layouts/AdminLayout.vue'
import PenulisLayout from '../layouts/PenulisLayout.vue'
import KepalaPenulisLayout from '../layouts/KepalaPenulisLayout.vue'
import AdminDashboard from '../views/admin/dashboard/Index.vue'
import { useAuthStore } from '../stores/auth'

const appName = 'SPI UII Dalwa'

const routes = [
  {
    path: '/',
    component: PublicLayout,
    children: [
      {
        path: '',
        name: 'Landing',
        component: LandingIndex,
        meta: { title: appName, publicActive: 'home' }
      },
      {
        path: 'news',
        name: 'News',
        component: NewsPage,
        meta: { title: `${appName} - News`, publicActive: 'news' }
      },
      {
        path: 'news/:id',
        name: 'DetailNews',
        component: DetailPage,
        meta: { title: `${appName} - Detail News`, detailType: 'news', publicActive: 'news' }
      },
      {
        path: 'pages/:slug',
        name: 'PublicPage',
        component: PageDetail,
        meta: { title: `${appName} - Page`, publicActive: 'page' }
      },
      {
        path: 'contact',
        name: 'Contact',
        component: ContactPage,
        meta: { title: `${appName} - Contact`, publicActive: 'contact' }
      },
      {
        path: 'info-terkini',
        redirect: '/news'
      },
      {
        path: 'info-terkini/:id',
        redirect: to => `/news/${to.params.id}`
      },
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { title: `${appName} - Login` }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { title: `${appName} - Daftar` }
  },
  {
    path: '/verify-email',
    name: 'VerifyEmail',
    component: VerifyEmail,
    meta: { title: `${appName} - Verifikasi Email` }
  },
  {
    path: '/administrator',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/administrator/dashboard'
      },
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: AdminDashboard,
        meta: { title: `${appName} - Admin Dashboard`, pageTitle: 'Dashboard', requiresAuth: true }
      },
      {
        path: 'news',
        name: 'AdminNews',
        component: () => import('../views/admin/news/Index.vue'),
        meta: { title: `${appName} - News`, pageTitle: 'News', requiresAuth: true }
      },
      {
        path: 'news/create',
        name: 'AdminNewsCreate',
        component: () => import('../views/admin/news/Form.vue'),
        meta: { title: `${appName} - Tambah News`, pageTitle: 'Tambah News', requiresAuth: true }
      },
      {
        path: 'news/:id/edit',
        name: 'AdminNewsEdit',
        component: () => import('../views/admin/news/Form.vue'),
        meta: { title: `${appName} - Edit News`, pageTitle: 'Edit News', requiresAuth: true }
      },
      {
        path: 'news-categories',
        name: 'AdminNewsCategories',
        component: () => import('../views/admin/newsCategories/Index.vue'),
        meta: { title: `${appName} - Kategori News`, pageTitle: 'Kategori News', requiresAuth: true }
      },
      {
        path: 'pages',
        name: 'AdminPages',
        component: () => import('../views/admin/pages/Index.vue'),
        meta: { title: `${appName} - Pages`, pageTitle: 'Pages', requiresAuth: true }
      },
      {
        path: 'pages/create',
        name: 'AdminPagesCreate',
        component: () => import('../views/admin/pages/Form.vue'),
        meta: { title: `${appName} - Tambah Page`, pageTitle: 'Tambah Page', requiresAuth: true }
      },
      {
        path: 'pages/:id/edit',
        name: 'AdminPagesEdit',
        component: () => import('../views/admin/pages/Form.vue'),
        meta: { title: `${appName} - Edit Page`, pageTitle: 'Edit Page', requiresAuth: true }
      },
      {
        path: 'menus',
        name: 'AdminMenus',
        component: () => import('../views/admin/menus/Index.vue'),
        meta: { title: `${appName} - Manajemen Menu`, pageTitle: 'Manajemen Menu', requiresAuth: true }
      },
      {
        path: 'info-terkini',
        redirect: '/administrator/news'
      },
      {
        path: 'info-terkini/create',
        redirect: '/administrator/news/create'
      },
      {
        path: 'info-terkini/:id/edit',
        redirect: to => `/administrator/news/${to.params.id}/edit`
      },
      {
        path: 'manajemen-user',
        name: 'AdminManajemenUser',
        component: () => import('../views/admin/user/User.vue'),
        meta: { title: `${appName} - Manajemen User`, pageTitle: 'Manajemen User', requiresAuth: true }
      },
      {
        path: 'manajemen-role',
        name: 'AdminManajemenRole',
        component: () => import('../views/admin/user/Role.vue'),
        meta: { title: `${appName} - Manajemen Role`, pageTitle: 'Manajemen Role', requiresAuth: true }
      },
      {
        path: 'profile',
        name: 'AdminProfile',
        component: () => import('../views/admin/profile/Index.vue'),
        meta: { title: `${appName} - Profile`, pageTitle: 'Profile', requiresAuth: true }
      },
      {
        path: 'pengaturan',
        name: 'AdminPengaturan',
        component: () => import('../views/admin/pengaturan/Index.vue'),
        meta: { title: `${appName} - Pengaturan`, pageTitle: 'Pengaturan', requiresAuth: true }
      }
    ]
  },
  {
    path: '/penulis',
    component: PenulisLayout,
    meta: { requiresAuth: true, roles: ['Penulis'] },
    children: [
      { path: '', redirect: '/penulis/dashboard' },
      {
        path: 'dashboard',
        name: 'PenulisDashboard',
        component: () => import('../views/penulis/dashboard/Index.vue'),
        meta: { title: `${appName} - Dashboard Penulis`, pageTitle: 'Dashboard Penulis', requiresAuth: true, roles: ['Penulis'] }
      },
      {
        path: 'news',
        name: 'PenulisNews',
        component: () => import('../views/penulis/news/Index.vue'),
        meta: { title: `${appName} - Artikel Saya`, pageTitle: 'Artikel Saya', requiresAuth: true, roles: ['Penulis'] }
      },
      {
        path: 'news/create',
        name: 'PenulisNewsCreate',
        component: () => import('../views/penulis/news/Form.vue'),
        meta: { title: `${appName} - Tambah Artikel`, pageTitle: 'Tambah Artikel', requiresAuth: true, roles: ['Penulis'] }
      },
      {
        path: 'news/:id/edit',
        name: 'PenulisNewsEdit',
        component: () => import('../views/penulis/news/Form.vue'),
        meta: { title: `${appName} - Edit Artikel`, pageTitle: 'Edit Artikel', requiresAuth: true, roles: ['Penulis'] }
      },
      {
        path: 'profile',
        name: 'PenulisProfile',
        component: () => import('../views/penulis/profile/Index.vue'),
        meta: { title: `${appName} - Profile Penulis`, pageTitle: 'Profile Penulis', requiresAuth: true, roles: ['Penulis'] }
      }
    ]
  },
  {
    path: '/kepala-penulis',
    component: KepalaPenulisLayout,
    meta: { requiresAuth: true, roles: ['Kepala Penulis'] },
    children: [
      { path: '', redirect: '/kepala-penulis/dashboard' },
      {
        path: 'dashboard',
        name: 'KepalaPenulisDashboard',
        component: () => import('../views/kepalaPenulis/dashboard/Index.vue'),
        meta: { title: `${appName} - Dashboard Kepala Penulis`, pageTitle: 'Dashboard Kepala Penulis', requiresAuth: true, roles: ['Kepala Penulis'] }
      },
      {
        path: 'news',
        name: 'KepalaPenulisNews',
        component: () => import('../views/kepalaPenulis/news/Index.vue'),
        meta: { title: `${appName} - Artikel Penulis`, pageTitle: 'Artikel Penulis', requiresAuth: true, roles: ['Kepala Penulis'] }
      },
      {
        path: 'news/create',
        name: 'KepalaPenulisNewsCreate',
        component: () => import('../views/kepalaPenulis/news/Form.vue'),
        meta: { title: `${appName} - Tambah Artikel Penulis`, pageTitle: 'Tambah Artikel Penulis', requiresAuth: true, roles: ['Kepala Penulis'] }
      },
      {
        path: 'news/:id/edit',
        name: 'KepalaPenulisNewsEdit',
        component: () => import('../views/kepalaPenulis/news/Form.vue'),
        meta: { title: `${appName} - Edit Artikel Penulis`, pageTitle: 'Edit Artikel Penulis', requiresAuth: true, roles: ['Kepala Penulis'] }
      },
      {
        path: 'writers',
        name: 'KepalaPenulisWriters',
        component: () => import('../views/kepalaPenulis/writers/Index.vue'),
        meta: { title: `${appName} - Akun Penulis`, pageTitle: 'Akun Penulis', requiresAuth: true, roles: ['Kepala Penulis'] }
      },
      {
        path: 'profile',
        name: 'KepalaPenulisProfile',
        component: () => import('../views/kepalaPenulis/profile/Index.vue'),
        meta: { title: `${appName} - Profile Kepala Penulis`, pageTitle: 'Profile Kepala Penulis', requiresAuth: true, roles: ['Kepala Penulis'] }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (to.hash) return { el: to.hash, behavior: 'smooth' }
    return savedPosition || { top: 0 }
  }
})

router.beforeEach(async (to) => {
  document.title = to.meta.title || appName

  const authStore = useAuthStore()

  if (to.meta.requiresAuth || to.name === 'Login') {
    await authStore.fetchUser()
  }

  const isAuthenticated = authStore.isAuthenticated

  if (to.meta.requiresAuth && !isAuthenticated) {
    return { name: 'Login' }
  }

  if (to.meta.requiresAuth && isAuthenticated) {
    const roleName = authStore.user?.role?.name
    const allowedRoles = to.meta.roles || ['Admin', 'Operator']
    if (!roleName || !allowedRoles.includes(roleName)) {
      await authStore.logout(false)
      return { name: 'Login' }
    }
  }

  if (to.name === 'Login' && isAuthenticated) {
    const roleName = authStore.user?.role?.name
    if (roleName === 'Penulis') return { name: 'PenulisDashboard' }
    if (roleName === 'Kepala Penulis') return { name: 'KepalaPenulisDashboard' }
    return { name: 'AdminDashboard' }
  }
})

export default router
