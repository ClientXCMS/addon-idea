<?php

namespace App\Addons\Idea\Models;

use App\Models\Account\Customer;
use Illuminate\Database\Eloquent\Model;

class IdeaVote extends Model
{
    protected $table = 'addon_idea_votes';

    protected $fillable = [
        'post_id',
        'customer_id',
        'source_user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(fn (IdeaVote $vote) => $vote->post?->refreshCounters());
        static::deleted(fn (IdeaVote $vote) => $vote->post?->refreshCounters());
    }

    public function post()
    {
        return $this->belongsTo(IdeaPost::class, 'post_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
