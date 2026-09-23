<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
        $table->uuid('id_user')->primary();
        $table->string('name', 100);
        $table->string('email', 100)->unique();
        $table->timestamp('email_verified_at')->nullable(); // biarkan, tidak dipakai tapi tidak masalah
        $table->string('password');
        $table->rememberToken();
        $table->string('role', 50);
        // Satu sumber kebenaran untuk state akun. Sebelumnya ada is_active
        // terpisah yang diset true saat akun dibuat/diverifikasi tapi TIDAK
        // PERNAH dicek di AuthenticatedSessionController@store — jadi kalau
        // admin "menonaktifkan" akun lewat is_active, user itu tetap bisa
        // login selama account_status masih 'verified'. 'suspended' di sini
        // menggantikan is_active=false dan otomatis ikut tercek di alur
        // login yang sudah ada (hanya 'verified' yang boleh masuk).
        $table->string('account_status', 50)->default('pending');
        $table->uuid('registered_by')->nullable();
        $table->softDeletes();
        $table->timestamps();

        $table->foreign('registered_by')->references('id_user')->on('users')->nullOnDelete();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            $table->foreign('user_id')->references('id_user')->on('users')->nullOnDelete();
        });

        // Kunci nilai role & account_status ke daftar yang benar-benar
        // dipakai aplikasi (enum UserRole, alur verifikasi akun), biar
        // tidak ada "magic string" nyasar dari luar Eloquent.
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_users_role CHECK (role IN ('pengguna', 'petugas', 'admin'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_users_account_status CHECK (account_status IN ('pending', 'verified', 'rejected', 'suspended'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
