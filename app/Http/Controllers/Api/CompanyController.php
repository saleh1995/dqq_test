<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Traits\ApiResponseTrait;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CompanyController extends Controller
{
    use ApiResponseTrait;

    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $companies = $this->companyService->getAllCompanies($perPage);

            return $this->apiResponse(
                CompanyResource::collection($companies)->resource,
                'Companies retrieved successfully',
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
    public function store(CompanyRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Check if company with same name already exists
            if (Company::where('name', $validatedData['name'])->exists()) {
                return $this->apiResponseError(
                    null,
                    'A company with this name already exists',
                    422
                );
            }

            $company = $this->companyService->createCompany($validatedData);

            return $this->apiResponse(
                new CompanyResource($company),
                'Company created successfully',
                201,
                true
            );
        } catch (ValidationException $e) {
            return $this->apiResponseException($e);
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
            $company = $this->companyService->getCompanyById($id);

            if (!$company) {
                return $this->apiResponseError(
                    null,
                    'Company not found',
                    404
                );
            }

            return $this->apiResponse(
                new CompanyResource($company),
                'Company retrieved successfully',
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
    public function update(CompanyRequest $request, string $id)
    {
        try {
            // Check if company exists
            if (!$this->companyService->companyExists($id)) {
                return $this->apiResponseError(
                    null,
                    'Company not found',
                    404
                );
            }

            $validatedData = $request->validated();

            // Check if company with same name already exists (excluding current company)
            if (Company::where('name', $validatedData['name'])->where('id', '!=', $id)->exists()) {
                return $this->apiResponseError(
                    null,
                    'A company with this name already exists',
                    422
                );
            }

            $company = $this->companyService->updateCompany($id, $validatedData);

            return $this->apiResponse(
                new CompanyResource($company),
                'Company updated successfully',
                200,
                true
            );
        } catch (ValidationException $e) {
            return $this->apiResponseException($e);
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
            // Check if company exists
            if (!$this->companyService->companyExists($id)) {
                return $this->apiResponseError(
                    null,
                    'Company not found',
                    404
                );
            }

            $deleted = $this->companyService->deleteCompany($id);

            if ($deleted) {
                return $this->apiResponse(
                    null,
                    'Company deleted successfully',
                    200,
                    true
                );
            } else {
                return $this->apiResponseError(
                    null,
                    'Failed to delete company',
                    500
                );
            }
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }
}
