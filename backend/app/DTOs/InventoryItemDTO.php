<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\InventoryItemType;
use App\Enums\InventoryItemStatus;

readonly class InventoryItemDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public string $name,
        public string $code,
        public InventoryItemType $type = InventoryItemType::SUPPLIES,
        public InventoryItemStatus $status = InventoryItemStatus::ACTIVE,
        public ?string $description = null,
        public ?string $category = null,
        public ?string $unit = null,
        public ?float $unitCost = null,
        public ?int $currentStock = null,
        public ?int $minimumStock = null,
        public ?int $maximumStock = null,
        public ?string $location = null,
        public ?int $supplierId = null,
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
            code: $data['code'],
            type: InventoryItemType::from($data['type'] ?? 'supplies'),
            status: InventoryItemStatus::from($data['status'] ?? 'active'),
            description: $data['description'] ?? null,
            category: $data['category'] ?? null,
            unit: $data['unit'] ?? null,
            unitCost: isset($data['unit_cost']) ? (float) $data['unit_cost'] : null,
            currentStock: $data['current_stock'] ?? null,
            minimumStock: $data['minimum_stock'] ?? null,
            maximumStock: $data['maximum_stock'] ?? null,
            location: $data['location'] ?? null,
            supplierId: $data['supplier_id'] ?? null,
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
            'code' => $this->code,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'description' => $this->description,
            'category' => $this->category,
            'unit' => $this->unit,
            'unit_cost' => $this->unitCost,
            'current_stock' => $this->currentStock,
            'minimum_stock' => $this->minimumStock,
            'maximum_stock' => $this->maximumStock,
            'location' => $this->location,
            'supplier_id' => $this->supplierId,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
