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
        Schema::create('pothole_reports', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id')->unique();
            $table->string('name')->nullable();
            $table->text('description');
            $table->integer('ward');
            $table->string('photo')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->enum('status', ['Pending', 'Ongoing', 'Completed', 'Rejected'])->default('Pending');
            $table->text('admin_remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pothole_reports');
    }
};
