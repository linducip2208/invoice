<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Link ulasan Google per workspace; dipakai di halaman NPS promoter. */
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('google_review_url', 500)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn('google_review_url');
        });
    }
};
