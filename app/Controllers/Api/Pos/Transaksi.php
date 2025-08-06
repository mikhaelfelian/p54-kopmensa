<?php

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\TransJualModel;
use App\Models\TransJualDetModel;
use CodeIgniter\API\ResponseTrait;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-06
 * Github: github.com/mikhaelfelian
 * description: API controller for managing Transactions (Transaksi) for the POS.
 * This file represents the Transaksi API controller.
 */
class Transaksi extends BaseController
{
    use ResponseTrait;

    /**
     * Get transactions by customer ID (id_pelanggan).
     * 
     * @param int|null $id_pelanggan Customer ID to filter transactions
     * @return \CodeIgniter\HTTP\Response
     */
    public function getTransaction($id_pelanggan = null)
    {
        $mTransJual = new TransJualModel();
        
        // If no id_pelanggan provided, get it from request
        if ($id_pelanggan === null) {
            $id_pelanggan = $this->request->getGet('id_pelanggan');
        }
        
        // Validate id_pelanggan parameter
        if (!$id_pelanggan) {
            return $this->failValidationErrors('Parameter id_pelanggan is required');
        }
        
        try {
            // Get transactions from tbl_trans_jual where id_pelanggan matches
            $transactions = $mTransJual->where('id_pelanggan', $id_pelanggan)->findAll();
            
            // Initialize TransJualDetModel for getting transaction details
            $mTransJualDet = new TransJualDetModel();
            
            // Format the response data
            $formattedTransactions = [];
            foreach ($transactions as $transaction) {
                // Get transaction details from tbl_trans_jual_det where id_penjualan matches
                $details = $mTransJualDet->where('id_penjualan', $transaction->id)->findAll();
                
                // Format transaction details
                $formattedDetails = [];
                foreach ($details as $detail) {
                    $formattedDetails[] = [
                        'id'             => (int)$detail->id,
                        'id_penjualan'   => (int)$detail->id_penjualan,
                        'id_item'        => (int)$detail->id_item,
                        'id_satuan'      => (int)$detail->id_satuan,
                        'id_kategori'    => (int)$detail->id_kategori,
                        'id_merk'        => (int)$detail->id_merk,
                        'created_at'     => $detail->created_at,
                        'updated_at'     => $detail->updated_at,
                        'no_nota'        => $detail->no_nota,
                        'kode'           => $detail->kode,
                        'produk'         => $detail->produk,
                        'satuan'         => $detail->satuan,
                        'keterangan'     => $detail->keterangan,
                        'harga'          => (float)$detail->harga,
                        'harga_beli'     => (float)$detail->harga_beli,
                        'jml'            => (int)$detail->jml,
                        'jml_satuan'     => (int)$detail->jml_satuan,
                        'disk1'          => (float)$detail->disk1,
                        'disk2'          => (float)$detail->disk2,
                        'disk3'          => (float)$detail->disk3,
                        'diskon'         => (float)$detail->diskon,
                        'potongan'       => (float)$detail->potongan,
                        'subtotal'       => (float)$detail->subtotal,
                        'status'         => (int)$detail->status
                    ];
                }
                
                $formattedTransactions[] = [
                    'id'             => (int)$transaction->id,
                    'id_user'        => (int)$transaction->id_user,
                    'id_sales'       => (int)$transaction->id_sales,
                    'id_pelanggan'   => (int)$transaction->id_pelanggan,
                    'id_gudang'      => (int)$transaction->id_gudang,
                    'no_nota'        => $transaction->no_nota,
                    'created_at'     => $transaction->created_at,
                    'updated_at'     => $transaction->updated_at,
                    'deleted_at'     => $transaction->deleted_at,
                    'tgl_bayar'      => $transaction->tgl_bayar,
                    'tgl_masuk'      => $transaction->tgl_masuk,
                    'tgl_keluar'     => $transaction->tgl_keluar,
                    'jml_total'      => (float)$transaction->jml_total,
                    'jml_biaya'      => (float)$transaction->jml_biaya,
                    'jml_ongkir'     => (float)$transaction->jml_ongkir,
                    'jml_retur'      => (float)$transaction->jml_retur,
                    'diskon'         => (float)$transaction->diskon,
                    'jml_diskon'     => (float)$transaction->jml_diskon,
                    'jml_subtotal'   => (float)$transaction->jml_subtotal,
                    'ppn'            => (int)$transaction->ppn,
                    'jml_ppn'        => (float)$transaction->jml_ppn,
                    'jml_gtotal'     => (float)$transaction->jml_gtotal,
                    'jml_bayar'      => (float)$transaction->jml_bayar,
                    'jml_kembali'    => (float)$transaction->jml_kembali,
                    'jml_kurang'     => (float)$transaction->jml_kurang,
                    'disk1'          => (float)$transaction->disk1,
                    'jml_disk1'      => (float)$transaction->jml_disk1,
                    'disk2'          => (float)$transaction->disk2,
                    'jml_disk2'      => (float)$transaction->jml_disk2,
                    'disk3'          => (float)$transaction->disk3,
                    'jml_disk3'      => (float)$transaction->jml_disk3,
                    'metode_bayar'   => $transaction->metode_bayar,
                    'status'         => $transaction->status,
                    'status_nota'    => $transaction->status_nota,
                    'status_ppn'     => $transaction->status_ppn,
                    'status_bayar'   => $transaction->status_bayar,
                    'status_retur'   => $transaction->status_retur,
                    'details'        => $formattedDetails
                ];
            }
            
            $data = [
                'success'      => true,
                'message'      => 'Transactions retrieved successfully',
                'total'        => count($formattedTransactions),
                'id_pelanggan' => (int) $id_pelanggan,
                'transactions' => $formattedTransactions,
            ];
            
            return $this->respond($data);
            
        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve transactions: ' . $e->getMessage());
        }
    }
}