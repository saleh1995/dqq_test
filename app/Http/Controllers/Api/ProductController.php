<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Traits\ApiResponseTrait;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use ApiResponseTrait;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $products = $this->productService->getAllProducts($perPage);

            return $this->apiResponse(
                ProductResource::collection($products)->resource,
                'Products retrieved successfully',
                200,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $product = $this->productService->createProduct($validatedData);

            return $this->apiResponse(
                new ProductResource($product),
                'Product created successfully',
                201,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                return $this->apiResponseError(
                    null,
                    'Product not found',
                    404
                );
            }

            return $this->apiResponse(
                new ProductResource($product),
                'Product retrieved successfully',
                200,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                return $this->apiResponseError(
                    null,
                    'Product not found',
                    404
                );
            }

            $validatedData = $request->validated();

            $updatedProduct = $this->productService->updateProduct($product, $validatedData);

            return $this->apiResponse(
                new ProductResource($updatedProduct),
                'Product updated successfully',
                200,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                return $this->apiResponseError(
                    null,
                    'Product not found',
                    404
                );
            }

            $this->productService->deleteProduct($product);

            return $this->apiResponse(
                null,
                'Product deleted successfully',
                200,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }
}
