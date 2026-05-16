<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id'
    ];

    public function revisions()
    {
        return $this->hasMany(DocumentRevision::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}