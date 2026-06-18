<?php

namespace App\Repositories\Interfaces;

interface ReportRepositoryInterface
{
    public function getDashboardStats(int $institutionId): array;

    public function getPatientStats(int $institutionId, ?string $from = null, ?string $to = null): array;

    public function getAppointmentStats(int $institutionId, ?string $from = null, ?string $to = null): array;

    public function getRevenueStats(int $institutionId, ?string $from = null, ?string $to = null): array;

    public function getLabStats(int $institutionId, ?string $from = null, ?string $to = null): array;

    public function getPharmacyStats(int $institutionId, ?string $from = null, ?string $to = null): array;
}
