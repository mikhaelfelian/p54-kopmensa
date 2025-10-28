<?php

namespace App\Libraries;

use App\Models\PelangganModel;

class PaymentGuard
{
    protected PelangganModel $pelangganModel;

    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
    }

    /**
     * Enforce rule: block payment using Piutang Anggota when member is blocked.
     *
     * @param int|null $customerId tbl_m_pelanggan.id
     * @param array $paymentMethods array of payment method entries
     * @return array [allowed => bool, message => string]
     */
    public function allowPayment(?int $customerId, array $paymentMethods): array
    {
        if (empty($paymentMethods)) {
            return ['allowed' => true, 'message' => ''];
        }

        // Detect if any of the payment methods is Piutang Anggota
        $usesPiutang = false;
        foreach ($paymentMethods as $payment) {
            $type = $payment['type'] ?? '';
            $platformId = $payment['platform_id'] ?? null;

            // Consider type '3' or 'credit' or explicit 'piutang' as Piutang Anggota
            if ($type === '3' || $type === 'credit' || strtolower((string) $type) === 'piutang') {
                $usesPiutang = true;
                break;
            }
        }

        if (!$usesPiutang) {
            return ['allowed' => true, 'message' => ''];
        }

        if (!$customerId) {
            return [
                'allowed' => false,
                'message' => 'Transaksi piutang hanya untuk anggota terdaftar.'
            ];
        }

        $pelanggan = $this->pelangganModel->find($customerId);
        if (!$pelanggan) {
            return [
                'allowed' => false,
                'message' => 'Anggota tidak ditemukan.'
            ];
        }

        if (($pelanggan->status_blokir ?? '0') === '1') {
            return [
                'allowed' => false,
                'message' => 'Akun anggota diblokir. Pembayaran Piutang ditolak.'
            ];
        }

        return ['allowed' => true, 'message' => ''];
    }
}


