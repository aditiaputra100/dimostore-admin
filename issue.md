# Task: Melengkapi Test Case Autentikasi API (AuthApiTest.php)

## Deskripsi
Anda ditugaskan untuk melengkapi dan memperbaiki skenario test case untuk autentikasi API di file `tests/Feature/AuthApiTest.php`. Beberapa test dasar sudah tersedia, tetapi perlu dilengkapi agar mencakup seluruh kriteria penerimaan (acceptance criteria) dan keamanan sesuai dengan dokumen `auth.md`. Framework testing yang digunakan adalah Pest PHP di ekosistem Laravel.

**PENTING**: Patuhi arsitektur response API standar proyek ini yang memiliki struktur JSON: `success`, `message`, `data`, `errors`, dan `meta`.

---

## Langkah-langkah Implementasi (Instruksi tanpa kode)

Ikuti langkah-langkah di bawah ini secara berurutan dan buatlah block `test('...', function () { ... })` untuk masing-masing skenario baru:

### 1. Perbaiki Test Case Registrasi yang Sudah Ada
*   Pada test case dengan deskripsi `'user can register test'`, payload request yang dikirim saat ini tidak menyertakan `password_confirmation`. Ubah payload tersebut untuk menyertakan `password_confirmation` yang nilainya sama persis dengan `password`. Hal ini wajib karena ada aturan `confirmed` pada request aslinya.

### 2. Tambahkan Test Case Validasi Registrasi Lanjutan (Skenario Gagal)
Tambahkan test case baru untuk memastikan validasi registrasi bekerja dengan benar:
*   **Email Sudah Terdaftar:** Buat data user secara otomatis (menggunakan User Factory) untuk menyimpan suatu alamat email ke dalam database. Kemudian lakukan request POST ke `/api/auth/register` dengan menggunakan payload yang valid, namun gunakan alamat email milik user yang baru saja dibuat. Pastikan response mengembalikan HTTP status **422** (Unprocessable Entity) dan pastikan terdapat validasi error spesifik pada field `email`.
*   **Password Kurang dari 8 Karakter:** Lakukan request POST ke `/api/auth/register` dengan mengirimkan data yang valid, namun isi field `password` dan `password_confirmation` dengan string berjumlah kurang dari 8 karakter (misal: "1234567"). Pastikan response mengembalikan HTTP status **422** dan terdapat validasi error spesifik pada field `password`.
*   **Password Confirmation Tidak Cocok:** Lakukan request POST ke `/api/auth/register` dengan memberikan nilai pada field `password` yang berbeda dengan field `password_confirmation`. Pastikan response mengembalikan HTTP status **422** dan terdapat validasi error spesifik pada field `password`.

### 3. Tambahkan Test Case Login Lanjutan
Skenario login sukses sudah tersedia, tambahkan test case untuk skenario login lainnya:
*   **Login Gagal (Password Salah):** Buat sebuah user aktif menggunakan User Factory lengkap dengan password. Lakukan request POST ke `/api/auth/login` menggunakan email yang benar namun berikan string sembarang untuk `password`. Pastikan response mengembalikan HTTP status **401** (Unauthorized) dan periksa bahwa properti `success` di dalam JSON respons bernilai `false`.
*   **Rate Limiting (Terlalu Banyak Percobaan Login):** Lakukan simulasi serangan _brute-force_ dengan mengirimkan request POST ke `/api/auth/login` sebanyak **6 kali berturut-turut** menggunakan loop/perulangan dalam 1 blok test. Pada request ke-1 sampai ke-5, berikan kredensial yang salah. Pada request ke-6, pastikan response mengembalikan HTTP status **429** (Too Many Requests) untuk memastikan proteksi throttle aktif sesuai dengan aturan (throttle:5,1).

### 4. Tambahkan Test Case Untuk Profil Pengguna (Endpoint `/me`)
Tambahkan skenario pengujian untuk endpoint GET `/api/auth/me`:
*   **Akses Ditolak Tanpa Token:** Lakukan request GET ke `/api/auth/me` tanpa menggunakan header authorization (tanpa token). Pastikan response mengembalikan HTTP status **401** (Unauthorized).
*   **Akses Berhasil Dengan Token:** Buat sebuah user baru menggunakan User Factory. Gunakan mekanisme autentikasi testing dari Laravel (contoh: fungsi `actingAs()` atau `Sanctum::actingAs()`) untuk bertindak sebagai user tersebut. Lalu, lakukan request GET ke `/api/auth/me`. Pastikan response mengembalikan HTTP status **200** (OK). Verifikasi struktur respons mengandung struktur API standar (`success`, `message`, `data`, `errors`, `meta`), dan pastikan field `email` yang berada di dalam key `data` nilainya sama dengan email milik user bersangkutan.

### 5. Tambahkan Test Case Logout
Tambahkan pengujian untuk fitur keluar aplikasi endpoint POST `/api/auth/logout`:
*   **Berhasil Logout dan Revoke Token:** Buat user baru menggunakan User Factory. Lakukan autentikasi (bisa melalui `actingAs` dengan token sanctum). Lakukan dua hal berikut secara berurutan:
    1.  Lakukan request POST ke `/api/auth/logout`. Pastikan HTTP status yang didapat adalah **200** (OK).
    2.  Lakukan request GET ke `/api/auth/me` _menggunakan konteks/token yang sama dengan langkah di atas_. Pastikan HTTP status yang didapat sekarang adalah **401** (Unauthorized). Hal ini membuktikan bahwa token sudah berhasil dihapus atau dibatalkan hak aksesnya dari sistem.

---
## Review dan Verifikasi
Pastikan untuk menjalankan ulang seluruh suite testing menggunakan `php artisan test --filter AuthApiTest` sesudah mengimplementasikan kode. Semua _checklist_ pada bagian _Testing Checklist_ di `auth.md` wajib berstatus Passed (Lulus).
