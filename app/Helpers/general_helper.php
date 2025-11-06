<?php

if (!function_exists('alnum')) {
    function alnum($string)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $string);
    }
}


if (!function_exists('isMenuActive')) {
    /**
     * Check if current menu is active
     *
     * @param string|array $paths Path or array of paths to check
     * @param bool $exact Match exact path or use contains
     * @return bool
     */
    function isMenuActive($paths, bool $exact = false): bool
    {
        $uri = service('uri');
        $segments = $uri->getSegments(); // Get all segments
        $currentPath = implode('/', $segments); // Join segments with /

        // Convert single path to array
        $paths = (array) $paths;

        foreach ($paths as $path) {
            // Remove leading/trailing slashes
            $path = trim($path, '/');

            if ($exact) {
                // Exact path matching
                if ($currentPath === $path) {
                    return true;
                }
            } else {
                // Contains path matching
                if (strpos($currentPath, $path) !== false) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('isStockable')) {
    /**
     * Check if item is stockable and return badge
     * 
     * @param mixed $value Value to check
     * @return string HTML badge element
     */
    function isStockable($value = '1'): string
    {
        if ($value) {
            return br() . '<span class="badge badge-success">Stockable</span>';
        }
        return ''; // Return empty string when not stockable
    }
}

if (!function_exists('isPPN')) {
    /**
     * Check if item is stockable and return badge
     * 
     * @param mixed $value Value to check
     * @return string HTML badge element
     */
    function isPPN($value = '1'): string
    {
        if ($value) {
            return nbs() . '<span class="badge badge-success">Include PPN</span>';
        }
        return ''; // Return empty string when not PPN
    }
}

if (!function_exists('jns_klm')) {
    /**
     * Get gender description based on the provided code
     * 
     * @param string|null $code Gender code
     * @return string Gender description
     */
    function jns_klm(?string $code): string
    {
        if (empty($code)) {
            return '-';
        }

        $genders = [
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            'B' => 'Banci',
            'G' => 'Gay'
        ];

        return $genders[$code] ?? 'Unknown';
    }
}

if (!function_exists('get_status_badge')) {
    /**
     * Get bootstrap badge class based on PO status
     * 
     * @param int $status Status code
     * @return string Bootstrap badge class
     */
    function get_status_badge($status)
    {
        $badges = [
            0 => 'secondary', // Draft
            1 => 'info',      // Menunggu Persetujuan
            2 => 'primary',   // Disetujui
            3 => 'danger',    // Ditolak
            4 => 'warning',   // Diterima
            5 => 'success'    // Selesai
        ];

        return $badges[$status] ?? 'secondary';
    }
}

if (!function_exists('statusPO')) {
    /**
     * Get PO status label and badge
     * 
     * @param int $status Status code
     * @return array Array containing status label and badge class
     */
    function statusPO($status)
    {
        switch ($status) {
            case 0:
                return [
                    'label' => 'Draft',
                    'badge' => 'secondary'
                ];
            case 1:
                return [
                    'label' => 'Proses',
                    'badge' => 'primary'
                ];
            case 3:
                return [
                    'label' => 'Ditolak',
                    'badge' => 'danger'
                ];
            case 4:
                return [
                    'label' => 'Disetujui',
                    'badge' => 'warning'
                ];
            case 5:
                return [
                    'label' => 'Selesai',
                    'badge' => 'success'
                ];
            default:
                return [
                    'label' => 'Unknown',
                    'badge' => 'secondary'
                ];
        }
    }
}

if (!function_exists('statusGd')) {
    /**
     * Get warehouse status label and badge
     * 
     * @param int $status Status code
     * @return array Array containing status label and badge class
     */
    function statusGd($status)
    {
        switch ($status) {
            case '1':
                return [
                    'label' => 'Utama',
                    'badge' => 'success'
                ];
            case '0':
                return [
                    'label' => '',
                    'badge' => ''
                ];
            default:
                return [
                    'label' => '',
                    'badge' => ''
                ];
        }
    }
}

if (!function_exists('statusHist')) {
    /**
     * Get status history label with badge
     * 
     * @param string $status Status code
     * @return array Label and badge class
     */
    function statusHist($status)
    {
        switch ($status) {
            case '1':
                return [
                    'label' => 'Stok Masuk Pembelian',
                    'badge' => 'success'
                ];
            case '2':
                return [
                    'label' => 'Stok Masuk',
                    'badge' => 'info'
                ];
            case '3':
                return [
                    'label' => 'Stok Masuk Retur Jual',
                    'badge' => 'primary'
                ];
            case '4':
                return [
                    'label' => 'Stok Keluar Penjualan',
                    'badge' => 'danger'
                ];
            case '5':
                return [
                    'label' => 'Stok Keluar Retur Beli',
                    'badge' => 'warning'
                ];
            case '6':
                return [
                    'label' => 'SO',
                    'badge' => 'dark'
                ];
            case '7':
                return [
                    'label' => 'Stok Keluar',
                    'badge' => 'danger'
                ];
            case '8':
                return [
                    'label' => 'Mutasi Antar Gudang',
                    'badge' => 'secondary'
                ];
            default:
                return [
                    'label' => '-',
                    'badge' => 'secondary'
                ];
        }
    }
}

if (!function_exists('statusMutasi')) {
    /**
     * Mendapatkan label dan badge status mutasi transfer gudang.
     * 
     * @param string|int $status
     * @return array
     */
    function statusMutasi($status)
    {
        switch ($status) {
            case '1':
                return [
                    'label' => 'Pindah Gudang',
                    'badge' => 'primary'
                ];
            case '2':
                return [
                    'label' => 'Stok Masuk',
                    'badge' => 'success'
                ];
            case '3':
                return [
                    'label' => 'Stok Keluar',
                    'badge' => 'danger'
                ];
            case '4':
                return [
                    'label' => 'Pindah Outlet',
                    'badge' => 'info'
                ];
            default:
                return [
                    'label' => '-',
                    'badge' => 'secondary'
                ];
        }
    }
}

if (!function_exists('statusOpn')) {
    /**
     * Mendapatkan label dan badge status stok opname.
     * 
     * Status:
     * 0 = Draft
     * 1 = Selesai
     * 2 = Dibatalkan
     * 3 = Dikonfirmasi
     * 
     * @param string|int $status
     * @return array
     */
    function statusOpn($status)
    {
        switch ((string) $status) {
            case '0':
                return [
                    'label' => 'Draft',
                    'badge' => 'secondary'
                ];
            case '1':
                return [
                    'label' => 'Selesai',
                    'badge' => 'success'
                ];
            case '2':
                return [
                    'label' => 'Dibatalkan',
                    'badge' => 'danger'
                ];
            case '3':
                return [
                    'label' => 'Dikonfirmasi',
                    'badge' => 'info'
                ];
            default:
                return [
                    'label' => '-',
                    'badge' => 'secondary'
                ];
        }
    }
}

if (!function_exists('tipeOpn')) {
    /**
     * Mendapatkan label tipe stok opname.
     * 
     * 1 = Gudang
     * 2 = Outlet
     * 
     * @param string|int $tipe
     * @return array
     */
    function tipeOpn($tipe)
    {
        switch ((string) $tipe) {
            case '1':
                return [
                    'label' => 'Gudang',
                    'badge' => 'success'
                ];
            case '2':
                return [
                    'label' => 'Outlet',
                    'badge' => 'secondary'
                ];
            default:
                return [
                    'label' => '-',
                    'badge' => 'secondary'
                ];
        }
    }
}

if (!function_exists('isItemActive')) {
    function isItemActive($status)
    {
        switch ($status) {
            case '1':
                return [
                    'label' => 'Aktif',
                    'badge' => 'success'
                ];
            case '0':
                return [
                    'label' => 'Non Aktif',
                    'badge' => 'danger'
                ];
            default:
                return [
                    'label' => '-',
                    'badge' => 'secondary'
                ];
        }
    }
}

if (!function_exists('statusNota')) {
    /**
     * Mendapatkan label dan badge status nota transfer.
     * 
     * Status:
     * 0 = Draft
     * 1 = Pending
     * 2 = Diproses
     * 3 = Selesai
     * 4 = Dibatalkan
     * 
     * @param string|int $status
     * @return array
     */
    function statusNota($status)
    {
        switch ((string) $status) {
            case '0':
                return [
                    'label' => 'Draft',
                    'badge' => 'secondary'
                ];
            case '1':
                return [
                    'label' => 'Pending',
                    'badge' => 'warning'
                ];
            case '2':
                return [
                    'label' => 'Diproses',
                    'badge' => 'info'
                ];
            case '3':
                return [
                    'label' => 'Selesai',
                    'badge' => 'success'
                ];
            case '4':
                return [
                    'label' => 'Dibatalkan',
                    'badge' => 'danger'
                ];
            default:
                return [
                    'label' => '-',
                    'badge' => 'secondary'
                ];
        }
    }
}

if (!function_exists('tipeMutasi')) {
    /**
     * Mendapatkan label dan badge tipe mutasi transfer.
     * 
     * Tipe:
     * 0 = Draft
     * 1 = Pindah Gudang
     * 2 = Stok Masuk
     * 3 = Stok Keluar
     * 
     * @param string|int $tipe
     * @return array
     */
    function tipeMutasi($tipe)
    {
        switch ((string) $tipe) {
            case '0':
                return [
                    'label' => 'Draft',
                    'badge' => 'secondary'
                ];
            case '1':
                return [
                    'label' => 'Antar Gudang',
                    'badge' => 'info'
                ];
            case '2':
                return [
                    'label' => 'Stok Masuk',
                    'badge' => 'success'
                ];
            case '3':
                return [
                    'label' => 'Stok Keluar',
                    'badge' => 'warning'
                ];
            case '4':
                return [
                    'label' => 'Antar Outlet',
                    'badge' => 'info'
                ];
            default:
                return [
                    'label' => '-',
                    'badge' => 'secondary'
                ];
        }
    }
}

if (!function_exists('generateUsername')) {
    /**
     * Generate a safe username from input string
     * 
     * Creates a username using first name only, with alphabetic characters,
     * lowercase, max 6 chars, and adds numeric characters.
     * 
     * @param string $input Input string to generate username from
     * @return string Generated username
     */
    function generateUsername($input)
    {
        // Get first name only (before first space)
        $firstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', trim($input)));

        // Limit to 4 characters for the name part
        $namePart = substr($firstName, 0, 4);

        // Generate 4 random digits
        $numberPart = '';
        for ($i = 0; $i < 3; $i++) {
            $numberPart .= random_int(0, 9);
        }

        // Concatenate and return (max 8 chars: 4 name + 4 number)
        return $namePart . $numberPart;
    }
}

if (!function_exists('statusShift')) {
    /**
     * Get shift status configuration with badge class, icon, and text
     * 
     * @param string|null $status Shift status (open, closed, approved, void)
     * @return array Array containing 'class', 'icon', and 'text' keys
     */
    function statusShift(?string $status): array
    {
        $statusConfig = [
            'open' => [
                'class' => 'badge-success',
                'icon' => 'fa-play-circle',
                'text' => 'Aktif'
            ],
            'closed' => [
                'class' => 'badge-warning',
                'icon' => 'fa-stop-circle',
                'text' => 'Ditutup'
            ],
            'approved' => [
                'class' => 'badge-info',
                'icon' => 'fa-check-circle',
                'text' => 'Disetujui'
            ],
            'void' => [
                'class' => 'badge-danger',
                'icon' => 'fa-times-circle',
                'text' => 'Batal'
            ]
        ];

        $status = $status ?? 'unknown';
        return $statusConfig[$status] ?? [
            'class' => 'badge-secondary',
            'icon' => 'fa-question-circle',
            'text' => 'Unknown'
        ];
    }
}




