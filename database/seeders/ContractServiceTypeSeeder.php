<?php

namespace Database\Seeders;

use App\Models\ContractServiceType;
use Illuminate\Database\Seeder;

class ContractServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Održavanje IT sustava',
                'slug' => 'it_system_maintenance',
                'description' => 'Računala, mreža, infrastruktura, osnovni IT sustav klijenta.',
                'sort_order' => 10,
            ],
            [
                'name' => 'Udaljena ili on-prem podrška',
                'slug' => 'remote_or_onsite_support',
                'description' => 'Udaljena i/ili terenska podrška za korisnike i sustave.',
                'sort_order' => 20,
            ],
            [
                'name' => 'Održavanje internet stranica',
                'slug' => 'website_maintenance',
                'description' => 'Ažuriranja, nadzor i održavanje web stranica.',
                'sort_order' => 30,
            ],
            [
                'name' => 'Održavanje web ili mail servera',
                'slug' => 'web_or_mail_server_maintenance',
                'description' => 'Administracija i održavanje web i mail poslužitelja.',
                'sort_order' => 40,
            ],
            [
                'name' => 'MS 365 administracija',
                'slug' => 'm365_administration',
                'description' => 'Administracija Microsoft 365 okruženja, korisnika i licenci.',
                'sort_order' => 50,
            ],
        ];

        foreach ($items as $item) {
            ContractServiceType::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ]
            );
        }
    }
}