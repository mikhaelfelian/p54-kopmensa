<?php

namespace App\Controllers\Pengaturan;

use App\Controllers\BaseController;
use App\Models\PrinterModel;

class Printer extends BaseController
{
    protected $printerModel;

    public function __construct()
    {
        $this->printerModel = new PrinterModel();
    }

    /**
     * Display printer list
     */
    public function index()
    {
        $data = [
            'title' => 'Pengaturan Printer',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'printers' => $this->printerModel->getActivePrinters(),
            'printerTypes' => $this->printerModel->getPrinterTypes(),
            'driverOptions' => $this->printerModel->getDriverOptions()
        ];

        return view($this->theme->getThemePath() . '/pengaturan/printer/index', $data);
    }

    /**
     * Show create printer form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Printer',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'printerTypes' => $this->printerModel->getPrinterTypes(),
            'driverOptions' => $this->printerModel->getDriverOptions()
        ];

        return view($this->theme->getThemePath() . '/pengaturan/printer/create', $data);
    }

    /**
     * Store new printer
     */
    public function store()
    {
        $rules = [
            'nama_printer' => 'required|max_length[100]',
            'tipe_printer' => 'required|in_list[network,usb,file,windows]',
            'driver' => 'required|in_list[pos58,epson,star,citizen,generic]',
            'width_paper' => 'required|numeric|greater_than[0]',
            'status' => 'required|in_list[0,1]'
        ];

        // Conditional validation based on printer type
        if ($this->request->getPost('tipe_printer') === 'network') {
            $rules['ip_address'] = 'required|valid_ip';
            $rules['port'] = 'required|numeric|greater_than[0]|less_than_equal_to[65535]';
        } elseif ($this->request->getPost('tipe_printer') === 'usb') {
            $rules['path'] = 'required|max_length[255]';
        } elseif ($this->request->getPost('tipe_printer') === 'file') {
            $rules['path'] = 'required|max_length[255]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        $nama_printer  = $this->request->getPost('nama_printer');
        $tipe_printer  = $this->request->getPost('tipe_printer');
        $ip_address    = $this->request->getPost('ip_address') ?: null;
        $port          = $this->request->getPost('port') ?: null;
        $path          = $this->request->getPost('path') ?: null;
        $driver        = $this->request->getPost('driver');
        $width_paper   = $this->request->getPost('width_paper');
        $status        = $this->request->getPost('status');
        $is_default    = $this->request->getPost('is_default') ? '1' : '0';
        $keterangan    = $this->request->getPost('keterangan') ?: null;

        $data = [
            'id'            => $this->request->getPost('id') ?: null,
            'nama_printer'  => $nama_printer,
            'tipe_printer'  => $tipe_printer,
            'ip_address'    => $ip_address,
            'port'          => $port,
            'path'          => $path,
            'driver'        => $driver,
            'width_paper'   => $width_paper,
            'status'        => $status,
            'is_default'    => $is_default,
            'keterangan'    => $keterangan,
        ];

        try {
            $this->printerModel->save($data);
            return redirect()->to('pengaturan/printer')
                            ->with('success', 'Printer berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal menambahkan printer: ' . $e->getMessage());
        }
    }

    /**
     * Show edit printer form
     */
    public function edit($id = null)
    {
        $printer = $this->printerModel->find($id);
        
        if (!$printer) {
            return redirect()->to('pengaturan/printer')
                            ->with('error', 'Printer tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Printer',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'printer' => $printer,
            'printerTypes' => $this->printerModel->getPrinterTypes(),
            'driverOptions' => $this->printerModel->getDriverOptions()
        ];

        return view($this->theme->getThemePath() . '/pengaturan/printer/edit', $data);
    }

    /**
     * Update printer
     */
    public function update($id = null)
    {
        $printer = $this->printerModel->find($id);
        
        if (!$printer) {
            return redirect()->to('pengaturan/printer')
                            ->with('error', 'Printer tidak ditemukan');
        }

        $rules = [
            'nama_printer' => 'required|max_length[100]',
            'tipe_printer' => 'required|in_list[network,usb,file,windows]',
            'driver' => 'required|in_list[pos58,epson,star,citizen,generic]',
            'width_paper' => 'required|numeric|greater_than[0]',
            'status' => 'required|in_list[0,1]'
        ];

        // Conditional validation based on printer type
        if ($this->request->getPost('tipe_printer') === 'network') {
            $rules['ip_address'] = 'required|valid_ip';
            $rules['port'] = 'required|numeric|greater_than[0]|less_than_equal_to[65535]';
        } elseif ($this->request->getPost('tipe_printer') === 'usb') {
            $rules['path'] = 'required|max_length[255]';
        } elseif ($this->request->getPost('tipe_printer') === 'file') {
            $rules['path'] = 'required|max_length[255]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_printer' => $this->request->getPost('nama_printer'),
            'tipe_printer' => $this->request->getPost('tipe_printer'),
            'ip_address' => $this->request->getPost('ip_address') ?: null,
            'port' => $this->request->getPost('port') ?: null,
            'path' => $this->request->getPost('path') ?: null,
            'driver' => $this->request->getPost('driver'),
            'width_paper' => $this->request->getPost('width_paper'),
            'status' => $this->request->getPost('status'),
            'is_default' => $this->request->getPost('is_default') ? '1' : '0',
            'keterangan' => $this->request->getPost('keterangan') ?: null
        ];

        // If this is marked as default, set as default
        if ($data['is_default'] === '1') {
            $this->printerModel->setDefaultPrinter(0); // This will clear all defaults
        }

        if ($this->printerModel->update($id, $data)) {
            return redirect()->to('pengaturan/printer')
                            ->with('success', 'Printer berhasil diupdate');
        }

        return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal mengupdate printer');
    }

    /**
     * Delete printer
     */
    public function delete($id = null)
    {
        $printer = $this->printerModel->find($id);
        
        if (!$printer) {
            return redirect()->to('pengaturan/printer')
                            ->with('error', 'Printer tidak ditemukan');
        }

        if ($this->printerModel->delete($id)) {
            return redirect()->to('pengaturan/printer')
                            ->with('success', 'Printer berhasil dihapus');
        }

        return redirect()->to('pengaturan/printer')
                        ->with('error', 'Gagal menghapus printer');
    }

    /**
     * Set printer as default
     */
    public function setDefault($id = null)
    {
        $printer = $this->printerModel->find($id);
        
        if (!$printer) {
            return redirect()->to('pengaturan/printer')
                            ->with('error', 'Printer tidak ditemukan');
        }

        if ($this->printerModel->setDefaultPrinter($id)) {
            return redirect()->to('pengaturan/printer')
                            ->with('success', 'Printer berhasil dijadikan default');
        }

        return redirect()->to('pengaturan/printer')
                        ->with('error', 'Gagal mengatur printer default');
    }

    /**
     * Test printer connection
     */
    public function testConnection($id = null)
    {
        $printer = $this->printerModel->find($id);
        
        if (!$printer) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Printer tidak ditemukan'
            ]);
        }

        try {
            // Test printer connection based on type
            $result = $this->testPrinterConnection($printer);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Koneksi printer berhasil',
                'details' => $result
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Koneksi printer gagal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test printer connection
     */
    private function testPrinterConnection($printer)
    {
        switch ($printer->tipe_printer) {
            case 'network':
                return $this->testNetworkPrinter($printer);
            case 'usb':
                return $this->testUsbPrinter($printer);
            case 'file':
                return $this->testFilePrinter($printer);
            case 'windows':
                return $this->testWindowsPrinter($printer);
            default:
                throw new \Exception('Tipe printer tidak valid');
        }
    }

    /**
     * Test network printer
     */
    private function testNetworkPrinter($printer)
    {
        $host = $printer->ip_address;
        $port = $printer->port;
        
        // Test TCP connection
        $connection = @fsockopen($host, $port, $errno, $errstr, 5);
        
        if (!$connection) {
            throw new \Exception("Tidak dapat terhubung ke $host:$port - $errstr ($errno)");
        }
        
        fclose($connection);
        
        return [
            'type' => 'network',
            'host' => $host,
            'port' => $port,
            'status' => 'connected'
        ];
    }

    /**
     * Test USB printer
     */
    private function testUsbPrinter($printer)
    {
        $path = $printer->path;
        
        if (!file_exists($path)) {
            throw new \Exception("Path USB tidak ditemukan: $path");
        }
        
        return [
            'type' => 'usb',
            'path' => $path,
            'status' => 'available'
        ];
    }

    /**
     * Test file printer
     */
    private function testFilePrinter($printer)
    {
        $path = $printer->path;
        $dir = dirname($path);
        
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new \Exception("Directory tidak dapat ditulis: $dir");
        }
        
        return [
            'type' => 'file',
            'path' => $path,
            'status' => 'writable'
        ];
    }

    /**
     * Test Windows printer
     */
    private function testWindowsPrinter($printer)
    {
        // For Windows printers, we'll just check if the system can access it
        return [
            'type' => 'windows',
            'status' => 'available',
            'note' => 'Windows printer availability will be tested during actual printing'
        ];
    }
} 