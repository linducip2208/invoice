<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Stok opt-in per produk; mati = perilaku lama (tanpa batas). */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('track_stock')->default(false)->after('is_active');
            $table->decimal('stock_qty', 12, 2)->default(0)->after('track_stock');
            $table->decimal('low_stock_at', 12, 2)->nullable()->after('stock_qty');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['track_stock', 'stock_qty', 'low_stock_at']);
        });
    }
};
