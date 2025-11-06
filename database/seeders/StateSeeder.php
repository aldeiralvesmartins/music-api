<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    public function run()
    {
        foreach (self::$states as $state) {
            DB::table('states')->insert([
                'id' => $state['id'],
                'acronyms' => $state['acronyms'],
                'name' => $state['name'],
                'country_id' => $state['country_id'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }

    public static $states = [
        [
            'id' => 1,
            'acronyms' => 'AC',
            'name' => 'Acre',
            'country_id' => 1
        ],
        [
            'id' => 2,
            'acronyms' => 'AL',
            'name' => 'Alagoas',
            'country_id' => 1
        ],
        [
            'id' => 3,
            'acronyms' => 'AP',
            'name' => 'Amapá',
            'country_id' => 1
        ],
        [
            'id' => 4,
            'acronyms' => 'AM',
            'name' => 'Amazonas',
            'country_id' => 1
        ],
        [
            'id' => 5,
            'acronyms' => 'BA',
            'name' => 'Bahia',
            'country_id' => 1
        ],
        [
            'id' => 6,
            'acronyms' => 'CE',
            'name' => 'Ceará',
            'country_id' => 1
        ],
        [
            'id' => 7,
            'acronyms' => 'DF',
            'name' => 'Distrito Federal',
            'country_id' => 1
        ],
        [
            'id' => 8,
            'acronyms' => 'ES',
            'name' => 'Espírito Santo',
            'country_id' => 1
        ],
        [
            'id' => 9,
            'acronyms' => 'GO',
            'name' => 'Goiás',
            'country_id' => 1
        ],
        [
            'id' => 10,
            'acronyms' => 'MA',
            'name' => 'Maranhão',
            'country_id' => 1
        ],
        [
            'id' => 11,
            'acronyms' => 'MT',
            'name' => 'Mato Grosso',
            'country_id' => 1
        ],
        [
            'id' => 12,
            'acronyms' => 'MS',
            'name' => 'Mato Grosso do Sul',
            'country_id' => 1
        ],
        [
            'id' => 13,
            'acronyms' => 'MG',
            'name' => 'Minas Gerais',
            'country_id' => 1
        ],
        [
            'id' => 14,
            'acronyms' => 'PA',
            'name' => 'Pará',
            'country_id' => 1
        ],
        [
            'id' => 15,
            'acronyms' => 'PB',
            'name' => 'Paraíba',
            'country_id' => 1
        ],
        [
            'id' => 16,
            'acronyms' => 'PR',
            'name' => 'Paraná',
            'country_id' => 1
        ],
        [
            'id' => 17,
            'acronyms' => 'PE',
            'name' => 'Pernambuco',
            'country_id' => 1
        ],
        [
            'id' => 18,
            'acronyms' => 'PI',
            'name' => 'Piauí',
            'country_id' => 1
        ],
        [
            'id' => 19,
            'acronyms' => 'RJ',
            'name' => 'Rio de Janeiro',
            'country_id' => 1
        ],
        [
            'id' => 20,
            'acronyms' => 'RN',
            'name' => 'Rio Grande do Norte',
            'country_id' => 1
        ],
        [
            'id' => 21,
            'acronyms' => 'RS',
            'name' => 'Rio Grande do Sul',
            'country_id' => 1
        ],
        [
            'id' => 22,
            'acronyms' => 'RO',
            'name' => 'Rondônia',
            'country_id' => 1
        ],
        [
            'id' => 23,
            'acronyms' => 'RR',
            'name' => 'Roraima',
            'country_id' => 1
        ],
        [
            'id' => 24,
            'acronyms' => 'SC',
            'name' => 'Santa Catarina',
            'country_id' => 1
        ],
        [
            'id' => 25,
            'acronyms' => 'SP',
            'name' => 'São Paulo',
            'country_id' => 1
        ],
        [
            'id' => 26,
            'acronyms' => 'SE',
            'name' => 'Sergipe',
            'country_id' => 1],
        [
            'id' => 27,
            'acronyms' => 'TO',
            'name' => 'Tocantins',
            'country_id' => 1
        ],
    ];
}
