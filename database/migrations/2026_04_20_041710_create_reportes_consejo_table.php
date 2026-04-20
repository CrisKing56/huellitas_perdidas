<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_consejo', function (Blueprint $table) {
            $table->bigIncrements('id_reporte');
            $table->unsignedBigInteger('consejo_id');
            $table->unsignedBigInteger('usuario_reporta_id');
            $table->string('motivo', 80);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['ABIERTO', 'EN_REVISION', 'RESUELTO', 'DESCARTADO'])->default('ABIERTO');
            $table->unsignedBigInteger('revisado_por')->nullable();
            $table->timestamp('revisado_en')->nullable();
            $table->string('accion_tomada', 120)->nullable();
            $table->string('motivo_resolucion', 255)->nullable();
            $table->timestamp('creado_en')->useCurrent();

            $table->foreign('consejo_id')
                ->references('id_consejo')
                ->on('consejos')
                ->onDelete('cascade');

            $table->foreign('usuario_reporta_id')
                ->references('id_usuario')
                ->on('usuarios')
                ->onDelete('cascade');

            $table->foreign('revisado_por')
                ->references('id_usuario')
                ->on('usuarios')
                ->nullOnDelete();

            $table->index(['consejo_id', 'estado']);
            $table->index(['usuario_reporta_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_consejo');
    }
};