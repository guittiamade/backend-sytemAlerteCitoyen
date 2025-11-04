<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profile;
use App\Models\Direction;
use App\Models\TypeAlerte;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            ['nom' => 'citoyen', 'description' => 'Citoyen pouvant soumettre des signalements'],
            ['nom' => 'gestionnaire', 'description' => 'Agent qui vérifie et redirige'],
            ['nom' => 'direction', 'description' => 'Service technique en charge du traitement'],
            ['nom' => 'super_admin', 'description' => 'Administrateur du système'],
        ];
        foreach ($roles as $role) {
            Profile::firstOrCreate(['nom' => $role['nom']], $role);
        }

        $directions = [
            ['description' => 'Voirie et Assainissement', 'direction_generale' => 'DGST'],
            ['description' => 'Hygiène et Salubrité', 'direction_generale' => 'DGST'],
            ['description' => 'Sécurité et Police Municipale', 'direction_generale' => 'DGSP'],
        ];
        foreach ($directions as $d) {
            Direction::firstOrCreate(['description' => $d['description']], $d);
        }

        $types = [
            ['nom' => 'Déchets', 'description' => 'Tas d’ordures, insalubrités'],
            ['nom' => 'Voirie', 'description' => 'Nids de poule, chaussée dégradée'],
            ['nom' => 'Sécurité', 'description' => 'Actes d’incivisme, danger public'],
        ];
        foreach ($types as $t) {
            TypeAlerte::firstOrCreate(['nom' => $t['nom']], $t);
        }

        // Sample user
        $citoyen = User::firstOrCreate(
            ['email' => 'citoyen@example.com'],
            [
                'name' => 'Citoyen Démo',
                'password' => bcrypt('password'),
                'profile_id' => Profile::where('nom', 'citoyen')->value('id'),
            ]
        );

        // Super admin web login
        User::firstOrCreate(
            ['email' => 'admin@demo.local'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'profile_id' => Profile::where('nom', 'super_admin')->value('id'),
            ]
        );
    }
}
