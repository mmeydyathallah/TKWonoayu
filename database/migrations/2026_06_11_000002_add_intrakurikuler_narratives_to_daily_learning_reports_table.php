<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_learning_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('daily_learning_reports', 'agama_budi_pekerti_narrative')) {
                $table->text('agama_budi_pekerti_narrative')->nullable()->after('agama_budi_pekerti_score');
            }

            if (! Schema::hasColumn('daily_learning_reports', 'jati_diri_narrative')) {
                $table->text('jati_diri_narrative')->nullable()->after('jati_diri_score');
            }

            if (! Schema::hasColumn('daily_learning_reports', 'literasi_steam_narrative')) {
                $table->text('literasi_steam_narrative')->nullable()->after('literasi_steam_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('daily_learning_reports', function (Blueprint $table) {
            if (Schema::hasColumn('daily_learning_reports', 'agama_budi_pekerti_narrative')) {
                $table->dropColumn('agama_budi_pekerti_narrative');
            }

            if (Schema::hasColumn('daily_learning_reports', 'jati_diri_narrative')) {
                $table->dropColumn('jati_diri_narrative');
            }

            if (Schema::hasColumn('daily_learning_reports', 'literasi_steam_narrative')) {
                $table->dropColumn('literasi_steam_narrative');
            }
        });
    }
};
