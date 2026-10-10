<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();

            $table->decimal('price_idr_monthly', 12, 2)->default(0);
            $table->decimal('price_idr_yearly', 12, 2)->default(0);
            $table->decimal('price_usd_monthly', 10, 2)->default(0);
            $table->decimal('price_usd_yearly', 10, 2)->default(0);

            $table->json('features')->nullable();

            $table->unsignedInteger('trial_days')->default(0);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_public', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
