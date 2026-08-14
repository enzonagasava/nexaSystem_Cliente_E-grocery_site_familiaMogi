<?php

namespace Database\Seeders;

use App\Models\EGroceryImage;
use App\Models\EGroceryAd;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EGrocerySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR'); // para dados em português

        // ------------------------------------------------------------
        // 1. Lista de produtos típicos de hortifrúti
        // ------------------------------------------------------------
        $productsData = [
            [
                'name' => 'Shimeji Branco',
                'category' => 'Cogumelos',
                'images' => [
                    'shimeji-branco-1.jpg',
                    'shimeji-branco-2.jpg',
                ],
            ],
            [
                'name' => 'Shimeji Marrom',
                'category' => 'Cogumelos',
                'images' => [
                    'shimeji-marrom.jpg',
                ],
            ],
            [
                'name' => 'Brócolis',
                'category' => 'Verduras',
                'images' => [
                    'brocolis-1.jpg',
                    'brocolis-2.jpg',
                ],
            ],
            [
                'name' => 'Alface Crespa',
                'category' => 'Verduras',
                'images' => [
                    'alface-crespa-1.jpg',
                    'alface-crespa-2.jpg',
                    'alface-crespa-3.jpg',
                ],
            ],
            [
                'name' => 'Alface Crespa Roxo',
                'category' => 'Verduras',
                'images' => [
                    'alface-crespa-roxo-1.jpg',
                ],
            ],
            [
                'name' => 'Couve Flor',
                'category' => 'Verduras',
                'images' => [
                    'couve-flor-1.jpg',
                ],
            ],
            [
                'name' => 'Couve',
                'category' => 'Verduras',
                'images' => [
                    'couve-1.jpg',
                ],
            ],
            [
                'name' => 'Alho Poró',
                'category' => 'Verduras',
                'images' => [
                    'alho-poro.jpg',
                ],
            ],
            [
                'name' => 'Cebolinha',
                'category' => 'Verduras',
                'images' => [
                    'cebolinha.jpg',
                ],
            ],
            [
                'name' => 'Rabanete',
                'category' => 'Legumes',
                'images' => [
                    'rabanete-1.jpg',
                    'rabanete-2.jpg'
                ],
            ],
            [
                'name' => 'Espinafre',
                'category' => 'Verduras',
                'images' => [
                    'espinafre-1.jpg',
                ],
            ],
            [
                'name' => 'Hortelã',
                'category' => 'Ervas',
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
                'description'       => 'Cultivado com cuidado desde o plantio até a colheita, este produto é selecionado para garantir frescor, qualidade e sabor. Trabalhamos com uma produção cuidadosa para levar à sua mesa produtos frescos e de qualidade.',
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
