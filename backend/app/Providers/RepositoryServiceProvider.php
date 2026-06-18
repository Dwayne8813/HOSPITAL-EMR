<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Interfaces\PatientRepositoryInterface;
use App\Repositories\Interfaces\MedicalRecordRepositoryInterface;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;
use App\Repositories\Interfaces\ConsultationRepositoryInterface;
use App\Repositories\Interfaces\SoapNoteRepositoryInterface;
use App\Repositories\Interfaces\EvolutionRepositoryInterface;
use App\Repositories\Interfaces\VitalSignRepositoryInterface;
use App\Repositories\Interfaces\DiagnosisRepositoryInterface;
use App\Repositories\Interfaces\PrescriptionRepositoryInterface;
use App\Repositories\Interfaces\LabOrderRepositoryInterface;
use App\Repositories\Interfaces\LabOrderItemRepositoryInterface;
use App\Repositories\Interfaces\ImagingOrderRepositoryInterface;
use App\Repositories\Interfaces\MedicationRepositoryInterface;
use App\Repositories\Interfaces\MedicationBatchRepositoryInterface;
use App\Repositories\Interfaces\MedicationDispensationRepositoryInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface;
use App\Repositories\Interfaces\InventoryItemRepositoryInterface;
use App\Repositories\Interfaces\InventoryMovementRepositoryInterface;
use App\Repositories\Interfaces\BuildingRepositoryInterface;
use App\Repositories\Interfaces\RoomRepositoryInterface;
use App\Repositories\Interfaces\BedRepositoryInterface;
use App\Repositories\Interfaces\HospitalizationRepositoryInterface;
use App\Repositories\Interfaces\SurgeryScheduleRepositoryInterface;
use App\Repositories\Interfaces\EmergencyTriageRepositoryInterface;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\ServiceCatalogRepositoryInterface;
use App\Repositories\Interfaces\CashRegisterRepositoryInterface;
use App\Repositories\Interfaces\CertificateRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\DoctorScheduleRepositoryInterface;
use App\Repositories\Interfaces\InstitutionRepositoryInterface;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;

use App\Repositories\Eloquent\EloquentPatientRepository;
use App\Repositories\Eloquent\EloquentMedicalRecordRepository;
use App\Repositories\Eloquent\EloquentAppointmentRepository;
use App\Repositories\Eloquent\EloquentConsultationRepository;
use App\Repositories\Eloquent\EloquentSoapNoteRepository;
use App\Repositories\Eloquent\EloquentEvolutionRepository;
use App\Repositories\Eloquent\EloquentVitalSignRepository;
use App\Repositories\Eloquent\EloquentDiagnosisRepository;
use App\Repositories\Eloquent\EloquentPrescriptionRepository;
use App\Repositories\Eloquent\EloquentLabOrderRepository;
use App\Repositories\Eloquent\EloquentLabOrderItemRepository;
use App\Repositories\Eloquent\EloquentImagingOrderRepository;
use App\Repositories\Eloquent\EloquentMedicationRepository;
use App\Repositories\Eloquent\EloquentMedicationBatchRepository;
use App\Repositories\Eloquent\EloquentMedicationDispensationRepository;
use App\Repositories\Eloquent\EloquentSupplierRepository;
use App\Repositories\Eloquent\EloquentPurchaseOrderRepository;
use App\Repositories\Eloquent\EloquentInventoryItemRepository;
use App\Repositories\Eloquent\EloquentInventoryMovementRepository;
use App\Repositories\Eloquent\EloquentBuildingRepository;
use App\Repositories\Eloquent\EloquentRoomRepository;
use App\Repositories\Eloquent\EloquentBedRepository;
use App\Repositories\Eloquent\EloquentHospitalizationRepository;
use App\Repositories\Eloquent\EloquentSurgeryScheduleRepository;
use App\Repositories\Eloquent\EloquentEmergencyTriageRepository;
use App\Repositories\Eloquent\EloquentInvoiceRepository;
use App\Repositories\Eloquent\EloquentPaymentRepository;
use App\Repositories\Eloquent\EloquentServiceCatalogRepository;
use App\Repositories\Eloquent\EloquentCashRegisterRepository;
use App\Repositories\Eloquent\EloquentCertificateRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Eloquent\EloquentNotificationRepository;
use App\Repositories\Eloquent\EloquentDoctorScheduleRepository;
use App\Repositories\Eloquent\EloquentInstitutionRepository;
use App\Repositories\Eloquent\EloquentReportRepository;
use App\Repositories\Eloquent\EloquentAuditLogRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $bindings = [
            PatientRepositoryInterface::class => EloquentPatientRepository::class,
            MedicalRecordRepositoryInterface::class => EloquentMedicalRecordRepository::class,
            AppointmentRepositoryInterface::class => EloquentAppointmentRepository::class,
            ConsultationRepositoryInterface::class => EloquentConsultationRepository::class,
            SoapNoteRepositoryInterface::class => EloquentSoapNoteRepository::class,
            EvolutionRepositoryInterface::class => EloquentEvolutionRepository::class,
            VitalSignRepositoryInterface::class => EloquentVitalSignRepository::class,
            DiagnosisRepositoryInterface::class => EloquentDiagnosisRepository::class,
            PrescriptionRepositoryInterface::class => EloquentPrescriptionRepository::class,
            LabOrderRepositoryInterface::class => EloquentLabOrderRepository::class,
            LabOrderItemRepositoryInterface::class => EloquentLabOrderItemRepository::class,
            ImagingOrderRepositoryInterface::class => EloquentImagingOrderRepository::class,
            MedicationRepositoryInterface::class => EloquentMedicationRepository::class,
            MedicationBatchRepositoryInterface::class => EloquentMedicationBatchRepository::class,
            MedicationDispensationRepositoryInterface::class => EloquentMedicationDispensationRepository::class,
            SupplierRepositoryInterface::class => EloquentSupplierRepository::class,
            PurchaseOrderRepositoryInterface::class => EloquentPurchaseOrderRepository::class,
            InventoryItemRepositoryInterface::class => EloquentInventoryItemRepository::class,
            InventoryMovementRepositoryInterface::class => EloquentInventoryMovementRepository::class,
            BuildingRepositoryInterface::class => EloquentBuildingRepository::class,
            RoomRepositoryInterface::class => EloquentRoomRepository::class,
            BedRepositoryInterface::class => EloquentBedRepository::class,
            HospitalizationRepositoryInterface::class => EloquentHospitalizationRepository::class,
            SurgeryScheduleRepositoryInterface::class => EloquentSurgeryScheduleRepository::class,
            EmergencyTriageRepositoryInterface::class => EloquentEmergencyTriageRepository::class,
            InvoiceRepositoryInterface::class => EloquentInvoiceRepository::class,
            PaymentRepositoryInterface::class => EloquentPaymentRepository::class,
            ServiceCatalogRepositoryInterface::class => EloquentServiceCatalogRepository::class,
            CashRegisterRepositoryInterface::class => EloquentCashRegisterRepository::class,
            CertificateRepositoryInterface::class => EloquentCertificateRepository::class,
            UserRepositoryInterface::class => EloquentUserRepository::class,
            NotificationRepositoryInterface::class => EloquentNotificationRepository::class,
            DoctorScheduleRepositoryInterface::class => EloquentDoctorScheduleRepository::class,
            InstitutionRepositoryInterface::class => EloquentInstitutionRepository::class,
            ReportRepositoryInterface::class => EloquentReportRepository::class,
            AuditLogRepositoryInterface::class => EloquentAuditLogRepository::class,
        ];

        foreach ($bindings as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }
}
