<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Agregar facebook_id si no existe
        if (!Schema::hasColumn('usuarios', 'facebook_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('facebook_id', 255)->nullable()->after('google_id');
            });

            Schema::table('usuarios', function (Blueprint $table) {
                $table->unique('facebook_id', 'usuarios_facebook_id_unique');
            });
        }

        // 2) Normalizar valores nulos o en minúsculas antes de cambiar el tipo
        DB::table('usuarios')
            ->whereNull('auth_provider')
            ->update(['auth_provider' => 'LOCAL']);

        DB::statement("
            UPDATE usuarios
            SET auth_provider = UPPER(auth_provider)
        ");

        // 3) Cambiar auth_provider a VARCHAR(50) para permitir FACEBOOK
        DB::statement("
            ALTER TABLE usuarios
            MODIFY auth_provider VARCHAR(50) NOT NULL DEFAULT 'LOCAL'
        ");
    }

    public function down(): void
    {
        // Si hubiera valores FACEBOOK, al volver atrás los convertimos a LOCAL
        DB::statement("
            UPDATE usuarios
            SET auth_provider = 'LOCAL'
            WHERE auth_provider NOT IN ('LOCAL', 'GOOGLE')
               OR auth_provider IS NULL
        ");

        // Regresar auth_provider al enum original
        DB::statement("
            ALTER TABLE usuarios
            MODIFY auth_provider ENUM('LOCAL','GOOGLE') NOT NULL DEFAULT 'LOCAL'
        ");

        // Eliminar unique y columna facebook_id si existe
        if (Schema::hasColumn('usuarios', 'facebook_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                try {
                    $table->dropUnique('usuarios_facebook_id_unique');
                } catch (\Throwable $e) {
                    // evitar fallo si el índice ya no existe
                }
            });

            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('facebook_id');
            });
        }
    }
};