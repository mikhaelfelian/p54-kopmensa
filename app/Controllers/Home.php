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
}
