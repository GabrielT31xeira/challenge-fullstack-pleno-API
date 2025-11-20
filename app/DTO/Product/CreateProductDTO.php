<?php

namespace App\DTO\Product;

use App\Http\Requests\Product\ProductStoreRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateProductDTO
{
    public function __construct(
        public string $name,
        public float $price,
        public string $categoryId,
        public array $tags = [],
        public int $quantity,
        public ?string $slug = null,
        public ?string $description = null,
        public ?float $costPrice = null,
        public ?int $minQuantity = null,
        public bool $active = true
    ) {}

    public static function fromRequest(ProductStoreRequest $request): self
    {
        return new self(
            name: $request->name,
            price: (float) $request->price,
            categoryId: $request->category_id,
            tags: $request->tags ?? [],
            quantity: (int) $request->quantity,
            slug: $request->slug ?? Str::slug($request->name),
            description: $request->description,
            costPrice: $request->has('cost_price') ? (float) $request->cost_price : null,
            minQuantity: $request->has('min_quantity') ? (int) $request->min_quantity : null,
            active: (bool) $request->get('active', true)
        );
    }

    public function getTags(): array
    {
        return $this->tags;
    }
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'cost_price' => $this->costPrice,
            'quantity' => $this->quantity,
            'min_quantity' => $this->minQuantity,
            'active' => $this->active,
            'category_id' => $this->categoryId,
        ];
    }

    public function validateBusinessRules(): void
    {
        if ($this->costPrice && $this->costPrice > ($this->price * 0.8)) {
            throw ValidationException::withMessages([
                'O custo não pode ser superior a 80% do preço de venda'
            ]);
        }

        if ($this->minQuantity && $this->minQuantity > $this->quantity) {
            throw ValidationException::withMessages([
                'A quantidade mínima não pode ser maior que a quantidade em estoque'
            ]);
        }
    }
}