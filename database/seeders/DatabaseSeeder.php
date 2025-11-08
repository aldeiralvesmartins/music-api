<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ---- CATEGORIAS ----
        $categories = [
            ['id' => Str::random(24), 'name' => 'Armas de Madeira', 'slug' => 'armas-madeira', 'description' => 'Armas tradicionais feitas em madeira de alta qualidade', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Armas Brancas', 'slug' => 'armas-brancas', 'description' => 'Lâminas artesanais para colecionadores e praticantes', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Armas Orientais', 'slug' => 'armas-orientais', 'description' => 'Armas tradicionais das artes marciais orientais', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }

        // ---- PRODUTOS ----
        $products = [
            'bastao_carvalho' => [
                'id' => Str::random(24),
                'name' => 'Bastão de Carvalho Maciço',
                'description' => 'Bastão tradicional de carvalho envelhecido, perfeito para treinos de defesa pessoal e artes marciais. Cada unidade é selecionada entre as melhores madeiras para garantir durabilidade e equilíbrio excepcionais.',
                'price' => 89.90,
                'image' => 'https://rpg.charlescorrea.com.br/wp-content/uploads/2021/12/Bo.jpg',
                'category_id' => $categories[0]['id'],
                'stock' => 25,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            'bastao_monge' => [
                'id' => Str::random(24),
                'name' => 'Bastão do Monge - Era Perdida',
                'description' => 'Inspirado nas tradições monásticas, este bastão combina simplicidade e eficácia. Ideal para praticantes que buscam conexão espiritual através das artes marciais, oferecendo controle preciso em cada movimento.',
                'price' => 120.00,
                'image' => 'https://eraperdida.weebly.com/uploads/2/7/1/1/27112571/3306938_orig.jpg',
                'category_id' => $categories[0]['id'],
                'stock' => 15,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            'nunchaku' => [
                'id' => Str::random(24),
                'name' => 'Nunchaku Profissional',
                'description' => 'Nunchaku de alta performance com correntes de aço e hastes de carvalho. Desenvolvido para treinos avançados, oferece velocidade e fluidez nos movimentos, sendo essencial para dominar esta arte milenar.',
                'price' => 75.50,
                'image' => 'https://rpg.charlescorrea.com.br/wp-content/uploads/2021/11/Nunchaku.jpg',
                'category_id' => $categories[0]['id'],
                'stock' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            'faca_artesanal' => [
                'id' => Str::random(24),
                'name' => 'Faca Artesanal para Churrasco',
                'description' => 'Mais que uma faca, uma obra de arte em aço inox. Lâmina temperada e afiada manualmente, perfeita para cortes precisos. O cabo ergonômico proporciona segurança e conforto durante o uso prolongado.',
                'price' => 150.00,
                'image' => 'https://images.tcdn.com.br/img/img_prod/1267948/copia_faca_artesanal_churrasco_aco_inox_rustica_2mm_10_polegadas_personalizada_33_1_f86006f01b08b165781385ae87da4352.jpg',
                'category_id' => $categories[1]['id'],
                'stock' => 40,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            'faca_gaucha' => [
                'id' => Str::random(24),
                'name' => 'Faca Gaúcha Tradicional',
                'description' => 'Símbolo da cultura gaúcha, esta faca combina tradição e funcionalidade. Lâmina robusta com acabamento em couro legítimo, ideal para atividades campestres e colecionadores que valorizam o artesanal.',
                'price' => 180.00,
                'image' => 'https://cdn.awsli.com.br/300x300/2539/2539754/produto/271955131/1-ld3zvwa87d.png',
                'category_id' => $categories[1]['id'],
                'stock' => 20,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            'katana_samurai' => [
                'id' => Str::random(24),
                'name' => 'Katana Samurai Artesanal',
                'description' => 'Katana forjada com aço carbono de alta resistência, seguindo as tradições dos mestres espadeiros japoneses. Lâmina full tang com tempera diferenciada, bainha em couro legítimo e detalhes em bronze.',
                'price' => 450.00,
                'image' => 'https://a-static.mlcdn.com.br/420x420/espada-estilo-katana-samurai-artesanal-18-adaga-carbono-bainha-couro-full-tang-brut-forge-facas-zanline/clizashop/125espp/b8ea892ad46c40f4a32212ee039d1d3f.jpeg',
                'category_id' => $categories[2]['id'],
                'stock' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            'jutte' => [
                'id' => Str::random(24),
                'name' => 'Jutte - Arma de Defesa Samurai',
                'description' => 'Arma tradicional japonesa utilizada por oficiais durante o período Edo. Projetada para defesa e imobilização, esta Jutte é perfeita para colecionadores e praticantes de kobudo que buscam autenticidade histórica.',
                'price' => 95.00,
                'image' => 'https://rpg.charlescorrea.com.br/wp-content/uploads/2021/12/Jutte-weapon.jpg',
                'category_id' => $categories[2]['id'],
                'stock' => 18,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $key => $product) {
            DB::table('products')->insert($product);

            $images = [
                'bastao_carvalho' => [
                    'https://rpg.charlescorrea.com.br/wp-content/uploads/2021/12/Bo.jpg',
                    'https://static.veracaampos.com.br/public/veracaampos/imagens/produtos/bastao-de-alongamento-em-madeira-2420.jpg',
                ],
                'bastao_monge' => [
                    'https://eraperdida.weebly.com/uploads/2/7/1/1/27112571/3306938_orig.jpg'],
                'nunchaku' => [
                    'https://rpg.charlescorrea.com.br/wp-content/uploads/2021/11/Nunchaku.jpg',
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSDKc0pS-0doKmOs65G6AnISefXw9vY-Tco3w&s',
                ],
                'faca_artesanal' => [
                    'https://images.tcdn.com.br/img/img_prod/1267948/copia_faca_artesanal_churrasco_aco_inox_rustica_2mm_10_polegadas_personalizada_33_1_f86006f01b08b165781385ae87da4352.jpg'],
                'faca_gaucha' => [
                    'https://cdn.awsli.com.br/300x300/2539/2539754/produto/271955131/1-ld3zvwa87d.png'],
                'katana_samurai' => [
                    'https://cdn.awsli.com.br/600x1000/2515/2515067/produto/2342375710a51647aa8.jpg',
                    'https://a-static.mlcdn.com.br/420x420/espada-estilo-katana-samurai-artesanal-18-adaga-carbono-bainha-couro-full-tang-brut-forge-facas-zanline/clizashop/125espp/b8ea892ad46c40f4a32212ee039d1d3f.jpeg'],
                'jutte' => [
                    'https://rpg.charlescorrea.com.br/wp-content/uploads/2021/12/Jutte-weapon.jpg'],
            ];

            foreach ($images[$key] as $url) {
                Image::create([
                    'url' => $url,
                    'imageable_type' => 'App\Models\Product',
                    'imageable_id' => $product['id'],
                ]);
            }
        }

        // ---- ADMIN ----
        DB::table('users')->insert([
            'id' => Str::random(24),
            'name' => 'Administrador',
            'email' => 'deirnogrc7@gmail.com',
            'password' => Hash::make('12345678'),
            'type' => 'admin',
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ---- LAYOUT SECTIONS ----
        $layoutSections = [
            [
                'name' => 'banner_principal',
                'title' => 'Coleção Samurai – Força e Tradição',
                'type' => 'banner',
                'content' => json_encode([
                    'images' => [
                        '/storage/banners/banner-samurai1.jpg',
                        '/storage/banners/banner-samurai2.jpg',
                        '/storage/banners/banner-samurai3.jpg'
                    ],
                    'link' => '/produtos',
                    'autoplay' => true,
                    'interval' => 4000
                ]),
                'position' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'categorias_destaque',
                'title' => 'Navegue por Categorias',
                'type' => 'categorias',
                'content' => json_encode([
                    'display' => 'grid',
                    'columns' => 3,
                    'style' => 'rounded',
                    'categories' => array_column($categories, 'id')
                ]),
                'position' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'produtos_mais_vendidos',
                'title' => 'Mais Vendidos',
                'type' => 'produtos',
                'content' => json_encode([
                    'limit' => 6,
                    'sort' => 'mais_vendidos',
                    'show_price' => true,
                    'show_button' => true
                ]),
                'position' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'texto_promocional',
                'title' => 'Qualidade e História em Cada Lâmina',
                'type' => 'texto',
                'content' => json_encode([
                    'text' => 'Cada peça do nosso acervo carrega a tradição dos mestres ferreiros e o espírito das artes marciais. Descubra a união entre força, equilíbrio e estética em produtos feitos para durar.',
                    'align' => 'center',
                    'background' => '#fdf6ec',
                    'color' => '#222',
                ]),
                'position' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($layoutSections as $section) {
            DB::table('layout_sections')->insert($section);
        }
    }
}
