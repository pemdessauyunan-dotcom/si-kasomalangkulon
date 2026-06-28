<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramBansos extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_program',
        'sumber_dana',
        'periode_mulai',
        'periode_selesai',
        'jenis_bantuan',
        'nominal',
        'kuota_penerima',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'nominal' => 'decimal:2',
    ];

    const STATUS_AKTIF = 'aktif';
    const STATUS_TIDAK_AKTIF = 'tidak_aktif';

    // Relasi ke penerima bansos
    public function penerimaBansos()
    {
        return $this->hasMany(PenerimaBansos::class);
    }

    // Scope untuk program aktif
    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    // Get jumlah penerima per status
    public function getPenerimaCountByStatus($status)
    {
        return $this->penerimaBansos()->where('status', $status)->count();
    }

    // Get total penerima
    public function getTotalPenerimaAttribute()
    {
        return $this->penerimaBansos()->where('status', 'disetujui')->count();
    }
}
