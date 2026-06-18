<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\MedicationStatus;

readonly class MedicationDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public string $name,
        public string $genericName,
        public ?string $category = null,
        public ?string $presentation = null,
        public ?string $manufacturer = null,
        public ?string $activeIngredient = null,
        public ?string $concentration = null,
        public ?string $administrationRoute = null,
        public ?float $unitPrice = null,
        public ?int $stockQuantity = null,
        public ?int $minimumStock = null,
        public ?string $unit = null,
        public MedicationStatus $status = MedicationStatus::ACTIVE,
        public ?string $notes = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            institutionId: $data['institution_id'],
            name: $data['name'],
            genericName: $data['generic_name'],
            category: $data['category'] ?? null,
            presentation: $data['presentation'] ?? null,
            manufacturer: $data['manufacturer'] ?? null,
            activeIngredient: $data['active_ingredient'] ?? null,
            concentration: $data['concentration'] ?? null,
            administrationRoute: $data['administration_route'] ?? null,
            unitPrice: isset($data['unit_price']) ? (float) $data['unit_price'] : null,
            stockQuantity: $data['stock_quantity'] ?? null,
            minimumStock: $data['minimum_stock'] ?? null,
            unit: $data['unit'] ?? null,
            status: MedicationStatus::from($data['status'] ?? 'active'),
            notes: $data['notes'] ?? null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'institution_id' => $this->institutionId,
            'name' => $this->name,
            'generic_name' => $this->genericName,
            'category' => $this->category,
            'presentation' => $this->presentation,
            'manufacturer' => $this->manufacturer,
            'active_ingredient' => $this->activeIngredient,
            'concentration' => $this->concentration,
            'administration_route' => $this->administrationRoute,
            'unit_price' => $this->unitPrice,
            'stock_quantity' => $this->stockQuantity,
            'minimum_stock' => $this->minimumStock,
            'unit' => $this->unit,
            'status' => $this->status->value,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
