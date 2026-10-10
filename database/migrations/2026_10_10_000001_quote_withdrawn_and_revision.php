<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quotes gain a WITHDRAWN state and an explicit revision counter.
        // MySQL enforces enums natively: extend it with raw SQL (additive only).
        // SQLite/Postgres do not enforce the enum: no-op there.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('draft','sent','accepted','rejected','expired','converted','withdrawn') NOT NULL DEFAULT 'draft'");
        }

        if (! Schema::hasColumn('quotes', 'revision')) {
            Schema::table('quotes', function ($table) {
                $table->unsignedInteger('revision')->default(1)->after('status');
            });
        }
    }

    public function down(): void
    {
        // Only roll back quotes that never used the new state; otherwise refuse.
        if (DB::table('quotes')->where('status', 'withdrawn')->exists()) {
            throw new RuntimeException('Cannot roll back: withdrawn quotes exist.');
        }
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('draft','sent','accepted','rejected','expired','converted') NOT NULL DEFAULT 'draft'");
        }
        Schema::table('quotes', function ($table) {
            $table->dropColumn('revision');
        });
    }
};
