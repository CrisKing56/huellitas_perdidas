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
        Schema::table('publicaciones_adopcion', function (Blueprint $table) {
            $table->string('otra_raza', 80)->nullable()->after('raza_id');
            $table->string('color_predominante', 120)->nullable()->after('tamano');
            $table->text('vacunas_aplicadas')->nullable()->after('color_predominante');
            $table->boolean('esterilizado')->nullable()->after('vacunas_aplicadas');
            $table->string('condicion_salud', 120)->nullable()->after('esterilizado');
            $table->text('descripcion_salud')->nullable()->after('condicion_salud');
            $table->text('requisitos')->nullable()->after('descripcion_salud');
            $table->string('colonia_barrio', 120)->nullable()->after('requisitos');
            $table->string('calle_referencias', 255)->nullable()->after('colonia_barrio');
            $table->decimal('latitud', 10, 7)->nullable()->after('calle_referencias');
            $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publicaciones_adopcion', function (Blueprint $table) {
            $table->dropColumn([
                'otra_raza',
                'color_predominante',
                'vacunas_aplicadas',
                'esterilizado',
                'condicion_salud',
                'descripcion_salud',
                'requisitos',
                'colonia_barrio',
                'calle_referencias',
                'latitud',
                'longitud',
            ]);
        });
    }
};