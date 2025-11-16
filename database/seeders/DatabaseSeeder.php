<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Image;
use App\Models\LayoutSection;
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
        $this->call(CompanySeeder::class);
        $companyId = app('company_id');
        // ---- CATEGORIAS ----
        $categories = [
            ['id' => Str::random(24), 'name' => 'Armas de Madeira', 'slug' => 'armas-madeira', 'description' => 'Armas tradicionais feitas em madeira de alta qualidade', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Armas Brancas', 'slug' => 'armas-brancas', 'description' => 'Lâminas artesanais para colecionadores e praticantes', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Armas Orientais', 'slug' => 'armas-orientais', 'description' => 'Armas tradicionais das artes marciais orientais', 'is_active' => true, 'company_id' => $companyId],
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
                'category_id' => $categories[0]['id'],
                'stock' => 25,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'company_id' => $companyId,
            ],
            'bastao_monge' => [
                'id' => Str::random(24),
                'name' => 'Bastão do Monge - Era Perdida',
                'description' => 'Inspirado nas tradições monásticas, este bastão combina simplicidade e eficácia. Ideal para praticantes que buscam conexão espiritual através das artes marciais, oferecendo controle preciso em cada movimento.',
                'price' => 120.00,
                'category_id' => $categories[0]['id'],
                'stock' => 15,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'company_id' => $companyId,
            ],
            'nunchaku' => [
                'id' => Str::random(24),
                'name' => 'Nunchaku Profissional',
                'description' => 'Nunchaku de alta performance com correntes de aço e hastes de carvalho. Desenvolvido para treinos avançados, oferece velocidade e fluidez nos movimentos, sendo essencial para dominar esta arte milenar.',
                'price' => 75.50,
                'category_id' => $categories[0]['id'],
                'stock' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'company_id' => $companyId,
            ],
            'faca_artesanal' => [
                'id' => Str::random(24),
                'name' => 'Faca Artesanal para Churrasco',
                'description' => 'Mais que uma faca, uma obra de arte em aço inox. Lâmina temperada e afiada manualmente, perfeita para cortes precisos. O cabo ergonômico proporciona segurança e conforto durante o uso prolongado.',
                'price' => 150.00,
                'category_id' => $categories[1]['id'],
                'stock' => 40,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'company_id' => $companyId,
            ],
            'faca_gaucha' => [
                'id' => Str::random(24),
                'name' => 'Faca Gaúcha Tradicional',
                'description' => 'Símbolo da cultura gaúcha, esta faca combina tradição e funcionalidade. Lâmina robusta com acabamento em couro legítimo, ideal para atividades campestres e colecionadores que valorizam o artesanal.',
                'price' => 180.00,
                'category_id' => $categories[1]['id'],
                'stock' => 20,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'company_id' => $companyId,
            ],
            'katana_samurai' => [
                'id' => Str::random(24),
                'name' => 'Katana Samurai Artesanal',
                'description' => 'Katana forjada com aço carbono de alta resistência, seguindo as tradições dos mestres espadeiros japoneses. Lâmina full tang com tempera diferenciada, bainha em couro legítimo e detalhes em bronze.',
                'price' => 450.00,
                'category_id' => $categories[2]['id'],
                'stock' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'company_id' => $companyId,
            ],
            'jutte' => [
                'id' => Str::random(24),
                'name' => 'Jutte - Arma de Defesa Samurai',
                'description' => 'Arma tradicional japonesa utilizada por oficiais durante o período Edo. Projetada para defesa e imobilização, esta Jutte é perfeita para colecionadores e praticantes de kobudo que buscam autenticidade histórica.',
                'price' => 95.00,
                'category_id' => $categories[2]['id'],
                'stock' => 18,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'company_id' => $companyId,
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
            'company_id' => $companyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Busca todos os IDs das categorias existentes no banco
        $categoryIds = Category::pluck('id')->toArray();

        // Define os layouts
        $layoutSections = [
            [
                'id' => 'LAYO_MNlzmnxZqRSpF5Ef16h',
                'name' => 'hero_principal',
                'title' => 'Hero',
                'type' => 'hero',
                'content' => [
                    'badge' => 'Armas 100% Autênticas',
                    'title' => 'Forja Marcial',
                    'title_emphasis' => 'em Cada Arma',
                    'subtitle' => 'Descubra a tradição...',
                    'images' => [
                        'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
                        'https://cjblademaster.com.br/wp-content/uploads/2023/08/d7e1bbc3-018d-4964-bd1d-5752709fcbc2.jpg',
                        'https://media.sketchfab.com/models/d39089f29fbb4b5babc1f1cf0d8ff652/thumbnails/191eef7f7ed148beb877f4ae56095e31/35be297d69d9445ab2d5899654f5d613.jpeg',
                        'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR5-XAe9e-NbZtoNR6csrqjLMfx6kiu7jStpjC9mmAYpJw636l4SPlSQh87g18p9ymUB3w&usqp=CAU'
                    ],
                    'video_url' => 'https://www.youtube.com/watch?v=sNNHlD8b-KM',
                    'ctas' => [
                        ['label' => 'Ver Armas', 'to' => '/#produtos'],
                        ['label' => 'Nossa História', 'action' => 'open_history', 'variant' => 'secondary'],
                    ],
                    'stats' => [
                        ['value' => '25+', 'label' => 'Anos de Tradição'],
                        ['value' => '10k+', 'label' => 'Guerreiros Satisfeitos'],
                        ['value' => '100%', 'label' => 'Autênticas'],
                    ],
                ],
                'position' => 1,
                'is_active' => true,
            ],
            [
                'id' => 'LAYO_mp8Ff63XgRXyHCL9QXG',
                'name' => 'produtos_mais_vendidos',
                'title' => 'Mais Vendidos',
                'type' => 'produtos',
                'content' => [
                    'badge' => 'Nossa Coleção',
                    'title' => 'Armas Marciais',
                    'subtitle' => 'Seleção exclusiva',
                    'show_category_filters' => true,
                    'category_ids' => $categoryIds, // ← dinamicamente do banco
                    'limit' => 8,
                ],
                'position' => 2,
                'is_active' => true,
            ],
            [
                'id' => 'LAYO_S93aKRNkk0B1t530SnM',
                'name' => 'banner_galeria',
                'title' => 'Galeria',
                'type' => 'pictures',
                'content' => [
                    'title' => 'Galeria',
                    'subtitle' => 'Imagens em destaque',
                    'images' => [
                        ['src' => 'https://.../pic1.jpg', 'title' => 'Katana'],
                        ['src' => 'https://.../pic2.jpg', 'caption' => 'Nunchaku'],
                        ['src' => 'https://.../pic3.jpg'],
                        ['src' => 'https://.../pic4.jpg'],
                    ],
                ],
                'position' => 3,
                'is_active' => true,
            ],
            [
                'id' => 'LAYO_QCpJ510jB2QvLoN13Sg',
                'name' => 'cta_principal',
                'title' => 'cta',
                'type' => 'cta',
                'content' => [
                    'title' => 'Pronto para dominar?',
                    'subtitle' => 'Faça seu pedido agora...',
                    'ctas' => [
                        ['label' => 'Criar Conta Grátis', 'to' => '/register'],
                    ],
                    'bullets' => [
                        'Entrega Segura',
                        'Armas Autênticas',
                        'Qualidade Garantida',
                    ],
                ],
                'position' => 4,
                'is_active' => true,
            ],
            [
                'id' => 'LAYO_QCpJ510jB2QvLoe54Sg',
                'name' => 'features',
                'title' => 'features',
                'type' => 'features',
                'content' => [
                    "title" => "Por que escolher a Forja?",
                    "subtitle" => "Compromissos que mantemos em cada pedido",
                    "items" => [
                        ["icon" => "shield", "title" => "Entrega Segura", "description" => "Armas entregues com segurança e discrição."],
                        ["icon" => "check", "title" => "Qualidade Garantida", "description" => "Materiais selecionados e técnicas tradicionais."],
                        ["icon" => "award", "title" => "Forjado com Honra", "description" => "Respeito marcial passado por gerações."]
                    ]
                ],
                'position' => 4,
                'is_active' => true,
            ],
        ];

        // Insere no banco
        foreach ($layoutSections as $section) {
            LayoutSection::create([
                'id' => $section['id'],
                'name' => $section['name'],
                'title' => $section['title'],
                'type' => $section['type'],
                'content' => $section['content'], // converte para JSON válido
                'position' => $section['position'],
                'is_active' => $section['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->call(StoreSettingsSeeder::class);

    }
}
