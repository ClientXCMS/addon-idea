<?php

namespace App\Addons\Idea\Models;

use Illuminate\Database\Eloquent\Model;

class IdeaImportMap extends Model
{
    protected $table = 'addon_idea_import_maps';

    protected $fillable = [
        'source',
        'type',
        'source_id',
        'target_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
