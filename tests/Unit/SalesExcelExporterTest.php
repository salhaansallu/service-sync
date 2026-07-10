<?php

namespace Tests\Unit;

use App\Services\SalesExcelExporter;
use PHPUnit\Framework\TestCase;

class SalesExcelExporterTest extends TestCase
{
    public function test_it_creates_an_excel_file_for_sales_data():
    {
        $exporter = new SalesExcelExporter();
        $sales = [
            [
                'bill_no' => 'INV-001',
                'total' => 100,
                'cost' => 60,
                'customer' => 1,
                'paid_at' => '2026-07-10 10:30:00',
            ],
        ];

        $tempFile = $exporter->export($sales, 'sales-test.xlsx');

        $this->assertFileExists($tempFile);
        $this->assertStringEndsWith('.xlsx', $tempFile);
        unlink($tempFile);
    }
}
