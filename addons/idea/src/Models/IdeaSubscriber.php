<?php

namespace App\Addons\Idea\Models;

use App\Models\Account\Customer;
use Illuminate\Database\Eloquent\Model;

class IdeaSubscriber extends Model
{
    protected $table = 'addon_idea_subscribers';

    protected $fillable = [
        'post_id',
        'customer_id',
        'source_user_id',
        'status',
    ];

    public function post()
    {
        return $this->belongsTo(IdeaPost::class, 'post_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
