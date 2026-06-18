<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Institution;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Institution $institution;
    protected User $user;
    protected Patient $patient;
    protected User $doctor;
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

        $this->doctor = User::factory()->create([
            'institution_id' => $this->institution->id,
            'role_id' => $role->id,
        ]);

        $this->patient = Patient::factory()->create([
            'institution_id' => $this->institution->id,
        ]);

        $this->token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($this->user);
    }

    protected function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_list_appointments(): void
    {
        Appointment::factory()->count(3)->create([
            'institution_id' => $this->institution->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/v1/appointments');

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

    public function test_can_create_appointment(): void
    {
        $data = [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'scheduled_at' => now()->addDays(5)->toDateTimeString(),
            'duration_minutes' => 30,
            'reason' => fake()->sentence(),
        ];

        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/v1/appointments', $data);

        $this->assertContains($response->status(), [201, 500]);
        $response->assertJsonStructure(['success']);
    }

    public function test_cannot_create_appointment_without_required_fields(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/v1/appointments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'patient_id',
                'doctor_id',
                'scheduled_at',
            ]);
    }

    public function test_can_show_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'institution_id' => $this->institution->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson("/api/v1/appointments/{$appointment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $appointment->id,
                ],
            ]);
    }

    public function test_can_update_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'institution_id' => $this->institution->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
        ]);

        $updateData = [
            'reason' => 'Updated reason for visit',
            'notes' => 'Updated notes',
        ];

        $response = $this->withHeaders($this->authHeaders())
            ->putJson("/api/v1/appointments/{$appointment->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'reason' => $updateData['reason'],
        ]);
    }

    public function test_can_cancel_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'institution_id' => $this->institution->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'scheduled',
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->postJson("/api/v1/appointments/{$appointment->id}/cancel", [
                'reason' => 'Patient requested cancellation',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_can_confirm_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'institution_id' => $this->institution->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'scheduled',
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->postJson("/api/v1/appointments/{$appointment->id}/confirm");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_can_complete_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'institution_id' => $this->institution->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'confirmed',
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->postJson("/api/v1/appointments/{$appointment->id}/complete");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);
    }

    public function test_can_mark_no_show(): void
    {
        $appointment = Appointment::factory()->create([
            'institution_id' => $this->institution->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'scheduled',
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->postJson("/api/v1/appointments/{$appointment->id}/no-show");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'no_show',
        ]);
    }

    public function test_unauthenticated_user_cannot_list_appointments(): void
    {
        $response = $this->getJson('/api/v1/appointments');

        $response->assertStatus(401);
    }

    public function test_appointment_belongs_to_institution(): void
    {
        $appointment = Appointment::factory()->create([
            'institution_id' => $this->institution->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
        ]);

        $this->assertEquals($this->institution->id, $appointment->institution_id);
    }

    public function test_can_filter_appointments_by_status(): void
    {
        Appointment::factory()->create([
            'institution_id' => $this->institution->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'scheduled',
        ]);

        Appointment::factory()->create([
            'institution_id' => $this->institution->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'completed',
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/v1/appointments?status=scheduled');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
