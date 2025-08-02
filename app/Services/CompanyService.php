<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyService
{
    /**
     * Get all companies with optional pagination
     */
    public function getAllCompanies(int $perPage = 10): LengthAwarePaginator
    {
        return Company::paginate($perPage);
    }

    /**
     * Get a single company by ID
     */
    public function getCompanyById(int $id): ?Company
    {
        return Company::find($id);
    }

    /**
     * Create a new company
     */
    public function createCompany(array $data): Company
    {
        return Company::create($data);
    }

    /**
     * Update an existing company
     */
    public function updateCompany(int $id, array $data): ?Company
    {
        $company = Company::find($id);

        if ($company) {
            $company->update($data);
            return $company->fresh();
        }

        return null;
    }

    /**
     * Delete a company
     */
    public function deleteCompany(int $id): bool
    {
        $company = Company::find($id);

        if ($company) {
            return $company->delete();
        }

        return false;
    }

    /**
     * Check if company exists
     */
    public function companyExists(int $id): bool
    {
        return Company::where('id', $id)->exists();
    }
}
