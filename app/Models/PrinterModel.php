<?php

namespace App\Models;

use CodeIgniter\Model;

class PrinterModel extends Model
{
    protected $table            = 'tbl_m_printer';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_printer',
        'tipe_printer',
        'ip_address',
        'port',
        'path',
        'driver',
        'width_paper',
        'status',
        'is_default',
        'keterangan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get default printer
     */
    public function getDefaultPrinter()
    {
        return $this->where('is_default', '1')
                    ->where('status', '1')
                    ->first();
    }

    /**
     * Get active printers
     */
    public function getActivePrinters()
    {
        return $this->where('status', '1')
                    ->orderBy('is_default', 'DESC')
                    ->orderBy('nama_printer', 'ASC')
                    ->findAll();
    }

    /**
     * Set printer as default
     */
    public function setDefaultPrinter($id)
    {
        // Remove default from all printers
        $this->set('is_default', '0')->update();
        
        // Set new default
        return $this->update($id, ['is_default' => '1']);
    }

    /**
     * Get printer by type
     */
    public function getPrintersByType($type)
    {
        return $this->where('tipe_printer', $type)
                    ->where('status', '1')
                    ->findAll();
    }

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        return $status === '1' ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Get printer type options
     */
    public function getPrinterTypes()
    {
        return [
            'network' => 'Network (IP)',
            'usb' => 'USB',
            'file' => 'File',
            'windows' => 'Windows Printer'
        ];
    }

    /**
     * Get driver options
     */
    public function getDriverOptions()
    {
        return [
            'pos58' => 'POS58',
            'epson' => 'Epson ESC/POS',
            'star' => 'Star',
            'citizen' => 'Citizen',
            'generic' => 'Generic ESC/POS'
        ];
    }
} 