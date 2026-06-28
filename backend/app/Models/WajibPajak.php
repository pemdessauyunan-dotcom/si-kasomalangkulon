<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WajibPajak extends Model
{
    use HasFactory;

    protected $fillable = [
        'nop',
        'warga_id',
        'alamat_objek',
        'luas_tanah',
        'luas_bangunan',
        'njop_tanah',
        'njop_bangunan',
        'pbb_terutang',
    ];

    protected $casts = [
        'luas_tanah' => 'decimal:2',
        'luas_bangunan' => 'decimal:2',
        'njop_tanah' => 'decimal:2',
        'njop_bangunan' => 'decimal:2',
        'pbb_terutang' => 'decimal:2',
    ];

    // Relasi ke warga
    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    // Relasi ke pembayaran PBB
    public function pembayaranPbb()
    {
        return $this->hasMany(PembayaranPbb::class);
    }

    // Scope untuk pencarian by NOP atau nama
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nop', 'like', "%{$keyword}%")
              ->orWhereHas('warga', function ($q) use ($keyword) {
                  $q->where('nama_lengkap', 'like', "%{$keyword}%");
              });
        });
    }

    // Get status pembayaran untuk tahun tertentu
    public function getStatusForYear($year)
    {
        return $this->pembayaranPbb()->where('tahun_pajak', $year)->first();
    }

    // Check if lunas for year
    public function isLunasForYear($year)
    {
        $pembayaran = $this->getStatusForYear($year);
        return $pembayaran && $pembayaran->status === 'approved';
    }
}
