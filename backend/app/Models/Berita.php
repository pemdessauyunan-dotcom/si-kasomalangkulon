<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'konten',
        'kategori',
        'gambar_utama',
        'penulis_id',
        'is_published',
        'published_at',
        'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer',
    ];

    const KATEGORI_BERITA_DESA = 'Berita Desa';
    const KATEGORI_PEMBANGUNAN = 'Pembangunan';
    const KATEGORI_WISATA = 'Wisata';
    const KATEGORI_PENGUMUMAN = 'Pengumuman';

    // Boot method untuk auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = Str::slug($berita->judul);
            }
        });
    }

    // Relasi ke penulis
    public function penulis()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    // Scope untuk published
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    // Scope untuk filter by kategori
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    // Scope untuk populer (by views)
    public function scopePopuler($query, $limit = 5)
    {
        return $query->orderBy('views', 'desc')->limit($limit);
    }

    // Scope untuk terbaru
    public function scopeTerbaru($query, $limit = 10)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    // Increment views
    public function incrementViews()
    {
        $this->increment('views');
    }
}
