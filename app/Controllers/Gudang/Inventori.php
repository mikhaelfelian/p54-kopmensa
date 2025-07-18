<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-07-15
 * Github : github.com/mikhaelfelian
 * description : Controller for managing inventory.
 * This file represents the Inventori controller.
 */

namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\ItemStokModel;
use App\Models\ItemHistModel;
use App\Models\GudangModel;
use App\Models\OutletModel;
use App\Models\KategoriModel;
use App\Models\MerkModel;

class Inventori extends BaseController
{
    protected $itemModel;
    protected $itemStokModel;
    protected $itemHistModel;
    protected $gudangModel;
    protected $outletModel;
    protected $kategoriModel;
    protected $merkModel;

    public function __construct()
    {
        parent::__construct();
        $this->itemModel = new ItemModel();
        $this->itemStokModel = new ItemStokModel();
        $this->itemHistModel = new ItemHistModel();
        $this->gudangModel = new GudangModel();
        $this->outletModel = new OutletModel();
        $this->kategoriModel = new KategoriModel();
        $this->merkModel = new MerkModel();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_items') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword') ?? '';
        $kat = $this->request->getVar('kategori');
        $merk = $this->request->getVar('merk');
        $stok = $this->request->getVar('stok');
        
        // Min stock filter
        $min_stok_operator = $this->request->getVar('min_stok_operator') ?? '';
        $min_stok_value = $this->request->getVar('min_stok_value') ?? '';
        
        // Harga Beli filter
        $harga_beli_operator = $this->request->getVar('harga_beli_operator') ?? '';
        $harga_beli_value = $this->request->getVar('harga_beli_value') ?? '';
        
        // Harga Jual filter
        $harga_jual_operator = $this->request->getVar('harga_jual_operator') ?? '';
        $harga_jual_value = $this->request->getVar('harga_jual_value') ?? '';

        // Apply filters to the query
        $this->itemModel->where('tbl_m_item.status_hps', '0');
        $this->itemModel->where('tbl_m_item.status_stok', '1'); // Only stockable items

        if ($kat) {
            $this->itemModel->where('tbl_m_item.id_kategori', $kat);
        }
        if ($merk) {
            $this->itemModel->where('tbl_m_item.id_merk', $merk);
        }
        if ($stok !== null && $stok !== '') {
            $this->itemModel->where('tbl_m_item.status_stok', $stok);
        }
        if ($keyword) {
            $this->itemModel->groupStart()
                ->like('tbl_m_item.item', $keyword)
                ->orLike('tbl_m_item.kode', $keyword)
                ->orLike('tbl_m_item.barcode', $keyword)
                ->orLike('tbl_m_item.deskripsi', $keyword)
                ->groupEnd();
        }
        
        // Apply min stock filter
        if ($min_stok_operator && $min_stok_value !== '') {
            $this->itemModel->where("tbl_m_item.jml_min {$min_stok_operator}", $min_stok_value);
        }
        
        // Apply harga beli filter
        if ($harga_beli_operator && $harga_beli_value !== '') {
            $this->itemModel->where("tbl_m_item.harga_beli {$harga_beli_operator}", format_angka_db($harga_beli_value));
        }
        
        // Apply harga jual filter
        if ($harga_jual_operator && $harga_jual_value !== '') {
            $this->itemModel->where("tbl_m_item.harga_jual {$harga_jual_operator}", format_angka_db($harga_jual_value));
        }

        $data = [
            'title'       => 'Data Inventori',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'items'       => $this->itemModel->getItemStocksWithRelations($perPage, $keyword),
            'pager'       => $this->itemModel->pager,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'keyword'     => $keyword,
            'kat'         => $kat,
            'merk'        => $merk,
            'stok'        => $stok,
            'min_stok_operator' => $min_stok_operator,
            'min_stok_value' => $min_stok_value,
            'harga_beli_operator' => $harga_beli_operator,
            'harga_beli_value' => $harga_beli_value,
            'harga_jual_operator' => $harga_jual_operator,
            'harga_jual_value' => $harga_jual_value,
            'kategori'    => $this->kategoriModel->findAll(),
            'merk_list'   => $this->merkModel->findAll(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Gudang</li>
                <li class="breadcrumb-item active">Inventori</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/inventori/index', $data);
    }

    public function export_to_excel()
    {
        // Get filter parameters
        $keyword = $this->request->getVar('keyword') ?? '';
        $kat = $this->request->getVar('kategori');
        $merk = $this->request->getVar('merk');
        $stok = $this->request->getVar('stok');
        
        // Min stock filter
        $min_stok_operator = $this->request->getVar('min_stok_operator') ?? '';
        $min_stok_value = $this->request->getVar('min_stok_value') ?? '';
        
        // Harga Beli filter
        $harga_beli_operator = $this->request->getVar('harga_beli_operator') ?? '';
        $harga_beli_value = $this->request->getVar('harga_beli_value') ?? '';
        
        // Harga Jual filter
        $harga_jual_operator = $this->request->getVar('harga_jual_operator') ?? '';
        $harga_jual_value = $this->request->getVar('harga_jual_value') ?? '';

        // Apply filters to the query
        $this->itemModel->where('tbl_m_item.status_hps', '0');
        $this->itemModel->where('tbl_m_item.status_stok', '1'); // Only stockable items

        if ($kat) {
            $this->itemModel->where('tbl_m_item.id_kategori', $kat);
        }
        if ($merk) {
            $this->itemModel->where('tbl_m_item.id_merk', $merk);
        }
        if ($stok !== null && $stok !== '') {
            $this->itemModel->where('tbl_m_item.status_stok', $stok);
        }
        if ($keyword) {
            $this->itemModel->groupStart()
                ->like('tbl_m_item.item', $keyword)
                ->orLike('tbl_m_item.kode', $keyword)
                ->orLike('tbl_m_item.barcode', $keyword)
                ->orLike('tbl_m_item.deskripsi', $keyword)
                ->groupEnd();
        }
        
        // Apply min stock filter
        if ($min_stok_operator && $min_stok_value !== '') {
            $this->itemModel->where("tbl_m_item.jml_min {$min_stok_operator}", $min_stok_value);
        }
        
        // Apply harga beli filter
        if ($harga_beli_operator && $harga_beli_value !== '') {
            $this->itemModel->where("tbl_m_item.harga_beli {$harga_beli_operator}", format_angka_db($harga_beli_value));
        }
        
        // Apply harga jual filter
        if ($harga_jual_operator && $harga_jual_value !== '') {
            $this->itemModel->where("tbl_m_item.harga_jual {$harga_jual_operator}", format_angka_db($harga_jual_value));
        }

        // Get all filtered data (no pagination)
        $items = $this->itemModel->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->orderBy('tbl_m_item.id', 'DESC')
            ->findAll();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'DATA INVENTORI');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = [
            'No', 'Kode', 'Barcode', 'Nama Item', 'Kategori', 'Merk', 'Deskripsi', 
            'Stok Min', 'Harga Beli', 'Harga Jual', 'Status Item'
        ];

        $col = 'A';
        $row = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }

        // Add data
        $row = 4;
        $no = 1;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->kode);
            $sheet->setCellValue('C' . $row, $item->barcode);
            $sheet->setCellValue('D' . $row, $item->item);
            $sheet->setCellValue('E' . $row, $item->kategori);
            $sheet->setCellValue('F' . $row, $item->merk);
            $sheet->setCellValue('G' . $row, $item->deskripsi);
            $sheet->setCellValue('H' . $row, $item->jml_min);
            $sheet->setCellValue('I' . $row, format_angka($item->harga_beli));
            $sheet->setCellValue('J' . $row, format_angka($item->harga_jual));
            $sheet->setCellValue('K' . $row, $item->status == '1' ? 'Aktif' : 'Non Aktif');
            
            $row++;
            $no++;
        }

        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A3:K' . ($row - 1))->applyFromArray($styleArray);

        // Set filename in yyyymmddhi format
        $filename = 'inventori_' . date('YmdHi') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Create Excel writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function detail($id)
    {
        $item       = $this->itemModel->find($id);
        $item_stok = $this->itemStokModel
            ->select('tbl_m_item_stok.*, tbl_m_outlet.nama as outlet_nama, tbl_m_gudang.gudang as gudang_nama')
            ->join('tbl_m_outlet', 'tbl_m_outlet.id = tbl_m_item_stok.id_outlet', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'left')
            ->where('tbl_m_item_stok.id_item', $id)
            ->findAll();


        if (!$item) {
            return redirect()->to(base_url('gudang/stok'))->with('error', 'Item tidak ditemukan.');
        }

        // Get pagination parameters
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 10;
        
        // Get filter parameters
        $filter_gd = $this->request->getVar('filter_gd');
        $filter_status = $this->request->getVar('filter_status');
        
        // Fetch paginated stock history data
        $stockHistory = $this->itemHistModel->getWithRelationsPaginated(
            $id, 
            $filter_gd, 
            $filter_status, 
            $perPage, 
            $page
        );


        $data = [
            'title'       => 'Detail Stok Item: ' . $item->item,
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'item'        => $item,
            'outlets'     => $item_stok,
            'stokData'    => $stockHistory['data'],
            'pager'       => $stockHistory['pager'],
            'current_page' => $stockHistory['current_page'],
            'per_page'    => $stockHistory['per_page'],
            'total'       => $stockHistory['total'],
            'filter_gd'   => $filter_gd,
            'filter_status' => $filter_status,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/stok') . '">Inventori</a></li>
                <li class="breadcrumb-item active">Detail Stok</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/inventori/detail', $data);
    }

    /**
     * Update stock quantity for specific outlet/warehouse
     * 
     * @param int $id Item ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function updateStock($id)
    {
        // Check if item exists
        $item = $this->itemModel->find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'Item tidak ditemukan.');
        }

        // Get form data - expecting jml array with outlet/warehouse IDs as keys
        $jmlData = $this->request->getPost('jml');
        $outletId = $this->request->getPost('outlet_id');
        $gudangId = $this->request->getPost('gudang_id');

        if (!$jmlData) {
            return redirect()->back()->with('error', 'Data stok tidak ditemukan.');
        }

        try {
            $this->db = \Config\Database::connect();
            $this->db->transStart();

            $updatedCount = 0;

            // Process each stock update
            foreach ($jmlData as $locationId => $quantity) {
                $quantity = (float) $quantity;

                // Determine if this is outlet or warehouse stock
                $existingStock = null;
                $isOutlet = false;

                // Check if this locationId is an outlet
                $outletStock = $this->itemStokModel
                    ->where('id_item', $id)
                    ->where('id_outlet', $locationId)
                    ->first();

                if ($outletStock) {
                    $existingStock = $outletStock;
                    $isOutlet = true;
                } else {
                    // Check if it's a warehouse
                    $warehouseStock = $this->itemStokModel
                        ->where('id_item', $id)
                        ->where('id_gudang', $locationId)
                        ->first();

                    if ($warehouseStock) {
                        $existingStock = $warehouseStock;
                        $isOutlet = false;
                    }
                }

                if ($existingStock) {
                    // Update existing stock record
                    $updateData = [
                        'jml' => $quantity,
                        'id_user' => $this->ionAuth->user()->row()->id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    // Use update() with where to ensure correct record is updated
                    $this->itemStokModel->where('id', $existingStock->id)->set($updateData)->update();
                    $updatedCount++;
                } else {
                    // Create new stock record
                    $insertData = [
                        'id_item' => $id,
                        'jml' => $quantity,
                        'id_user' => $this->ionAuth->user()->row()->id,
                        'status' => '1',
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    if ($isOutlet) {
                        $insertData['id_outlet'] = $locationId;
                        $insertData['id_gudang'] = null;
                    } else {
                        $insertData['id_gudang'] = $locationId;
                        $insertData['id_outlet'] = null;
                    }

                    $this->itemStokModel->insert($insertData);
                    $updatedCount++;
                }

                // Add to history
                $historyData = [
                    'id_item'     => $id,
                    'id_user'     => $this->ionAuth->user()->row()->id,
                    'tgl_masuk'   => date('Y-m-d H:i:s'),
                    'no_nota'     => 'STOCK-UPDATE-' . date('YmdHis'),
                    'kode'        => $item->kode,
                    'item'        => $item->item,
                    'keterangan'  => 'Update Stok Manual',
                    'jml'         => $quantity,
                    'status'      => '2', // Stok Masuk
                    'sp'          => '0'
                ];

                if ($isOutlet) {
                    $historyData['id_outlet'] = $locationId;
                } else {
                    $historyData['id_gudang'] = $locationId;
                }

                // Insert history if ItemHistModel exists
                if (class_exists('App\Models\ItemHistModel')) {
                    $itemHistModel = new \App\Models\ItemHistModel();
                    $itemHistModel->addHistory($historyData);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal mengupdate stok');
            }

            $message = $updatedCount > 0 ?
                "Berhasil mengupdate {$updatedCount} stok item." :
                "Tidak ada stok yang diupdate.";

            // Redirect to detail page after success
            return redirect()->to(base_url('gudang/stok/detail/' . $id))->with('success', $message);

        } catch (\Exception $e) {
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }

            log_message('error', 'Stock update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengupdate stok: ' . $e->getMessage());
        }
    }
} 