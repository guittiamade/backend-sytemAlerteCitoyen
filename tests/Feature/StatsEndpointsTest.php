<?php

namespace Tests\Feature;

use App\Models\Alerte;
use App\Models\Direction;
use App\Models\Profile;
use App\Models\TypeAlerte;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StatsEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function makeProfiles(): void
    {
        foreach (['citoyen','gestionnaire','direction','super_admin'] as $idx => $name) {
            Profile::create(['id' => $idx + 1, 'nom' => $name]);
        }
    }

    private function makeType(string $nom = 'Voirie'): TypeAlerte
    {
        return TypeAlerte::create(['nom' => $nom, 'description' => null]);
    }

    public function test_citoyen_stats_returns_counts(): void
    {
        $this->makeProfiles();
        $type = $this->makeType();

        $citoyen = User::create([
            'name' => 'Citoyen 1',
            'email' => 'citoyen@example.com',
            'password' => bcrypt('password'),
            'profile_id' => Profile::where('nom','citoyen')->value('id'),
        ]);

        // Autres utilisateurs pour bruit
        $other = User::create([
            'name' => 'Autre',
            'email' => 'autre@example.com',
            'password' => bcrypt('password'),
            'profile_id' => Profile::where('nom','citoyen')->value('id'),
        ]);

        // Créer des alertes pour le citoyen
        Alerte::create(['titre' => 'A1', 'type_alerte_id' => $type->id, 'citoyen_id' => $citoyen->id, 'statut' => 'en_attente']);
        Alerte::create(['titre' => 'A2', 'type_alerte_id' => $type->id, 'citoyen_id' => $citoyen->id, 'statut' => 'en_cours']);
        Alerte::create(['titre' => 'A3', 'type_alerte_id' => $type->id, 'citoyen_id' => $citoyen->id, 'statut' => 'termine']);
        // Bruit: alerte d'un autre citoyen
        Alerte::create(['titre' => 'B1', 'type_alerte_id' => $type->id, 'citoyen_id' => $other->id, 'statut' => 'en_attente']);

        Sanctum::actingAs($citoyen);
        $res = $this->getJson('/api/citoyen/stats')->assertOk()->json();
        $this->assertSame(3, $res['total']);
        $this->assertSame(1, $res['en_attente']);
        $this->assertSame(1, $res['en_cours']);
        $this->assertSame(1, $res['termine']);
    }

    public function test_gestionnaire_stats_returns_counts(): void
    {
        $this->makeProfiles();
        $type = $this->makeType();

        $gestionnaire = User::create([
            'name' => 'Gestionnaire 1',
            'email' => 'gest@example.com',
            'password' => bcrypt('password'),
            'profile_id' => Profile::where('nom','gestionnaire')->value('id'),
        ]);

        $citoyen = User::create([
            'name' => 'Citoyen 2',
            'email' => 'cit2@example.com',
            'password' => bcrypt('password'),
            'profile_id' => Profile::where('nom','citoyen')->value('id'),
        ]);

        Alerte::create(['titre' => 'G1', 'type_alerte_id' => $type->id, 'citoyen_id' => $citoyen->id, 'gestionnaire_id' => $gestionnaire->id, 'statut' => 'en_cours']);
        Alerte::create(['titre' => 'G2', 'type_alerte_id' => $type->id, 'citoyen_id' => $citoyen->id, 'gestionnaire_id' => $gestionnaire->id, 'statut' => 'termine']);
        // Bruit: assignée à un autre gestionnaire
        $otherGest = User::create([
            'name' => 'Gestionnaire 2',
            'email' => 'gest2@example.com',
            'password' => bcrypt('password'),
            'profile_id' => Profile::where('nom','gestionnaire')->value('id'),
        ]);
        Alerte::create(['titre' => 'G3', 'type_alerte_id' => $type->id, 'citoyen_id' => $citoyen->id, 'gestionnaire_id' => $otherGest->id, 'statut' => 'en_cours']);

        Sanctum::actingAs($gestionnaire);
        $res = $this->getJson('/api/gestionnaire/stats')->assertOk()->json();
        $this->assertSame(2, $res['assignes']);
        $this->assertSame(1, $res['en_cours']);
        $this->assertSame(1, $res['termine']);
    }

    public function test_direction_stats_returns_counts_and_handles_null(): void
    {
        $this->makeProfiles();
        $type = $this->makeType();
        $direction = Direction::create(['description' => 'Voirie']);

        $directionUser = User::create([
            'name' => 'Direction 1',
            'email' => 'dir@example.com',
            'password' => bcrypt('password'),
            'profile_id' => Profile::where('nom','direction')->value('id'),
            'direction_id' => $direction->id,
        ]);

        $citoyen = User::create([
            'name' => 'Citoyen 3',
            'email' => 'cit3@example.com',
            'password' => bcrypt('password'),
            'profile_id' => Profile::where('nom','citoyen')->value('id'),
        ]);

        Alerte::create(['titre' => 'D1', 'type_alerte_id' => $type->id, 'citoyen_id' => $citoyen->id, 'direction_id' => $direction->id, 'statut' => 'en_cours']);
        Alerte::create(['titre' => 'D2', 'type_alerte_id' => $type->id, 'citoyen_id' => $citoyen->id, 'direction_id' => $direction->id, 'statut' => 'termine']);

        Sanctum::actingAs($directionUser);
        $res = $this->getJson('/api/direction/stats')->assertOk()->json();
        $this->assertSame(2, $res['receptionnes']);
        $this->assertSame(1, $res['en_cours']);
        $this->assertSame(1, $res['termine']);

        // Cas null direction_id
        $directionUser->direction_id = null;
        $directionUser->save();
        Sanctum::actingAs($directionUser);
        $res2 = $this->getJson('/api/direction/stats')->assertOk()->json();
        $this->assertSame(0, $res2['receptionnes']);
        $this->assertSame(0, $res2['en_cours']);
        $this->assertSame(0, $res2['termine']);
    }

    public function test_admin_stats_returns_counts(): void
    {
        $this->makeProfiles();
        $type = $this->makeType();

        // Quelques alertes globales
        $cit = User::create([
            'name' => 'C', 'email' => 'c@example.com', 'password' => bcrypt('password'),
            'profile_id' => Profile::where('nom','citoyen')->value('id'),
        ]);
        Alerte::create(['titre' => 'A', 'type_alerte_id' => $type->id, 'citoyen_id' => $cit->id, 'statut' => 'en_attente']);
        Alerte::create(['titre' => 'B', 'type_alerte_id' => $type->id, 'citoyen_id' => $cit->id, 'statut' => 'en_cours']);
        Alerte::create(['titre' => 'C', 'type_alerte_id' => $type->id, 'citoyen_id' => $cit->id, 'statut' => 'termine']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'profile_id' => Profile::where('nom','super_admin')->value('id'),
        ]);

        Sanctum::actingAs($admin);
        $res = $this->getJson('/api/admin/stats')->assertOk()->json();
        $this->assertSame(3, $res['total']);
        $this->assertSame(1, $res['en_attente']);
        $this->assertSame(1, $res['en_cours']);
        $this->assertSame(1, $res['termine']);
    }
}
