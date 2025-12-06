<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Gêneros Principais
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Rock',
                'slug' => 'rock',
                'description' => 'Gênero musical que se originou no rock and roll dos anos 1950, caracterizado por guitarras elétricas, bateria e vocais potentes.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Pop',
                'slug' => 'pop',
                'description' => 'Música popular caracterizada por melodias cativantes, refrões repetitivos e produção comercial.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Hip Hop/Rap',
                'slug' => 'hip-hop-rap',
                'description' => 'Gênero musical que incorpora rimas ritmadas e linguagem falada, acompanhadas por batidas eletrônicas ou samples.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Jazz',
                'slug' => 'jazz',
                'description' => 'Gênero musical que se originou nas comunidades afro-americanas, caracterizado por improvisação, swing e harmonias complexas.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Blues',
                'slug' => 'blues',
                'description' => 'Gênero musical com raízes afro-americanas, caracterizado por progressões de acordes específicas e letras emotivas.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'R&B/Soul',
                'slug' => 'rnb-soul',
                'description' => 'Música rhythm and blues e soul, caracterizada por batidas suaves, vocais emotivos e influências gospel.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Eletrônica',
                'slug' => 'eletronica',
                'description' => 'Música criada usando instrumentos eletrônicos, sintetizadores e tecnologia de produção digital.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Country',
                'slug' => 'country',
                'description' => 'Gênero musical originário do sul dos Estados Unidos, com raízes na música folk e blues.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Funk',
                'slug' => 'funk',
                'description' => 'Gênero musical com batidas rítmicas fortes, linhas de baixo proeminentes e influências do soul e jazz.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Reggae',
                'slug' => 'reggae',
                'description' => 'Gênero musical jamaicano caracterizado por ritmos off-beat e letras frequentemente sociais ou espirituais.',
                'is_active' => true,
            ],

            // Subgêneros do Rock
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Rock Clássico',
                'slug' => 'rock-classico',
                'description' => 'Rock das décadas de 1960, 1970 e início dos anos 1980, incluindo bandas lendárias do gênero.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Hard Rock',
                'slug' => 'hard-rock',
                'description' => 'Subgênero do rock mais pesado e agressivo, com distorção intensa e solos de guitarra.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Heavy Metal',
                'slug' => 'heavy-metal',
                'description' => 'Gênero derivado do hard rock, caracterizado por riffs pesados, bateria rápida e vocais poderosos.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Punk Rock',
                'slug' => 'punk-rock',
                'description' => 'Gênero de rock simplificado e energético, com atitude rebelde e letras contestadoras.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Grunge',
                'slug' => 'grunge',
                'description' => 'Subgênero do rock alternativo originário de Seattle, com distorção pesada e letras introspectivas.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Rock Alternativo',
                'slug' => 'rock-alternativo',
                'description' => 'Gênero que surgiu como alternativa ao rock mainstream, com diversas influências e experimentações.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Indie Rock',
                'slug' => 'indie-rock',
                'description' => 'Rock produzido por gravadoras independentes, com foco na autenticidade e diversidade sonora.',
                'is_active' => true,
            ],

            // Subgêneros da Música Eletrônica
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'House',
                'slug' => 'house',
                'description' => 'Gênero eletrônico caracterizado por batidas 4/4, linhas de baixo proeminentes e influências disco.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Techno',
                'slug' => 'techno',
                'description' => 'Música eletrônica com batidas repetitivas, sintetizadores e foco no ritmo para dança.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Trance',
                'slug' => 'trance',
                'description' => 'Gênero eletrônico com batidas progressivas, melodias emotivas e climas atmosféricos.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Drum and Bass',
                'slug' => 'drum-and-bass',
                'description' => 'Gênero eletrônico com batidas breakbeat aceleradas e linhas de baixo profundas.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Dubstep',
                'slug' => 'dubstep',
                'description' => 'Gênero eletrônico caracterizado por batidas quebradas, baixos pesados e efeitos sonoros.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Ambient',
                'slug' => 'ambient',
                'description' => 'Música eletrônica atmosférica e textural, focada na criação de ambientes sonoros.',
                'is_active' => true,
            ],

            // Música Brasileira
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'MPB',
                'slug' => 'mpb',
                'description' => 'Música Popular Brasileira, movimento que mescla influências brasileiras com elementos internacionais.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Samba',
                'slug' => 'samba',
                'description' => 'Gênero musical brasileiro com raízes africanas, caracterizado por ritmo sincopado e letras poéticas.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Bossa Nova',
                'slug' => 'bossa-nova',
                'description' => 'Estilo musical brasileiro que mistura samba e jazz, com harmonias sofisticadas e suavidade.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Forró',
                'slug' => 'forro',
                'description' => 'Gênero musical nordestino com influências europeias e africanas, dançado em pares.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Axé',
                'slug' => 'axe',
                'description' => 'Gênero musical baiano que mistura frevo, reggae, maracatu e outros ritmos.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Funk Carioca',
                'slug' => 'funk-carioca',
                'description' => 'Gênero musical brasileiro originário do Rio de Janeiro, com batidas eletrônicas e letras características.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Sertanejo',
                'slug' => 'sertanejo',
                'description' => 'Música caipira brasileira que evoluiu para diferentes vertentes ao longo dos anos.',
                'is_active' => true,
            ],

            // Gêneros Internacionais
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'K-Pop',
                'slug' => 'k-pop',
                'description' => 'Música popular coreana que mistura diversos gêneros com coreografias elaboradas.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Reggaeton',
                'slug' => 'reggaeton',
                'description' => 'Gênero musical originário de Porto Rico, com influências de reggae, hip hop e música latina.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Flamenco',
                'slug' => 'flamenco',
                'description' => 'Arte musical espanhola que combina canto, guitarra, dança e palmas.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Salsa',
                'slug' => 'salsa',
                'description' => 'Gênero musical caribenho com influências cubanas, porto-riquenhas e jazz.',
                'is_active' => true,
            ],

            // Gêneros Clássicos e Eruditos
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Clássica',
                'slug' => 'classica',
                'description' => 'Música erudita ocidental, abrangendo desde o período barroco até o contemporâneo.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Ópera',
                'slug' => 'opera',
                'description' => 'Forma de teatro musical onde a ação é cantada com acompanhamento instrumental.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Instrumental',
                'slug' => 'instrumental',
                'description' => 'Música sem vocais, focada na performance e composição dos instrumentos.',
                'is_active' => true,
            ],

            // Outros Gêneros
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Folk',
                'slug' => 'folk',
                'description' => 'Música tradicional ou contemporânea com raízes na cultura popular.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Gospel',
                'slug' => 'gospel',
                'description' => 'Música cristã com influências do blues, jazz e soul, caracterizada por vocais potentes.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'New Age',
                'slug' => 'new-age',
                'description' => 'Música relaxante e meditativa, frequentemente usada para relaxamento e terapia.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Lo-fi',
                'slug' => 'lo-fi',
                'description' => 'Música com produção de baixa fidelidade, caracterizada por batidas relaxantes e atmosfera calmante.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Trap',
                'slug' => 'trap',
                'description' => 'Subgênero do hip hop com batidas lentas, 808s pesados e atmosfera sombria.',
                'is_active' => true,
            ],
            [
                'id' => 'cat_' . Str::random(20),
                'name' => 'Synthwave',
                'slug' => 'synthwave',
                'description' => 'Gênero musical inspirado nas trilhas sonoras dos anos 1980, com sintetizadores e atmosfera retro.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }
    }
}
