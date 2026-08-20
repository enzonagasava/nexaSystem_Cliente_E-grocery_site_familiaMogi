<?php

namespace Database\Seeders;

use App\Models\EGroceryImage;
use App\Models\EGroceryAd;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EGrocerySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR'); // para dados em português

        User::create([
            'name' => 'enzo',
            'email' => 'enzonagasava@gmail.com',
            'telefone' => '5511941560613',
            'endereco' => [
                'cep' => '08751-605',
                'logradouro' => 'Estrada Missao Kinoshita',
                'numero' => '15',
                'complemento' => '',
                'bairro' => 'Quatinga',
                'cidade' => 'Mogi das Cruzes',
                'estado' => 'SP',
                'pais' => 'Brasil',
            ],
            'password' => 'admin',
            'email_verified_at' => now(),
        ]);
        // ------------------------------------------------------------
        // 1. Lista de produtos típicos de hortifrúti
        // ------------------------------------------------------------
        $productsData = [
            [
                'name' => 'Shimeji Branco',
                'category' => 'Cogumelos',
                'description' => 'Cultivado com cuidado para garantir textura macia, sabor delicado e frescor. Ideal para refogados, risotos, massas e diversos pratos do dia a dia.',
                'images' => [
                    'shimeji-branco-1.jpg',
                    'shimeji-branco-2.jpg',
                ],
            ],

            [
                'name' => 'Shimeji Marrom',
                'category' => 'Cogumelos',
                'description' => 'Fresco e selecionado, possui sabor marcante e textura agradável. Uma excelente opção para refogados, massas, risotos e acompanhamentos.',
                'images' => [
                    'shimeji-marrom.jpg',
                ],
            ],

            [
                'name' => 'Brócolis',
                'category' => 'Verduras',
                'description' => 'Selecionado para chegar fresco à sua mesa, com textura firme e sabor característico. Perfeito para saladas, refogados, acompanhamentos e receitas variadas.',
                'images' => [
                    'brocolis-1.jpg',
                    'brocolis-2.jpg',
                ],
            ],

            [
                'name' => 'Alface Crespa',
                'category' => 'Verduras',
                'description' => 'Folhas frescas, crocantes e cuidadosamente selecionadas. Ideal para saladas, sanduíches e acompanhamentos leves e saborosos.',
                'images' => [
                    'alface-crespa-1.jpg',
                    'alface-crespa-2.jpg',
                    'alface-crespa-3.jpg',
                ],
            ],

            [
                'name' => 'Alface Crespa Roxo',
                'category' => 'Verduras',
                'description' => 'Folhas frescas e crocantes, com coloração intensa e sabor suave. Excelente para deixar saladas mais variadas, coloridas e apetitosas.',
                'images' => [
                    'alface-crespa-roxo-1.jpg',
                ],
            ],

            [
                'name' => 'Couve Flor',
                'category' => 'Verduras',
                'description' => 'Selecionada pela qualidade e frescor, apresenta textura firme e sabor suave. Versátil para cozinhar, gratinar, refogar ou preparar diferentes receitas.',
                'images' => [
                    'couve-flor-1.jpg',
                ],
            ],

            [
                'name' => 'Couve',
                'category' => 'Verduras',
                'description' => 'Folhas frescas e bem selecionadas, com textura firme e sabor característico. Ótima para refogados, caldos, sopas, acompanhamentos e sucos.',
                'images' => [
                    'couve-1.jpg',
                ],
            ],

            [
                'name' => 'Alho Poró',
                'category' => 'Verduras',
                'description' => 'Fresco e aromático, possui sabor suave e levemente adocicado. Ideal para sopas, caldos, refogados, tortas, risotos e diversas preparações.',
                'images' => [
                    'alho-poro.jpg',
                ],
            ],

            [
                'name' => 'Cebolinha',
                'category' => 'Verduras',
                'description' => 'Fresca e aromática, é perfeita para dar mais sabor e frescor às refeições. Pode ser utilizada em saladas, molhos, sopas, carnes e acompanhamentos.',
                'images' => [
                    'cebolinha.jpg',
                ],
            ],

            [
                'name' => 'Rabanete',
                'category' => 'Legumes',
                'description' => 'Fresco, crocante e com sabor levemente picante. Uma ótima opção para saladas, conservas e acompanhamentos, trazendo sabor e textura às refeições.',
                'images' => [
                    'rabanete-1.jpg',
                    'rabanete-2.jpg',
                ],
            ],

            [
                'name' => 'Espinafre',
                'category' => 'Verduras',
                'description' => 'Folhas frescas e selecionadas, com sabor suave e textura delicada. Versátil para refogados, omeletes, massas, tortas, sopas e outras receitas.',
                'images' => [
                    'espinafre-1.jpg',
                ],
            ],

            [
                'name' => 'Hortelã',
                'category' => 'Ervas',
                'description' => 'Fresca e aromática, possui sabor refrescante e marcante. Ideal para chás, sucos, molhos, saladas, sobremesas e diversas preparações.',
                'images' => [
                    'hortela-1.jpg',
                ],
            ],
        ];



        $imageDirectory = public_path('images/produtos');

        foreach ($productsData as $productData) {

            /*
             * --------------------------------------------------------
             * Produto
             * --------------------------------------------------------
             */

            $product = EGroceryAd::create([
                'external_ad_id'    => 'ad_' . Str::uuid(),
                'title'             => $productData['name'],
                'category'          => $productData['category'],
                'description'       => $productData['description'],
                'status'            => 'active',
                'priority'          => $faker->numberBetween(1, 5),
                'starts_at'         => Carbon::now()->subDays(
                    rand(0, 5)
                ),
                'ends_at'           => Carbon::now()->addDays(
                    rand(5, 15)
                ),
                'source_updated_at' => Carbon::now(),
                'payload'           => [
                    'banner' => $faker->imageUrl(
                        800,
                        200,
                        'ad',
                        true
                    ),
                ],
            ]);

            /*
             * --------------------------------------------------------
             * Imagens do produto
             * --------------------------------------------------------
             */

            foreach ($productData['images'] as $position => $filename) {

                $filePath = $imageDirectory . '/' . $filename;

                if (! File::exists($filePath)) {
                    $this->command->warn(
                        "Imagem não encontrada: {$filePath}"
                    );

                    continue;
                }

                $externalImageId = 'img_' . Str::uuid();

                EGroceryImage::create([
                    'product_id'        => $product->id,
                    'external_image_id' => $externalImageId,

                    'storage_key'       => 'images/produtos/' . $filename,

                    'url'               => asset(
                        'images/produtos/' . $filename
                    ),

                    'mime_type'        => File::mimeType($filePath),

                    'width'            => null,
                    'height'           => null,

                    'checksum'         => md5_file($filePath),

                    'source_updated_at' => Carbon::now(),

                    'payload' => [
                        'source' => 'local',
                        'alt' => $productData['name'],
                        'position' => $position,
                    ],
                ]);
            }
        }
    }
}
