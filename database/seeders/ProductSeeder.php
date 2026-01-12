<?php

namespace Database\Seeders;

use App\Models\Tenants\Category;
use App\Models\Tenants\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductSeeder extends Seeder
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
        Product::truncate();

        // Get data from Excel file
        $excelFile = base_path('LIST_HARGA_BARANG_JUAL.xlsx');

        if (file_exists($excelFile)) {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($excelFile);
            $sheetNames = $spreadsheet->getSheetNames();

            // Read data from each sheet
            $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array) {
                    return $array;
                }
            }, $excelFile);

            foreach ($data as $sheetIndex => $sheetData) {
                $sheetName = $sheetNames[$sheetIndex];

                // Find the category for this sheet
                $category = Category::where('name', $sheetName)->first();

                if (!$category) {
                    continue;
                }

                // Skip the header row and process products
                foreach ($sheetData as $rowIndex => $row) {
                    // Skip header rows and empty rows
                    if ($rowIndex === 0 || empty($row[1]) || $row[1] === 'Nama Produk' || is_null($row[1])) {
                        continue;
                    }

                    $productName = trim($row[1]);

                    // Skip if product name is empty after trimming
                    if (empty($productName)) {
                        continue;
                    }

                    // Determine selling price based on sheet structure
                    // Some sheets have: No, Nama Produk, Harga Jual strip, Harga Jual Satuan
                    // Some sheets have: No, Nama Produk, Harga Jual Satuan
                    // Some sheets have: No, Nama Produk, Harga Jual Satuan, Harga Jual Renceng

                    $sellingPrice = 0;

                    // Try to get selling price from different column positions
                    if (isset($row[2]) && is_numeric($row[2])) {
                        $sellingPrice = $row[2];
                    } elseif (isset($row[3]) && is_numeric($row[3])) {
                        $sellingPrice = $row[3];
                    }

                    // Skip if no valid selling price
                    if ($sellingPrice <= 0) {
                        continue;
                    }

                    // Create the product
                    Product::create([
                        'category_id' => $category->id,
                        'name' => $productName,
                        'stock' => 0,
                        'initial_price' => 0,
                        'selling_price' => $sellingPrice,
                        'unit' => 'PCS',
                        'type' => 'product',
                    ]);
                }
            }
        } else {
            // Fallback to factory if Excel file doesn't exist
            Product::factory()->count(10)->create();
        }
    }
}
