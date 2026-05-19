<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentEditLog extends Model
{
    protected $fillable = [
        'document_id',
        'editor_name',
        'content_before',
        'content_after',
        'summary',
    ];
}
