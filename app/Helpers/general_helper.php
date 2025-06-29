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
            return br().'<span class="badge badge-success">Stockable</span>';
        }
        return ''; // Return empty string when not stockable
    }
}

if (!function_exists('jns_klm')) {
    /**
     * Get gender description based on the provided code
     * 
     * @param string $code Gender code
     * @return string Gender description
     */
    function jns_klm(string $code): string
    {
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

function tipeRawat($tipe)
{
    switch ($tipe) {
        case '1':
            return 'Rawat Jalan';
        case '2':
            return 'Rawat Inap';
        case '3':
            return 'Laboratorium';
        case '4':
            return 'Radiologi';
        default:
            return '-';
    }
}

