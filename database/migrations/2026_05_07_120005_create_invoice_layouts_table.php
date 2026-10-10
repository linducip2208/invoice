<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('blocks');
            $table->json('settings')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['workspace_id', 'is_default']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('invoice_layout_id')->nullable()->after('template')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['invoice_layout_id']);
            $table->dropColumn('invoice_layout_id');
        });
        Schema::dropIfExists('invoice_layouts');
    }
};
