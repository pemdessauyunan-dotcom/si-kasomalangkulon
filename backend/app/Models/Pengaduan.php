<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pengaduan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pengaduan',
        'nama_pelapor',
        'kontak_pelapor',
        'is_anonim',
        'subjek',
        'isi_pengaduan',
        'lokasi_kejadian',
        'tanggal_kejadian',
        'lampiran_foto',
        'status',
        'tindak_lanjut',
        'penanggung_jawab_id',
        'resolved_at',
    ];

    protected $casts = [
        'is_anonim' => 'boolean',
        'tanggal_kejadian' => 'date',
        'lampiran_foto' => 'array',
        'resolved_at' => 'datetime',
    ];

    const STATUS_DITERIMA = 'diterima';
    const STATUS_DIPROSES = 'diproses';
    const STATUS_SELESAI = 'selesai';
    const STATUS_DITOLAK = 'ditolak';

    // Boot method untuk auto-generate nomor pengaduan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pengaduan) {
            if (empty($pengaduan->nomor_pengaduan)) {
                $pengaduan->nomor_pengaduan = 'ADU-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // Relasi ke penanggung jawab
    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id');
    }

    // Scope untuk filter by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk terbaru
    public function scopeTerbaru($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Cek apakah anonim
    public function isAnonim(): bool
    {
        return $this->is_anonim;
    }

    // Get status badge info
    public function getStatusBadgeInfo(): array
    {
        return match ($this->status) {
            self::STATUS_DITERIMA => ['color' => 'blue', 'label' => 'Diterima'],
            self::STATUS_DIPROSES => ['color' => 'yellow', 'label' => 'Diproses'],
            self::STATUS_SELESAI => ['color' => 'green', 'label' => 'Selesai'],
            self::STATUS_DITOLAK => ['color' => 'red', 'label' => 'Ditolak'],
            default => ['color' => 'gray', 'label' => 'Unknown'],
        };
    }
}
