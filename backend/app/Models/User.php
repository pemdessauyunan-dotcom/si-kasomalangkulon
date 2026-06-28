<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'rt_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relasi ke RT
    public function rt()
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    // Relasi ke warga (jika user adalah masyarakat)
    public function warga()
    {
        return $this->hasOne(Warga::class);
    }

    // Relasi sebagai kolektor PBB
    public function pembayaranPbbAsKolektor()
    {
        return $this->hasMany(PembayaranPbb::class, 'kolektor_id');
    }

    // Relasi sebagai verifikator PBB
    public function pembayaranPbbAsVerifikator()
    {
        return $this->hasMany(PembayaranPbb::class, 'verifikator_id');
    }

    // Relasi sebagai pengusul bansos
    public function penerimaBansosAsPengusul()
    {
        return $this->hasMany(PenerimaBansos::class, 'pengusul_id');
    }

    // Relasi sebagai verifikator bansos
    public function penerimaBansosAsVerifikator()
    {
        return $this->hasMany(PenerimaBansos::class, 'verifikator_id');
    }

    // Relasi sebagai penulis berita
    public function beritas()
    {
        return $this->hasMany(Berita::class, 'penulis_id');
    }

    // Relasi sebagai penanggung jawab pengaduan
    public function pengaduans()
    {
        return $this->hasMany(Pengaduan::class, 'penanggung_jawab_id');
    }

    // Cek apakah admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Cek apakah RT
    public function isRt(): bool
    {
        return $this->role === 'rt';
    }

    // Cek apakah kolektor
    public function isKolektor(): bool
    {
        return $this->role === 'kolektor';
    }

    // Cek apakah masyarakat (disiapkan tapi belum aktif)
    public function isMasyarakat(): bool
    {
        return $this->role === 'masyarakat';
    }

    // Feature flag untuk login masyarakat
    public static function isWargaLoginEnabled(): bool
    {
        return config('app.feature_warga_login', false);
    }
}
