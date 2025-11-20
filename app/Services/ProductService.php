<?php

namespace App\Services;

use App\DTO\Product\CreateProductDTO;
use App\DTO\Product\UpdateProductDTO;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Tag\TagRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function __construct(
        protected ProductRepository $productsRepository,
        protected TagRepository $tagsRepository,
    ) {}

    public function list(array $filters)
    {
        return $this->productsRepository->paginate($filters);
    }

    public function show(string $id)
    {
        return $this->productsRepository->find($id);
    }

    public function create(CreateProductDTO $dto)
    {
        $product = $this->productsRepository->create($dto->toArray());

        if (!empty($dto->getTags())) {
            $this->associateTags($product, $dto->getTags());
        }
        return $product->load(['category', 'tags']);
    }

    private function associateTags($product, array $tagIds): void
    {
        $existingTags = $this->tagsRepository->findMany($tagIds);

        if (count($existingTags) !== count($tagIds)) {
            throw ValidationException::withMessages(['Uma ou mais tags não foram encontradas']);
        }

        $product->tags()->sync($tagIds);
    }

    public function update(UpdateProductDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $product = $this->productsRepository->find($dto->id);
            if (!$product) {
                throw ValidationException::withMessages(['Produto não encontrado']);
            }

            if ($dto->slug && $this->productsRepository->slugExists($dto->slug, $dto->id)) {
                throw ValidationException::withMessages(['Produto não encontrado']);
            }

            $this->productsRepository->update($dto->id, $dto->toArray());

            if ($dto->hasTags()) {
                $this->associateTags($product, $dto->getTags());
            }

            return $this->productsRepository->findWithRelations($dto->id);
        });
    }

    public function delete(string $id)
    {
        return $this->productsRepository->delete($id);
    }
}
