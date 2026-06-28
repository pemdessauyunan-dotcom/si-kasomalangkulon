# Dokumentasi Website Desa Kasomalang Kulon

## Panduan Penggunaan untuk Setiap Role

### 1. Admin Desa

**Akses:** Login di `/login/admin`

#### Dashboard
- Melihat ringkasan statistik desa (penduduk, PBB, bansos)
- Notifikasi pembayaran PBB pending
- Notifikasi usulan bansos baru

#### Manajemen PBB
1. **Melihat Daftar Pembayaran**
   - Filter berdasarkan status (Pending, Approved, Rejected)
   - Filter berdasarkan tahun pajak
   - Cari berdasarkan NOP atau nama wajib pajak

2. **Verifikasi Pembayaran**
   - Buka detail pembayaran pending
   - Lihat foto bukti SPPT (bisa di-zoom)
   - Klik "Approve" jika valid
   - Klik "Reject" dan isi alasan jika tidak valid

3. **Laporan**
   - Export rekap PBB per tahun ke Excel/PDF
   - Grafik realisasi vs target
   - Daftar wajib pajak belum bayar per RT

#### Manajemen Bansos
1. **Verifikasi Usulan**
   - Lihat usulan dari RT
   - Approve atau reject dengan catatan
   - Tetapkan penerima resmi

2. **Kelola Program**
   - Tambah/edit program bansos baru
   - Set periode dan kuota
   - Aktifkan/nonaktifkan program

#### Manajemen Pengguna
- Buat akun RT baru
- Buat akun Kolektor baru
- Reset password pengguna
- Nonaktifkan akun

---

### 2. RT (Ketua RT)

**Akses:** Login di `/login/rt`

#### Dashboard
- Statistik warga di RT-nya
- Jumlah pembayaran PBB di wilayahnya
- Usulan bansos yang diajukan

#### Data Warga
- Lihat daftar warga di RT-nya
- Tidak bisa melihat RT lain

#### PBB
- Lihat status pembayaran PBB warga RT-nya
- Filter: lunas, pending, belum bayar
- Export data untuk laporan

#### Bansos
1. **Ajukan Usulan**
   - Pilih warga di RT-nya
   - Pilih program bansos
   - Isi alasan pengajuan
   - Submit untuk diverifikasi admin

2. **Monitor Usulan**
   - Lihat status usulan (Diusulkan, Disetujui, Ditolak)
   - Baca catatan penolakan jika ada

---

### 3. Kolektor PBB

**Akses:** Login di `/login/kolektor`

#### Dashboard
- Ringkasan setoran PBB
- Jumlah pembayaran yang diinput
- Status verifikasi (pending, approved, rejected)

#### Input Pembayaran
1. **Input Baru**
   - Pilih wajib pajak dari daftar
   - Input jumlah dibayar
   - Input tanggal bayar
   - Upload foto bukti SPPT (wajib)
   - Submit → status otomatis "Pending"

2. **Riwayat Input**
   - Lihat semua pembayaran yang diinput
   - Filter berdasarkan status
   - Lihat catatan penolakan jika ditolak
   - Input ulang jika ditolak

#### Wilayah Tugas
- Kolektor ditugaskan ke RT tertentu
- Hanya bisa input pembayaran untuk RT tugasnya

---

### 4. Masyarakat (Publik - Tanpa Login)

#### Cek Status PBB
1. Buka halaman `/pbb`
2. Masukkan NOP atau nama
3. Lihat status pembayaran per tahun
4. Info yang ditampilkan dibatasi untuk keamanan

#### Cek Status Bansos
1. Buka halaman `/bansos/cek`
2. Masukkan NIK
3. Lihat status kepesertaan bansos
4. Hanya menampilkan status umum (terdaftar/tidak)

#### Pengaduan
1. Buka halaman `/pengaduan`
2. Isi form pengaduan (bisa anonim)
3. Upload foto jika ada
4. Catat nomor pengaduan untuk tracking

#### Berita & Informasi
- Baca berita desa
- Lihat galeri kegiatan
- Download formulir layanan

---

## Struktur Database

### Tabel Utama

1. **users** - Semua pengguna sistem (admin, rt, kolektor, masyarakat)
2. **rts** - Data RT/RW/dusun
3. **warga** - Data penduduk desa
4. **wajib_pajak** - Data objek PBB
5. **pembayaran_pbb** - Riwayat pembayaran PBB (menggabungkan 7 sheet Excel 2020-2026)
6. **program_bansos** - Program bantuan sosial
7. **penerima_bansos** - Penerima bantuan per program
8. **beritas** - Berita/artikel desa
9. **pengaduans** - Pengaduan masyarakat
10. **galeris** - Foto/video kegiatan
11. **struktur_organisasi** - Perangkat desa
12. **activity_logs** - Log aktivitas sistem

---

## Migrasi Data dari Excel

### Persiapan File Excel

1. **Database Masyarakat**
   - Kolom wajib: NIK (kunci utama), nama, alamat, RT/RW
   - Pastikan NIK format text (16 digit)

2. **Database PBB (7 sheet: 2020-2026)**
   - Header konsisten di semua sheet
   - Kolom wajib: NOP, nama, alamat, jumlah bayar, status
   - Bersihkan merged cells dan subtotal

3. **Database Bansos**
   - Kolom wajib: NIK, nama, program, status
   - Pastikan NIK match dengan database masyarakat

### Proses Migrasi

```bash
# Di server backend (IDCloudHost)
php artisan migrate --seed

# Jalankan script migrasi Excel
php artisan migrate:excel path/to/file.xlsx
```

### Setelah Migrasi

- Data historis PBB 2020-2025 otomatis status "Lunas"
- Tahun 2026+ mengikuti alur normal (pending → approve/reject)
- Excel asli tetap disimpan sebagai arsip

---

## Konfigurasi Environment

### Frontend (.env.local)

```env
NEXT_PUBLIC_API_URL=https://api.kasomalangkulon.id
NEXT_PUBLIC_SITE_NAME="Desa Kasomalang Kulon"
```

### Backend (.env)

```env
APP_NAME="SI Kasomalang Kulon"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://api.kasomalangkulon.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kasomalangkulon
DB_USERNAME=...
DB_PASSWORD=...

FILESYSTEM_DISK=public
UPLOAD_MAX_SIZE=5242880

# Feature flags
FEATURE_WARGA_LOGIN=false
```

---

## Keamanan

1. **Authentication**: JWT token dengan expiry
2. **Authorization**: Role-based middleware di setiap endpoint
3. **File Upload**: Validasi tipe dan ukuran file
4. **Input Sanitization**: Mencegah SQL injection & XSS
5. **Rate Limiting**: Pada endpoint publik (cek PBB, cek bansos, pengaduan)
6. **Activity Logging**: Semua aksi penting tercatat
7. **Password Hashing**: bcrypt/argon2

---

## Deployment

### Frontend (Vercel)

1. Connect repository GitHub
2. Set environment variables
3. Auto-deploy on push to main branch
4. Custom domain: `kasomalangkulon.vercel.app` atau domain custom

### Backend (IDCloudHost)

1. Setup Cloud VPS (2 vCPU, 4GB RAM, 40GB SSD)
2. Install PHP, Composer, MySQL/PostgreSQL
3. Clone repository backend
4. Setup SSL (Let's Encrypt)
5. Configure nginx/Apache
6. Setup automatic backup database

### Domain

- Frontend: `www.kasomalangkulon.id` → Vercel
- Backend API: `api.kasomalangkulon.id` → IDCloudHost
- CORS: Backend mengizinkan domain frontend

---

## Troubleshooting

###常见问题

1. **CORS Error**
   - Pastikan backend CORS config mengizinkan domain frontend
   - Check `cors.php` di Laravel

2. **Upload Gagal**
   - Cek permission folder `storage/app/public`
   - Cek `upload_max_filesize` di php.ini

3. **Token Expired**
   - Refresh token di frontend
   - Adjust token expiry di backend

4. **Migrasi Excel Gagal**
   - Pastikan format Excel sesuai template
   - Cek encoding file (harus UTF-8)

---

## Kontak Support

Untuk bantuan teknis, hubungi:
- Email: dev@kasomalangkulon.id
- WhatsApp: [Nomor Developer]
