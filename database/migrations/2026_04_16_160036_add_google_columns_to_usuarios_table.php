<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleColumnsToUsuariosTable extends Migration
{
    public function up()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Añadimos las nuevas columnas para Google
            $table->string('google_id')->nullable()->unique();  // Para almacenar el ID de Google
            $table->string('google_avatar')->nullable();       // Para almacenar la URL de la foto de Google
            $table->string('auth_provider')->nullable();       // Para almacenar el proveedor de autenticación (Google)
        });
    }

    public function down()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Eliminar las columnas si revertimos la migración
            $table->dropColumn(['google_id', 'google_avatar', 'auth_provider']);
        });
    }
}