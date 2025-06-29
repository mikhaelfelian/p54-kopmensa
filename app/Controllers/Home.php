<?php
/**
 * Home Controller
 * 
 * Created by Mikhael Felian Waskito
 * Created at 2024-01-09
 */

namespace App\Controllers;

use App\Models\ItemModel;

class Home extends BaseController
{
    protected $itemModel;

    public function __construct()
    {
        $this->itemModel = new ItemModel();
    }

    // Add SKU
    public function test()
    {
        echo "<meta http-equiv='refresh' content='7;url=" . base_url('home/test') . "'>";
        $items = $this->itemModel->where('sp', '0')->orderBy('id', 'DESC')->limit(500)->findAll();
        
        $output = '';
        foreach ($items as $item) {
            // Generate new kode for each item
            $newKode = $this->itemModel->generateKode($item->id_kategori, $item->tipe);
            
            // Update the item with new kode
            $this->itemModel->update($item->id, [
                'kode' => $newKode,
                'sp' => '1'
            ]);
            
            $output .= "Updated Item ID: " . $item->id . " with new kode: " . $newKode . "\n";
        }
        
        return $this->response->setContentType('text/html')->setBody('<pre>' . htmlspecialchars($output) . '</pre>');
    }

    

    // Add warehouse and outlet to item stok
    public function test2()
    {
        echo "<meta http-equiv='refresh' content='7;url=" . base_url('home/test') . "'>";
        $items = $this->itemModel->where('sp', '0')->orderBy('id', 'DESC')->limit(500)->findAll();
        
        $output = '';
        foreach ($items as $item) {
            // Insert from GudangModel into ItemStokModel
            $gudangModel = new \App\Models\GudangModel();
            $itemStokModel = new \App\Models\ItemStokModel();
            
            // Get gudang data
            $gudangData = $gudangModel->findAll();
            
            foreach ($gudangData as $gudang) {
                // Check if item stok already exists for this item and gudang
                $existingStok = $itemStokModel->where('id_item', $item->id)
                                             ->where('id_gudang', $gudang->id)
                                             ->get()->getResult();
                
                if (!$existingStok) {
                    foreach ($existingStok as $stok) {
                        if (!$stok) {
                            // Insert new item stok record
                            $itemStokModel->insert([
                                'id_item'    => $item->id,
                                'id_gudang'  => $gudang->id,
                                'jml'        => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                            
                            $output .= "Inserted ItemStok for Item ID: " . $item->id . " and Gudang ID: " . $gudang->id . "\n";
                        }
                    }
                }
            }
            
            // Insert from OutletModel into ItemStokModel
            $outletModel = new \App\Models\OutletModel();
            
            // Get outlet data
            $outletData = $outletModel->findAll();
            
            foreach ($outletData as $outlet) {
                // Check if item stok already exists for this item and outlet
                $existingStok = $itemStokModel->where('id_item', $item->id)
                                             ->where('id_outlet', $outlet->id)
                                             ->get()->getResult();
                
                if (!$existingStok) {
                    foreach ($existingStok as $stok) {
                        if (!$stok) {
                            // Insert new item stok record
                            $itemStokModel->insert([
                                'id_item'    => $item->id,
                                'id_outlet'  => $outlet->id,
                                'jml'        => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                            
                            $output .= "Inserted ItemStok for Item ID: " . $item->id . " and Outlet ID: " . $outlet->id . "\n";
                        }
                    }
                }
            }
            
            // Update the item with sp flag
            $this->itemModel->update($item->id, [
                'sp' => '1'
            ]);
            
            $output .= "Updated Item ID: " . $item->id . " with sp flag\n";
        }
        
        return $this->response->setContentType('text/html')->setBody('<pre>' . htmlspecialchars($output) . '</pre>');
    }
}
