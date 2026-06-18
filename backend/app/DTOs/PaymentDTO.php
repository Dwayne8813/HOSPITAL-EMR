<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;

readonly class PaymentDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $invoiceId,
        public int $patientId,
        public string $paymentNumber,
        public float $amount,
        public PaymentMethod $method,
        public PaymentStatus $status = PaymentStatus::COMPLETED,
        public ?string $reference = null,
        public ?string $notes = null,
        public ?int $processedBy = null,
        public ?\DateTimeImmutable $paymentDate = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            institutionId: $data['institution_id'],
            invoiceId: $data['invoice_id'],
            patientId: $data['patient_id'],
            paymentNumber: $data['payment_number'],
            amount: (float) $data['amount'],
            method: PaymentMethod::from($data['method']),
            status: PaymentStatus::from($data['status'] ?? 'completed'),
            reference: $data['reference'] ?? null,
            notes: $data['notes'] ?? null,
            processedBy: $data['processed_by'] ?? null,
            paymentDate: isset($data['payment_date']) ? new \DateTimeImmutable($data['payment_date']) : null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'institution_id' => $this->institutionId,
            'invoice_id' => $this->invoiceId,
            'patient_id' => $this->patientId,
            'payment_number' => $this->paymentNumber,
            'amount' => $this->amount,
            'method' => $this->method->value,
            'status' => $this->status->value,
            'reference' => $this->reference,
            'notes' => $this->notes,
            'processed_by' => $this->processedBy,
            'payment_date' => $this->paymentDate?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
