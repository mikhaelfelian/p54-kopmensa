<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ItemStokModel;
use CodeIgniter\HTTP\ResponseInterface;

class StockController extends BaseController
{
    /**
     * Add or update stock for a given id_gudang, id_item, and jml
     * Accepts POST or GET: id_gudang, id_item, jml
     * Returns JSON
     */
    public function addStock()
    {
        $id_gudang = $this->request->getVar('id_gudang');
        $id_item   = $this->request->getVar('id_item');
        $jml       = $this->request->getVar('jml');

        if (!$id_gudang || !$id_item || $jml === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'id_gudang, id_item, and jml are required.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $itemStokModel = new ItemStokModel();
        $existing = $itemStokModel->where('id_gudang', $id_gudang)
                                  ->where('id_item', $id_item)
                                  ->first();

        if ($existing) {
            // Update: add to existing stock
            $newJml = floatval($existing->jml) + floatval($jml);
            $itemStokModel->update($existing->id, [
                'jml' => $newJml
            ]);
            $message = 'Stock updated.';
        } else {
            // Insert new
            $itemStokModel->insert([
                'id_gudang' => $id_gudang,
                'id_item'   => $id_item,
                'jml'       => $jml,
                'status'    => 1
            ]);
            $message = 'Stock inserted.';
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $message
        ]);
    }
} 