<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    use HasFactory;

    protected $table = 'rts';

    protected $fillable = [
        'nama_dusun',
        'rw',
        'rt',
    ];

    // Relasi ke users (pengurus RT)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relasi ke warga
    public function warga()
    {
        return $this->hasMany(Warga::class);
    }

    // Scope untuk filter by dusun
    public function scopeByDusun($query, $dusun)
    {
        return $query->where('nama_dusun', $dusun);
    }

    // Full name representation
    public function getFullNameAttribute(): string
    {
        return "RT {$this->rt} / RW {$this->rw} - Dusun {$this->nama_dusun}";
    }
}
