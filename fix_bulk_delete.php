<?php
/**
 * Script to fix all bulk delete functionality across all modules
 */

echo "Fixing bulk delete functionality...\n";

// List of controllers that need bulk delete fixes
$controllers = [
    'app/Controllers/Master/Pelanggan.php',
    'app/Controllers/Master/Supplier.php', 
    'app/Controllers/Master/Karyawan.php',
    'app/Controllers/Master/Kategori.php',
    'app/Controllers/Master/Merk.php',
    'app/Controllers/Master/Varian.php',
    'app/Controllers/Master/Satuan.php',
    'app/Controllers/Master/Gudang.php',
    'app/Controllers/Master/Outlet.php',
    'app/Controllers/Master/PelangganGrup.php',
    'app/Controllers/Master/Platform.php',
    'app/Controllers/Master/Item.php'
];

// Standard bulk delete method template
$bulkDeleteTemplate = '
    public function bulk_delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                \'success\' => false,
                \'message\' => \'Invalid request\'
            ]);
        }

        $itemIds = $this->request->getPost(\'item_ids\');

        if (empty($itemIds) || !is_array($itemIds)) {
            return $this->response->setJSON([
                \'success\' => false,
                \'message\' => \'Tidak ada data yang dipilih untuk dihapus\'
            ]);
        }

        try {
            $deletedCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($itemIds as $id) {
                try {
                    if ($this->{MODEL_NAME}Model->delete($id)) {
                        $deletedCount++;
                    } else {
                        $failedCount++;
                        $errors[] = "Gagal menghapus data ID: {$id}";
                    }
                } catch (\\Exception $e) {
                    $failedCount++;
                    $errors[] = "Error menghapus data ID {$id}: " . $e->getMessage();
                }
            }

            $message = "Berhasil menghapus {$deletedCount} data";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} data gagal dihapus";
            }

            return $this->response->setJSON([
                \'success\' => true,
                \'message\' => $message,
                \'deleted_count\' => $deletedCount,
                \'failed_count\' => $failedCount,
                \'errors\' => $errors
            ]);

        } catch (\\Exception $e) {
            log_message(\'error\', \'[Bulk Delete] \' . $e->getMessage());
            return $this->response->setJSON([
                \'success\' => false,
                \'message\' => \'Terjadi kesalahan saat menghapus data: \' . $e->getMessage()
            ]);
        }
    }';

// Model name mapping
$modelMapping = [
    'Pelanggan' => 'pelanggan',
    'Supplier' => 'supplier', 
    'Karyawan' => 'karyawan',
    'Kategori' => 'kategori',
    'Merk' => 'merk',
    'Varian' => 'varian',
    'Satuan' => 'satuan',
    'Gudang' => 'gudang',
    'Outlet' => 'outlet',
    'PelangganGrup' => 'pelanggangrup',
    'Platform' => 'platform',
    'Item' => 'item'
];

foreach ($controllers as $controller) {
    if (file_exists($controller)) {
        echo "Processing: $controller\n";
        
        $content = file_get_contents($controller);
        $controllerName = basename($controller, '.php');
        $modelName = $modelMapping[$controllerName] ?? strtolower($controllerName);
        
        // Replace MODEL_NAME placeholder
        $bulkDeleteMethod = str_replace('{MODEL_NAME}', $modelName, $bulkDeleteTemplate);
        
        // Check if bulk_delete method exists
        if (strpos($content, 'public function bulk_delete()') !== false) {
            // Replace existing method
            $pattern = '/public function bulk_delete\(\)\s*\{[^}]+\}/s';
            $content = preg_replace($pattern, $bulkDeleteMethod, $content);
        } else {
            // Add method before the closing brace
            $content = str_replace('}', $bulkDeleteMethod . "\n}", $content);
        }
        
        file_put_contents($controller, $content);
        echo "Updated: $controller\n";
    }
}

echo "Bulk delete controller methods fixed!\n";
?>
