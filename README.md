

| **Nama** | [Muhammad Tsaqib Al Farisi] |
| **NIM** | [220103067] |
| **Kelas** | [TI22A2] |


## Tema Kasus: Buku Tamu Digital

Sistem Buku Tamu Digital adalah aplikasi berbasis web yang digunakan untuk mencatat dan mengelola data kunjungan tamu pada sebuah instansi, perusahaan, atau lembaga. Sistem ini menggantikan buku tamu fisik dengan solusi digital yang lebih efisien, terorganisir, dan mudah diakses.

## Fitur Utama

- **Autentikasi** — Login dengan username & password terenkripsi
- **Dashboard** — Statistik tamu (total, hari ini, sedang hadir, menunggu)
- **CRUD Data Tamu** — Tambah, lihat, edit, dan hapus data tamu
- **Filter & Pencarian** — Cari tamu berdasarkan nama, instansi, status, atau tanggal
- **Kelola Pengguna** *(Admin only)* — Manajemen akun petugas (resepsionis)
- **Profil** — Ubah nama & password sendiri

## Teknologi

- **Backend**: PHP Native
- **Frontend**: Bootstrap 5 + Bootstrap Icons
- **Database**: MySQL
- **Autentikasi**: PHP Session + password_hash (bcrypt)

---

## Instalasi

1. Clone atau copy folder `buku-tamu` ke direktori `htdocs` 
2. Import file `database.sql` ke MySQL
3. Sesuaikan konfigurasi database di `config/database.php`
4. Akses di browser: `http://localhost/buku-tamu`

### Akun Login

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin1` | `@12345Admin` |
| Petugas | `petugas` | `password` |
