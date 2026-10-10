<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 50);
            $table->boolean('is_default')->default(false);
            $table->string('preview_image')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();

            $table->unique(['slug', 'workspace_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_templates');
    }
};
