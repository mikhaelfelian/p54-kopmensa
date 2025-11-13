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
     * Enforce rule: block payment using Piutang Anggota when member is blocked or archived.
     * Also validates spending limit.
     *
     * @param int|null $customerId tbl_m_pelanggan.id
     * @param array $paymentMethods array of payment method entries
     * @param float|null $transactionAmount total transaction amount for limit validation
     * @return array [allowed => bool, message => string]
     */
    public function allowPayment(?int $customerId, array $paymentMethods, ?float $transactionAmount = null): array
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

        // Get customer with status_hps filter to exclude archived members
        $pelanggan = $this->pelangganModel
            ->where('id', $customerId)
            ->where('status_hps', '0') // Exclude archived members
            ->first();

        if (!$pelanggan) {
            return [
                'allowed' => false,
                'message' => 'Anggota tidak ditemukan atau telah diarsipkan.'
            ];
        }

        // Check if member is blocked
        if (($pelanggan->status_blokir ?? '0') === '1') {
            return [
                'allowed' => false,
                'message' => 'Akun anggota diblokir. Pembayaran Piutang ditolak.'
            ];
        }

        // Check spending limit if transaction amount is provided
        if ($transactionAmount !== null && $transactionAmount > 0) {
            $limit = (float) ($pelanggan->limit ?? 0);
            if ($limit > 0 && $transactionAmount > $limit) {
                return [
                    'allowed' => false,
                    'message' => "Transaksi melebihi batas kredit anggota. Batas: " . number_format($limit, 0, ',', '.') . ", Transaksi: " . number_format($transactionAmount, 0, ',', '.')
                ];
            }
        }

        return ['allowed' => true, 'message' => ''];
    }
}


