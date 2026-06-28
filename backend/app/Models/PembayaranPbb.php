<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPbb extends Model
{
    use HasFactory;

    protected $fillable = [
        'wajib_pajak_id',
        'tahun_pajak',
        'jumlah_bayar',
        'tanggal_bayar',
        'status',
        'bukti_sppt_path',
        'catatan_penolakan',
        'kolektor_id',
        'verifikator_id',
        'verified_at',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'date',
        'verified_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Relasi ke wajib pajak
    public function wajibPajak()
    {
        return $this->belongsTo(WajibPajak::class);
    }

    // Relasi ke kolektor yang input
    public function kolektor()
    {
        return $this->belongsTo(User::class, 'kolektor_id');
    }

    // Relasi ke verifikator (admin)
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    // Scope untuk filter by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter by tahun
    public function scopeByYear($query, $year)
    {
        return $query->where('tahun_pajak', $year);
    }

    // Scope untuk filter by RT (untuk RT melihat data wilayahnya)
    public function scopeByRt($query, $rtId)
    {
        return $query->whereHas('wajibPajak.warga', function ($q) use ($rtId) {
            $q->where('rt_id', $rtId);
        });
    }

    // Scope untuk filter by kolektor
    public function scopeByKolektor($query, $kolektorId)
    {
        return $query->where('kolektor_id', $kolektorId);
    }

    // Cek apakah pending
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Cek apakah approved
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    // Cek apakah rejected
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // Approve pembayaran
    public function approve($verifikatorId)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'verifikator_id' => $verifikatorId,
            'verified_at' => now(),
        ]);
    }

    // Reject pembayaran
    public function reject($verifikatorId, $catatan)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'verifikator_id' => $verifikatorId,
            'catatan_penolakan' => $catatan,
            'verified_at' => now(),
        ]);
    }

    // Get status badge info
    public function getStatusBadgeInfo(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING => ['color' => 'yellow', 'label' => 'Pending'],
            self::STATUS_APPROVED => ['color' => 'green', 'label' => 'Lunas'],
            self::STATUS_REJECTED => ['color' => 'red', 'label' => 'Ditolak'],
            default => ['color' => 'gray', 'label' => 'Unknown'],
        };
    }
}
