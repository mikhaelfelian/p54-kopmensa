<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PelangganPoin extends BaseConfig
{
    /** Set false to disable accrual (balances remain visible). */
    public bool $enabled = true;

    /** Rupiah (line subtotal) per 1 poin before multipliers. */
    public float $rupiahPerPoint = 1000.0;

    /**
     * Multiplier by item tipe (tbl_m_item.tipe): 1=item, 2=jasa, 3=paket
     *
     * @var array<string, float>
     */
    public array $itemTipeMultiplier = [
        '1' => 1.0,
        '2' => 1.0,
        '3' => 1.0,
    ];
}
