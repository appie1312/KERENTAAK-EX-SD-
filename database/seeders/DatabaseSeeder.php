<?php

namespace Database\Seeders;

use App\Models\Bestelling;
use App\Models\Medewerker;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Lisa Jansen',
                'email' => 'eigenaar@kniplokettiko.nl',
                'role' => User::ROLE_OWNER,
            ],
            [
                'name' => 'Mila de Vries',
                'email' => 'medewerker@kniplokettiko.nl',
                'role' => User::ROLE_EMPLOYEE,
            ],
            [
                'name' => 'Sanne Bakker',
                'email' => 'klant@kniplokettiko.nl',
                'role' => User::ROLE_CUSTOMER,
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('Welkom123'),
                    'role' => $user['role'],
                ],
            );
        }

        Medewerker::query()->updateOrCreate(
            ['email' => 'mila@example.com'],
            [
                'name' => 'Mila de Vries',
                'role' => 'medewerker',
                'phone' => '0612345678',
            ],
        );

        Medewerker::query()->updateOrCreate(
            ['email' => 'daan@example.com'],
            [
                'name' => 'Daan Smit',
                'role' => 'medewerker',
                'phone' => '0687654321',
            ],
        );

        Medewerker::query()->updateOrCreate(
            ['email' => 'sara@example.com'],
            [
                'name' => 'Sara de Groot',
                'role' => 'medewerker',
                'phone' => '0611223344',
            ],
        );

        Medewerker::query()->updateOrCreate(
            ['email' => 'tim@example.com'],
            [
                'name' => 'Tim van den Berg',
                'role' => 'medewerker',
                'phone' => '0681122334',
            ],
        );

        Medewerker::query()->updateOrCreate(
            ['email' => 'julia@example.com'],
            [
                'name' => 'Julia Visser',
                'role' => Medewerker::ROLE_INTERN,
                'phone' => '0619988776',
            ],
        );

        Medewerker::query()->updateOrCreate(
            ['email' => 'noah@example.com'],
            [
                'name' => 'Noah de Wit',
                'role' => Medewerker::ROLE_INTERN,
                'phone' => '0613344556',
            ],
        );

        $bestellingen = [
            [
                'klant_naam' => 'Sanne Bakker',
                'orderdatum' => now()->subDays(2)->toDateString(),
                'verwachte_leverdatum' => now()->addDays(3)->toDateString(),
                'status' => 'Nieuw',
                'totaalprijs' => 125.50,
                'opmerking' => 'Behandelingen voor klant.',
                'is_actief' => true,
            ],
            [
                'klant_naam' => 'Lisa Jansen',
                'orderdatum' => now()->subDays(5)->toDateString(),
                'verwachte_leverdatum' => now()->subDays(1)->toDateString(),
                'status' => 'In behandeling',
                'totaalprijs' => 89.00,
                'opmerking' => 'Reeds bevestigd.',
                'is_actief' => true,
            ],
            [
                'klant_naam' => 'Daan Smit',
                'orderdatum' => now()->subDays(10)->toDateString(),
                'verwachte_leverdatum' => now()->addDays(1)->toDateString(),
                'status' => 'Afgerond',
                'totaalprijs' => 240.75,
                'opmerking' => 'Voltooide order.',
                'is_actief' => true,
            ],
        ];

        foreach ($bestellingen as $bestelling) {
            Bestelling::query()->updateOrCreate(
                [
                    'klant_naam' => $bestelling['klant_naam'],
                    'orderdatum' => $bestelling['orderdatum'],
                ],
                $bestelling,
            );
        }
    }
}
