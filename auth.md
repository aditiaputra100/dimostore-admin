# 🔐 [BE] Task #18 — AuthController: Register, Login, Logout, Me

**Phase:** Phase 3 – REST API Backend
**Prioritas:** 🔴 Critical
**Estimasi:** 1 hari (24 Juni 2026)
**Tipe:** Backend API (Laravel 11 + Sanctum)

---

## 📋 Deskripsi Singkat
Membangun REST API untuk autentikasi customer (bukan admin — admin login lewat Filament session, lihat Task #1-2). Endpoint ini menjadi fondasi bagi seluruh fitur client side yang membutuhkan status login (checkout, profil, riwayat pesanan, dll), jadi harus selesai di awal Phase 3 karena banyak task lain bergantung padanya.

## 🎯 Tujuan
Customer dapat mendaftar, login, mendapatkan token JWT (Sanctum), mengambil data akun sendiri, dan logout (invalidate token) melalui REST API yang dikonsumsi oleh React client.

## 🔗 Referensi PRD
- Bagian 7.1 — Spesifikasi API Endpoint: Autentikasi (Public)
- Bagian 5.1 — Tabel `users`
- Bagian 8.2 — NF6 (bcrypt), NF8 (JWT via Sanctum), NF9 (validasi FormRequest), NF10 (rate limit login), NF11 (sanitasi input)
- Bagian 12.1 & 12.2 — Format Respons API & HTTP Status Code
- Bagian 3.2.1 — C1.1 s/d C1.4 (kebutuhan client side terkait auth)

## 📁 File yang Dibuat/Diubah
| File | Keterangan |
|---|---|
| `app/Http/Controllers/Api/AuthController.php` | Baru — 4 method: register, login, logout, me |
| `app/Http/Requests/Auth/RegisterRequest.php` | Baru — validasi registrasi |
| `app/Http/Requests/Auth/LoginRequest.php` | Baru — validasi login |
| `app/Http/Resources/UserResource.php` | Baru — transformasi data user ke JSON |
| `routes/api.php` | Edit — tambahkan route group `/auth` |

---

## 🔌 Detail Endpoint

### 1️⃣ POST `/api/auth/register`
**Auth:** Tidak perlu (public)

**Request Body:**
```json
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Validasi (RegisterRequest):**
- `name`: required, string, max:100
- `email`: required, email, max:150, unique:users,email
- `password`: required, string, min:8, confirmed

**Alur Proses:**
1. Validasi input via FormRequest.
2. Hash password dengan `Hash::make()` (Bcrypt — NF6).
3. Simpan user baru, default `is_active = 1`.
4. Generate token akses via `$user->createToken('auth_token')->plainTextToken`.
5. Kembalikan data user (tanpa password) + token.

**Response Sukses — 201 Created:**
```json
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": {
    "user": { "id": 1, "name": "Budi Santoso", "email": "budi@example.com", "phone": null, "avatar": null },
    "token": "1|abcdef123456..."
  },
  "errors": null,
  "meta": null
}
```

**Response Gagal — 422 Unprocessable Entity:**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "data": null,
  "errors": { "email": ["Email sudah terdaftar"] },
  "meta": null
}
```

---

### 2️⃣ POST `/api/auth/login`
**Auth:** Tidak perlu (public), tapi dibatasi rate limit

**Request Body:**
```json
{ "email": "budi@example.com", "password": "password123" }
```

**Validasi (LoginRequest):** `email` required|email, `password` required|string

**Alur Proses:**
1. Cek kredensial dengan `Auth::attempt()` atau manual `Hash::check()`.
2. Jika gagal → response 401 (JANGAN beri tahu apakah email atau password yang salah, cukup pesan generik demi keamanan).
3. Jika user `is_active = 0` → response 403 "Akun Anda dinonaktifkan".
4. Jika berhasil → buat token baru.
5. Kembalikan data user + token.

**Response Sukses — 200 OK:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": { "id": 1, "name": "Budi Santoso", "email": "budi@example.com" },
    "token": "2|xyz987..."
  },
  "errors": null,
  "meta": null
}
```

**Response Gagal — 401 Unauthorized:**
```json
{ "success": false, "message": "Email atau password salah", "data": null, "errors": null, "meta": null }
```

**⚠️ Wajib:** Terapkan middleware `throttle:5,1` (maks 5 percobaan gagal per menit per IP — NF10) khusus di route ini.

---

### 3️⃣ POST `/api/auth/logout`
**Auth:** Wajib (`auth:sanctum`)

**Alur Proses:**
1. Ambil token yang sedang dipakai request (`$request->user()->currentAccessToken()`).
2. Revoke/hapus token tersebut dari database.

**Response Sukses — 200 OK:**
```json
{ "success": true, "message": "Logout berhasil", "data": null, "errors": null, "meta": null }
```

---

### 4️⃣ GET `/api/auth/me`
**Auth:** Wajib (`auth:sanctum`)

**Deskripsi:** Dipakai client untuk cek status login & ambil data user saat aplikasi pertama kali dimuat (dipakai oleh AuthStore Zustand di Task #28).

**Response Sukses — 200 OK:**
```json
{
  "success": true,
  "message": "Data user berhasil diambil",
  "data": { "id": 1, "name": "Budi Santoso", "email": "budi@example.com", "phone": null, "avatar": null, "created_at": "2026-06-20T10:00:00Z" },
  "errors": null,
  "meta": null
}
```

**Response Gagal — 401 Unauthorized** (token invalid/expired):
```json
{ "success": false, "message": "Unauthenticated", "data": null, "errors": null, "meta": null }
```

---

## ⚠️ Catatan Penting untuk Developer
- Gunakan `UserResource` untuk semua response user, JANGAN kirim field `password` atau `remember_token` ke client.
- Semua response HARUS mengikuti format standar di Bagian 12.1 PRD (success, message, data, errors, meta) — buat Trait/Helper `ApiResponse` agar konsisten dipakai controller lain (Task #19, #20, dst).
- Route `/auth/logout` dan `/auth/me` didaftarkan dalam route group dengan middleware `auth:sanctum`.
- Password minimal 8 karakter sesuai standar keamanan umum (PRD tidak menyebutkan angka pasti, ini keputusan PM).

---

## ✅ Checklist Progress (update status di card ini)
- [ ] Setup route group `/api/auth` di `routes/api.php`
- [ ] Buat `RegisterRequest` + rules validasi
- [ ] Buat `LoginRequest` + rules validasi
- [ ] Buat `UserResource`
- [ ] Buat helper/trait `ApiResponse` untuk format response standar
- [ ] Implementasi method `register()`
- [ ] Implementasi method `login()` + rate limiting
- [ ] Implementasi method `logout()`
- [ ] Implementasi method `me()`
- [ ] Testing manual via Postman (lihat checklist testing di bawah)
- [ ] Code review & merge ke branch `dev`

## 🧪 Testing Checklist (Postman)
- [ ] Register dengan data valid → 201 + token diterima
- [ ] Register dengan email yang sudah ada → 422
- [ ] Register dengan password < 8 karakter → 422
- [ ] Register dengan password_confirmation tidak cocok → 422
- [ ] Login dengan kredensial benar → 200 + token
- [ ] Login dengan password salah → 401
- [ ] Login 6x berturut-turut dalam 1 menit → percobaan ke-6 kena 429 Too Many Requests
- [ ] GET /me tanpa token → 401
- [ ] GET /me dengan token valid → 200 + data user
- [ ] Logout dengan token valid → 200, lalu coba GET /me pakai token yang sama → harus 401 (token sudah invalid)

## 🔗 Dependency
- ⛔ Blocked by: Task #3 (Install Sanctum), Task #4 (Migration `users`), Task #5 (Model `User`)
- ➡️ Blocking: Task #28 (AuthStore Zustand di frontend butuh endpoint ini)

## 📎 Lampiran
Lihat file terpisah `task-18-wireframe.md` untuk referensi tampilan Login & Register di client (konteks agar struktur data API sesuai kebutuhan UI).