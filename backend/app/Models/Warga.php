<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'alamat',
        'rt_id',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'pendidikan_terakhir',
        'pekerjaan',
        'status_perkawinan',
        'agama',
        'user_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relasi ke RT
    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    // Relasi ke user (jika warga punya akun)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke wajib pajak
    public function wajibPajak()
    {
        return $this->hasMany(WajibPajak::class);
    }

    // Relasi ke penerima bansos
    public function penerimaBansos()
    {
        return $this->hasMany(PenerimaBansos::class);
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama_lengkap', 'like', "%{$keyword}%")
              ->orWhere('nik', 'like', "%{$keyword}%");
        });
    }

    // Scope untuk filter by RT
    public function scopeByRt($query, $rtId)
    {
        return $query->where('rt_id', $rtId);
    }
}
