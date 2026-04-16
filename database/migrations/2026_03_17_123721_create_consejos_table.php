<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si la tabla ya existe, no intentamos crearla otra vez
        if (Schema::hasTable('consejos')) {
            return;
        }

        Schema::create('consejos', function (Blueprint $table) {
            $table->id('id_consejo');
            $table->unsignedBigInteger('id_usuario'); // Para saber quién lo publicó

            $table->string('titulo', 100);
            $table->string('resumen', 200);
            $table->string('categoria');
            $table->string('especie');
            $table->string('etiquetas')->nullable(); // Es opcional según tu diseño
            $table->text('descripcion');

            // Guardaremos las rutas de las imágenes en formato JSON
            $table->json('imagenes')->nullable();

            // Para diferenciar entre 'Borrador' y 'Publicado'
            $table->string('estado')->default('publicado');

            $table->timestamps();

            // Si luego quieres enlazar con usuarios, descomenta esto
            // $table->foreign('id_usuario')
            //     ->references('id_usuario')
            //     ->on('usuarios')
            //     ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('consejos')) {
            Schema::dropIfExists('consejos');
        }
    }
};