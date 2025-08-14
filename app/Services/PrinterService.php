<?php

namespace App\Services;

use App\Models\PrinterModel;

class PrinterService
{
    protected $printerModel;
    protected $connector;
    protected $printer;

    public function __construct()
    {
        $this->printerModel = new PrinterModel();
    }

    /**
     * Get default printer
     */
    public function getDefaultPrinter()
    {
        return $this->printerModel->getDefaultPrinter();
    }

    /**
     * Initialize printer connection
     */
    public function initializePrinter($printerId = null)
    {
        if (!$printerId) {
            $printer = $this->getDefaultPrinter();
        } else {
            $printer = $this->printerModel->find($printerId);
        }

        if (!$printer) {
            throw new \Exception('Printer tidak ditemukan');
        }

        try {
            $this->connector = $this->createConnector($printer);
            $this->printer = new \Mike42\Escpos\Printer($this->connector);
            
            return $this->printer;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menginisialisasi printer: ' . $e->getMessage());
        }
    }

    /**
     * Create connector based on printer type
     */
    private function createConnector($printer)
    {
        switch ($printer->tipe_printer) {
            case 'network':
                return new \Mike42\Escpos\PrintConnectors\NetworkPrintConnector(
                    $printer->ip_address,
                    $printer->port
                );
            
            case 'usb':
                return new \Mike42\Escpos\PrintConnectors\FilePrintConnector($printer->path);
            
            case 'file':
                return new \Mike42\Escpos\PrintConnectors\FilePrintConnector($printer->path);
            
            case 'windows':
                return new \Mike42\Escpos\PrintConnectors\WindowsPrintConnector($printer->path);
            
            default:
                throw new \Exception('Tipe printer tidak valid: ' . $printer->tipe_printer);
        }
    }

    /**
     * Print receipt
     */
    public function printReceipt($transactionData, $printerId = null)
    {
        try {
            $printer = $this->initializePrinter($printerId);
            
            // Get printer settings
            $printerSettings = $printerId ? $this->printerModel->find($printerId) : $this->getDefaultPrinter();
            
            // Print header
            $this->printHeader($printer, $transactionData, $printerSettings);
            
            // Print items
            $this->printItems($printer, $transactionData['items']);
            
            // Print totals
            $this->printTotals($printer, $transactionData);
            
            // Print footer
            $this->printFooter($printer, $transactionData);
            
            // Cut paper
            $printer->cut();
            
            // Close connection
            $printer->close();
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Gagal mencetak struk: ' . $e->getMessage());
        }
    }

    /**
     * Print header
     */
    private function printHeader($printer, $transactionData, $printerSettings)
    {
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
        
        // Company name
        $printer->setTextSize(2, 2);
        $printer->text("KOPMENSA\n");
        
        // Address
        $printer->setTextSize(1, 1);
        $printer->text("Jl. Contoh No. 123\n");
        $printer->text("Jakarta, Indonesia\n");
        $printer->text("Telp: (021) 1234-5678\n");
        
        // Separator
        $printer->text(str_repeat("-", $printerSettings->width_paper) . "\n");
        
        // Transaction info
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_LEFT);
        $printer->text("No. Nota: " . $transactionData['no_nota'] . "\n");
        $printer->text("Tanggal: " . date('d/m/Y H:i', strtotime($transactionData['tgl_masuk'])) . "\n");
        $printer->text("Kasir: " . $transactionData['kasir'] . "\n");
        
        if (!empty($transactionData['customer_name'])) {
            $printer->text("Pelanggan: " . $transactionData['customer_name'] . "\n");
        }
        
        $printer->text(str_repeat("-", $printerSettings->width_paper) . "\n");
    }

    /**
     * Print items
     */
    private function printItems($printer, $items)
    {
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_LEFT);
        
        foreach ($items as $item) {
            // Item name
            $printer->text($item['name'] . "\n");
            
            // Item details
            $printer->text(sprintf(
                "%s x %s = %s\n",
                $item['quantity'],
                number_format($item['price'], 0, ',', '.'),
                number_format($item['total'], 0, ',', '.')
            ));
        }
        
        $printer->text(str_repeat("-", 32) . "\n");
    }

    /**
     * Print totals
     */
    private function printTotals($printer, $transactionData)
    {
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_RIGHT);
        
        $printer->text("Subtotal: " . number_format($transactionData['jml_subtotal'], 0, ',', '.') . "\n");
        
        if (!empty($transactionData['jml_diskon']) && $transactionData['jml_diskon'] > 0) {
            $printer->text("Diskon: " . number_format($transactionData['jml_diskon'], 0, ',', '.') . "\n");
        }
        
        if (!empty($transactionData['jml_ppn']) && $transactionData['jml_ppn'] > 0) {
            $printer->text("PPN: " . number_format($transactionData['jml_ppn'], 0, ',', '.') . "\n");
        }
        
        $printer->setTextSize(2, 2);
        $printer->text("TOTAL: " . number_format($transactionData['jml_gtotal'], 0, ',', '.') . "\n");
        $printer->setTextSize(1, 1);
        
        $printer->text(str_repeat("-", 32) . "\n");
    }

    /**
     * Print footer
     */
    private function printFooter($printer, $transactionData)
    {
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
        
        $printer->text("Terima kasih atas kunjungan Anda\n");
        $printer->text("Barang yang sudah dibeli tidak dapat dikembalikan\n");
        $printer->text("Semoga hari Anda menyenangkan!\n");
        
        $printer->text(str_repeat("=", 32) . "\n");
    }

    /**
     * Test printer connection
     */
    public function testPrinter($printerId = null)
    {
        try {
            $printer = $this->initializePrinter($printerId);
            
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text("TEST PRINTER\n");
            $printer->setTextSize(1, 1);
            $printer->text("Koneksi printer berhasil!\n");
            $printer->text("Tanggal: " . date('d/m/Y H:i:s') . "\n");
            $printer->cut();
            $printer->close();
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Test printer gagal: ' . $e->getMessage());
        }
    }

    /**
     * Print simple text
     */
    public function printText($text, $printerId = null)
    {
        try {
            $printer = $this->initializePrinter($printerId);
            
            $printer->text($text . "\n");
            $printer->cut();
            $printer->close();
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Gagal mencetak teks: ' . $e->getMessage());
        }
    }

    /**
     * Close printer connection
     */
    public function closePrinter()
    {
        if ($this->printer) {
            $this->printer->close();
        }
        
        if ($this->connector) {
            $this->connector->close();
        }
    }
} 