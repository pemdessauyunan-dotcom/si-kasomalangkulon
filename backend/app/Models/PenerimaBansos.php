<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaBansos extends Model
{
    use HasFactory;

    protected $fillable = [
        'warga_id',
        'program_bansos_id',
        'status',
        'alasan_pengajuan',
        'catatan_penolakan',
        'pengusul_id',
        'verifikator_id',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    const STATUS_DIUSULKAN = 'diusulkan';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_NONAKTIF = 'nonaktif';

    // Relasi ke warga
    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    // Relasi ke program bansos
    public function programBansos()
    {
        return $this->belongsTo(ProgramBansos::class);
    }

    // Relasi ke pengusul (RT)
    public function pengusul()
    {
        return $this->belongsTo(User::class, 'pengusul_id');
    }

    // Relasi ke verifikator (Admin)
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    // Scope untuk filter by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter by program
    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_bansos_id', $programId);
    }

    // Scope untuk filter by RT (untuk RT melihat usulan wilayahnya)
    public function scopeByRt($query, $rtId)
    {
        return $query->whereHas('warga', function ($q) use ($rtId) {
            $q->where('rt_id', $rtId);
        });
    }

    // Approve usulan
    public function approve($verifikatorId)
    {
        $this->update([
            'status' => self::STATUS_DISETUJUI,
            'verifikator_id' => $verifikatorId,
            'verified_at' => now(),
        ]);
    }

    // Reject usulan
    public function reject($verifikatorId, $catatan)
    {
        $this->update([
            'status' => self::STATUS_DITOLAK,
            'verifikator_id' => $verifikatorId,
            'catatan_penolakan' => $catatan,
            'verified_at' => now(),
        ]);
    }

    // Get status badge info
    public function getStatusBadgeInfo(): array
    {
        return match ($this->status) {
            self::STATUS_DIUSULKAN => ['color' => 'yellow', 'label' => 'Diusulkan'],
            self::STATUS_DISETUJUI => ['color' => 'green', 'label' => 'Disetujui'],
            self::STATUS_DITOLAK => ['color' => 'red', 'label' => 'Ditolak'],
            self::STATUS_NONAKTIF => ['color' => 'gray', 'label' => 'Nonaktif'],
            default => ['color' => 'gray', 'label' => 'Unknown'],
        };
    }
}
