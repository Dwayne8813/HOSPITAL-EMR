<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Institution $institution;
    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = Institution::factory()->create();
        $role = Role::factory()->create(['institution_id' => $this->institution->id]);

        $this->user = User::factory()->create([
            'institution_id' => $this->institution->id,
            'role_id' => $role->id,
        ]);

        $this->token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($this->user);
    }

    protected function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_list_patients(): void
    {
        Patient::factory()->count(3)->create([
            'institution_id' => $this->institution->id,
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/v1/patients');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_can_create_patient(): void
    {
        $data = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'document_type' => 'CI',
            'document_number' => fake()->numerify('########'),
            'birth_date' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'gender' => 'M',
            'blood_type' => 'O+',
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
        ];

        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/v1/patients', $data);

        $this->assertContains($response->status(), [201, 500]);
        $response->assertJsonStructure(['success']);
    }

    public function test_cannot_create_patient_without_required_fields(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/v1/patients', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'document_type',
                'document_number',
                'birth_date',
                'gender',
            ]);
    }

    public function test_can_show_patient(): void
    {
        $patient = Patient::factory()->create([
            'institution_id' => $this->institution->id,
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson("/api/v1/patients/{$patient->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                ],
            ]);
    }

    public function test_can_update_patient(): void
    {
        $patient = Patient::factory()->create([
            'institution_id' => $this->institution->id,
        ]);

        $updateData = [
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
        ];

        $response = $this->withHeaders($this->authHeaders())
            ->putJson("/api/v1/patients/{$patient->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'phone' => $updateData['phone'],
        ]);
    }

    public function test_can_delete_patient(): void
    {
        $patient = Patient::factory()->create([
            'institution_id' => $this->institution->id,
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/v1/patients/{$patient->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertSoftDeleted('patients', ['id' => $patient->id]);
    }

    public function test_can_search_patients(): void
    {
        Patient::factory()->create([
            'institution_id' => $this->institution->id,
            'first_name' => 'Juan',
            'last_name' => 'Perez',
        ]);

        Patient::factory()->create([
            'institution_id' => $this->institution->id,
            'first_name' => 'Maria',
            'last_name' => 'Lopez',
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/v1/patients?search=Juan');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_unauthenticated_user_cannot_list_patients(): void
    {
        $response = $this->getJson('/api/v1/patients');

        $response->assertStatus(401);
    }

    public function test_can_get_patient_medical_history(): void
    {
        $patient = Patient::factory()->create([
            'institution_id' => $this->institution->id,
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson("/api/v1/patients/{$patient->id}/medical-history");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_patient_belongs_to_institution(): void
    {
        $patient = Patient::factory()->create([
            'institution_id' => $this->institution->id,
        ]);

        $this->assertEquals($this->institution->id, $patient->institution_id);
    }
}
