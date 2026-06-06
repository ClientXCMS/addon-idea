<?php

namespace App\Addons\Idea\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IdeaTag extends Model
{
    protected $table = 'addon_idea_tags';

    protected $fillable = [
        'name',
        'slug',
        'color',
        'is_public',
        'source_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (IdeaTag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = static::uniqueSlug($tag->name);
            }
            $tag->color = ltrim($tag->color ?: '2563eb', '#');
        });

        static::updating(function (IdeaTag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = static::uniqueSlug($tag->name, $tag->id);
            }
            $tag->color = ltrim($tag->color ?: '2563eb', '#');
        });
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: Str::random(8);
        $slug = $base;
        $index = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }

    public function posts()
    {
        return $this->belongsToMany(IdeaPost::class, 'addon_idea_post_tag', 'tag_id', 'post_id')->withTimestamps();
    }
}
