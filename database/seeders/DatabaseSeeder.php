<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Image;
use App\Models\LayoutSection;
use App\Models\Product;
use App\Models\User;
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
        // Limpar dados existentes
        DB::table('users')->delete();
        DB::table('products')->delete();
        DB::table('categories')->delete();
        DB::table('companies')->delete();
        DB::table('layout_sections')->delete();
        DB::table('images')->delete();
        DB::table('store_settings')->delete();

        // Criar as duas companhias
        $companies = [
            [
                'id' => 'COMP_' . Str::random(19),
                'name' => 'Fashion Store - Loja 1',
                'slug' => 'fashion-store',
                'domain' => 'commercefront.taskanalyzer.com',
                'description' => 'Loja de roupas e acessórios fashion',
                'industry' => 'Moda',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'COMP_' . Str::random(19),
                'name' => 'Confeitaria Doce Sabor - Loja 2',
                'slug' => 'confeitaria-doce-sabor',
                'domain' => 'loja2.taskanalyzer.com',
                'description' => 'Confeitaria especializada em bolos e doces finos',
                'industry' => 'Alimentação',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($companies as $company) {
            DB::table('companies')->insert($company);
        }

        // Semear dados para cada companhia
        $this->call([
            CompanyDataSeeder::class,
        ]);
    }
}

class CompanyDataSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            // Configurar o contexto da companhia atual
            app()->instance('company_id', $company->id);
            app()->instance('company', $company);

            if ($company->industry === 'Moda') {
                $this->seedFashionStore($company);
            } else {
                $this->seedConfeitaria($company);
            }
        }
    }

    private function seedFashionStore(Company $company): void
    {
        // ---- ADMIN PARA MODA ----
        $adminId = $this->createAdminUser($company, 'admin@loja1.com', 'Admin Loja 1', '70373047193');

        // ---- STORE SETTINGS PARA MODA ----
        $this->seedFashionStoreSettings($company, $adminId);

        // ---- CATEGORIAS PARA MODA ----
        $categories = [
            ['id' => Str::random(24), 'name' => 'Camisetas', 'slug' => 'camisetas', 'description' => 'Camisetas básicas, estampadas e temáticas para todos os estilos', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Calças', 'slug' => 'calcas', 'description' => 'Jeans, sarja, leggings e calças casuais', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Camisas', 'slug' => 'camisas', 'description' => 'Camisas sociais, polo e casuais', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Bermudas', 'slug' => 'bermudas', 'description' => 'Bermudas jeans, sarja e esportivas', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Blusas', 'slug' => 'blusas', 'description' => 'Blusas femininas, regatas e mangas longas', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Vestidos', 'slug' => 'vestidos', 'description' => 'Vestidos curtos, longos e casuais', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Saias', 'slug' => 'saias', 'description' => 'Saias jeans, evasê e midi', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Casacos', 'slug' => 'casacos', 'description' => 'Jaquetas, moletons e blazers', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Tênis', 'slug' => 'tenis', 'description' => 'Tênis casuais, esportivos e lifestyle', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Sapatos', 'slug' => 'sapatos', 'description' => 'Sapatos sociais, mocassim e oxford', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Sandálias', 'slug' => 'sandalias', 'description' => 'Sandálias femininas, rasteiras e plataformas', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Bolsas', 'slug' => 'bolsas', 'description' => 'Bolsas de couro, totô e carteiras', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Acessórios', 'slug' => 'acessorios', 'description' => 'Cintos, óculos, bonés e relógios', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Roupas Íntimas', 'slug' => 'roupas-intimas', 'description' => 'Calcinhas, cuecas e pijamas', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Esportivo', 'slug' => 'esportivo', 'description' => 'Roupas fitness e atividades esportivas', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            $category['company_id'] = $company->id;
            DB::table('categories')->insert($category);
        }

        // ---- PRODUTOS PARA MODA ----
        $products = [];
        $categoryIds = DB::table('categories')->where('company_id', $company->id)->pluck('id')->toArray();

        // Função para gerar produtos em lote
        $generateProducts = function($categoryIndex, $count, $nameTemplate, $priceRange, $descriptionTemplate) use (&$products, $categories, $company) {
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
                    'company_id' => $company->id,
                ];
            }
        };

        // Camisetas (20 produtos)
        $generateProducts(0, 20, 'Camiseta Básica Cotton %d', [29.90, 79.90], 'Camiseta 100%% algodão, modelo %d. Tecido macio e respirável, perfeita para o dia a dia. Disponível em diversas cores.');
        $generateProducts(0, 10, 'Camiseta Estampada Urban %d', [49.90, 99.90], 'Camiseta com estampa exclusiva urban %d. Design moderno e jovial, ideal para compor looks casuais e descolados.');

        // Calças (15 produtos)
        $generateProducts(1, 10, 'Calça Jeans Slim Fit %d', [89.90, 189.90], 'Calça jeans slim fit modelo %d. Com elastano para melhor conforto e mobilidade. Perfeita para looks casuais.');
        $generateProducts(1, 5, 'Calça Sarja Cargo %d', [79.90, 159.90], 'Calça sarja cargo modelo %d. Bolsos funcionais e tecido resistente. Ideal para dia a dia e atividades urbanas.');

        // Camisas (10 produtos)
        $generateProducts(2, 7, 'Camisa Social Slim %d', [99.90, 229.90], 'Camisa social slim fit modelo %d. Tecido de alta qualidade, perfeita para o ambiente corporativo e ocasiões especiais.');
        $generateProducts(2, 3, 'Camisa Polo Premium %d', [79.90, 169.90], 'Camisa polo premium modelo %d. Acabamento impecável e toque suave. Ideal para looks smart casual.');

        // Produtos premium/destaque (10 produtos especiais)
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
                'company_id' => $company->id,
            ];
        }

        // Inserir produtos
        foreach ($products as $product) {
            DB::table('products')->insert($product);
        }

        // ---- IMAGENS PARA MODA ----
        $this->seedProductImages($company);

        // ---- LAYOUT COMPLETO PARA MODA ----
        $this->seedFashionLayout($company, $categoryIds);

        $this->command->info("Dados da Loja de Moda ({$company->name}) criados com sucesso!");
    }

    private function seedConfeitaria(Company $company): void
    {
        // ---- ADMIN PARA CONFEITARIA ----
        $adminId = $this->createAdminUser($company, 'admin@loja2.com', 'Admin Confeitaria', '70373047194');

        // ---- STORE SETTINGS PARA CONFEITARIA ----
        $this->seedConfeitariaStoreSettings($company, $adminId);

        // ---- CATEGORIAS PARA CONFEITARIA ----
        $categories = [
            ['id' => Str::random(24), 'name' => 'Bolos Caseiros', 'slug' => 'bolos-caseiros', 'description' => 'Bolos tradicionais e caseiros feitos com carinho', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Bolos Decorados', 'slug' => 'bolos-decorados', 'description' => 'Bolos para festas e eventos especiais', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Doces Finos', 'slug' => 'doces-finos', 'description' => 'Doces sofisticados para ocasiões especiais', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Tortas Doces', 'slug' => 'tortas-doces', 'description' => 'Tortas doces e sobremesas', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Cupcakes', 'slug' => 'cupcakes', 'description' => 'Cupcakes decorados e temáticos', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Brownies', 'slug' => 'brownies', 'description' => 'Brownies tradicionais e especiais', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Pães Doces', 'slug' => 'paes-doces', 'description' => 'Pães doces e massas folhadas', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Salgados', 'slug' => 'salgados', 'description' => 'Salgados para festas e eventos', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Doces de Copo', 'slug' => 'doces-de-copo', 'description' => 'Sobremesas individuais em copos', 'is_active' => true],
            ['id' => Str::random(24), 'name' => 'Kits Festa', 'slug' => 'kits-festa', 'description' => 'Kits completos para festas e eventos', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            $category['company_id'] = $company->id;
            DB::table('categories')->insert($category);
        }

        // ---- PRODUTOS PARA CONFEITARIA ----
        $products = [
            // Bolos Caseiros
            ['id' => Str::random(24), 'name' => 'Bolo de Chocolate Tradicional', 'description' => 'Bolo de chocolate fofinho com cobertura de chocolate', 'price' => 45.90, 'category_id' => $categories[0]['id'], 'stock' => 10, 'is_active' => true, 'company_id' => $company->id],
            ['id' => Str::random(24), 'name' => 'Bolo de Cenoura com Cobertura', 'description' => 'Bolo de cenoura úmido com cobertura de chocolate', 'price' => 42.90, 'category_id' => $categories[0]['id'], 'stock' => 8, 'is_active' => true, 'company_id' => $company->id],
            ['id' => Str::random(24), 'name' => 'Bolo de Fubá Cremoso', 'description' => 'Bolo de fubá cremoso tradicional', 'price' => 38.90, 'category_id' => $categories[0]['id'], 'stock' => 12, 'is_active' => true, 'company_id' => $company->id],

            // Bolos Decorados
            ['id' => Str::random(24), 'name' => 'Bolo de Aniversário Personalizado', 'description' => 'Bolo decorado conforme tema da festa', 'price' => 120.00, 'category_id' => $categories[1]['id'], 'stock' => 5, 'is_active' => true, 'company_id' => $company->id],
            ['id' => Str::random(24), 'name' => 'Bolo Noivo/Noiva 3 Andares', 'description' => 'Bolo de casamento luxuoso com 3 andares', 'price' => 350.00, 'category_id' => $categories[1]['id'], 'stock' => 2, 'is_active' => true, 'company_id' => $company->id],

            // Doces Finos
            ['id' => Str::random(24), 'name' => 'Brigadeiro Gourmet', 'description' => 'Brigadeiro premium com chocolate belga', 'price' => 3.50, 'category_id' => $categories[2]['id'], 'stock' => 50, 'is_active' => true, 'company_id' => $company->id],
            ['id' => Str::random(24), 'name' => 'Beijinho de Coco', 'description' => 'Doce de coco com leite condensado', 'price' => 3.00, 'category_id' => $categories[2]['id'], 'stock' => 40, 'is_active' => true, 'company_id' => $company->id],

            // Tortas Doces
            ['id' => Str::random(24), 'name' => 'Torta de Limão', 'description' => 'Torta de limão com massa crocante', 'price' => 52.90, 'category_id' => $categories[3]['id'], 'stock' => 7, 'is_active' => true, 'company_id' => $company->id],
            ['id' => Str::random(24), 'name' => 'Torta Holandesa', 'description' => 'Torta com creme e chocolate', 'price' => 58.90, 'category_id' => $categories[3]['id'], 'stock' => 5, 'is_active' => true, 'company_id' => $company->id],

            // Cupcakes
            ['id' => Str::random(24), 'name' => 'Kit 6 Cupcakes Decorados', 'description' => '6 cupcakes com decoração temática', 'price' => 35.00, 'category_id' => $categories[4]['id'], 'stock' => 15, 'is_active' => true, 'company_id' => $company->id],

            // Brownies
            ['id' => Str::random(24), 'name' => 'Brownie de Chocolate com Nozes', 'description' => 'Brownie intenso com pedaços de nozes', 'price' => 12.90, 'category_id' => $categories[5]['id'], 'stock' => 25, 'is_active' => true, 'company_id' => $company->id],

            // Kits Festa
            ['id' => Str::random(24), 'name' => 'Kit Festa Completo (20 pessoas)', 'description' => 'Bolo, doces e salgados para 20 pessoas', 'price' => 299.90, 'category_id' => $categories[9]['id'], 'stock' => 4, 'is_active' => true, 'company_id' => $company->id],
        ];

        foreach ($products as $product) {
            $product['created_at'] = now();
            $product['updated_at'] = now();
            DB::table('products')->insert($product);
        }

        // ---- IMAGENS PARA CONFEITARIA ----
        $this->seedConfeitariaImages($company);

        // ---- LAYOUT COMPLETO PARA CONFEITARIA ----
        $this->seedConfeitariaLayout($company);

        $this->command->info("Dados da Confeitaria ({$company->name}) criados com sucesso!");
    }

    private function seedFashionStoreSettings(Company $company, string $adminId): void
    {
        $storeSettings = [
            [
                'id' => 'STORE_' . Str::random(20),
                'user_id' => $adminId,
                'store_name' => 'Fashion Store',
                'primary_color' => '#3B82F6',
                'secondary_color' => '#1E40AF',
                'background_color' => '#FFFFFF',
                'text_color' => '#1F2937',
                'font_family' => 'Inter, sans-serif',
                'logo_url' => 'https://images.unsplash.com/photo-1565688534245-05d6b5be184a?ixlib=rb-4.0.1&auto=format&fit=crop&w=200&q=80',
                'favicon_url' => 'https://images.unsplash.com/photo-1565688534245-05d6b5be184a?ixlib=rb-4.0.1&auto=format&fit=crop&w=32&q=80',
                'custom_css' => '/* Custom CSS for Fashion Store */',
                'custom_js' => '// Custom JS for Fashion Store',
                'is_active' => false,
                'company_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'STORE_' . Str::random(20),
                'user_id' => $adminId,
                'store_name' => 'Fashion Store - Configurações de Layout',
                'primary_color' => '#EC4899',
                'secondary_color' => '#BE185D',
                'background_color' => '#FDF2F8',
                'text_color' => '#1F2937',
                'font_family' => 'Poppins, sans-serif',
                'logo_url' => 'https://images.unsplash.com/photo-1565688534245-05d6b5be184a?ixlib=rb-4.0.1&auto=format&fit=crop&w=200&q=80',
                'favicon_url' => 'https://images.unsplash.com/photo-1565688534245-05d6b5be184a?ixlib=rb-4.0.1&auto=format&fit=crop&w=32&q=80',
                'custom_css' => '.hero-section { background: linear-gradient(135deg, #EC4899 0%, #BE185D 100%); }',
                'custom_js' => 'console.log("Fashion Store JS loaded");',
                'is_active' => false,
                'company_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'STORE_' . Str::random(20),
                'user_id' => $adminId,
                'store_name' => 'Fashion Store - Configurações de Tema',
                'primary_color' => '#8B5CF6',
                'secondary_color' => '#7C3AED',
                'background_color' => '#FAF5FF',
                'text_color' => '#1F2937',
                'font_family' => 'Montserrat, sans-serif',
                'logo_url' => 'https://images.unsplash.com/photo-1565688534245-05d6b5be184a?ixlib=rb-4.0.1&auto=format&fit=crop&w=200&q=80',
                'favicon_url' => 'https://images.unsplash.com/photo-1565688534245-05d6b5be184a?ixlib=rb-4.0.1&auto=format&fit=crop&w=32&q=80',
                'custom_css' => '.btn-primary { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); border: none; }',
                'custom_js' => '// Fashion Store theme customization',
                'is_active' => true,
                'company_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($storeSettings as $setting) {
            DB::table('store_settings')->insert($setting);
        }
    }

    private function seedConfeitariaStoreSettings(Company $company, string $adminId): void
    {
        $storeSettings = [
            [
                'id' => 'STORE_' . Str::random(20),
                'user_id' => $adminId,
                'store_name' => 'Confeitaria Doce Sabor',
                'primary_color' => '#F59E0B',
                'secondary_color' => '#D97706',
                'background_color' => '#FFFBEB',
                'text_color' => '#1F2937',
                'font_family' => 'Dancing Script, cursive',
                'logo_url' => 'https://images.unsplash.com/photo-1555507036-ab794f27d2e9?ixlib=rb-4.0.1&auto=format&fit=crop&w=200&q=80',
                'favicon_url' => 'https://images.unsplash.com/photo-1555507036-ab794f27d2e9?ixlib=rb-4.0.1&auto=format&fit=crop&w=32&q=80',
                'custom_css' => '/* Custom CSS for Confeitaria */',
                'custom_js' => '// Custom JS for Confeitaria',
                'is_active' => false,
                'company_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'STORE_' . Str::random(20),
                'user_id' => $adminId,
                'store_name' => 'Confeitaria Doce Sabor - Tema Doce',
                'primary_color' => '#EC4899',
                'secondary_color' => '#BE185D',
                'background_color' => '#FDF2F8',
                'text_color' => '#1F2937',
                'font_family' => 'Pacifico, cursive',
                'logo_url' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.1&auto=format&fit=crop&w=200&q=80',
                'favicon_url' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.1&auto=format&fit=crop&w=32&q=80',
                'custom_css' => '.hero-section { background: linear-gradient(135deg, #EC4899 0%, #BE185D 100%); color: white; }',
                'custom_js' => 'console.log("Confeitaria Doce Sabor JS loaded");',
                'is_active' => false,
                'company_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'STORE_' . Str::random(20),
                'user_id' => $adminId,
                'store_name' => 'Confeitaria Doce Sabor - Tema Clássico',
                'primary_color' => '#A78BFA',
                'secondary_color' => '#8B5CF6',
                'background_color' => '#F5F3FF',
                'text_color' => '#1F2937',
                'font_family' => 'Playfair Display, serif',
                'logo_url' => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?ixlib=rb-4.0.1&auto=format&fit=crop&w=200&q=80',
                'favicon_url' => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?ixlib=rb-4.0.1&auto=format&fit=crop&w=32&q=80',
                'custom_css' => '.btn-primary { background: linear-gradient(135deg, #A78BFA 0%, #8B5CF6 100%); border: none; border-radius: 25px; }',
                'custom_js' => '// Confeitaria classic theme customization',
                'is_active' => true,
                'company_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($storeSettings as $setting) {
            DB::table('store_settings')->insert($setting);
        }
    }

    private function seedProductImages(Company $company): void
    {
        $productIds = DB::table('products')->where('company_id', $company->id)->pluck('id')->toArray();
        $imageUrls = [
            'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1586790170083-2f9ceadc732d?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1582418702059-97ebafb35d09?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
        ];

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
                    'company_id' => $company->id,
                ]);
            }
        }
    }

    private function seedConfeitariaImages(Company $company): void
    {
        $productIds = DB::table('products')->where('company_id', $company->id)->pluck('id')->toArray();
        $imageUrls = [
            'https://images.unsplash.com/photo-1565958011703-44f9829ba187?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1555507036-ab794f27d2e9?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
        ];

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
                    'company_id' => $company->id,
                ]);
            }
        }
    }

    private function seedFashionLayout(Company $company, array $categoryIds): void
    {
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

        foreach ($layoutSections as $section) {
            $section['company_id'] = $company->id;
            $section['created_at'] = now();
            $section['updated_at'] = now();
            LayoutSection::create($section);
        }
    }

    private function seedConfeitariaLayout(Company $company): void
    {
        $categoryIds = DB::table('categories')->where('company_id', $company->id)->pluck('id')->toArray();

        $layoutSections = [
            [
                'id' => 'LAYO_CONF_' . Str::random(16),
                'name' => 'hero_principal',
                'title' => 'Hero Confeitaria',
                'type' => 'hero',
                'content' => [
                    'badge' => 'Doces Artesanais',
                    'title' => 'Confeitaria Doce Sabor',
                    'title_emphasis' => 'Sabor Inesquecível',
                    'subtitle' => 'Bolos, doces finos e sobremesas feitas com ingredientes selecionados',
                    'images' => [
                        'https://images.unsplash.com/photo-1565958011703-44f9829ba187?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80',
                    ],
                    'ctas' => [
                        ['label' => 'Ver Cardápio', 'to' => '/#produtos'],
                        ['label' => 'Encomendar', 'action' => 'open_orders', 'variant' => 'secondary'],
                    ],
                    'stats' => [
                        ['value' => '50+', 'label' => 'Sabores'],
                        ['value' => '10', 'label' => 'Categorias'],
                        ['value' => '48h', 'label' => 'Encomenda'],
                    ],
                ],
                'position' => 1,
                'is_active' => true,
            ],
            [
                'id' => 'LAYO_CONF_' . Str::random(16),
                'name' => 'produtos_mais_vendidos',
                'title' => 'Mais Pedidos',
                'type' => 'produtos',
                'content' => [
                    'badge' => 'Destaques',
                    'title' => 'Os Mais Pedidos',
                    'subtitle' => 'Doces mais amados pelos nossos clientes',
                    'show_category_filters' => true,
                    'category_ids' => $categoryIds,
                    'limit' => 8,
                ],
                'position' => 2,
                'is_active' => true,
            ],
            [
                'id' => 'LAYO_CONF_' . Str::random(16),
                'name' => 'banner_galeria',
                'title' => 'Nossas Criações',
                'type' => 'pictures',
                'content' => [
                    'title' => 'Nossas Especialidades',
                    'subtitle' => 'Doces feitos com carinho e qualidade',
                    'images' => [
                        ['src' => 'https://images.unsplash.com/photo-1555507036-ab794f27d2e9?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80', 'title' => 'Bolos Decorados'],
                        ['src' => 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80', 'caption' => 'Doces Finos'],
                        ['src' => 'https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80', 'title' => 'Tortas Especiais'],
                        ['src' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.1&auto=format&fit=crop&w=800&q=80', 'caption' => 'Cupcakes Temáticos'],
                    ],
                ],
                'position' => 3,
                'is_active' => true,
            ],
            [
                'id' => 'LAYO_CONF_' . Str::random(16),
                'name' => 'cta_principal',
                'title' => 'cta',
                'type' => 'cta',
                'content' => [
                    'title' => 'Pronto para adoçar seu dia?',
                    'subtitle' => 'Faça sua encomenda e surpreenda-se com nossos sabores!',
                    'ctas' => [
                        ['label' => 'Fazer Encomenda', 'to' => '/products'],
                        ['label' => 'Ver Cardápio', 'to' => '/categories', 'variant' => 'secondary'],
                    ],
                    'bullets' => [
                        'Encomenda com 48h de antecedência',
                        'Ingredientes selecionados',
                        'Entrega gratuita na região',
                    ],
                ],
                'position' => 4,
                'is_active' => true,
            ],
            [
                'id' => 'LAYO_CONF_' . Str::random(16),
                'name' => 'features',
                'title' => 'features',
                'type' => 'features',
                'content' => [
                    "title" => "Por que escolher nossa confeitaria?",
                    "subtitle" => "Tudo que você precisa para momentos especiais",
                    "items" => [
                        ["icon" => "star", "title" => "Qualidade Premium", "description" => "Ingredientes selecionados e receitas exclusivas"],
                        ["icon" => "clock", "title" => "Entrega Agendada", "description" => "Receba seus pedidos no horário combinado"],
                        ["icon" => "heart", "title" => "Feito com Amor", "description" => "Cada doce preparado com carinho e dedicação"],
                        ["icon" => "award", "title" => "Tradição", "description" => "Anos de experiência em confeitaria artesanal"],
                        ["icon" => "users", "title" => "Atendimento Personalizado", "description" => "Ajudamos a criar o doce perfeito para sua ocasião"],
                        ["icon" => "phone", "title" => "Facilidade", "description" => "Encomendas por WhatsApp ou site"],
                    ]
                ],
                'position' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($layoutSections as $section) {
            $section['company_id'] = $company->id;
            $section['created_at'] = now();
            $section['updated_at'] = now();
            LayoutSection::create($section);
        }
    }

    private function createAdminUser(Company $company, string $email, string $name, string $taxpayer): string
    {
        $adminId = Str::random(24);

        DB::table('users')->insert([
            'id' => $adminId,
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('12345678'),
            'type' => 'admin',
            'is_admin' => true,
            'taxpayer' => $taxpayer,
            'company_id' => $company->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $adminId;
    }
}
