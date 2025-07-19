<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-29
 * 
 * Publik Controller
 * 
 * Controller for handling public endpoints including autocomplete
 */

namespace App\Controllers;

use App\Models\ItemModel;
use App\Models\ItemStokModel;

class Publik extends BaseController
{
    protected $itemModel;
    protected $itemStokModel;

    public function __construct()
    {
        parent::__construct();
        $this->itemModel = new ItemModel();
        $this->itemStokModel = new ItemStokModel();
    }

    /**
     * Get items for autocomplete
     */
     public function getItemsStock()
     {
         try {
             $term = $this->request->getGet('term');

             // Build the query based on the actual tbl_m_item structure
             $builder = $this->db->table('tbl_m_item');
             $builder->select('
                 id,
                 kode,
                 barcode,
                 item,
                 deskripsi,
                 jml_min,
                 harga_beli,
                 harga_jual,
                 foto,
                 tipe,
                 status,
                 status_stok,
                 status_hps,
                 sp
             ');
             $builder->where('status', '1');
             $builder->where('status_stok', '1');
             $builder->where('status_hps', '0');

             // Add search condition if term provided
             if (!empty($term)) {
                 $builder->groupStart()
                     ->like('item', $term)
                     ->orLike('kode', $term)
                     ->orLike('barcode', $term)
                     ->orLike('deskripsi', $term)
                     ->groupEnd();
             }

             $query = $builder->get();
             $results = $query->getResult();

             // Format the results
             $data = [];
             foreach ($results as $item) {
                 $data[] = [
                     'id'         => $item->id,
                     'kode'       => $item->kode,
                     'barcode'    => $item->barcode,
                     'label'      => $item->item . ($item->kode ? ' (' . $item->kode . ')' : ''),
                     'item'       => $item->item,
                     'deskripsi'  => $item->deskripsi,
                     'jml_min'    => (float)$item->jml_min,
                     'harga_beli' => (float)$item->harga_beli,
                     'harga_jual' => (float)$item->harga_jual,
                     'foto'       => $item->foto,
                     'tipe'       => $item->tipe,
                     'status'     => (int)$item->status,
                     'status_stok'=> (int)$item->status_stok,
                     'status_hps' => (int)$item->status_hps,
                     'sp'         => (int)$item->sp
                 ];
             }

             // Disable CSRF for this request
             if (isset($_COOKIE['csrf_cookie_name'])) {
                 unset($_COOKIE['csrf_cookie_name']);
                 setcookie('csrf_cookie_name', '', time() - 3600, '/');
             }

             // Send direct JSON response
             header('Content-Type: application/json; charset=utf-8');
             echo json_encode($data);
             exit();
         } catch (\Exception $e) {
             // Log the error
             log_message('error', '[Publik::getItemsStock] Error: ' . $e->getMessage());

             // Send error response
             header('HTTP/1.1 500 Internal Server Error');
             header('Content-Type: application/json; charset=utf-8');
             echo json_encode([
                 'error' => true,
                 'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
             ]);
             exit();
         }
     }

    public function getItems()
    {
        try {
            $term = $this->request->getGet('term');

            // Build the query based on the actual tbl_m_item structure
            $builder = $this->db->table('tbl_m_item');
            $builder->select('
                id,
                kode,
                barcode,
                item,
                deskripsi,
                jml_min,
                harga_beli,
                harga_jual,
                foto,
                tipe,
                status,
                status_stok,
                status_hps,
                sp
            ');
            $builder->where('status', '1');
            $builder->where('status_stok', '1');
            $builder->where('status_hps', '0');

            // Add search condition if term provided
            if (!empty($term)) {
                $builder->groupStart()
                    ->like('item', $term)
                    ->orLike('kode', $term)
                    ->orLike('barcode', $term)
                    ->orLike('deskripsi', $term)
                    ->groupEnd();
            }

            $query = $builder->get();
            $results = $query->getResult();

            // Format the results
            $data = [];

            // Get all item IDs from $results
            $itemIds = [];
            foreach ($results as $item) {
                $itemIds[] = $item->id;
            }

            // Fetch stok for all items, grouped by id_item and id_gudang using ItemStokModel
            $stokData = [];
            if (!empty($itemIds)) {

                // Get all stock records for these items
                $stokRows = $this->itemStokModel
                    ->whereIn('id_item', $itemIds)
                    ->findAll();

                foreach ($stokRows as $stokRow) {
                    // Group by item, then by gudang
                    if (!isset($stokData[$stokRow->id_item])) {
                        $stokData[$stokRow->id_item] = [];
                    }
                    $stokData[$stokRow->id_item][$stokRow->id_gudang] = (float)$stokRow->jml;
                }
            }

            foreach ($results as $item) {
                $data[] = [
                    'id'         => $item->id,
                    'kode'       => $item->kode,
                    'barcode'    => $item->barcode,
                    'label'      => $item->item . ($item->kode ? ' (' . $item->kode . ')' : ''),
                    'item'       => $item->item,
                    'deskripsi'  => $item->deskripsi,
                    'jml_min'    => (float)$item->jml_min,
                    'jml'        => (float)$this->itemStokModel->select('SUM(jml) as jml')->where('id_item', $item->id)->first()->jml,
                    'stok'       => isset($stokData[$item->id]) ? $stokData[$item->id] : [],
                    'harga_beli' => (float)$item->harga_beli,
                    'harga_jual' => (float)$item->harga_jual,
                    'foto'       => $item->foto,
                    'tipe'       => $item->tipe,
                    'status'     => (int)$item->status,
                    'status_stok'=> (int)$item->status_stok,
                    'sp'         => (int)$item->sp
                ];
            }

            // Disable CSRF for this request
            if (isset($_COOKIE['csrf_cookie_name'])) {
                unset($_COOKIE['csrf_cookie_name']);
                setcookie('csrf_cookie_name', '', time() - 3600, '/');
            }

            // Send direct JSON response
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
            exit();
        } catch (\Exception $e) {
            // Log the error
            log_message('error', '[Publik::getItems] Error: ' . $e->getMessage());

            // Send error response
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => true,
                'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
            exit();
        }
    }

    public function getSatuan()
    {
        try {
            // Load SatuanModel
            $satuanModel = new \App\Models\SatuanModel();

            // Get all satuan as associative arrays
            $satuans = $satuanModel->findAll();

            // Format data as array of associative arrays (like items API)
            $result = [];
            foreach ($satuans as $satuan) {
                $result[] = [
                    'id'          => isset($satuan->id) ? $satuan->id : null,
                    'kode'        => isset($satuan->kode) ? $satuan->kode : null,
                    'satuanBesar' => isset($satuan->satuanBesar) ? $satuan->satuanBesar : null,
                    'jml'         => isset($satuan->jml) ? (float)$satuan->jml : null,
                ];
            }

            // Send JSON response in the required format
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
            exit();
        } catch (\Exception $e) {
            // Log the error
            log_message('error', '[Publik::getSatuan] Error: ' . $e->getMessage());

            // Send error response
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
            exit();
        }
    }

} 