<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ShiftModel;

class ShiftFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip shift check for superadmin
        if (session()->get('group_id') == 1) {
            return;
        }

        // Skip shift check for shift management pages
        $uri = $request->uri->getPath();
        if (strpos($uri, 'shift') !== false || 
            strpos($uri, 'petty') !== false || 
            strpos($uri, 'petty-category') !== false) {
            return;
        }

        // Check if user is trying to access transaction pages
        $transactionPages = [
            'transaksi/jual/cashier',
            'transaksi/jual',
            'transaksi/jual/create',
            'transaksi/jual/edit',
            'transaksi/jual/view'
        ];

        $isTransactionPage = false;
        foreach ($transactionPages as $page) {
            if (strpos($uri, $page) !== false) {
                $isTransactionPage = true;
                break;
            }
        }

        if ($isTransactionPage) {
            $outlet_id = session()->get('outlet_id');
            if (!$outlet_id) {
                session()->setFlashdata('error', 'Outlet tidak terpilih');
                return redirect()->to('/dashboard');
            }

            $shiftModel = new ShiftModel();
            $activeShift = $shiftModel->getActiveShift($outlet_id);

            if (!$activeShift) {
                session()->setFlashdata('error', 'Tidak ada shift aktif. Silakan buka shift terlebih dahulu sebelum melakukan transaksi.');
                return redirect()->to('/shift/open');
            }

            // Store active shift info in session for easy access
            session()->set('active_shift_id', $activeShift['id']);
            session()->set('active_shift_code', $activeShift['shift_code']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after the request
    }
}
