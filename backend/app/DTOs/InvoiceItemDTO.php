<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class InvoiceItemDTO
{
    public function __construct(
        public int $id,
        public int $invoiceId,
        public string $description,
        public int $quantity,
        public float $unitPrice,
        public float $totalPrice,
        public ?string $serviceCode = null,
        public ?float $discount = null,
        public ?float $taxRate = null,
        public ?\DateTimeImmutable $createdAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            invoiceId: $data['invoice_id'],
            description: $data['description'],
            quantity: $data['quantity'],
            unitPrice: (float) $data['unit_price'],
            totalPrice: (float) $data['total_price'],
            serviceCode: $data['service_code'] ?? null,
            discount: isset($data['discount']) ? (float) $data['discount'] : null,
            taxRate: isset($data['tax_rate']) ? (float) $data['tax_rate'] : null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoiceId,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'total_price' => $this->totalPrice,
            'service_code' => $this->serviceCode,
            'discount' => $this->discount,
            'tax_rate' => $this->taxRate,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
