<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // =========================
        // CATEGORÍAS DE CONSEJO
        // =========================
        $categorias = [
            'Salud',
            'Higiene',
            'Alimentación',
            'Entrenamiento',
            'Prevención',
            'Primeros auxilios',
            'Adopción responsable',
            'Bienestar',
        ];

        foreach ($categorias as $categoria) {
            $existe = DB::table('categorias_consejo')
                ->where('nombre', $categoria)
                ->exists();

            if (!$existe) {
                DB::table('categorias_consejo')->insert([
                    'nombre' => $categoria,
                ]);
            }
        }

        // =========================
        // ETIQUETAS DE CONSEJO
        // =========================
        $etiquetas = [
            'Alimentación',
            'Medicamentos',
            'Vacunas',
            'Desparasitación',
            'Entrenamiento',
            'Comportamiento',
            'Higiene',
            'Emergencias',
            'Primeros auxilios',
            'Cachorros',
            'Gatitos',
            'Perros',
            'Gatos',
            'Adopción',
            'Salud preventiva',
        ];

        foreach ($etiquetas as $etiqueta) {
            $existe = DB::table('etiquetas')
                ->where('nombre', $etiqueta)
                ->exists();

            if (!$existe) {
                DB::table('etiquetas')->insert([
                    'nombre' => $etiqueta,
                    'activo' => 1,
                ]);
            }
        }
    }

    public function down(): void
    {
        $categorias = [
            'Salud',
            'Higiene',
            'Alimentación',
            'Entrenamiento',
            'Prevención',
            'Primeros auxilios',
            'Adopción responsable',
            'Bienestar',
        ];

        DB::table('categorias_consejo')
            ->whereIn('nombre', $categorias)
            ->delete();

        $etiquetas = [
            'Alimentación',
            'Medicamentos',
            'Vacunas',
            'Desparasitación',
            'Entrenamiento',
            'Comportamiento',
            'Higiene',
            'Emergencias',
            'Primeros auxilios',
            'Cachorros',
            'Gatitos',
            'Perros',
            'Gatos',
            'Adopción',
            'Salud preventiva',
        ];

        DB::table('etiquetas')
            ->whereIn('nombre', $etiquetas)
            ->delete();
    }
};