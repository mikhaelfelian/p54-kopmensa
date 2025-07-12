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
use App\Models\MedTransDetModel;
use App\Models\MedTransIcdModel;

class Publik extends BaseController
{
    protected $itemModel;
    protected $medTransDetModel;

    public function __construct()
    {
        parent::__construct();
        $this->itemModel = new ItemModel();
        $this->medTransDetModel = new \App\Models\MedTransDetModel();
        $this->medTransIcdModel = new \App\Models\MedTransIcdModel();
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


} 