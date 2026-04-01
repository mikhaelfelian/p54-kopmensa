<?php

namespace App\Libraries;

use App\Models\ItemModel;
use App\Models\KategoriModel;
use App\Models\PelangganModel;
use App\Models\PelangganPoinLogModel;
use App\Models\TransJualDetModel;
use App\Models\TransJualModel;
use Config\PelangganPoin;

/**
 * Loyalty points accrual for completed sales with a linked pelanggan.
 */
class PelangganPoinService
{
    protected PelangganPoin $config;
    protected TransJualModel $transJualModel;
    protected TransJualDetModel $transJualDetModel;
    protected PelangganModel $pelangganModel;
    protected PelangganPoinLogModel $poinLogModel;
    protected ItemModel $itemModel;
    protected KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->config = config('PelangganPoin');
        $this->transJualModel = new TransJualModel();
        $this->transJualDetModel = new TransJualDetModel();
        $this->pelangganModel = new PelangganModel();
        $this->poinLogModel = new PelangganPoinLogModel();
        $this->itemModel = new ItemModel();
        $this->kategoriModel = new KategoriModel();
    }

    /**
     * Idempotent: one log row per id_penjualan.
     */
    public function accrueForSale(int $idPenjualan): void
    {
        if (! $this->config->enabled) {
            return;
        }

        $db = $this->pelangganModel->db;
        if (! $db->tableExists('tbl_pelanggan_poin_log') || ! $db->fieldExists('poin', 'tbl_m_pelanggan')) {
            return;
        }

        if ($this->poinLogModel->where('id_penjualan', $idPenjualan)->first()) {
            return;
        }

        $sale = $this->transJualModel->find($idPenjualan);
        if (! $sale) {
            return;
        }

        if (empty($sale->id_pelanggan) || (int) $sale->id_pelanggan <= 0) {
            return;
        }

        if ((string) $sale->status !== '1') {
            return;
        }

        $lines = $this->transJualDetModel->where('id_penjualan', $idPenjualan)->findAll();
        if ($lines === []) {
            return;
        }

        $rupiahPer = max(0.01, (float) $this->config->rupiahPerPoint);
        $totalPoints = 0.0;

        foreach ($lines as $line) {
            $subtotal = (float) ($line->subtotal ?? 0);
            if ($subtotal <= 0) {
                continue;
            }

            $base = $subtotal / $rupiahPer;
            $katMult = 1.0;
            if (! empty($line->id_kategori)) {
                $kat = $this->kategoriModel->find((int) $line->id_kategori);
                if ($kat && isset($kat->poin_multiplier)) {
                    $katMult = max(0.0, (float) $kat->poin_multiplier);
                }
            }

            $tipeMult = 1.0;
            if (! empty($line->id_item)) {
                $item = $this->itemModel->find((int) $line->id_item);
                if ($item && isset($item->tipe)) {
                    $t = (string) $item->tipe;
                    $tipeMult = (float) ($this->config->itemTipeMultiplier[$t] ?? 1.0);
                }
            }

            $totalPoints += $base * $katMult * $tipeMult;
        }

        $poinEarned = round($totalPoints, 2);
        if ($poinEarned <= 0) {
            return;
        }

        $idPelanggan = (int) $sale->id_pelanggan;
        $db->transStart();

        try {
            $this->poinLogModel->insert([
                'id_penjualan' => $idPenjualan,
                'id_pelanggan' => $idPelanggan,
                'poin'         => $poinEarned,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

            $db->query(
                'UPDATE tbl_m_pelanggan SET poin = COALESCE(poin, 0) + ?, poin_updated_at = ? WHERE id = ?',
                [$poinEarned, date('Y-m-d H:i:s'), $idPelanggan]
            );

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[PelangganPoinService] ' . $e->getMessage());
        }
    }
}
