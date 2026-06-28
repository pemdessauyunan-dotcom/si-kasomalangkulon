<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel users untuk semua role (Admin, RT, Kolektor, Masyarakat)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'rt', 'kolektor', 'masyarakat'])->default('masyarakat');
            $table->boolean('is_active')->default(true);
            $table->foreignId('rt_id')->nullable()->constrained('rts')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabel RT/RW
        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dusun');
            $table->integer('rw');
            $table->integer('rt');
            $table->timestamps();
            
            $table->unique(['nama_dusun', 'rw', 'rt']);
        });

        // Tabel warga/masyarakat
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap');
            $table->string('alamat');
            $table->foreignId('rt_id')->constrained('rts')->onDelete('cascade');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'])->nullable();
            $table->string('agama')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['nik', 'rt_id']);
        });

        // Tabel wajib pajak PBB
        Schema::create('wajib_pajak', function (Blueprint $table) {
            $table->id();
            $table->string('nop', 20)->unique(); // Nomor Objek Pajak
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->string('alamat_objek');
            $table->decimal('luas_tanah', 12, 2)->nullable();
            $table->decimal('luas_bangunan', 12, 2)->nullable();
            $table->decimal('njop_tanah', 15, 2)->nullable();
            $table->decimal('njop_bangunan', 15, 2)->nullable();
            $table->decimal('pbb_terutang', 15, 2);
            $table->timestamps();
            
            $table->index(['nop', 'warga_id']);
        });

        // Tabel pembayaran PBB (menggabungkan 7 sheet Excel 2020-2026)
        Schema::create('pembayaran_pbb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wajib_pajak_id')->constrained('wajib_pajak')->onDelete('cascade');
            $table->integer('tahun_pajak');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->date('tanggal_bayar');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('bukti_sppt_path')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->foreignId('kolektor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->unique(['wajib_pajak_id', 'tahun_pajak']);
            $table->index(['status', 'tahun_pajak']);
        });

        // Tabel program bansos
        Schema::create('program_bansos', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program');
            $table->string('sumber_dana');
            $table->date('periode_mulai');
            $table->date('periode_selesai')->nullable();
            $table->string('jenis_bantuan'); // Uang/Sembako/dll
            $table->decimal('nominal', 15, 2)->nullable();
            $table->integer('kuota_penerima')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->timestamps();
        });

        // Tabel penerima bansos
        Schema::create('penerima_bansos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->foreignId('program_bansos_id')->constrained('program_bansos')->onDelete('cascade');
            $table->enum('status', ['diusulkan', 'disetujui', 'ditolak', 'nonaktif'])->default('diusulkan');
            $table->text('alasan_pengajuan')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->foreignId('pengusul_id')->constrained('users')->onDelete('cascade'); // RT yang mengusulkan
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->unique(['warga_id', 'program_bansos_id']);
            $table->index(['status', 'program_bansos_id']);
        });

        // Tabel berita/artikel
        Schema::create('beritas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan')->nullable();
            $table->longText('konten');
            $table->enum('kategori', ['Berita Desa', 'Pembangunan', 'Wisata', 'Pengumuman']);
            $table->string('gambar_utama')->nullable();
            $table->foreignId('penulis_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('views')->default(0);
            $table->timestamps();
            
            $table->index(['kategori', 'is_published', 'published_at']);
        });

        // Tabel pengaduan masyarakat
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengaduan')->unique();
            $table->string('nama_pelapor')->nullable(); // Bisa anonim
            $table->string('kontak_pelapor')->nullable();
            $table->boolean('is_anonim')->default(false);
            $table->string('subjek');
            $table->text('isi_pengaduan');
            $table->string('lokasi_kejadian')->nullable();
            $table->date('tanggal_kejadian')->nullable();
            $table->json('lampiran_foto')->nullable(); // Array path foto
            $table->enum('status', ['diterima', 'diproses', 'selesai', 'ditolak'])->default('diterima');
            $table->text('tindak_lanjut')->nullable();
            $table->foreignId('penanggung_jawab_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
        });

        // Tabel galeri kegiatan
        Schema::create('galeris', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('path_file');
            $table->enum('tipe', ['foto', 'video']);
            $table->foreignId('album_id')->nullable()->constrained('galeri_albums')->onDelete('set null');
            $table->integer('urutan')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Tabel album galeri
        Schema::create('galeri_albums', function (Blueprint $table) {
            $table->id();
            $table->string('nama_album');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_kegiatan')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Tabel struktur organisasi
        Schema::create('struktur_organisasi', function (Blueprint $table) {
            $table->id();
            $table->string('jabatan');
            $table->string('nama_pejabat');
            $table->string('foto')->nullable();
            $table->string('nip')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->integer('urutan')->default(0);
            $table->foreignId('atasan_id')->nullable()->constrained('struktur_organisasi')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel statistik pengunjung
        Schema::create('statistik_pengunjung', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->integer('jumlah_kunjungan')->default(0);
            $table->integer('pengunjung_unik')->default(0);
            $table->timestamps();
            
            $table->unique('tanggal');
        });

        // Tabel activity log
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('statistik_pengunjung');
        Schema::dropIfExists('struktur_organisasi');
        Schema::dropIfExists('galeri_albums');
        Schema::dropIfExists('galeris');
        Schema::dropIfExists('pengaduans');
        Schema::dropIfExists('beritas');
        Schema::dropIfExists('penerima_bansos');
        Schema::dropIfExists('program_bansos');
        Schema::dropIfExists('pembayaran_pbb');
        Schema::dropIfExists('wajib_pajak');
        Schema::dropIfExists('warga');
        Schema::dropIfExists('rts');
        Schema::dropIfExists('users');
    }
};
