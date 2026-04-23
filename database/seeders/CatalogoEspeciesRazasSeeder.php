<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoEspeciesRazasSeeder extends Seeder
{
    public function run(): void
    {
        $especies = [
            'Perro',
            'Gato',
            'Conejo',
            'Cuy',
            'Hámster',
            'Hurón',
            'Chinchilla',
            'Tortuga',
            'Ave',
            'Pez',
            'Erizo',
            'Ratón',
            'Jerbo',
        ];

        foreach ($especies as $nombreEspecie) {
            DB::table('especies')->updateOrInsert(
                ['nombre' => $nombreEspecie],
                ['activo' => 1]
            );
        }

        $mapaEspecies = DB::table('especies')
            ->pluck('id_especie', 'nombre')
            ->toArray();

        $razasPorEspecie = [
            'Perro' => [
                'Labrador Retriever',
                'Golden Retriever',
                'Pastor Alemán',
                'Chihuahua',
                'Poodle',
                'Pitbull',
                'Husky Siberiano',
                'Pug',
                'Bulldog Francés',
                'Bulldog Inglés',
                'Rottweiler',
                'Doberman',
                'Boxer',
                'Beagle',
                'Shih Tzu',
                'Yorkshire Terrier',
                'Border Collie',
                'Cocker Spaniel',
                'Dálmata',
                'Mestizo',
            ],
            'Gato' => [
                'Siamés',
                'Persa',
                'Maine Coon',
                'Angora',
                'Bengalí',
                'Azul Ruso',
                'British Shorthair',
                'Sphynx',
                'Ragdoll',
                'Bosque de Noruega',
                'Mestizo',
            ],
            'Conejo' => [
                'Belier',
                'Cabeza de León',
                'Mini Rex',
                'Holandés',
                'Californiano',
                'Angora',
                'Arlequín',
                'Mini Lop',
                'Mestizo',
            ],
            'Cuy' => [
                'Americano',
                'Abisinio',
                'Peruano',
                'Sheltie',
                'Teddy',
                'Coronet',
                'Mestizo',
            ],
            'Hámster' => [
                'Sirio',
                'Ruso Campbell',
                'Ruso Enano',
                'Roborowski',
                'Chino',
            ],
            'Hurón' => [
                'Doméstico',
            ],
            'Chinchilla' => [
                'Estándar',
                'Velvet',
                'Beige',
            ],
            'Tortuga' => [
                'Orejas Rojas',
                'Rusa',
                'Griega',
                'Caja',
            ],
            'Ave' => [
                'Canario',
                'Periquito Australiano',
                'Ninfa',
                'Agapornis',
                'Cacatúa',
                'Loro Amazona',
                'Perico Monje',
                'Diamante Mandarín',
                'Mestiza',
            ],
            'Pez' => [
                'Betta',
                'Goldfish',
                'Guppy',
                'Molly',
                'Platy',
                'Tetra Neón',
                'Cebra',
                'Koi',
            ],
            'Erizo' => [
                'Africano',
            ],
            'Ratón' => [
                'Fancy',
            ],
            'Jerbo' => [
                'Mongol',
            ],
        ];

        foreach ($razasPorEspecie as $nombreEspecie => $razas) {
            if (!isset($mapaEspecies[$nombreEspecie])) {
                continue;
            }

            $idEspecie = $mapaEspecies[$nombreEspecie];

            foreach ($razas as $nombreRaza) {
                DB::table('razas')->updateOrInsert(
                    [
                        'especie_id' => $idEspecie,
                        'nombre' => $nombreRaza,
                    ],
                    []
                );
            }
        }
    }
}