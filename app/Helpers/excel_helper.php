<?php

if (!function_exists('createExcelTemplate')) {
    /**
     * Create Excel template for import
     * 
     * @param array $headers Array of column headers
     * @param array $sampleData Array of sample data rows
     * @param string $filename Template filename
     * @return string File path
     */
    function createExcelTemplate($headers, $sampleData = [], $filename = 'template.xlsx')
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1', $header);
            $col++;
        }
        
        // Style headers
        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E2E8F0');
        
        // Add sample data
        $row = 2;
        foreach ($sampleData as $sampleRow) {
            $col = 1;
            foreach ($sampleRow as $value) {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Save file
        $templateDir = FCPATH . 'assets/templates/';
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0777, true);
        }
        
        $filepath = $templateDir . $filename;
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);
        
        return $filepath;
    }
}

if (!function_exists('readExcelFile')) {
    /**
     * Read Excel file and return data as array
     * 
     * @param string $filePath Path to Excel file
     * @param int $startRow Starting row (default: 2, skip header)
     * @return array
     */
    function readExcelFile($filePath, $startRow = 2)
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $data = [];
        $highestRow = $worksheet->getHighestRow();
        
        for ($row = $startRow; $row <= $highestRow; $row++) {
            $rowData = [];
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCell(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row)->getCalculatedValue();
                $rowData[] = $cellValue;
            }
            
            // Skip empty rows
            if (!empty(array_filter($rowData))) {
                $data[] = $rowData;
            }
        }
        
        return $data;
    }
}

if (!function_exists('exportToExcel')) {
    /**
     * Export data to Excel file
     * 
     * @param array $data Array of data to export
     * @param array $headers Array of column headers
     * @param string $filename Export filename
     * @return void
     */
    function exportToExcel($data, $headers, $filename = 'export.xlsx')
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1', $header);
            $col++;
        }
        
        // Style headers
        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E2E8F0');
        
        // Add data
        $row = 2;
        foreach ($data as $rowData) {
            $col = 1;
            foreach ($rowData as $value) {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

if (!function_exists('validateExcelFile')) {
    /**
     * Validate uploaded Excel file
     * 
     * @param object $file Uploaded file object
     * @return array Validation result
     */
    function validateExcelFile($file)
    {
        $result = [
            'valid' => true,
            'errors' => []
        ];
        
        if (!$file || !$file->isValid()) {
            $result['valid'] = false;
            $result['errors'][] = 'File Excel tidak valid';
            return $result;
        }
        
        // Check file extension
        $allowedExtensions = ['xlsx', 'xls'];
        $fileExtension = $file->getClientExtension();
        
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            $result['valid'] = false;
            $result['errors'][] = 'File harus berformat Excel (.xlsx atau .xls)';
        }
        
        // Check file size (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            $result['valid'] = false;
            $result['errors'][] = 'Ukuran file maksimal 5MB';
        }
        
        return $result;
    }
}
