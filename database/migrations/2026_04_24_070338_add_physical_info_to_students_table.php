<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('weight_kg', 5, 2)->nullable()->after('distance_to_school_km');
            $table->decimal('height_cm', 5, 2)->nullable()->after('weight_kg');
            $table->decimal('head_circumference_cm', 5, 2)->nullable()->after('height_cm');
            $table->string('blood_type', 4)->nullable()->after('head_circumference_cm');
            $table->text('health_history')->nullable()->after('blood_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['weight_kg', 'height_cm', 'head_circumference_cm', 'blood_type', 'health_history']);
        });
    }
};
