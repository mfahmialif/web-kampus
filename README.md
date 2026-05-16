# Run Prompt Plan: `_agent\install.md`

Sebelum instalasi, konfigurasi, atau development, jalankan interactive prompt berdasarkan instruksi di [`_agent/install.md`](_agent/install.md). Prompt tersebut wajib mengumpulkan URL lokal, URL production, nama website, database, dan warna utama, lalu menyimpan hasilnya ke `_agent/specsweb.md`.

Development tidak boleh dimulai sebelum `_agent/specsweb.md` selesai dibuat dan disetujui sebagai acuan konfigurasi project.

## Struktur Project

Project ini terdiri dari dua aplikasi utama:

- `backend/press.app`: Backend Laravel.
- `frontend`: Frontend Vue 3 + Vite.
- `backend/public_html`: Public path untuk akses backend melalui web server lokal.
- `_agent`: Dokumen instruksi dan hasil planning untuk agent.

## Environment

Backend menyediakan contoh environment untuk development dan production:

- `backend/press.app/.env.development.example`
- `backend/press.app/.env.production.example`

Frontend menyediakan contoh environment untuk development dan production:

- `frontend/.env.development.example`
- `frontend/.env.production.example`

Salin file example sesuai mode yang dipakai:

```bash
cp backend/press.app/.env.development.example backend/press.app/.env
cp frontend/.env.development.example frontend/.env.development
```

Untuk production:

```bash
cp backend/press.app/.env.production.example backend/press.app/.env
cp frontend/.env.production.example frontend/.env.production
```

Isi `APP_KEY`, database, domain, dan konfigurasi lain sesuai hasil `_agent/specsweb.md`.

## Setup Backend

Masuk ke folder backend:

```bash
cd backend/press.app
composer install
php artisan key:generate
php artisan migrate:f --seed
```

Catatan awal implementasi dari `_agent/install.md`:

- Sebelum development backend Laravel, jalankan `php artisan key:generate`.
- Seeder tabel `app_setting` harus disesuaikan dengan nama website, `smtp_from_name`, dan `accent_color`.
- `accent_color` juga harus diimplementasikan di halaman public.
- Semua seeder harus disesuaikan dengan nama website.
- Setelah selesai, jalankan `php artisan migrate:f --seed`.

## Setup Frontend

Masuk ke folder frontend:

```bash
cd frontend
npm install
npm run dev
```

Build production:

```bash
npm run build
```

## URL Default Saat Ini

Nilai default yang ada pada env example saat ini:

- Frontend dev: `http://localhost:5173` atau `http://localhost:3000`
- Backend dev: `http://localhost/spi/backend/public_html`
- Frontend production: `https://spi.uiidalwa.ac.id`
- Backend production: `https://spiapp.uiidalwa.ac.id`
- Database default: `spi_app`

Nilai tersebut harus disesuaikan kembali berdasarkan hasil prompt `_agent/install.md` untuk project/folder yang sedang dikerjakan.
