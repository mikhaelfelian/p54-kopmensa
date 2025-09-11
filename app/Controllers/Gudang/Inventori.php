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
use App\Models\SatuanModel;
use App\Models\TransMutasiModel;

class Inventori extends BaseController
{
    protected $itemModel;
    protected $itemStokModel;
    protected $itemHistModel;
    protected $gudangModel;
    protected $outletModel;
    protected $kategoriModel;
    protected $merkModel;
    protected $satuanModel;
    public function __construct()
    {
        parent::__construct();
        $this->itemModel        = new ItemModel();
        $this->itemStokModel    = new ItemStokModel();
        $this->itemHistModel    = new ItemHistModel();
        $this->gudangModel      = new GudangModel();
        $this->outletModel      = new OutletModel();
        $this->kategoriModel    = new KategoriModel();
        $this->merkModel        = new MerkModel();
        $this->satuanModel      = new SatuanModel();
    }

    public function index()
    {
        $curr_page = $this->request->getVar('page_items') ?? 1;
        $per_page = $this->request->getVar('per_page') ?? 100; // Show more items by default
        
        // Handle "All Items" option
        if ($per_page == -1) {
            $per_page = 999999; // Large number to get all items
        }
        $keyword = $this->request->getVar('keyword') ?? '';
        $kat = $this->request->getVar('kategori');
        $merk = $this->request->getVar('merk');
        $stok = $this->request->getVar('stok');
        $outlet_filter = $this->request->getVar('outlet_filter') ?? ''; // New outlet filter
        
        // Min stock filter
        $min_stok_operator = $this->request->getVar('min_stok_operator') ?? '';
        $min_stok_value = $this->request->getVar('min_stok_value') ?? '';
        
        // Harga Beli filter
        $harga_beli_operator = $this->request->getVar('harga_beli_operator') ?? '';
        $harga_beli_value = $this->request->getVar('harga_beli_value') ?? '';
        
        // Harga Jual filter
        $harga_jual_operator = $this->request->getVar('harga_jual_operator') ?? '';
        $harga_jual_value = $this->request->getVar('harga_jual_value') ?? '';

        // Prepare filters array
        $filters = [
            'keyword' => $keyword,
            'kategori' => $kat,
            'merk' => $merk,
            'stok' => $stok,
            'min_stok_operator' => $min_stok_operator,
            'min_stok_value' => $min_stok_value,
            'harga_beli_operator' => $harga_beli_operator,
            'harga_beli_value' => $harga_beli_value,
            'harga_jual_operator' => $harga_jual_operator,
            'harga_jual_value' => $harga_jual_value
        ];

        // Use outlet filtering if requested
        if ($outlet_filter && is_numeric($outlet_filter)) {
            // Get paginated outlet inventory data for specific outlet
            $builder = $this->gudangModel->getItemStocksInSpecificOutlet($outlet_filter, $filters);
            
            // Manual pagination for database builder
            $totalRows = $builder->countAllResults(false); // false keeps the query for reuse
            $offset = ($curr_page - 1) * $per_page;
            
            $items = $builder->limit($per_page, $offset)->get()->getResult();
            
            // Create pager like in Item.php
            $pager = \Config\Services::pager();
            $pager->store('items', $curr_page, $per_page, $totalRows, 0);
        } else {
            // Follow Item.php pattern exactly
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

            // Follow Item.php pattern exactly - pass parameters like Item.php does
            $items = $this->itemModel->getItemStocksWithRelations($per_page, $keyword, $curr_page, $kat, $stok, null);
            $pager = $this->itemModel->pager;
        }

        $data = [
            'title'       => 'Data Inventori',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'items'       => $items,
            'pager'       => $pager,
            'currentPage' => $curr_page,
            'perPage'     => $per_page,
            'keyword'     => $keyword,
            'kat'         => $kat,
            'merk'        => $merk,
            'stok'        => $stok,
            'outlet_filter' => $outlet_filter, // Add outlet filter to view data
            'min_stok_operator' => $min_stok_operator,
            'min_stok_value' => $min_stok_value,
            'harga_beli_operator' => $harga_beli_operator,
            'harga_beli_value' => $harga_beli_value,
            'harga_jual_operator' => $harga_jual_operator,
            'harga_jual_value' => $harga_jual_value,
            'kategori'    => $this->kategoriModel->findAll(),
            'merk_list'   => $this->merkModel->findAll(),
            'outlets'     => $this->gudangModel->getOutlets(), // Add outlets for filter dropdown
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
        // Get filter parameters (formatted for clarity)
        $keyword             = $this->request->getVar('keyword') ?? '';
        $kat                 = $this->request->getVar('kategori');
        $merk                = $this->request->getVar('merk');
        $stok                = $this->request->getVar('stok');
        $outlet_filter       = $this->request->getVar('outlet_filter') ?? '';

        // Min stock filter
        $min_stok_operator   = $this->request->getVar('min_stok_operator') ?? '';
        $min_stok_value      = $this->request->getVar('min_stok_value') ?? '';

        // Harga Beli filter
        $harga_beli_operator = $this->request->getVar('harga_beli_operator') ?? '';
        $harga_beli_value    = $this->request->getVar('harga_beli_value') ?? '';

        // Harga Jual filter
        $harga_jual_operator = $this->request->getVar('harga_jual_operator') ?? '';
        $harga_jual_value    = $this->request->getVar('harga_jual_value') ?? '';

        // Prepare filters array
        $filters = [
            'keyword' => $keyword,
            'kategori' => $kat,
            'merk' => $merk,
            'stok' => $stok,
            'min_stok_operator' => $min_stok_operator,
            'min_stok_value' => $min_stok_value,
            'harga_beli_operator' => $harga_beli_operator,
            'harga_beli_value' => $harga_beli_value,
            'harga_jual_operator' => $harga_jual_operator,
            'harga_jual_value' => $harga_jual_value
        ];

        // Get items based on outlet filter
        if ($outlet_filter && is_numeric($outlet_filter)) {
            // Get specific outlet inventory data (no pagination for export)
            $builder = $this->gudangModel->getItemStocksInSpecificOutlet($outlet_filter, $filters);
            $items = $builder->get()->getResult();
            
            // Get outlet name for filename
            $outlet = $this->gudangModel->find($outlet_filter);
            $outlet_name = $outlet ? $outlet->nama : 'outlet';
            $filename_prefix = 'inventori_' . strtolower(str_replace(' ', '_', $outlet_name)) . '_';
        } else {
            // Use original ItemModel filtering for all warehouses
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
            
            $filename_prefix = 'inventori_';
        }

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $title = ($outlet_filter && is_numeric($outlet_filter)) ? 'DATA INVENTORI OUTLET' : 'DATA INVENTORI';
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set headers based on outlet filter
        if ($outlet_filter && is_numeric($outlet_filter)) {
            $headers = [
                'No', 'Gudang', 'Kode Item', 'Barcode', 'Nama Item', 'Kategori', 'Merk', 
                'Stok', 'Stok Min', 'Harga Beli', 'Harga Jual'
            ];
        } else {
            $headers = [
                'No', 'Kode', 'Barcode', 'Nama Item', 'Kategori', 'Merk', 'Deskripsi', 
                'Stok Min', 'Harga Beli', 'Harga Jual', 'Status Item'
            ];
        }

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
            if ($outlet_filter && is_numeric($outlet_filter)) {
                // Outlet data format
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item->gudang_nama ?? '');
                $sheet->setCellValue('C' . $row, $item->item_kode ?? '');
                $sheet->setCellValue('D' . $row, $item->barcode ?? '');
                $sheet->setCellValue('E' . $row, $item->item ?? '');
                $sheet->setCellValue('F' . $row, $item->kategori ?? '');
                $sheet->setCellValue('G' . $row, $item->merk ?? '');
                $sheet->setCellValue('H' . $row, $item->stok ?? 0);
                $sheet->setCellValue('I' . $row, $item->stok_min ?? 0);
                $sheet->setCellValue('J' . $row, format_angka($item->harga_beli ?? 0));
                $sheet->setCellValue('K' . $row, format_angka($item->harga_jual ?? 0));
            } else {
                // Regular data format
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item->kode ?? '');
                $sheet->setCellValue('C' . $row, $item->barcode ?? '');
                $sheet->setCellValue('D' . $row, $item->item ?? '');
                $sheet->setCellValue('E' . $row, $item->kategori ?? '');
                $sheet->setCellValue('F' . $row, $item->merk ?? '');
                $sheet->setCellValue('G' . $row, $item->deskripsi ?? '');
                $sheet->setCellValue('H' . $row, $item->jml_min ?? 0);
                $sheet->setCellValue('I' . $row, format_angka($item->harga_beli ?? 0));
                $sheet->setCellValue('J' . $row, format_angka($item->harga_jual ?? 0));
                $sheet->setCellValue('K' . $row, isset($item->status) && $item->status == '1' ? 'Aktif' : 'Non Aktif');
            }
            
            $row++;
            $no++;
        }

        // Auto size columns
        $maxCol = ($outlet_filter && is_numeric($outlet_filter)) ? 'K' : 'K';
        foreach (range('A', $maxCol) as $col) {
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
        $sheet->getStyle("A3:{$maxCol}" . ($row - 1))->applyFromArray($styleArray);

        // Set filename in yyyymmddhi format
        $filename = $filename_prefix . date('YmdHi') . '.xlsx';

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
        $item = $this->itemModel->find($id);
        $item_stok = $this->itemStokModel
            ->select('tbl_m_item_stok.*, tbl_m_gudang.nama as gudang_nama')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'left')
            ->where('tbl_m_item_stok.id_item', $id)
            ->findAll();


        if (!$item) {
            return redirect()->to(base_url('gudang/stok'))->with('error', 'Item tidak ditemukan.');
        }

        // Get pagination parameters
        $page           = $this->request->getVar('page') ?? 1;
        $perPage        = $this->pengaturan->pagination_limit;
        
        // Get filter parameters
        $filter_gd      = $this->request->getVar('filter_gd');
        $filter_status  = $this->request->getVar('filter_status');
        $filter_jml     = $this->request->getVar('filter_jml');
        $filter_ket     = $this->request->getVar('filter_ket');
        $filter_date_from = $this->request->getVar('filter_date_from');
        $filter_date_to   = $this->request->getVar('filter_date_to');
        
        // Convert empty strings to null for proper filtering
        $filter_gd      = ($filter_gd === '' || $filter_gd === null) ? null : (int)$filter_gd;
        $filter_status  = ($filter_status === '' || $filter_status === null) ? null : $filter_status;
        $filter_jml     = ($filter_jml === '' || $filter_jml === null) ? null : (float)$filter_jml;
        $filter_ket     = ($filter_ket === '' || $filter_ket === null) ? null : trim($filter_ket);
        $filter_date_from = ($filter_date_from === '' || $filter_date_from === null) ? null : $filter_date_from;
        $filter_date_to   = ($filter_date_to === '' || $filter_date_to === null) ? null : $filter_date_to;
        
        // Fetch paginated stock history data with all filters
        $stockHistory = $this->itemHistModel->getWithRelationsPaginated(
                (int)$id, 
                $filter_gd, 
                $filter_status, 
                $perPage, 
                $page,
                $filter_jml,
                $filter_ket,
                $filter_date_from,
                $filter_date_to
        );

        // Get active warehouses for filter dropdown
        $warehouses = $this->gudangModel->where('status', '1')->where('status_hps', '0')->findAll();


        $data = [
            'title'         => 'Detail Stok Item: ' . $item->item,
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'item'          => $item,
            'outlets'       => $item_stok,
            'stokData'      => $stockHistory['data'],
            'pager'         => $stockHistory['pager'],
            'current_page'  => $stockHistory['current_page'],
            'per_page'      => $stockHistory['per_page'],
            'total'         => $stockHistory['total'],
            'total_stok'    => $this->itemStokModel->getTotalStock($id),
            'filter_gd'     => $filter_gd,
            'filter_status' => $filter_status,
            'filter_jml'    => $filter_jml,
            'filter_ket'    => $filter_ket,
            'filter_date_from' => $filter_date_from,
            'filter_date_to'   => $filter_date_to,
            'warehouses'    => $warehouses,
            'satuan'        => $this->satuanModel->where('status', '1')->findAll(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/stok') . '">Inventori</a></li>
                <li class="breadcrumb-item active">Detail Stok</li>
            ',
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

        // Get form data - expecting jml array with warehouse IDs as keys
        $jmlData = $this->request->getPost('jml');
        $gudangId = $this->request->getPost('gudang_id');

        if (!$jmlData) {
            return redirect()->back()->with('error', 'Data stok tidak ditemukan.');
        }

        try {
            $this->db = \Config\Database::connect();
            $this->db->transStart();

            $updatedCount = 0;

            // Process each stock update
            foreach ($jmlData as $gudangId => $quantity) {
                $quantity = (float) $quantity;

                // Only use id_gudang for stock
                $existingStock = $this->itemStokModel
                    ->where('id_item', $id)
                    ->where('id_gudang', $gudangId)
                    ->first();

                if ($existingStock) {
                    // Update existing stock record
                    $updateData = [
                        'jml' => $quantity,
                        'id_user' => $this->ionAuth->user()->row()->id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $this->itemStokModel->where('id', $existingStock->id)->set($updateData)->update();
                    $updatedCount++;
                } else {
                    // Create new stock record
                    $insertData = [
                        'id_item'    => $id,
                        'id_gudang'  => $gudangId,
                        'jml'        => $quantity,
                        'id_user'    => $this->ionAuth->user()->row()->id,
                        'status'     => '1',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

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
                    'sp'          => '0',
                    'id_gudang'   => $gudangId
                ];

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