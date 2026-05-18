<?php

// app/Models/DocumentEditLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentEditLog extends Model
{
    protected $fillable = [
        'document_id',
        'editor_name',
        'action',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}