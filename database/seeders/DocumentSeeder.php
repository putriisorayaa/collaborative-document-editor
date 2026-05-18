<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        Document::create([
            'title' => 'Dokumen Kolaboratif Pertama',
            'content' => '<p>Mulai mengetik di sini...</p>',
            'last_edited_at' => now(),
            'last_editor_name' => 'System',
        ]);
    }
}