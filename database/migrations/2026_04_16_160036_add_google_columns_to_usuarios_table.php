<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('usuarios')) {
            return;
        }

        $hasGoogleId = Schema::hasColumn('usuarios', 'google_id');
        $hasAuthProvider = Schema::hasColumn('usuarios', 'auth_provider');
        $hasGoogleAvatar = Schema::hasColumn('usuarios', 'google_avatar');
        $hasEmailVerifiedAt = Schema::hasColumn('usuarios', 'email_verified_at');
        $hasRememberToken = Schema::hasColumn('usuarios', 'remember_token');

        if (
            !$hasGoogleId ||
            !$hasAuthProvider ||
            !$hasGoogleAvatar ||
            !$hasEmailVerifiedAt ||
            !$hasRememberToken
        ) {
            Schema::table('usuarios', function (Blueprint $table) use (
                $hasGoogleId,
                $hasAuthProvider,
                $hasGoogleAvatar,
                $hasEmailVerifiedAt,
                $hasRememberToken
            ) {
                if (!$hasGoogleId) {
                    $table->string('google_id', 191)->nullable()->unique();
                }

                if (!$hasAuthProvider) {
                    $table->enum('auth_provider', ['LOCAL', 'GOOGLE'])->default('LOCAL');
                }

                if (!$hasGoogleAvatar) {
                    $table->string('google_avatar', 255)->nullable();
                }

                if (!$hasEmailVerifiedAt) {
                    $table->timestamp('email_verified_at')->nullable();
                }

                if (!$hasRememberToken) {
                    $table->string('remember_token', 100)->nullable();
                }
            });
        }

        // Estos cambios los hiciste manualmente en Workbench.
        // Los dejamos también en migración para que el equipo los reciba.
        try {
            DB::statement("ALTER TABLE usuarios MODIFY telefono CHAR(10) NULL");
        } catch (\Throwable $e) {
            // Ya estaba así o no aplica
        }

        try {
            DB::statement("ALTER TABLE usuarios MODIFY password_hash VARCHAR(255) NULL");
        } catch (\Throwable $e) {
            // Ya estaba así o no aplica
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('usuarios')) {
            return;
        }

        // Lo dejamos vacío para no romper rollbacks
        // porque varias columnas pudieron haber sido agregadas manualmente.
    }
};