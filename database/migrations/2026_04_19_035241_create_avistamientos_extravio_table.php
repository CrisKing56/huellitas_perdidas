<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avistamientos_extravio', function (Blueprint $table) {
            $table->bigIncrements('id_avistamiento');

            $table->unsignedBigInteger('publicacion_id');
            $table->unsignedBigInteger('usuario_reportante_id')->nullable();
            $table->unsignedBigInteger('ubicacion_id')->nullable();

            $table->string('nombre_contacto', 120)->nullable();
            $table->string('telefono_contacto', 20)->nullable();

            $table->date('fecha_avistamiento')->nullable();
            $table->string('colonia_barrio', 120)->nullable();
            $table->string('calle_referencias', 200)->nullable();

            $table->text('descripcion');
            $table->string('foto_url', 255)->nullable();

            $table->enum('estado', ['ENVIADO', 'VISTO', 'DESCARTADO'])->default('ENVIADO');

            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('visto_en')->nullable();

            $table->foreign('publicacion_id')
                ->references('id_publicacion')
                ->on('publicaciones_extravio')
                ->cascadeOnDelete();

            $table->foreign('usuario_reportante_id')
                ->references('id_usuario')
                ->on('usuarios')
                ->nullOnDelete();

            $table->foreign('ubicacion_id')
                ->references('id_ubicacion')
                ->on('ubicaciones')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avistamientos_extravio');
    }
};