<?php

namespace App\Features\Posts\Models;

use App\Models\User;
use App\Traits\OptimizesImages;   // ⭐ TAMBAHAN
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    use OptimizesImages;           // ⭐ TAMBAHAN

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category',
        'content',
        'image',
        'image_caption',
        'published_at',
        'is_published',
        'views',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_published' => 'boolean',
    ];

    /**
     * ⭐ Konfigurasi auto-convert WebP untuk kolom `image`.
     * - quality: 85 (kualitas tinggi, mata tidak bisa bedakan)
     * - max_width: 1200 (cukup untuk thumbnail & preview)
     */
    protected $optimizableImages = [
        'image' => [
            'disk'      => 'public',
            'quality'   => 85,
            'max_width' => 1200,
        ],
    ];

    // Relasi: Berita ditulis oleh User
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}