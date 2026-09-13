<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_status_laporan', function (Blueprint $table) {
            $table->uuid('id_log')->primary();
            $table->uuid('id_laporan');
            $table->string('status_before', 50)->nullable();
            $table->string('status_after', 50);
            $table->uuid('changed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_laporan')->references('id_laporan')->on('reports')->cascadeOnDelete();
            $table->foreign('changed_by')->references('id_user')->on('users')->nullOnDelete();

            $table->index('id_laporan', 'idx_log_status_laporan_id_laporan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_status_laporan');
    }
};
