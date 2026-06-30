<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->timestamp('lifetime_at')->nullable()->after('lifetime');
        });

        DB::table('teams')
            ->where('lifetime', true)
            ->whereNull('lifetime_at')
            ->update(['lifetime_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('lifetime_at');
        });
    }
};
