<?php

namespace App\Services;

use RuntimeException;

class SalesExcelExporter
{
    public function export(array $sales, string $fileName = 'sales-export.xlsx', ?string $companyName = null): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'sales-export-');
        if ($tempFile === false) {
            throw new RuntimeException('Unable to create temporary file for sales export.');
        }

        $targetPath = $tempFile . '.xlsx';
        unlink($tempFile);

        $zip = new \ZipArchive();
        if ($zip->open($targetPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create Excel file.');
        }

        $rows = [];
        $rows[] = [trim($companyName) ?: 'Sales Export'];
        $rows[] = ['Generated At', date('Y-m-d H:i:s')];
        $rows[] = [];
        $rows[] = [
            'Bill No',
            'Customer',
            'Cashier',
            'Sales Date',
            'Total',
            'Cost',
            'Profit',
            'Products',
            'Product Count',
        ];

        foreach ($sales as $sale) {
            $productSummary = $this->buildProductSummary($sale);
            $rows[] = [
                $this->getValue($sale, 'bill_no'),
                $this->getValue($sale, 'customer_name', $this->getValue($sale, 'customer')),
                $this->getValue($sale, 'cashier_name', $this->getValue($sale, 'cashier')),
                $this->getValue($sale, 'paid_at', $this->getValue($sale, 'created_at')),
                $this->toNumber($this->getValue($sale, 'total')),
                $this->toNumber($this->getValue($sale, 'cost')),
                $this->toNumber($this->getValue($sale, 'profit', $this->toNumber($this->getValue($sale, 'total')) - $this->toNumber($this->getValue($sale, 'cost')))),
                $productSummary['summary'],
                $productSummary['count'],
            ];
        }

        $sheetXml = $this->buildSheetXml($rows);

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('docProps/app.xml', $this->appPropsXml());
        $zip->addFromString('docProps/core.xml', $this->corePropsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        if (!empty($fileName)) {
            $downloadPath = dirname($targetPath) . DIRECTORY_SEPARATOR . $fileName;
            rename($targetPath, $downloadPath);
            return $downloadPath;
        }

        return $targetPath;
    }

    private function buildSheetXml(array $rows): string
    {
        $sheetData = '';
        foreach ($rows as $index => $row) {
            $rowNum = $index + 1;
            $cells = '';
            foreach ($row as $columnIndex => $value) {
                $columnLetter = $this->columnLetter($columnIndex + 1);
                $cells .= $this->buildCellXml($columnLetter, $rowNum, $value);
            }

            $sheetData .= '<row r="' . $rowNum . '">' . $cells . '</row>';
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheetData>{$sheetData}</sheetData>
</worksheet>
XML;
    }

    private function buildCellXml(string $columnLetter, int $rowNum, $value): string
    {
        $cellReference = $columnLetter . $rowNum;
        if ($value === null || $value === '') {
            return '<c r="' . $cellReference . '"/>';
        }

        if (is_numeric($value)) {
            return '<c r="' . $cellReference . '"><v>' . $this->escapeXml((string) $value) . '</v></c>';
        }

        return '<c r="' . $cellReference . '" t="inlineStr"><is><t>' . $this->escapeXml((string) $value) . '</t></is></c>';
    }

    private function contentTypesXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
</Types>
XML;
    }

    private function rootRelsXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML;
    }

    private function workbookXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Sales" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>
XML;
    }

    private function workbookRelsXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML;
    }

    private function stylesXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>
  <fills count="1"><fill><patternFill patternType="none"/></fill></fills>
  <borders count="1"><border/></borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" applyAlignment="1"><alignment horizontal="left"/></xf></cellXfs>
  <cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>
</styleSheet>
XML;
    }

    private function appPropsXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"
  xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>service-sync</Application>
</Properties>
XML;
    }

    private function corePropsXml(): string
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:dcterms="http://purl.org/dc/terms/"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:title>Sales Export</dc:title>
  <dc:creator>service-sync</dc:creator>
  <cp:lastModifiedBy>service-sync</cp:lastModifiedBy>
  <dcterms:created xsi:type="dcterms:W3CDTF">{$timestamp}</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">{$timestamp}</dcterms:modified>
</cp:coreProperties>
XML;
    }

    private function buildProductSummary($sale): array
    {
        $productItems = [];

        foreach (['products', 'spares'] as $field) {
            $rawValue = $this->getValue($sale, $field);
            if (empty($rawValue)) {
                continue;
            }

            $decoded = $rawValue;
            if (is_string($decoded)) {
                $decoded = htmlspecialchars_decode($decoded);
                $decoded = json_decode($decoded, true);
            }

            if (!is_array($decoded)) {
                continue;
            }

            foreach ($decoded as $item) {
                if (!is_array($item) && !is_object($item)) {
                    continue;
                }

                $name = $this->getValue($item, 'name', $this->getValue($item, 'pro_name', $this->getValue($item, 'product_name')));
                $sku = $this->getValue($item, 'sku', $this->getValue($item, 'code'));
                $qty = $this->getValue($item, 'qty', $this->getValue($item, 'quantity', $this->getValue($item, 'unit_qty')));
                $unitPrice = $this->getValue($item, 'unit', $this->getValue($item, 'price', $this->getValue($item, 'cost')));

                if (empty($name) && empty($sku)) {
                    continue;
                }

                $productItems[] = [
                    'name' => $name ?? 'N/A',
                    'sku' => $sku ?? 'N/A',
                    'qty' => $qty ?? 0,
                    'unit_price' => $unitPrice ?? 0,
                ];
            }
        }

        if (empty($productItems)) {
            return ['summary' => 'No products', 'count' => 0];
        }

        $summaryParts = [];
        foreach (array_slice($productItems, 0, 4) as $productItem) {
            $name = $productItem['name'];
            $sku = $productItem['sku'];
            $qty = $productItem['qty'];
            $summaryParts[] = $name . ($sku ? ' (' . $sku . ')' : '') . ($qty ? ' x' . $qty : '');
        }

        if (count($productItems) > 4) {
            $summaryParts[] = '...';
        }

        return [
            'summary' => implode('; ', $summaryParts),
            'count' => count($productItems),
        ];
    }

    private function getValue($record, string $key, $default = null)
    {
        if (is_array($record) && array_key_exists($key, $record)) {
            return $record[$key];
        }

        if (is_object($record) && isset($record->$key)) {
            return $record->$key;
        }

        return $default;
    }

    private function toNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (float) $value;
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26);
        }

        return $letter;
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
