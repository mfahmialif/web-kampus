<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = [
        'title', 'slug', 'news_category_id', 'body', 'image_path', 'video_path',
        'speaker', 'duration', 'status', 'author_id', 'created_by',
    ];

    protected static function booted(): void
    {
        static::saving(function (News $news) {
            if (! $news->slug && $news->title) {
                $news->slug = static::uniqueSlug($news->title, $news->id);
            } elseif ($news->isDirty('slug')) {
                $news->slug = static::uniqueSlug($news->slug, $news->id);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NewsCategory::class, 'news_category_news')
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(NewsComment::class);
    }

    public static function findBySlugOrId(string $identifier): ?self
    {
        $query = static::query()->where('slug', $identifier);

        if (ctype_digit($identifier)) {
            $query->orWhereKey((int) $identifier);
        }

        return $query->first();
    }

    public static function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'news';
        $slug = $base;
        $counter = 2;

        while (static::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
