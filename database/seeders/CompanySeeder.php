<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            'Tech Solutions Inc.',
            'Global Manufacturing Co.',
            'Digital Innovations Ltd.',
            'Green Energy Corp.',
            'Healthcare Systems LLC',
            'Financial Services Group',
            'Retail Solutions International',
            'Transportation & Logistics Co.',
            'Education Technology Partners',
            'Food & Beverage Industries',
        ];

        foreach ($companies as $companyName) {
            Company::updateOrCreate(
                ['name' => $companyName],
                ['name' => $companyName]
            );
        }
    }
}
