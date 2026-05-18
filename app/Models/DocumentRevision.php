<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRevision extends Model
{
    protected $fillable = [
        'document_id',
        'revision_no',
        'editor_name',
        'content',
        'summary',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}