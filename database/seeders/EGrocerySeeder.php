<?php

namespace Database\Seeders;

use App\Models\EGroceryImage;
use App\Models\EGroceryAd;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class EGrocerySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR'); // para dados em português

        // ------------------------------------------------------------
        // 1. Lista de produtos típicos de hortifrúti
        // ------------------------------------------------------------
        $productsData = [
            ['name' => 'Shitake Branco', 'category' => 'Cogumelos'],
            ['name' => 'Shimeji', 'category' => 'Cogumelos'],
            ['name' => 'Alho', 'category' => 'Legumes'],
            ['name' => 'Batata', 'category' => 'Legumes'],
            ['name' => 'Cenoura', 'category' => 'Legumes'],
            ['name' => 'Beterraba', 'category' => 'Legumes'],
            ['name' => 'Abobrinha', 'category' => 'Legumes'],
            ['name' => 'Pimentão', 'category' => 'Legumes'],
            ['name' => 'Alface', 'category' => 'Verduras'],
            ['name' => 'Rúcula', 'category' => 'Verduras'],
            ['name' => 'Espinafre', 'category' => 'Verduras'],
            ['name' => 'Couve', 'category' => 'Verduras'],
            ['name' => 'Repolho', 'category' => 'Verduras'],
        ];

        // ------------------------------------------------------------
        // 2. Criar imagens e produtos
        // ------------------------------------------------------------
        foreach ($productsData as $product) {
            // Gera um external_image_id único para este produto
            $externalImageId = 'img_' . uniqid();

            // Cria a imagem
            EGroceryImage::create([
                'external_image_id'   => $externalImageId,
                'storage_key'         => 'hortifruti/' . $externalImageId . '.jpg',
                'url'                 => $faker->imageUrl(640, 480, 'food', true, $product['name']),
                'mime_type'           => 'image/jpeg',
                'width'               => 640,
                'height'              => 480,
                'checksum'            => md5($externalImageId . $faker->randomNumber()),
                'source_updated_at'   => Carbon::now(),
                'payload'             => ['source' => 'faker', 'alt' => $product['name']],
            ]);
        }

        // ------------------------------------------------------------
        // 3. Criar anúncios (ads)
        // ------------------------------------------------------------
        $adTitles = [
            'Ofertas da semana em frutas',
            'Desconto em verduras orgânicas',
            'Promoção de legumes frescos',
            'Kit hortifrúti com 30% off',
            'Super preço em batatas e cenouras',
            'Temperos naturais com desconto',
            'Melhores frutas da estação',
            'Liquidação de hortifrúti',
        ];

        foreach ($adTitles as $title) {
            EGroceryAd::create([
                'external_ad_id'      => 'ad_' . uniqid(),
                'title'               => $title,
                'description'         => $faker->sentence(10),
                'status'              => $faker->randomElement(['active', 'inactive']),
                'priority'            => $faker->numberBetween(1, 5),
                'starts_at'           => Carbon::now()->subDays(rand(0, 5)),
                'ends_at'             => Carbon::now()->addDays(rand(5, 15)),
                'source_updated_at'   => Carbon::now(),
                'payload'             => ['banner' => $faker->imageUrl(800, 200, 'ad', true)],
            ]);
        }
    }
}