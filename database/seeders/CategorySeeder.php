<?php

namespace Database\Seeders;

use App\Models\Tenants\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        Category::truncate();

        // Get sheet names from Excel file
        $excelFile = base_path('SISTEM.xlsx');

        if (file_exists($excelFile)) {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($excelFile);
            $sheetNames = $spreadsheet->getSheetNames();

            // Create categories from sheet names
            foreach ($sheetNames as $sheetName) {
                Category::create([
                    'name' => $sheetName
                ]);
            }
        } else {
            // Fallback to default category if Excel file doesn't exist
            Category::create([
                'name' => "UMUM"
            ]);
        }
    }
}
