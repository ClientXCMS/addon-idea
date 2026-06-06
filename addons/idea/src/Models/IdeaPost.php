<?php

namespace App\Addons\Idea\Models;

use App\Models\Account\Customer;
use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class IdeaPost extends Model
{
    use SoftDeletes;

    public const STATUS_OPEN = 'open';
    public const STATUS_PLANNED = 'planned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_DUPLICATE = 'duplicate';

    protected $table = 'addon_idea_posts';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
        'response',
        'response_at',
        'response_admin_id',
        'customer_id',
        'source_id',
        'votes_count',
        'comments_count',
    ];

    protected $casts = [
        'response_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'votes_count' => 'integer',
        'comments_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (IdeaPost $post) {
            if (empty($post->slug)) {
                $post->slug = static::uniqueSlug($post->title);
            }
            if (empty($post->customer_id) && auth('web')->check()) {
                $post->customer_id = auth('web')->id();
            }
        });

        static::updating(function (IdeaPost $post) {
            if (empty($post->slug)) {
                $post->slug = static::uniqueSlug($post->title, $post->id);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_PLANNED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_REJECTED,
            self::STATUS_DUPLICATE,
        ];
    }

    public static function statusLabels(): array
    {
        return collect(static::statuses())->mapWithKeys(fn ($status) => [$status => __('idea::lang.statuses.'.$status)])->toArray();
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: Str::random(8);
        $slug = $base;
        $index = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function responseAdmin()
    {
        return $this->belongsTo(Admin::class, 'response_admin_id');
    }

    public function tags()
    {
        return $this->belongsToMany(IdeaTag::class, 'addon_idea_post_tag', 'post_id', 'tag_id')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(IdeaComment::class, 'post_id');
    }

    public function votes()
    {
        return $this->hasMany(IdeaVote::class, 'post_id');
    }

    public function subscribers()
    {
        return $this->hasMany(IdeaSubscriber::class, 'post_id');
    }

    public function hasVoted(?int $customerId = null): bool
    {
        $customerId = $customerId ?: auth('web')->id();
        if (! $customerId) {
            return false;
        }

        return $this->votes()->where('customer_id', $customerId)->exists();
    }

    public function isSubscribed(?int $customerId = null): bool
    {
        $customerId = $customerId ?: auth('web')->id();
        if (! $customerId) {
            return false;
        }

        return $this->subscribers()->where('customer_id', $customerId)->exists();
    }

    public function refreshCounters(): void
    {
        $this->forceFill([
            'votes_count' => $this->votes()->count(),
            'comments_count' => $this->comments()->count(),
        ])->save();
    }
}
