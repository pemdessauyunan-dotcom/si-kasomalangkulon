# Website Resmi Desa Kasomalang Kulon

Website resmi pemerintahan desa dengan dua sisi utama:
1. **Website Publik (Front-End)** - Portal informasi desa yang transparan, modern, dan profesional
2. **Dashboard Internal (Back-Office)** - Sistem multi-role untuk Admin Desa, RT, dan Kolektor

## Arsitektur

- **Frontend**: Next.js + Tailwind CSS (deploy di Vercel)
- **Backend**: Laravel (deploy di IDCloudHost)
- **Database**: PostgreSQL/MySQL di server IDCloudHost
- **File Storage**: Object Storage IDCloudHost

## Struktur Proyek

```
/workspace
├── frontend/          # Next.js frontend untuk Vercel
├── backend/           # Laravel backend untuk IDCloudHost
└── docs/             # Dokumentasi
```

## Setup Development

### Frontend
```bash
cd frontend
npm install
npm run dev
```

### Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Role & Akses

| Role | Akses Utama |
|---|---|
| Admin Desa | Akses penuh: kelola konten, approve/reject PBB, kelola bansos, kelola pengguna |
| RT | Lihat & kelola data warga di RT-nya, lihat status PBB, ajukan usulan bansos |
| Kolektor | Input pembayaran PBB, upload bukti SPPT, lihat riwayat |
| Masyarakat | (Disiapkan, belum diaktifkan) |

## Fitur Utama

1. **Modul PBB** - Upload bukti → Pending → Approve/Reject
2. **Modul Bansos** - Usulan RT → Verifikasi Admin
3. **CMS Berita/Artikel**
4. **Manajemen Pengaduan**
5. **Statistik Penduduk**
6. **Transparansi APBDes**

## Migrasi Data

Script migrasi tersedia untuk impor data dari Excel:
- Database Masyarakat (NIK sebagai kunci)
- Database PBB 2020-2026 (NOP sebagai kunci)
- Database Penerima Bansos

## Dokumentasi

Lihat folder `docs/` untuk dokumentasi lengkap penggunaan per role.