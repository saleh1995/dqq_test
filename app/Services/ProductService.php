<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Get all products with pagination
     */
    public function getAllProducts(int $perPage = 10): LengthAwarePaginator
    {
        return Product::orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get product by ID
     */
    public function getProductById(string $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update an existing product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    /**
     * Delete a product
     */
    public function deleteProduct(Product $product): bool
    {
        return $product->delete();
    }
}
