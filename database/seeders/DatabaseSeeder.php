<?php

namespace Database\Seeders;

use App\Services\NodeService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(NodeService $nodes): void
    {
        // Sucursal de prueba
        $branch = $nodes->createNode([
            'name'           => 'Sucursal Centro',
            'type'           => 'branch',
            'location'       => 'Av. Juárez 123, CDMX',
            'cash_available' => 500000,
            'responsible'    => [
                'full_name' => 'Juan Pérez',
                'email'     => 'juan@banco.test',
            ],
        ]);
        $this->command->info("Branch creada. API Key: {$branch['api_key']}");

        // ATM de prueba
        $atm = $nodes->createNode([
            'name'           => 'ATM Plaza',
            'type'           => 'atm',
            'location'       => 'Plaza Comercial',
            'cash_available' => 100000,
            'responsible'    => [
                'full_name' => 'María López',
                'email'     => 'maria@banco.test',
            ],
        ]);
        $this->command->info("ATM creado. API Key: {$atm['api_key']}");
    }
}