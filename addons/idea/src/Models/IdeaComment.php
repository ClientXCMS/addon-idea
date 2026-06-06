<?php

namespace App\Addons\Idea\Models;

use App\Models\Account\Customer;
use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdeaComment extends Model
{
    use SoftDeletes;

    protected $table = 'addon_idea_comments';

    protected $fillable = [
        'post_id',
        'customer_id',
        'admin_id',
        'content',
        'source_id',
        'edited_at',
        'deleted_by_admin_id',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(fn (IdeaComment $comment) => $comment->post?->refreshCounters());
        static::deleted(fn (IdeaComment $comment) => $comment->post?->refreshCounters());
        static::restored(fn (IdeaComment $comment) => $comment->post?->refreshCounters());
    }

    public function post()
    {
        return $this->belongsTo(IdeaPost::class, 'post_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function deletedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'deleted_by_admin_id');
    }
}
