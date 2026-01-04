<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $row) {
            $row->integer('report_count')->default(1)->after('status');
            $row->boolean('is_high_priority')->default(false)->after('report_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $row) {
            $row->dropColumn(['report_count', 'is_high_priority']);
        });
    }
};
