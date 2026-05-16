# Ask First

Sebelum memulai development remake codebase website program studi, lakukan interactive prompt dengan menanyakan kebutuhan user satu per satu.

AI harus terlebih dahulu menginspeksi / membaca nama folder project saat ini untuk dijadikan acuan penamaan frontend, backend, domain, dan database.

Contoh:
- Folder: spi
- Maka rekomendasi:
  - frontend production: https://spi.uiidalwa.ac.id
  - backend production: https://spiapp.uiidalwa.ac.id
  - database: spi_app

Setiap pertanyaan harus:
- Menunggu jawaban user terlebih dahulu sebelum lanjut
- Memberikan beberapa pilihan cepat yang bisa langsung dipilih user
- Tetap mengizinkan user menginput manual jika pilihannya tidak tersedia
- Menganalisa nama folder project untuk membuat rekomendasi otomatis

---

# Flow Pertanyaan

## 1. URL Frontend Lokal

Tanyakan:
"Masukkan URL frontend lokal"

Berikan contoh pilihan:
- http://localhost:3000
- http://localhost:5173
- http://127.0.0.1:3000

Izinkan custom input.

---

## 2. URL Backend Lokal

Tanyakan:
"Masukkan URL backend lokal"

Gunakan nama folder project sebagai acuan.

Format rekomendasi:
- http://localhost/{namafolder}/backend/public_html

Contoh:
- http://localhost/spiapp/backend/public_html
- http://localhost/paiapp/backend/public_html

Berikan pilihan tambahan:
- http://localhost:8000
- http://localhost:8080
- http://127.0.0.1:8000

Izinkan custom input.

---

## 3. URL Frontend Production

Tanyakan:
"Masukkan URL frontend production"

Gunakan nama folder project sebagai acuan.

Contoh rekomendasi:
- https://spi.uiidalwa.ac.id
- https://pai.uiidalwa.ac.id
- https://mpi.uiidalwa.ac.id

Izinkan custom input.

---

## 4. URL Backend Production

Tanyakan:
"Masukkan URL backend production"

Gunakan format:
{namafolder}app

Contoh rekomendasi:
- https://spiapp.uiidalwa.ac.id
- https://paiapp.uiidalwa.ac.id
- https://mpiapp.uiidalwa.ac.id

Berikan pilihan tambahan:
- https://api.domain.ac.id
- https://backend.domain.ac.id

Izinkan custom input.

---

## 5. Nama Website

Tanyakan:
"Masukkan nama website"

Gunakan nama folder sebagai referensi awal.

Contoh:
- Jika folder `spi`
  - Prodi SPI
  - Website Prodi SPI
  - Sistem Informasi SPI

Izinkan custom input.

---

## 6. Nama Database

Tanyakan:
"Masukkan nama database"

Tambahkan catatan:
"Database harus sudah dibuat terlebih dahulu"

Gunakan nama folder sebagai acuan.

Contoh:
- spi_app
- pai_app
- mpi_app

Izinkan custom input.

---

## 7. Warna Utama Website

Tanyakan:
"Pilih warna utama website"

Berikan pilihan cepat:
- Biru
- Hijau
- Merah
- Emerald
- Navy
- Orange
- Ungu
- Custom

Jika user memilih custom, minta kode HEX atau nama warna.

---

# Setelah Semua Pertanyaan

Setelah semua jawaban diterima:

1. Tampilkan rangkuman konfigurasi yang sudah dipilih user
2. Buat file bernama `specsweb.md`
3. Simpan seluruh hasil konfigurasi dan planning ke dalam file tersebut
4. Gunakan isi `specsweb.md` di folder _agent sebagai acuan utama proses development berikutnya
5. Jangan memulai development sebelum seluruh konfigurasi selesai dibuat

---

# Aturan Awal Implementasi

Sebelum memulai development backend Laravel, jalankan terlebih dahulu:

```bash

php artisan key:generate

di seeder juga ganti untuk tabel app_setting, isikan system name dan smtp_from_namenya, accent_color
oh ya untuk accent_color ini implementasikan juga di halaman public
sesuaikan semua seedernya dengan nama website

setelah selesai semua, jalankan php artisan migrate:f --seed
