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
            ['id' => Str::random(24), 'name' => 'Camisetas', 'slug' => 'camisetas', 'description' => 'Camisetas básicas, estampadas e temáticas para todos os estilos', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Calças', 'slug' => 'calcas', 'description' => 'Jeans, sarja, leggings e calças casuais', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Camisas', 'slug' => 'camisas', 'description' => 'Camisas sociais, polo e casuais', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Bermudas', 'slug' => 'bermudas', 'description' => 'Bermudas jeans, sarja e esportivas', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Blusas', 'slug' => 'blusas', 'description' => 'Blusas femininas, regatas e mangas longas', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Vestidos', 'slug' => 'vestidos', 'description' => 'Vestidos curtos, longos e casuais', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Saias', 'slug' => 'saias', 'description' => 'Saias jeans, evasê e midi', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Casacos', 'slug' => 'casacos', 'description' => 'Jaquetas, moletons e blazers', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Tênis', 'slug' => 'tenis', 'description' => 'Tênis casuais, esportivos e lifestyle', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Sapatos', 'slug' => 'sapatos', 'description' => 'Sapatos sociais, mocassim e oxford', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Sandálias', 'slug' => 'sandalias', 'description' => 'Sandálias femininas, rasteiras e plataformas', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Bolsas', 'slug' => 'bolsas', 'description' => 'Bolsas de couro, totô e carteiras', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Acessórios', 'slug' => 'acessorios', 'description' => 'Cintos, óculos, bonés e relógios', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Roupas Íntimas', 'slug' => 'roupas-intimas', 'description' => 'Calcinhas, cuecas e pijamas', 'is_active' => true, 'company_id' => $companyId],
            ['id' => Str::random(24), 'name' => 'Esportivo', 'slug' => 'esportivo', 'description' => 'Roupas fitness e atividades esportivas', 'is_active' => true, 'company_id' => $companyId],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }

        // ---- PRODUTOS ----
        $products = [];

        // Função para gerar produtos em lote
        $generateProducts = function($categoryIndex, $count, $nameTemplate, $priceRange, $descriptionTemplate) use (&$products, $categories, $companyId) {
            for ($i = 1; $i <= $count; $i++) {
                $products[] = [
                    'id' => Str::random(24),
                    'name' => sprintf($nameTemplate, $i),
                    'description' => sprintf($descriptionTemplate, $i),
                    'price' => rand($priceRange[0] * 100, $priceRange[1] * 100) / 100,
                    'category_id' => $categories[$categoryIndex]['id'],
                    'stock' => rand(5, 100),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'company_id' => $companyId,
                ];
            }
        };

        // Camisetas (50 produtos)
        $generateProducts(0, 50, 'Camiseta Básica Cotton %d', [29.90, 79.90],
            'Camiseta 100%% algodão, modelo %d. Tecido macio e respirável, perfeita para o dia a dia. Disponível em diversas cores.');

        $generateProducts(0, 30, 'Camiseta Estampada Urban %d', [49.90, 99.90],
            'Camiseta com estampa exclusiva urban %d. Design moderno e jovial, ideal para compor looks casuais e descolados.');

        // Calças (40 produtos)
        $generateProducts(1, 25, 'Calça Jeans Slim Fit %d', [89.90, 189.90],
            'Calça jeans slim fit modelo %d. Com elastano para melhor conforto e mobilidade. Perfeita para looks casuais.');

        $generateProducts(1, 15, 'Calça Sarja Cargo %d', [79.90, 159.90],
            'Calça sarja cargo modelo %d. Bolsos funcionais e tecido resistente. Ideal para dia a dia e atividades urbanas.');

        // Camisas (35 produtos)
        $generateProducts(2, 20, 'Camisa Social Slim %d', [99.90, 229.90],
            'Camisa social slim fit modelo %d. Tecido de alta qualidade, perfeita para o ambiente corporativo e ocasiões especiais.');

        $generateProducts(2, 15, 'Camisa Polo Premium %d', [79.90, 169.90],
            'Camisa polo premium modelo %d. Acabamento impecável e toque suave. Ideal para looks smart casual.');

        // Bermudas (30 produtos)
        $generateProducts(3, 20, 'Bermuda Jeans %d', [59.90, 129.90],
            'Bermuda jeans modelo %d. Corte moderno e confortável, perfeita para o verão e dias quentes.');

        $generateProducts(3, 10, 'Bermuda Sarja %d', [49.90, 109.90],
            'Bermuda sarja modelo %d. Tecido leve e versátil, ideal para momentos de lazer e descanso.');

        // Blusas (40 produtos)
        $generateProducts(4, 25, 'Blusa Feminina Manga Longa %d', [39.90, 119.90],
            'Blusa feminina manga longa modelo %d. Tecido leve e aconchegante, perfeita para diversas ocasiões.');

        $generateProducts(4, 15, 'Blusa Regata Alcinha %d', [29.90, 89.90],
            'Blusa regata alcinha modelo %d. Ideal para dias quentes e composição de looks despojados.');

        // Vestidos (35 produtos)
        $generateProducts(5, 20, 'Vestido Midi Floral %d', [79.90, 199.90],
            'Vestido midi floral modelo %d. Tecido fluido e estampa delicada, perfeito para eventos especiais.');

        $generateProducts(5, 15, 'Vestido Curto Casual %d', [59.90, 149.90],
            'Vestido curto casual modelo %d. Confortável e versátil, ideal para o dia a dia e encontros informais.');

        // Saias (25 produtos)
        $generateProducts(6, 15, 'Saia Jeans %d', [49.90, 129.90],
            'Saia jeans modelo %d. Corte moderno e versátil, combina com diversos tipos de looks.');

        $generateProducts(6, 10, 'Saia Evasê %d', [69.90, 159.90],
            'Saia evasê modelo %d. Caimento perfeito e movimento suave, ideal para composições elegantes.');

        // Casacos (30 produtos)
        $generateProducts(7, 20, 'Moletone Capuz %d', [89.90, 189.90],
            'Moletone com capuz modelo %d. Confortável e quentinho, perfeito para dias frios e momentos de relaxamento.');

        $generateProducts(7, 10, 'Jaqueta Jeans %d', [119.90, 249.90],
            'Jaqueta jeans modelo %d. Peça atemporal e versátil, essencial para qualquer guarda-roupa.');

        // Tênis (40 produtos)
        $generateProducts(8, 25, 'Tênis Casual Lifestyle %d', [129.90, 299.90],
            'Tênis casual lifestyle modelo %d. Conforto e estilo para o dia a dia, com design moderno e confortável.');

        $generateProducts(8, 15, 'Tênis Esportivo Performance %d', [159.90, 399.90],
            'Tênis esportivo performance modelo %d. Tecnologia de amortecimento e suporte para atividades físicas.');

        // Sapatos (30 produtos)
        $generateProducts(9, 20, 'Sapato Social Couro %d', [149.90, 349.90],
            'Sapato social em couro legítimo modelo %d. Acabamento impecável para ocasiões formais e profissionais.');

        $generateProducts(9, 10, 'Mocassim Confort %d', [129.90, 279.90],
            'Mocassim confort modelo %d. Elegância e conforto para o dia a dia corporativo e eventos sociais.');

        // Sandálias (25 produtos)
        $generateProducts(10, 15, 'Sandália Rasteira %d', [39.90, 129.90],
            'Sandália rasteira modelo %d. Confortável e versátil, perfeita para o verão e dias quentes.');

        $generateProducts(10, 10, 'Sandália Plataforma %d', [79.90, 189.90],
            'Sandália plataforma modelo %d. Design moderno e confortável, ideal para looks descolados.');

        // Bolsas (25 produtos)
        $generateProducts(11, 15, 'Bolsa Couro Legítimo %d', [99.90, 299.90],
            'Bolsa em couro legítimo modelo %d. Design funcional e elegante, perfeita para o dia a dia.');

        $generateProducts(11, 10, 'Carteira Feminina %d', [49.90, 149.90],
            'Carteira feminina modelo %d. Múltiplos compartimentos e acabamento refinado.');

        // Acessórios (30 produtos)
        $generateProducts(12, 10, 'Cinto Couro %d', [29.90, 89.90],
            'Cinto em couro legítimo modelo %d. Fivela moderna e durabilidade garantida.');

        $generateProducts(12, 10, 'Óculos Solar Fashion %d', [79.90, 199.90],
            'Óculos solar fashion modelo %d. Proteção UV e design moderno para compor seu look.');

        $generateProducts(12, 10, 'Boné Adjust %d', [39.90, 99.90],
            'Boné adjust modelo %d. Ajuste perfeito e design urbano para completar seu estilo.');

        // Roupas Íntimas (25 produtos)
        $generateProducts(13, 15, 'Kit Calcinhas Algodão %d', [49.90, 119.90],
            'Kit com 3 calcinhas em algodão modelo %d. Conforto e respirabilidade para o dia a dia.');

        $generateProducts(13, 10, 'Kit Cuecas Cotton %d', [59.90, 129.90],
            'Kit com 3 cuecas cotton modelo %d. Conforto e qualidade para uso diário.');

        // Esportivo (25 produtos)
        $generateProducts(14, 15, 'Conjunto Esportivo Fitness %d', [89.90, 199.90],
            'Conjunto esportivo fitness modelo %d. Tecido dry-fit e modelagem anatômica para atividades físicas.');

        $generateProducts(14, 10, 'Legging Esportiva %d', [69.90, 159.90],
            'Legging esportiva modelo %d. Compressão ideal e liberdade de movimento para exercícios.');

        // Produtos premium/destaque (20 produtos especiais)
        $premiumProducts = [
            [
                'name' => 'Jaqueta de Couro Legítimo Premium',
                'description' => 'Jaqueta em couro legítimo de primeira qualidade. Acabamento artesanal e forro interno em seda. Peça exclusiva e durável.',
                'price' => 599.90,
                'category_id' => $categories[7]['id'],
            ],
            [
                'name' => 'Vestido Longo de Gala',
                'description' => 'Vestido longo para ocasiões especiais. Tecido luxuoso com detalhes em renda e bordados artesanais.',
                'price' => 459.90,
                'category_id' => $categories[5]['id'],
            ],
            [
                'name' => 'Tênis Limited Edition',
                'description' => 'Edição limitada em colaboração com artista renomado. Design exclusivo e numerado.',
                'price' => 899.90,
                'category_id' => $categories[8]['id'],
            ],
            [
                'name' => 'Bolsa Designer Signature',
                'description' => 'Bolsa assinada por designer internacional. Couro italiano e detalhes em metal nobre.',
                'price' => 1299.90,
                'category_id' => $categories[11]['id'],
            ],
            [
                'name' => 'Smoking Social Premium',
                'description' => 'Smoking social em lã merino. Corte impecável para eventos de gala e cerimônias.',
                'price' => 899.90,
                'category_id' => $categories[2]['id'],
            ],
            [
                'name' => 'Conjunto de Malhas Cashmere',
                'description' => 'Conjunto de malhas em cashmere puro. Conforto e sofisticação em peças atemporais.',
                'price' => 759.90,
                'category_id' => $categories[4]['id'],
            ],
            [
                'name' => 'Sapato Oxford Artesanal',
                'description' => 'Sapato oxford feito à mão. Acabamento em couro cordovão e sola de madeira.',
                'price' => 699.90,
                'category_id' => $categories[9]['id'],
            ],
            [
                'name' => 'Jaqueta Bomber Limited',
                'description' => 'Jaqueta bomber edição limitada. Tecido técnico com detalhes em couro e bordados exclusivos.',
                'price' => 459.90,
                'category_id' => $categories[7]['id'],
            ],
            [
                'name' => 'Vestido Noiva Casual',
                'description' => 'Vestido estilo noiva para cerimônias intimistas. Renda francesa e caimento perfeito.',
                'price' => 1299.90,
                'category_id' => $categories[5]['id'],
            ],
            [
                'name' => 'Conjunto Terninho Executivo',
                'description' => 'Conjunto terninho em linho italiano. Perfeito para executivas e ocasiões corporativas.',
                'price' => 899.90,
                'category_id' => $categories[2]['id'],
            ],
            [
                'name' => 'Tênis Retro Collector',
                'description' => 'Reedição de tênis clássico dos anos 90. Design fiel aos originais com tecnologia atual.',
                'price' => 399.90,
                'category_id' => $categories[8]['id'],
            ],
            [
                'name' => 'Bolsa Tote Executive',
                'description' => 'Bolsa tote em couro estrutural. Compartimentos organizados para profissionais.',
                'price' => 559.90,
                'category_id' => $categories[11]['id'],
            ],
            [
                'name' => 'Blazer Alfaiataria',
                'description' => 'Blazer em corte de alfaiataria. Tecido inglês e acabamento impecável.',
                'price' => 389.90,
                'category_id' => $categories[7]['id'],
            ],
            [
                'name' => 'Conjunto Pijama Seda',
                'description' => 'Conjunto de pijama em seda natural. Conforto e elegância para noites especiais.',
                'price' => 299.90,
                'category_id' => $categories[13]['id'],
            ],
            [
                'name' => 'Sandália Designer Heels',
                'description' => 'Sandália de salto assinada por designer. Couro italiano e detalhes em cristal.',
                'price' => 659.90,
                'category_id' => $categories[10]['id'],
            ],
            [
                'name' => 'Parka Inverno Térmica',
                'description' => 'Parka térmica para inverno rigoroso. Impermeável e forro térmico premium.',
                'price' => 789.90,
                'category_id' => $categories[7]['id'],
            ],
            [
                'name' => 'Conjunto Esportivo Tech',
                'description' => 'Conjunto esportivo com tecnologia dry-fit avançada. Regulação de temperatura e compressão inteligente.',
                'price' => 289.90,
                'category_id' => $categories[14]['id'],
            ],
            [
                'name' => 'Relógio Smart Fashion',
                'description' => 'Relógio smart com design fashion. Tecnologia wearable e pulseiras intercambiáveis.',
                'price' => 459.90,
                'category_id' => $categories[12]['id'],
            ],
            [
                'name' => 'Mala de Viagem Premium',
                'description' => 'Mala de viagem em policarbonato. Rodas silenciosas e sistema de fechamento TSA.',
                'price' => 899.90,
                'category_id' => $categories[11]['id'],
            ],
            [
                'name' => 'Kit Acessórios Premium',
                'description' => 'Kit completo com cinto, carteira e porta-cartões em couro italiano.',
                'price' => 399.90,
                'category_id' => $categories[12]['id'],
            ],
        ];

        foreach ($premiumProducts as $premium) {
            $products[] = [
                'id' => Str::random(24),
                'name' => $premium['name'],
                'description' => $premium['description'],
                'price' => $premium['price'],
                'category_id' => $premium['category_id'],
                'stock' => rand(3, 15),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'company_id' => $companyId,
            ];
        }

        // Inserir todos os produtos no banco
        foreach ($products as $product) {
            DB::table('products')->insert($product);
        }

        // ---- IMAGENS PARA OS PRODUTOS ----
        $productIds = DB::table('products')->pluck('id')->toArray();
        $imageUrls = [
            // Camisetas
            'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1586790170083-2f9ceadc732d?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',

            // Calças
            'https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1582418702059-97ebafb35d09?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',

            // Camisas
            'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1621072156002-e2fccdc0b176?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',

            // Vestidos
            'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1595777457583-95e059d581b8?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',

            // Tênis
            'https://images.unsplash.com/photo-1549298916-b41d501d3772?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',

            // Bolsas
            'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1584917865442-de89df76afd3?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',

            // Acessórios
            'https://images.unsplash.com/photo-1582142306909-195724d1a6ec?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
        ];

        // Adicionar 2-3 imagens para cada produto
        foreach ($productIds as $productId) {
            $randomImages = array_rand($imageUrls, rand(2, 3));
            if (!is_array($randomImages)) {
                $randomImages = [$randomImages];
            }
            foreach ($randomImages as $imageIndex) {
                Image::create([
                    'url' => $imageUrls[$imageIndex],
                    'imageable_type' => 'App\Models\Product',
                    'imageable_id' => $productId,
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
            'taxpayer' => '70373047193',
            'company_id' => $companyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Busca todos os IDs das categorias existentes no banco
        $categoryIds = Category::pluck('id')->toArray();

        // Define os layouts atualizados
        $layoutSections = [
            [
                'id' => 'LAYO_MNlzmnxZqRSpF5Ef16h',
                'name' => 'hero_principal',
                'title' => 'Hero',
                'type' => 'hero',
                'content' => [
                    'badge' => '500+ Produtos Exclusivos',
                    'title' => 'Moda Fashion',
                    'title_emphasis' => 'para Todos os Estilos',
                    'subtitle' => 'Descubra as últimas tendências com qualidade e preços incríveis',
                    'images' => [
                        'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1562157873-818bc0726f68?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583496661160-fb5886a13d77?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80'
                    ],
                    'video_url' => 'https://www.youtube.com/watch?v=sNNHlD8b-KM',
                    'ctas' => [
                        ['label' => 'Ver Catálogo', 'to' => '/#produtos'],
                        ['label' => 'Novidades', 'action' => 'open_new_arrivals', 'variant' => 'secondary'],
                    ],
                    'stats' => [
                        ['value' => '500+', 'label' => 'Produtos'],
                        ['value' => '15', 'label' => 'Categorias'],
                        ['value' => '24h', 'label' => 'Entrega'],
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
                    'badge' => 'Destaques',
                    'title' => 'Os Favoritos',
                    'subtitle' => 'Produtos mais amados pelos nossos clientes',
                    'show_category_filters' => true,
                    'category_ids' => $categoryIds,
                    'limit' => 12,
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
                    'title' => 'Inspiração Fashion',
                    'subtitle' => 'Looks que vão transformar seu estilo',
                    'images' => [
                        ['src' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80', 'title' => 'Look Casual'],
                        ['src' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80', 'caption' => 'Estilo Elegante'],
                        ['src' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80', 'title' => 'Moda Verão'],
                        ['src' => 'https://images.unsplash.com/photo-1519457431-44ccd64a579b?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80', 'caption' => 'Street Style'],
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
                    'title' => 'Pronto para renovar seu guarda-roupa?',
                    'subtitle' => 'Mais de 500 produtos esperando por você!',
                    'ctas' => [
                        ['label' => 'Explorar Catálogo', 'to' => '/products'],
                        ['label' => 'Cadastre-se', 'to' => '/register', 'variant' => 'secondary'],
                    ],
                    'bullets' => [
                        'Entrega em 24h',
                        'Troca Facilitada',
                        'Parcele em 12x',
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
                    "title" => "Por que comprar conosco?",
                    "subtitle" => "Tudo que você precisa para uma experiência incrível",
                    "items" => [
                        ["icon" => "truck", "title" => "Entrega Rápida", "description" => "Receba em até 24h na capital e 48h interior"],
                        ["icon" => "check", "title" => "Qualidade", "description" => "Produtos selecionados e materiais premium"],
                        ["icon" => "refresh", "title" => "Troca Grátis", "description" => "30 dias para trocar se não ficar satisfeito"],
                        ["icon" => "credit-card", "title" => "Parcele", "description" => "Em até 12x sem juros no cartão"],
                        ["icon" => "shield", "title" => "Compra Segura", "description" => "Seus dados protegidos e pagamento seguro"],
                        ["icon" => "users", "title" => "Atendimento", "description" => "Suporte especializado para te ajudar"],
                    ]
                ],
                'position' => 5,
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
                'content' => $section['content'],
                'position' => $section['position'],
                'is_active' => $section['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->call(StoreSettingsSeeder::class);
    }
}
