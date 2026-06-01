<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizeTable('students');
        $this->normalizeTable('daily_assessments');
    }

    public function down(): void
    {
        // The previous A1/A2/B1/B2 split cannot be restored reliably after merging.
    }

    private function normalizeTable(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'class_group')) {
            return;
        }

        DB::table($table)->whereIn('class_group', ['A1', 'A2'])->update(['class_group' => 'A']);
        DB::table($table)->whereIn('class_group', ['B1', 'B2'])->update(['class_group' => 'B']);
        DB::table($table)->where('class_group', 'like', 'Kelompok A%')->update(['class_group' => 'A']);
        DB::table($table)->where('class_group', 'like', 'Kelompok B%')->update(['class_group' => 'B']);
    }
};
