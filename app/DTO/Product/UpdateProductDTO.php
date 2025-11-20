<?php
// app/DTO/UpdateProductDTO.php
namespace App\DTO\Product;

use App\Http\Requests\Product\ProductUpdateRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateProductDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public float $price,
        public string $categoryId,
        public int $quantity,
        public ?string $slug = null,
        public ?string $description = null,
        public ?float $costPrice = null,
        public ?int $minQuantity = null,
        public ?bool $active = null,
        public array $tags = []
    ) {}

    public static function fromRequest(ProductUpdateRequest $request, string $productId): self
    {
        return new self(
            id: $productId,
            name: $request->name,
            price: (float) $request->price,
            categoryId: $request->category_id,
            quantity: (int) $request->quantity,
            slug: $request->slug ?? Str::slug($request->name),
            description: $request->description,
            costPrice: $request->has('cost_price') ? (float) $request->cost_price : null,
            minQuantity: $request->has('min_quantity') ? (int) $request->min_quantity : null,
            active: $request->has('active') ? (bool) $request->active : null,
            tags: $request->tags ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'cost_price' => $this->costPrice,
            'quantity' => $this->quantity,
            'min_quantity' => $this->minQuantity,
            'active' => $this->active,
            'category_id' => $this->categoryId,
        ], fn($value) => !is_null($value));
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function hasTags(): bool
    {
        return !empty($this->tags);
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

        if ($this->hasTags() && count($this->tags) > 10) {
            throw ValidationException::withMessages([
                'Um produto não pode ter mais de 10 tags'
            ]);
        }
    }
}