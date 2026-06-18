<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\InvoicePaymentStatus;

readonly class InvoiceDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $patientId,
        public ?int $appointmentId = null,
        public ?int $hospitalizationId = null,
        public string $invoiceNumber,
        public InvoicePaymentStatus $status = InvoicePaymentStatus::PENDING,
        public float $subtotal,
        public float $taxAmount,
        public float $discountAmount,
        public float $totalAmount,
        public float $paidAmount,
        public ?string $notes = null,
        public ?\DateTimeImmutable $dueDate = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
        /** @var InvoiceItemDTO[] */
        public array $items = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        $items = [];
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $items[] = InvoiceItemDTO::fromArray($item);
            }
        }

        return new self(
            id: $data['id'],
            institutionId: $data['institution_id'],
            patientId: $data['patient_id'],
            appointmentId: $data['appointment_id'] ?? null,
            hospitalizationId: $data['hospitalization_id'] ?? null,
            invoiceNumber: $data['invoice_number'],
            status: InvoicePaymentStatus::from($data['status'] ?? 'pending'),
            subtotal: (float) $data['subtotal'],
            taxAmount: (float) $data['tax_amount'],
            discountAmount: (float) $data['discount_amount'],
            totalAmount: (float) $data['total_amount'],
            paidAmount: (float) $data['paid_amount'],
            notes: $data['notes'] ?? null,
            dueDate: isset($data['due_date']) ? new \DateTimeImmutable($data['due_date']) : null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null,
            items: $items,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'institution_id' => $this->institutionId,
            'patient_id' => $this->patientId,
            'appointment_id' => $this->appointmentId,
            'hospitalization_id' => $this->hospitalizationId,
            'invoice_number' => $this->invoiceNumber,
            'status' => $this->status->value,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->taxAmount,
            'discount_amount' => $this->discountAmount,
            'total_amount' => $this->totalAmount,
            'paid_amount' => $this->paidAmount,
            'notes' => $this->notes,
            'due_date' => $this->dueDate?->format('Y-m-d'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'items' => array_map(fn(InvoiceItemDTO $item) => $item->toArray(), $this->items),
        ];
    }
}
