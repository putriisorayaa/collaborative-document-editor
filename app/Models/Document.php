<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'title',
        'content',
        'last_edited_at',
        'last_editor_name',
    ];

    protected $casts = [
        'last_edited_at' => 'datetime',
    ];

    public function revisions(): HasMany
    {
        return $this->hasMany(DocumentRevision::class);
    }

    public function editLogs(): HasMany
    {
        return $this->hasMany(DocumentEditLog::class);
    }
}
