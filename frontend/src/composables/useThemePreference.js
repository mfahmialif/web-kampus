export const APP_THEME_STORAGE_KEY = 'spi-theme'

const LEGACY_THEME_KEYS = [
  'admin-theme',
  'penulis-theme',
  'kepala-penulis-theme',
]

export function getSystemTheme() {
  if (typeof window === 'undefined' || !window.matchMedia) {
    return 'light'
  }

  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
}

export function normalizeTheme(value, allowSystem = false) {
  if (value === 'light' || value === 'dark') {
    return value
  }

  if (allowSystem && value === 'system') {
    return value
  }

  return null
}

export function resolveThemePreference(value) {
  return value === 'system' ? getSystemTheme() : normalizeTheme(value) || getSystemTheme()
}

export function readThemePreference(options = {}) {
  const {
    allowSystem = false,
    fallback = allowSystem ? 'system' : getSystemTheme(),
    legacyKeys = [],
  } = options

  try {
    const keys = [APP_THEME_STORAGE_KEY, 'spi-public-theme', 'sastraarab-public-theme', ...legacyKeys, ...LEGACY_THEME_KEYS]
    for (const key of [...new Set(keys)]) {
      const stored = normalizeTheme(localStorage.getItem(key), allowSystem)
      if (stored) {
        return stored
      }
    }
  } catch {
    return fallback
  }

  return fallback
}

export function writeThemePreference(value, options = {}) {
  const { legacyKeys = [] } = options
  const normalized = normalizeTheme(value, true) || 'system'

  try {
    if (normalized === 'system') {
      localStorage.removeItem(APP_THEME_STORAGE_KEY)
    } else {
      localStorage.setItem(APP_THEME_STORAGE_KEY, normalized)
    }

    for (const key of legacyKeys) {
      localStorage.setItem(key, resolveThemePreference(normalized))
    }
  } catch {
    // Keep the in-memory theme when storage is unavailable.
  }
}
