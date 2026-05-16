<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = Role::updateOrCreate(['name' => 'Admin'], [
            'description' => 'Full access untuk mengelola website SPI UII Dalwa.',
            'status' => 'Active',
        ]);

        $operator = Role::updateOrCreate(['name' => 'Operator'], [
            'description' => 'Dapat mengelola dan menerbitkan konten website.',
            'status' => 'Active',
        ]);

        $penulisRole = Role::updateOrCreate(['name' => 'Penulis'], [
            'description' => 'Akses dashboard penulis untuk mengelola artikel berita miliknya sendiri.',
            'status' => 'Active',
        ]);

        $kepalaPenulisRole = Role::updateOrCreate(['name' => 'Kepala Penulis'], [
            'description' => 'Akses dashboard kepala penulis untuk mengelola artikel dan akun penulis.',
            'status' => 'Active',
        ]);

        $adminUser = User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrator',
            'email' => 'admin@spi.local',
            'password' => bcrypt('password'),
            'role_id' => $admin->id,
            'status' => 'Active',
            'last_active_at' => now(),
        ]);

        User::updateOrCreate(['username' => 'operator'], [
            'name' => 'Operator Konten',
            'email' => 'operator@spi.local',
            'password' => bcrypt('password'),
            'role_id' => $operator->id,
            'status' => 'Active',
            'last_active_at' => now()->subHours(2),
        ]);

        $penulisUser = User::updateOrCreate(['username' => 'penulis'], [
            'name' => 'Penulis Berita',
            'email' => 'penulis@spi.local',
            'password' => bcrypt('password'),
            'role_id' => $penulisRole->id,
            'status' => 'Active',
            'last_active_at' => now()->subMinutes(25),
        ]);

        User::updateOrCreate(['username' => 'kepala_penulis'], [
            'name' => 'Kepala Penulis',
            'email' => 'kepala.penulis@spi.local',
            'password' => bcrypt('password'),
            'role_id' => $kepalaPenulisRole->id,
            'status' => 'Active',
            'last_active_at' => now()->subMinutes(20),
        ]);

        AppSetting::setValue('system_name', 'SPI UII Dalwa');
        AppSetting::setValue('smtp_from_name', 'SPI UII Dalwa');
        AppSetting::setValue('accent_color', '#78350f');

        $categories = collect([
            [
                'name' => 'Akademik',
                'slug' => 'akademik',
                'type' => 'Artikel',
                'description' => 'Informasi akademik Program Studi SPI.',
            ],
            [
                'name' => 'Kegiatan Mahasiswa',
                'slug' => 'kegiatan-mahasiswa',
                'type' => 'Artikel',
                'description' => 'Kabar kegiatan mahasiswa dan komunitas akademik SPI.',
            ],
            [
                'name' => 'Galeri Prodi',
                'slug' => 'galeri-prodi',
                'type' => 'Gambar',
                'description' => 'Dokumentasi visual kegiatan Program Studi SPI.',
            ],
            [
                'name' => 'Video Prodi',
                'slug' => 'video-prodi',
                'type' => 'Video',
                'description' => 'Konten video kegiatan dan informasi Program Studi SPI.',
            ],
        ])->mapWithKeys(fn ($category) => [
            $category['slug'] => NewsCategory::updateOrCreate(['slug' => $category['slug']], $category),
        ]);

        $newsItems = [
            [
                'title' => 'Website SPI UII Dalwa Resmi Diperbarui',
                'category_slug' => 'akademik',
                'body' => '<p>Website SPI UII Dalwa hadir sebagai kanal informasi akademik, berita, kegiatan, dan layanan komunikasi prodi.</p>',
                'status' => 'Published',
            ],
            [
                'title' => 'Diskusi Sejarah dan Peradaban Islam untuk Mahasiswa',
                'category_slug' => 'kegiatan-mahasiswa',
                'body' => '<p>Kegiatan diskusi rutin menjadi ruang penguatan wawasan sejarah, analisis peradaban, dan tradisi keilmuan Islam.</p>',
                'status' => 'Published',
            ],
            [
                'title' => 'Dokumentasi Kegiatan Akademik SPI',
                'category_slug' => 'galeri-prodi',
                'body' => null,
                'status' => 'Published',
            ],
            [
                'title' => 'Profil SPI UII Dalwa',
                'category_slug' => 'video-prodi',
                'body' => '<p>Video pengenalan prodi memuat informasi singkat tentang pembelajaran, kegiatan, dan layanan SPI UII Dalwa.</p>',
                'speaker' => 'Tim SPI UII Dalwa',
                'duration' => 1800,
                'status' => 'Draft',
            ],
        ];

        foreach ($newsItems as $index => $item) {
            $categorySlug = $item['category_slug'];
            unset($item['category_slug']);

            $news = News::updateOrCreate(['title' => $item['title']], [
                ...$item,
                'news_category_id' => $categories[$categorySlug]->id,
                'created_by' => $adminUser->id,
                'author_id' => $index === 1 ? $penulisUser->id : null,
                'created_at' => now()->subHours($index * 3),
                'updated_at' => now()->subHours($index * 3),
            ]);
            $news->categories()->syncWithoutDetaching([$categories[$categorySlug]->id]);
        }

        $seededPages = collect([
            'prodi' => [
                'title' => 'Program Studi SPI',
                'body' => '<h2>Profil Program Studi</h2><p>SPI UII Dalwa mengembangkan pembelajaran sejarah, peradaban, dan kajian keislaman berbasis tradisi akademik yang kuat serta kebutuhan masyarakat modern.</p><p>Mahasiswa diarahkan untuk memiliki kompetensi riset historis, analisis peradaban, penulisan akademik, dan komunikasi gagasan secara profesional.</p>',
            ],
            'vmts' => [
                'title' => 'VMTS',
                'body' => '<h2>Visi</h2><p>Menjadi program studi yang unggul dalam pengembangan sejarah dan peradaban Islam, berkarakter, serta berkontribusi bagi masyarakat.</p><h2>Misi</h2><p>Menyelenggarakan pendidikan bermutu, penelitian yang relevan, pengabdian masyarakat, dan kerja sama akademik untuk memperkuat literasi sejarah Islam.</p><h2>Tujuan</h2><p>Menghasilkan lulusan yang kompeten, adaptif, etis, dan mampu berkarya di bidang pendidikan, penelitian, media, kebudayaan, dan pengembangan masyarakat.</p>',
            ],
            'sejarah-singkat' => [
                'title' => 'Sejarah Singkat',
                'body' => '<h2>Sejarah Singkat Program Studi</h2><p>SPI UII Dalwa tumbuh dari kebutuhan lembaga pendidikan tinggi untuk menyediakan ruang akademik yang fokus pada kajian sejarah, peradaban, dan pemikiran Islam.</p><p>Perjalanan prodi terus diperkuat melalui kurikulum, kegiatan mahasiswa, publikasi ilmiah, dan kolaborasi dengan berbagai mitra akademik.</p>',
            ],
            'tridarma' => [
                'title' => 'Tridarma Perguruan Tinggi',
                'body' => '<h2>Tridarma Prodi</h2><p>Tridarma menjadi landasan kerja SPI UII Dalwa dalam menyelenggarakan pendidikan, penelitian, dan pengabdian kepada masyarakat.</p><p>Setiap aktivitas akademik diarahkan untuk menghasilkan manfaat keilmuan, penguatan karakter, dan kontribusi nyata bagi lingkungan sosial.</p>',
            ],
            'pendidikan' => [
                'title' => 'Pendidikan',
                'body' => '<h2>Pendidikan dan Kurikulum</h2><p>Kegiatan pendidikan dirancang untuk membangun kemampuan berbahasa Arab aktif dan pasif, analisis sastra, kajian budaya, metodologi penelitian, dan keterampilan profesional.</p><p>Proses pembelajaran memadukan kuliah, diskusi teks, praktik penerjemahan, presentasi, proyek kreatif, dan pemanfaatan teknologi pembelajaran.</p>',
            ],
            'penelitian' => [
                'title' => 'Penelitian',
                'body' => '<h2>Penelitian</h2><p>Penelitian dosen dan mahasiswa diarahkan pada kajian sejarah Islam, peradaban, manuskrip, budaya, pemikiran, dan isu-isu kontemporer yang relevan.</p><p>Hasil penelitian didorong untuk dipublikasikan, dipresentasikan dalam forum ilmiah, dan menjadi dasar pengembangan pembelajaran.</p>',
            ],
            'pengabdian' => [
                'title' => 'Pengabdian',
                'body' => '<h2>Pengabdian kepada Masyarakat</h2><p>Program pengabdian dilakukan melalui pelatihan bahasa Arab, pendampingan literasi, penguatan kompetensi keagamaan, dan kegiatan sosial edukatif bersama masyarakat.</p><p>Kegiatan ini menjadi ruang penerapan ilmu sekaligus sarana memperkuat hubungan prodi dengan lembaga dan komunitas.</p>',
            ],
            'publikasi' => [
                'title' => 'Publikasi',
                'body' => '<h2>Publikasi Akademik</h2><p>Halaman publikasi memuat informasi karya ilmiah dosen dan mahasiswa, artikel, prosiding, buku, serta luaran akademik lain yang berkaitan dengan sejarah dan peradaban Islam.</p><p>Publikasi menjadi bagian penting dari budaya akademik prodi untuk menyebarkan gagasan dan hasil kajian kepada masyarakat luas.</p>',
            ],
            'dosen' => [
                'title' => 'Dosen',
                'body' => '<h2>Dosen Program Studi</h2><p>Dosen SPI UII Dalwa memiliki keahlian dalam bidang sejarah, peradaban, budaya, pemikiran, dan kajian keislaman.</p><p>Selain mengajar, dosen aktif melakukan penelitian, pengabdian, pembimbingan mahasiswa, dan pengembangan jejaring akademik.</p>',
            ],
            'mahasiswa' => [
                'title' => 'Mahasiswa',
                'body' => '<h2>Kemahasiswaan</h2><p>Mahasiswa SPI didorong aktif dalam kegiatan akademik, organisasi, diskusi, lomba, pelatihan, dan pengembangan minat bakat.</p><p>Prodi menyediakan pendampingan agar mahasiswa mampu mengembangkan kapasitas intelektual, kepemimpinan, kreativitas, dan kepedulian sosial.</p>',
            ],
            'prestasi-mahasiswa' => [
                'title' => 'Prestasi Mahasiswa',
                'body' => '<h2>Prestasi Mahasiswa</h2><p>Prestasi mahasiswa mencakup capaian dalam bidang akademik, karya tulis, diskusi ilmiah, seni, organisasi, dan kegiatan pengabdian.</p><p>Setiap prestasi menjadi bukti semangat belajar, kerja kolaboratif, dan dukungan ekosistem akademik SPI UII Dalwa.</p>',
            ],
            'alumni' => [
                'title' => 'Alumni',
                'body' => '<h2>Alumni</h2><p>Alumni SPI UII Dalwa berkiprah di berbagai bidang seperti pendidikan, penelitian, media, lembaga sosial, administrasi, kewirausahaan, dan studi lanjut.</p><p>Jejaring alumni menjadi mitra penting dalam pengembangan prodi, informasi karier, dan penguatan hubungan dengan dunia kerja.</p>',
            ],
            'kuesioner' => [
                'title' => 'Kuesioner Alumni',
                'body' => '<h2>Kuesioner Alumni</h2><p>Kuesioner alumni digunakan untuk menghimpun umpan balik mengenai relevansi kurikulum, pengalaman belajar, kebutuhan kompetensi, dan perkembangan karier lulusan.</p><p>Masukan alumni menjadi bahan evaluasi berkelanjutan agar layanan akademik dan pengembangan prodi semakin baik.</p>',
            ],
        ])->mapWithKeys(fn ($page, $slug) => [
            $slug => Page::updateOrCreate(['slug' => $slug], [
                'title' => $page['title'],
                'body' => $page['body'],
                'status' => 'Published',
                'created_by' => $adminUser->id,
            ]),
        ]);

        $primaryMenu = Menu::updateOrCreate(['slug' => 'primary'], ['name' => 'Primary Navigation']);
        $primaryMenu->items()->delete();

        $createMenuItem = function (string $label, string $type, ?int $referenceId, ?string $url, int $sortOrder, ?MenuItem $parent = null) use ($primaryMenu) {
            return MenuItem::create([
                'menu_id' => $primaryMenu->id,
                'label' => $label,
                'type' => $type,
                'reference_id' => $referenceId,
                'url' => $url,
                'target' => '_self',
                'parent_id' => $parent?->id,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);
        };

        $createCustomMenuItem = fn (string $label, string $url, int $sortOrder, ?MenuItem $parent = null) => $createMenuItem($label, 'custom', null, $url, $sortOrder, $parent);
        $createPageMenuItem = fn (string $label, string $slug, int $sortOrder, ?MenuItem $parent = null) => $createMenuItem($label, 'page', $seededPages[$slug]->id, null, $sortOrder, $parent);

        $createCustomMenuItem('Home', '/', 1);
        $createCustomMenuItem('Berita', '/news', 2);
        $prodiMenu = $createPageMenuItem('Prodi', 'prodi', 3);
        $createPageMenuItem('VMTS', 'vmts', 1, $prodiMenu);
        $createPageMenuItem('Sejarah Singkat', 'sejarah-singkat', 2, $prodiMenu);

        $tridarmaMenu = $createPageMenuItem('Tridarma', 'tridarma', 4);
        $createPageMenuItem('Pendidikan', 'pendidikan', 1, $tridarmaMenu);
        $createPageMenuItem('Penelitian', 'penelitian', 2, $tridarmaMenu);
        $createPageMenuItem('Pengabdian', 'pengabdian', 3, $tridarmaMenu);

        $createPageMenuItem('Publikasi', 'publikasi', 5);
        $createPageMenuItem('Dosen', 'dosen', 6);

        $mahasiswaMenu = $createPageMenuItem('Mahasiswa', 'mahasiswa', 7);
        $createPageMenuItem('Mahasiswa', 'mahasiswa', 1, $mahasiswaMenu);
        $createPageMenuItem('Prestasi Mahasiswa', 'prestasi-mahasiswa', 2, $mahasiswaMenu);

        $alumniMenu = $createPageMenuItem('Alumni', 'alumni', 8);
        $createPageMenuItem('Alumni', 'alumni', 1, $alumniMenu);
        $createPageMenuItem('Kuesioner', 'kuesioner', 2, $alumniMenu);
        $createCustomMenuItem('Contact', '/contact', 9);

        $this->command?->newLine();
        $this->command?->info('Login seed accounts:');
        $this->command?->line('Admin         : username=admin          password=password');
        $this->command?->line('Operator      : username=operator       password=password');
        $this->command?->line('Penulis       : username=penulis        password=password');
        $this->command?->line('Kepala Penulis: username=kepala_penulis password=password');
        $this->command?->newLine();
    }
}
