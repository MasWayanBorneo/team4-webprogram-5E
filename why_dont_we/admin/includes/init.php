<?php
/* ============================================================================
   DASHBOARD ADMIN — dipanggil di baris pertama setiap halaman admin:
     require __DIR__ . '/includes/init.php';
   Isinya: koneksi database, daftar menu, dan fungsi bantu (pesan, upload
   gambar, isian form, tombol aksi) yang dipakai semua halaman CRUD.
   ============================================================================ */
require __DIR__ . '/../../includes/db.php';

session_start();

/* ----------------------------------------------------------------------------
   LOGIN — BELUM ADA.
   Sebelum folder admin/ ikut ter-deploy ke situs online, pasang pengecekan
   login di sini (memakai tabel admins). Tanpa login, siapa pun yang membuka
   /admin/ bisa menambah, mengubah, dan menghapus data.
   ------------------------------------------------------------------------- */

/* --- menu samping: [file, judul, ikon Bootstrap Icons] per kelompok -------- */
$admin_menu = [
    'Data Masuk' => [
        ['ticket_orders.php', 'Pesanan Tiket', 'bi-receipt', 'ticket_orders'],
        ['limelight_members.php', 'Anggota Limelight', 'bi-people', 'limelight_members'],
        ['subscribers.php', 'Subscriber', 'bi-envelope', 'subscribers'],
    ],
    'Konten' => [
        ['concerts.php', 'Konser', 'bi-geo-alt', 'concerts'],
        ['ticket_categories.php', 'Kategori Tiket', 'bi-tags', 'ticket_categories'],
        ['news.php', 'Berita', 'bi-newspaper', 'news'],
        ['news_categories.php', 'Kategori Berita', 'bi-folder', 'news_categories'],
        ['products.php', 'Produk Merch', 'bi-bag', 'products'],
        ['limelight_benefits.php', 'Benefit Limelight', 'bi-star', 'limelight_benefits'],
    ],
    'Musik & Personel' => [
        ['songs.php', 'Lagu', 'bi-music-note-beamed', 'songs'],
        ['albums.php', 'Album', 'bi-disc', 'albums'],
        ['videos.php', 'Video', 'bi-youtube', 'videos'],
        ['members.php', 'Personel', 'bi-person-badge', 'members'],
        ['member_trivia.php', 'Trivia Personel', 'bi-lightbulb', 'member_trivia'],
    ],
    'Pengaturan' => [
        ['admins.php', 'Akun Admin', 'bi-shield-lock', 'admins'],
    ],
];

/* --- alur halaman ------------------------------------------------------------ */
function is_post()
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

// isian form yang sudah dirapikan (spasi di awal/akhir dibuang)
function post($key)
{
    return trim($_POST[$key] ?? '');
}

// isian kosong disimpan sebagai NULL (untuk kolom yang boleh kosong)
function null_if_empty($value)
{
    return $value === '' ? null : $value;
}

// validasi angka bulat dalam rentang tertentu (isian form selalu berupa teks)
function is_int_between($value, $min, $max)
{
    return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]) !== false;
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

// pesan yang tampil sekali di halaman berikutnya (setelah redirect)
function flash($type, $message)
{
    $_SESSION['flash'] = [$type, $message];
}

/* --- pesan error dari MySQL dibuat mudah dipahami ----------------------------- */
function db_error_message($stmt)
{
    switch (mysqli_stmt_errno($stmt)) {
        case 1062:
            return 'Data dengan nilai yang sama sudah ada. Kolom yang bertanda unik tidak boleh sama.';
        case 1451:
            return 'Data ini masih dipakai oleh data lain, jadi tidak bisa dihapus. Hapus atau pindahkan dulu data yang memakainya.';
        case 1452:
            return 'Data yang dipilih (relasi) tidak ditemukan.';
        default:
            return 'Terjadi kesalahan database: ' . mysqli_stmt_error($stmt);
    }
}

// simpan (INSERT/UPDATE): berhasil → pesan + kembali ke daftar; gagal → pesan error di form
function finish_save($stmt, $back, &$errors)
{
    if (mysqli_stmt_execute($stmt)) {
        flash('success', 'Data berhasil disimpan.');
        redirect($back);
    }
    $errors[] = db_error_message($stmt);
}

// hapus (DELETE): selalu kembali ke daftar dengan pesan berhasil/gagal
function finish_delete($stmt, $back)
{
    if (mysqli_stmt_execute($stmt)) {
        flash('success', 'Data berhasil dihapus.');
    } else {
        flash('danger', db_error_message($stmt));
    }
    redirect($back);
}

/* --- upload file (fitur plus) -------------------------------------------------
   Menyimpan file dari <input type="file" name="$field"> ke folder
   assets/uploads/<$folder> dan mengembalikan path-nya untuk disimpan di database.
   Kalau tidak ada file yang dipilih, path lama ($current) dipakai lagi.
   $allowed = [jenis file (MIME) => ekstensi]; jenis file dicek dari ISI file,
   bukan dari namanya, supaya file lain yang hanya diganti namanya ditolak. */
function upload_file($field, $current, $allowed, $max_mb, $folder, $kind, &$errors)
{
    $file = $_FILES[$field] ?? null;
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $current;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Upload $kind gagal (kode error {$file['error']}). Pastikan ukurannya tidak melebihi $max_mb MB.";
        return $current;
    }
    if ($file['size'] > $max_mb * 1024 * 1024) {
        $errors[] = "Ukuran $kind maksimal $max_mb MB.";
        return $current;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        $errors[] = "File $kind harus berformat " . strtoupper(implode(', ', array_unique($allowed))) . '.';
        return $current;
    }

    // nama file baru yang acak, supaya tidak menimpa file lain
    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $dir = __DIR__ . '/../../assets/uploads' . ($folder ? "/$folder" : '');
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], "$dir/$name")) {
        $errors[] = ucfirst($kind) . ' tidak bisa disimpan di server.';
        return $current;
    }
    return 'assets/uploads/' . ($folder ? "$folder/" : '') . $name;
}

// gambar: JPG, PNG, WebP (GIF tidak dipakai karena ditolak server hosting), maks 2 MB
function upload_image($field, $current, &$errors)
{
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    return upload_file($field, $current, $allowed, 2, '', 'gambar', $errors);
}

// audio lagu: MP3 saja, maks 10 MB, disimpan di assets/uploads/audio/
function upload_audio($field, $current, &$errors)
{
    $allowed = ['audio/mpeg' => 'mp3', 'audio/mp3' => 'mp3', 'audio/x-mpeg' => 'mp3'];
    return upload_file($field, $current, $allowed, 10, 'audio', 'audio', $errors);
}

// file yang lebih besar dari batas server (post_max_size) membuat $_POST kosong,
// sehingga form terlihat "tidak terjadi apa-apa"; di sini diberi pesan yang jelas
if (is_post() && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    flash('danger', 'File yang di-upload terlalu besar untuk server (batas ' . ini_get('post_max_size') . '). Pilih file yang lebih kecil.');
    redirect($_SERVER['REQUEST_URI']);
}

/* --- tanggal & jam: format database ↔ format <input type="datetime-local"> ---- */
function to_datetime_input($value)
{
    return $value ? date('Y-m-d\TH:i', strtotime($value)) : '';
}

function from_datetime_input($value)
{
    $date = DateTime::createFromFormat('Y-m-d\TH:i', $value);
    return $date ? $date->format('Y-m-d H:i:s') : null;
}

// pilihan dropdown dari tabel lain: [id => label]
function options_from($conn, $sql)
{
    $options = [];
    foreach (mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_NUM) as [$value, $label]) {
        $options[$value] = $label;
    }
    return $options;
}

/* --- tampilan: gambar kecil, tombol aksi, pesan error ------------------------- */
// path gambar di database (assets/...) dilihat dari folder admin/; URL lengkap dipakai apa adanya
function media_url($path)
{
    return preg_match('#^https?://#', $path) ? $path : '../' . $path;
}

function thumb($path)
{
    if (!$path) {
        return '<span class="text-secondary">—</span>';
    }
    return '<img src="' . e(media_url($path)) . '" alt="" class="rounded border" style="width:48px;height:48px;object-fit:cover">';
}

// tombol Edit + Hapus di setiap baris tabel; hapus memakai POST dan konfirmasi
function action_buttons($page, $id)
{
    return '<a href="' . $page . '?action=edit&id=' . (int) $id . '" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a> '
        . '<form method="post" action="' . $page . '" class="d-inline" onsubmit="return confirm(\'Hapus data ini?\')">'
        . '<input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="' . (int) $id . '">'
        . '<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button></form>';
}

function show_errors($errors)
{
    if ($errors) {
        echo '<div class="alert alert-danger"><ul class="mb-0">';
        foreach ($errors as $error) {
            echo '<li>' . e($error) . '</li>';
        }
        echo '</ul></div>';
    }
}

/* --- isian form (Bootstrap 5) -------------------------------------------------- */
function field_text($name, $label, $value, $attrs = '', $help = '')
{
    echo '<div class="mb-3"><label class="form-label" for="f-' . $name . '">' . e($label) . '</label>'
        . '<input class="form-control" id="f-' . $name . '" name="' . $name . '" value="' . e($value) . '" ' . $attrs . '>'
        . ($help ? '<div class="form-text">' . e($help) . '</div>' : '') . '</div>';
}

function field_textarea($name, $label, $value, $rows = 4, $attrs = '', $help = '')
{
    echo '<div class="mb-3"><label class="form-label" for="f-' . $name . '">' . e($label) . '</label>'
        . '<textarea class="form-control" id="f-' . $name . '" name="' . $name . '" rows="' . $rows . '" ' . $attrs . '>' . e($value) . '</textarea>'
        . ($help ? '<div class="form-text">' . e($help) . '</div>' : '') . '</div>';
}

// $empty = teks pilihan kosong (untuk kolom yang boleh NULL), null = wajib dipilih
function field_select($name, $label, $options, $selected, $empty = null, $help = '')
{
    echo '<div class="mb-3"><label class="form-label" for="f-' . $name . '">' . e($label) . '</label>'
        . '<select class="form-select" id="f-' . $name . '" name="' . $name . '"' . ($empty === null ? ' required' : '') . '>';
    if ($empty !== null) {
        echo '<option value="">' . e($empty) . '</option>';
    }
    foreach ($options as $value => $text) {
        echo '<option value="' . e($value) . '"' . ((string) $value === (string) $selected ? ' selected' : '') . '>' . e($text) . '</option>';
    }
    echo '</select>' . ($help ? '<div class="form-text">' . e($help) . '</div>' : '') . '</div>';
}

function field_checkbox($name, $label, $checked)
{
    echo '<div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="f-' . $name . '" name="' . $name . '" value="1"' . ($checked ? ' checked' : '') . '>'
        . '<label class="form-check-label" for="f-' . $name . '">' . e($label) . '</label></div>';
}

// gambar: pratinjau + path sekarang (boleh diketik/URL) + pilih file baru untuk upload
function field_image($name, $label, $value)
{
    echo '<div class="mb-3"><label class="form-label" for="f-' . $name . '">' . e($label) . '</label>';
    if ($value) {
        echo '<div class="mb-2"><img src="' . e(media_url($value)) . '" alt="" class="rounded border" style="max-height:120px"></div>';
    }
    echo '<input class="form-control mb-2" name="' . $name . '_current" value="' . e($value) . '" placeholder="assets/image/nama-file.jpg">'
        . '<input class="form-control" type="file" id="f-' . $name . '" name="' . $name . '" accept=".jpg,.jpeg,.png,.webp">'
        . '<div class="form-text">Pilih file untuk upload gambar baru (JPG, PNG, WebP, maks 2 MB), atau isi path gambar yang sudah ada di atas.</div></div>';
}

// audio: pemutar untuk mendengar file sekarang + path (boleh diketik) + pilih file MP3 baru
function field_audio($name, $label, $value)
{
    echo '<div class="mb-3"><label class="form-label" for="f-' . $name . '">' . e($label) . '</label>';
    if ($value) {
        echo '<div class="mb-2"><audio controls preload="none" src="' . e(media_url($value)) . '" class="w-100"></audio></div>';
    }
    echo '<input class="form-control mb-2" name="' . $name . '_current" value="' . e($value) . '" placeholder="assets/audio/nama-lagu.mp3">'
        . '<input class="form-control" type="file" id="f-' . $name . '" name="' . $name . '" accept=".mp3,audio/mpeg">'
        . '<div class="form-text">Pilih file untuk upload lagu baru (MP3, maks 10 MB), atau isi path file yang sudah ada di atas.</div></div>';
}

// tombol Simpan + Batal di bawah form
function form_buttons($back)
{
    echo '<button class="btn btn-primary" name="do" value="save"><i class="bi bi-check-lg"></i> Simpan</button> '
        . '<a href="' . $back . '" class="btn btn-secondary">Batal</a>';
}
