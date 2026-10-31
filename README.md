# team4-webdesign-4E
web for band Why Don't We
# team4-webdesign-4E
web for band Why Don't We

Project ini adalah tugas praktikum Pemrograman Web Tim 4 kelas 5E, Program Studi Teknik Informatika, Jurusan Teknik Elektro, Politeknik Negeri Pontianak.

## Tentang Website

Website resmi (fan site) untuk boy band **Why Don't We** yang beranggotakan Daniel Seavey, Corbyn Besson, Jack Avery, Jonah Marais, dan Zach Herron. Pengunjung bisa mengenal profil band dan membernya, mendengarkan lagu, melihat jadwal tour, memesan tiket konser, membeli merchandise, dan membaca berita terbaru.

Seluruh halaman memakai satu tema yang sama (background toska, kartu biru dengan border hijau neon, tombol hijau, dan judul berfont *Special Elite*), serta responsif untuk desktop, tablet, dan HP.

## Halaman dan Fitur

| Halaman | File | Fitur |
|---|---|---|
| Home | `index.html` | Hero dengan background GIF, rilis terbaru, lagu pilihan, daftar member, jadwal tour, berita terbaru, video, dan form newsletter |
| About | `src/member/profile.php` | Profil band, timeline sejarah, galeri foto, daftar member, dan form pendaftaran fans "Limelight" |
| Profil Member | `src/member/corbyn.php`, `daniel.php`, `jack.php`, `jonah.php`, `zach.php` | Foto, fakta singkat (tanggal lahir, zodiak, kota asal), bio, dan trivia setiap member |
| Music | `src/music/music.php` | Pemutar audio lagu populer, popup lirik, daftar album dengan dropdown link streaming (Spotify, Apple Music, YouTube Music), dan video YouTube |
| Tour | `src/tour/tour.php` | Daftar konser beserta status tiket (Available, Coming Soon, Sold Out) dan arsip tour |
| Ticket | `src/ticket/ticket.php` | Form pemesanan tiket dengan pilihan kategori (Regular/VIP) dan total harga yang langsung terhitung |
| Invoice | `src/ticket/invoice.php` | Invoice dari pesanan tiket dan tombol unduh invoice dalam format PDF |
| Merch | `src/merch/merch.php` | Katalog merchandise (produk unggulan dan semua produk) dengan keranjang belanja |
| News | `src/article/news.php` | Berita utama dan daftar berita, masing-masing terhubung ke halaman artikel (`src/article/article-*.php`) |

## Teknologi

- **HTML5 dan PHP**: halaman utama `index.html`, halaman lainnya berekstensi `.php` dan dijalankan lewat Apache (XAMPP)
- **CSS3**: token tema terpusat di `src/css/base.css` (CSS custom properties), header dan footer bersama (`header.css`, `footer.css`), serta CSS per halaman
- **JavaScript (vanilla)**: menampilkan produk merch dari data, keranjang belanja (disimpan di `localStorage`), total harga tiket, data pesanan ke invoice (`sessionStorage`), dan dropdown Stream di halaman Music
- **Library dan layanan eksternal**: [jsPDF](https://github.com/parallax/jsPDF) untuk invoice PDF, Font Awesome untuk ikon, Google Fonts (*Special Elite*, *Space Grotesk*, *Caveat*), dan YouTube embed
- **Database**: rancangan tabel produk merch tersedia di `database/merch.sql` (MySQL)

## Struktur Folder

```
why_dont_we/
├── index.html          # halaman Home
├── assets/
│   ├── audio/          # file lagu (.mp3)
│   ├── image/          # foto band, member, cover album, produk
│   └── svg/            # logo platform streaming
├── database/
│   └── merch.sql       # rancangan tabel products
└── src/
    ├── article/        # News dan halaman artikel
    ├── css/            # base.css (tema), header.css, footer.css, CSS per halaman
    ├── data/           # products.js (data produk merch)
    ├── js/             # merch.js, music.js
    ├── member/         # About dan profil kelima member
    ├── merch/          # halaman Merch
    ├── music/          # halaman Music
    ├── ticket/         # Ticket dan Invoice
    └── tour/           # halaman Tour
```

## Cara Menjalankan

1. Install [XAMPP](https://www.apachefriends.org/) lalu jalankan **Apache** dari XAMPP Control Panel.
2. Letakkan folder repository ini di `C:\xampp\htdocs\`, sehingga menjadi `C:\xampp\htdocs\team4-webdesign-4E`.
3. Buka `http://localhost/team4-webdesign-4E/why_dont_we/` di browser.

Halaman `.php` harus dibuka lewat `http://localhost/...`, bukan dengan membuka file langsung dari folder.
