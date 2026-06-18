<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'institution_id',
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'role_id',
        'specialty_id',
        'is_active',
        'mfa_enabled',
        'mfa_secret',
        'email_verified_at',
        'last_login',
        'failed_attempts',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'locked_until' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'mfa_enabled' => 'boolean',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'institution_id' => $this->institution_id,
            'role' => $this->role?->slug,
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'doctor_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function loginHistory(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function evolutions(): HasMany
    {
        return $this->hasMany(Evolution::class, 'doctor_id');
    }

    public function vitalSigns(): HasMany
    {
        return $this->hasMany(VitalSign::class, 'recorded_by');
    }

    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class, 'doctor_id');
    }

    public function imagingOrders(): HasMany
    {
        return $this->hasMany(ImagingOrder::class, 'doctor_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'doctor_id');
    }

    public function telemedicineSessions(): HasMany
    {
        return $this->hasMany(TelemedicineSession::class, 'doctor_id');
    }

    public function telemedicineMessages(): HasMany
    {
        return $this->hasMany(TelemedicineMessage::class, 'sender_id');
    }

    public function doctorSchedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id');
    }

    public function appointmentSlots(): HasMany
    {
        return $this->hasMany(AppointmentSlot::class, 'doctor_id');
    }

    public function hospitalizations(): HasMany
    {
        return $this->hasMany(Hospitalization::class, 'doctor_id');
    }

    public function hospitalizationEvolutions(): HasMany
    {
        return $this->hasMany(HospitalizationEvolution::class, 'doctor_id');
    }

    public function bedTransfers(): HasMany
    {
        return $this->hasMany(BedTransfer::class, 'transferred_by');
    }

    public function surgerySchedules(): HasMany
    {
        return $this->hasMany(SurgerySchedule::class, 'doctor_id');
    }

    public function surgeryTeamMemberships(): HasMany
    {
        return $this->hasMany(SurgeryTeam::class, 'user_id');
    }

    public function surgeryReports(): HasMany
    {
        return $this->hasMany(SurgeryReport::class, 'performed_by');
    }

    public function emergencyTriages(): HasMany
    {
        return $this->hasMany(EmergencyTriage::class, 'doctor_id');
    }

    public function nursingNotes(): HasMany
    {
        return $this->hasMany(NursingNote::class, 'nurse_id');
    }

    public function treatmentPlans(): HasMany
    {
        return $this->hasMany(TreatmentPlan::class, 'doctor_id');
    }

    public function clinicalNotes(): HasMany
    {
        return $this->hasMany(ClinicalNote::class);
    }

    public function clinicalAlerts(): HasMany
    {
        return $this->hasMany(ClinicalAlert::class);
    }

    public function sentCommunications(): HasMany
    {
        return $this->hasMany(Communication::class, 'sender_id');
    }

    public function receivedCommunications(): HasMany
    {
        return $this->hasMany(Communication::class, 'recipient_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function dashboardWidgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class);
    }

    public function referralLettersAsFrom(): HasMany
    {
        return $this->hasMany(ReferralLetter::class, 'from_doctor_id');
    }

    public function referralLettersAsTo(): HasMany
    {
        return $this->hasMany(ReferralLetter::class, 'to_doctor_id');
    }

    public function consultationTemplates(): HasMany
    {
        return $this->hasMany(ConsultationTemplate::class, 'created_by');
    }

    public function reportTemplates(): HasMany
    {
        return $this->hasMany(ReportTemplate::class, 'created_by');
    }

    public function savedReports(): HasMany
    {
        return $this->hasMany(SavedReport::class, 'generated_by');
    }

    public function medicalRecordAttachments(): HasMany
    {
        return $this->hasMany(MedicalRecordAttachment::class, 'uploaded_by');
    }

    public function patientVitalsHistory(): HasMany
    {
        return $this->hasMany(PatientVitalsHistory::class, 'recorded_by');
    }

    public function passwordHistory(): HasMany
    {
        return $this->hasMany(PasswordHistory::class);
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    public function labSpecimens(): HasMany
    {
        return $this->hasMany(LabSpecimen::class, 'collected_by');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'performed_by');
    }

    public function medicationDispensations(): HasMany
    {
        return $this->hasMany(MedicationDispensation::class, 'dispensed_by');
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class, 'performed_by');
    }

    public function cashArrears(): HasMany
    {
        return $this->hasMany(CashArrear::class, 'performed_by');
    }

    public function discountAuthorizations(): HasMany
    {
        return $this->hasMany(DiscountAuthorization::class, 'authorized_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'received_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeDoctors($query)
    {
        return $query->whereHas('role', fn ($q) => $q->where('slug', 'doctor'));
    }

    public function scopeNurses($query)
    {
        return $query->whereHas('role', fn ($q) => $q->where('slug', 'nurse'));
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (User $user) {
            if (is_null($user->institution_id) && auth()->check()) {
                $user->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
