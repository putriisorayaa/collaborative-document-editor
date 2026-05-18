<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('revision_no');
            $table->string('editor_name')->nullable();
            $table->longText('content');
            $table->string('summary')->nullable();
            $table->timestamps();

            $table->unique(['document_id', 'revision_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_revisions');
    }
};