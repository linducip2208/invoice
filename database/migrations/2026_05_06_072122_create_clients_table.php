<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->default('Indonesia');
            $table->string('postal_code', 20)->nullable();
            $table->string('tax_id', 100)->nullable();
            $table->text('notes')->nullable();
            $table->char('view_token', 36)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('workspace_id');
            $table->index('view_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
