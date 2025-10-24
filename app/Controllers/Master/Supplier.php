<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Supplier Controller
 * 
 * Controller for managing supplier data
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\SupplierModel;
use App\Models\PengaturanModel;
use App\Models\ItemModel;
use App\Models\KategoriModel;
use App\Models\MerkModel;
use App\Models\SatuanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Supplier extends BaseController
{
    protected $supplierModel;
    protected $validation;
    protected $pengaturan;
    protected $itemModel;
    protected $kategoriModel;
    protected $merkModel;
    protected $satuanModel;
    protected $ionAuth;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
        $this->itemModel = new ItemModel();
        $this->kategoriModel = new KategoriModel();
        $this->merkModel = new MerkModel();
        $this->satuanModel = new SatuanModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_supplier') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Start with the model query
        $query = $this->supplierModel;

        // Only show non-deleted suppliers (active records)
        $query->where('status_hps', '0');

        // Filter by name/code/npwp
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('nama', $search)
                ->orLike('kode', $search)
                ->orLike('npwp', $search)
                ->groupEnd();
        }

        // Filter by type
        $selectedTipe = $this->request->getVar('tipe');
        if ($selectedTipe !== null && $selectedTipe !== '') {
            $query->where('tipe', $selectedTipe);
        }

        // Get total records for pagination
        $total = $query->countAllResults(false);

        $data = [
            'title'          => 'Data Supplier',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'suppliers'      => $query->paginate($perPage, 'supplier'),
            'pager'          => $this->supplierModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'total'          => $total,
            'search'         => $search,
            'selectedTipe'   => $selectedTipe,
            'selectedStatus' => null,  // Set to null since we're not using status filter
            'getTipeLabel'   => function($tipe) {
                return $this->supplierModel->getTipeLabel($tipe);
            },
            'getStatusLabel' => function($status) {
                return $this->supplierModel->getStatusLabel($status);
            },
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Supplier</li>
            ',
            'trashCount'     => $this->supplierModel->onlyDeleted()->countAllResults()
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/index', $data);
    }

    /**
     * Display create form
     */
    public function create()
    {
        $data = [
            'title'       => 'Tambah Supplier',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'validation'  => $this->validation,
            'kode'        => $this->supplierModel->generateKode(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/create', $data);
    }

    /**
     * Store new supplier data
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'kode' => 'required|max_length[20]|is_unique[tbl_m_supplier.kode]',
            'nama' => 'required|max_length[255]',
            'alamat' => 'required',
            'no_hp' => 'required|max_length[20]',
            'tipe' => 'required|in_list[1,2]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validator);
        }

        try {
            $data = [
                'kode'       => $this->request->getPost('kode'),
                'nama'       => $this->request->getPost('nama'),
                'alamat'     => $this->request->getPost('alamat'),
                'no_hp'      => $this->request->getPost('no_hp'),
                'tipe'       => $this->request->getPost('tipe'),
                'status'     => '1',
                'status_hps' => '0'
            ];

            if (!$this->supplierModel->insert($data)) {
                throw new \RuntimeException('Gagal menyimpan data supplier');
            }

            return redirect()->to(base_url('master/supplier'))
                           ->with('success', 'Data supplier berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', '[Supplier::store] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data supplier');
        }
    }

    /**
     * Display edit form
     */
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return redirect()->to('master/supplier')
                           ->with('error', 'Data supplier tidak ditemukan');
        }

        $data = [
            'title'       => 'Edit Supplier',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'validation'  => $this->validation,
            'supplier'    => $supplier,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/edit', $data);
    }

    /**
     * Update supplier data
     */
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        // Validation rules
        $rules = [
            'kode' => "required|max_length[20]|is_unique[tbl_m_supplier.kode,id,{$id}]",
            'nama' => 'required|max_length[255]',
            'alamat' => 'required',
            'no_hp' => 'required|max_length[20]',
            'tipe' => 'required|in_list[1,2]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validator);
        }

        try {
            $data = [
                'kode'       => $this->request->getPost('kode'),
                'nama'       => $this->request->getPost('nama'),
                'alamat'     => $this->request->getPost('alamat'),
                'no_hp'      => $this->request->getPost('no_hp'),
                'tipe'       => $this->request->getPost('tipe')
            ];

            if (!$this->supplierModel->update($id, $data)) {
                throw new \RuntimeException('Gagal mengupdate data supplier');
            }

            return redirect()->to(base_url('master/supplier'))
                           ->with('success', 'Data supplier berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Supplier::update] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate data supplier');
        }
    }

    /**
     * Display supplier details
     */
    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return redirect()->to('master/supplier')
                           ->with('error', 'Data supplier tidak ditemukan');
        }

        $data = [
            'title'       => 'Detail Supplier',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'supplier'    => $supplier,
            'getTipeLabel'   => function($tipe) {
                return $this->supplierModel->getTipeLabel($tipe);
            },
            'getStatusLabel' => function($status) {
                return $this->supplierModel->getStatusLabel($status);
            },
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/detail', $data);
    }

    /**
     * Delete supplier data
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        try {
            $supplier = $this->supplierModel->find($id);
            if (!$supplier) {
                throw new \RuntimeException('Data supplier tidak ditemukan');
            }

            // Soft delete by updating status_hps
            if (!$this->supplierModel->update($id, ['status_hps' => '1'])) {
                throw new \RuntimeException('Gagal menghapus data supplier');
            }

            return redirect()->to(base_url('master/supplier'))
                           ->with('success', 'Data supplier berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Supplier::delete] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data supplier');
        }
    }

    /**
     * Display list of trashed suppliers
     */
    public function trash()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'tipe'   => $this->request->getGet('tipe'),
            'q'      => $this->request->getGet('q')
        ];

        $query = $this->supplierModel->onlyDeleted();

        // Apply filters
        if (!empty($filters['q'])) {
            $query->groupStart()
                ->like('nama', $filters['q'])
                ->orLike('kode', $filters['q'])
                ->orLike('npwp', $filters['q'])
                ->groupEnd();
        }

        if (!empty($filters['tipe'])) {
            $query->where('tipe', $filters['tipe']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $data = [
            'title'         => 'Data Sampah Supplier',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'suppliers'     => $query->paginate(10, 'suppliers'),
            'pager'         => $this->supplierModel->pager,
            'selectedTipe'  => $filters['tipe'],
            'selectedStatus'=> $filters['status'],
            'search'        => $filters['q'],
            'trashCount'    => $this->supplierModel->onlyDeleted()->countAllResults(),
            'getTipeLabel'  => function($tipe) {
                return $this->supplierModel->getTipeLabel($tipe);
            },
            'getStatusLabel' => function($status) {
                return $this->supplierModel->getStatusLabel($status);
            }
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/trash', $data);
    }

    /**
     * Display and manage items for a supplier
     */
    public function items($id = null)
    {
        if (!$id) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return redirect()->to('master/supplier')
                           ->with('error', 'Data supplier tidak ditemukan');
        }

        // Get items associated with this supplier
        $keyword = $this->request->getVar('keyword') ?? '';
        $kategori = $this->request->getVar('kategori');
        $merk = $this->request->getVar('merk');
        $perPage = $this->request->getVar('per_page') ?? 20;

        // Build query for items
        $itemsQuery = $this->itemModel->select('
                tbl_m_item.*,
                tbl_m_kategori.kategori as kategori_nama,
                tbl_m_merk.merk as merk_nama,
                tbl_m_satuan.SatuanBesar as satuan_nama
            ')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_m_item.status_hps', '0');

        // Filter by supplier
        $itemsQuery->where('tbl_m_item.id_supplier', $id);

        // Apply filters
        if ($keyword) {
            $itemsQuery->groupStart()
                ->like('tbl_m_item.item', $keyword)
                ->orLike('tbl_m_item.kode', $keyword)
                ->orLike('tbl_m_item.barcode', $keyword)
                ->groupEnd();
        }

        if ($kategori) {
            $itemsQuery->where('tbl_m_item.id_kategori', $kategori);
        }

        if ($merk) {
            $itemsQuery->where('tbl_m_item.id_merk', $merk);
        }

        $items = $itemsQuery->paginate($perPage, 'items');

        // Get filter options
        $kategoriList = $this->kategoriModel->where('status', '1')->findAll();
        $merkList = $this->merkModel->where('status', '1')->findAll();

        $data = [
            'title' => 'Item Settings - ' . $supplier->nama,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'supplier' => $supplier,
            'items' => $items,
            'pager' => $this->itemModel->pager,
            'keyword' => $keyword,
            'kategori' => $kategori,
            'merk' => $merk,
            'perPage' => $perPage,
            'kategoriList' => $kategoriList,
            'merkList' => $merkList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item active">Item Settings</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/items', $data);
    }

    /**
     * Add new item for supplier
     */
    public function addItem($supplierId = null)
    {
        if (!$supplierId) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        $supplier = $this->supplierModel->find($supplierId);
        if (!$supplier) {
            return redirect()->to('master/supplier')
                           ->with('error', 'Data supplier tidak ditemukan');
        }

        // Get filter options
        $kategoriList = $this->kategoriModel->where('status', '1')->findAll();
        $merkList = $this->merkModel->where('status', '1')->findAll();
        $satuanList = $this->satuanModel->findAll();

        $data = [
            'title' => 'Tambah Item - ' . $supplier->nama,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'supplier' => $supplier,
            'validation' => $this->validation,
            'kategoriList' => $kategoriList,
            'merkList' => $merkList,
            'satuanList' => $satuanList,
            'kode' => $this->itemModel->generateKode(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier/items/' . $supplierId) . '">Item Settings</a></li>
                <li class="breadcrumb-item active">Tambah Item</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/add_item', $data);
    }

    /**
     * Store new item for supplier
     */
    public function storeItem($supplierId = null)
    {
        if (!$supplierId) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        $supplier = $this->supplierModel->find($supplierId);
        if (!$supplier) {
            return redirect()->to('master/supplier')
                           ->with('error', 'Data supplier tidak ditemukan');
        }

        // Validation rules
        $rules = [
            'item' => 'required|max_length[255]',
            'id_kategori' => 'required|integer',
            'id_merk' => 'required|integer',
            'id_satuan' => 'required|integer',
            'harga_beli' => 'required|numeric|greater_than_equal_to[0]',
            'harga_jual' => 'required|numeric|greater_than_equal_to[0]',
            'min_stok' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        try {
            // Check for duplicate item name
            $existingItem = $this->itemModel->where('item', $this->request->getPost('item'))
                                          ->where('status_hps', '0')
                                          ->first();
            
            if ($existingItem) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Item dengan nama tersebut sudah ada. Silakan gunakan nama yang berbeda.');
            }

            $data = [
                'kode' => $this->itemModel->generateKode(),
                'item' => $this->request->getPost('item'),
                'barcode' => $this->request->getPost('barcode'),
                'id_kategori' => $this->request->getPost('id_kategori'),
                'id_merk' => $this->request->getPost('id_merk'),
                'id_satuan' => $this->request->getPost('id_satuan'),
                'id_supplier' => $supplierId,
                'harga_beli' => $this->request->getPost('harga_beli'),
                'harga_jual' => $this->request->getPost('harga_jual'),
                'min_stok' => $this->request->getPost('min_stok'),
                'keterangan' => $this->request->getPost('keterangan'),
                'status' => '1',
                'status_hps' => '0'
            ];

            if (!$this->itemModel->insert($data)) {
                throw new \RuntimeException('Gagal menyimpan data item');
            }

            return redirect()->to(base_url('master/supplier/items/' . $supplierId))
                           ->with('success', 'Item berhasil ditambahkan ke supplier');

        } catch (\Exception $e) {
            log_message('error', '[Supplier::storeItem] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data item: ' . $e->getMessage());
        }
    }

    /**
     * Export supplier data to Excel
     */
    public function export()
    {
        try {
            // Get the same data as index method with filters
            $query = $this->supplierModel;
            
            // Only show non-deleted suppliers (active records)
            $query->where('status_hps', '0');

            // Apply same filters as index
            $search = $this->request->getVar('search');
            if ($search) {
                $query->groupStart()
                    ->like('nama', $search)
                    ->orLike('kode', $search)
                    ->orLike('npwp', $search)
                    ->groupEnd();
            }

            $selectedTipe = $this->request->getVar('tipe');
            if ($selectedTipe !== null && $selectedTipe !== '') {
                $query->where('tipe', $selectedTipe);
            }

            // Get all data (no pagination for export)
            $suppliers = $query->findAll();

            // Create new spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('Kopmensa System')
                ->setLastModifiedBy('Kopmensa System')
                ->setTitle('Data Supplier')
                ->setSubject('Export Data Supplier')
                ->setDescription('Data Supplier exported from Kopmensa System');

            // Set column headers
            $headers = [
                'A1' => 'No',
                'B1' => 'Kode',
                'C1' => 'Nama',
                'D1' => 'NPWP',
                'E1' => 'Alamat',
                'F1' => 'RT/RW',
                'G1' => 'Kelurahan',
                'H1' => 'Kecamatan',
                'I1' => 'Kota',
                'J1' => 'No. Telepon',
                'K1' => 'No. HP',
                'L1' => 'Tipe',
                'M1' => 'Status',
                'N1' => 'Tanggal Dibuat'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Style the header row
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ];

            $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(12);
            $sheet->getColumnDimension('C')->setWidth(25);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(10);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(15);
            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('L')->setWidth(12);
            $sheet->getColumnDimension('M')->setWidth(12);
            $sheet->getColumnDimension('N')->setWidth(18);

            // Fill data
            $row = 2;
            $no = 1;
            foreach ($suppliers as $supplier) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $supplier->kode);
                $sheet->setCellValue('C' . $row, $supplier->nama);
                $sheet->setCellValue('D' . $row, $supplier->npwp);
                $sheet->setCellValue('E' . $row, $supplier->alamat);
                $sheet->setCellValue('F' . $row, $supplier->rt . '/' . $supplier->rw);
                $sheet->setCellValue('G' . $row, $supplier->kelurahan);
                $sheet->setCellValue('H' . $row, $supplier->kecamatan);
                $sheet->setCellValue('I' . $row, $supplier->kota);
                $sheet->setCellValue('J' . $row, $supplier->no_tlp);
                $sheet->setCellValue('K' . $row, $supplier->no_hp);
                $sheet->setCellValue('L' . $row, $this->supplierModel->getTipeLabel($supplier->tipe));
                $sheet->setCellValue('M' . $row, $this->supplierModel->getStatusLabel($supplier->status));
                $sheet->setCellValue('N' . $row, date('d/m/Y H:i', strtotime($supplier->created_at ?? '')));
                $row++;
            }

            // Style data rows
            $dataRange = 'A2:N' . ($row - 1);
            $dataStyle = [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ];

            if ($row > 2) {
                $sheet->getStyle($dataRange)->applyFromArray($dataStyle);
                
                // Center align specific columns
                $sheet->getStyle('A2:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('L2:M' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Generate filename
            $filename = 'Data_Supplier_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            // Create writer and output
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', '[Supplier::export] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    /**
     * Show CSV import form
     */
    public function importForm()
    {
        $data = [
            'title'         => 'Import Data Supplier',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item active">Import Excel</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/supplier/import', $data);
    }

    /**
     * Process Excel import
     */
    public function importCsv()
    {
        $file = $this->request->getFile('excel_file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()
                ->with('error', 'File Excel tidak valid');
        }

        // Validation rules
        $rules = [
            'excel_file' => [
                'rules' => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]|max_size[excel_file,5120]',
                'errors' => [
                    'uploaded' => 'File Excel harus diupload',
                    'ext_in' => 'File harus berformat Excel',
                    'max_size' => 'Ukuran file maksimal 5MB'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        try {
            $csvData = [];
            $handle = fopen($file->getTempName(), 'r');
            
            // Skip header row
            $header = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 3) { // At least nama, alamat, telepon
                    $csvData[] = [
                        'nama' => trim($row[0] ?? ''),
                        'alamat' => trim($row[1] ?? ''),
                        'telepon' => trim($row[2] ?? ''),
                        'email' => trim($row[3] ?? ''),
                        'contact_person' => trim($row[4] ?? ''),
                        'keterangan' => trim($row[5] ?? ''),
                        'status' => isset($row[6]) ? trim($row[6]) : '1'
                    ];
                }
            }
            fclose($handle);

            if (empty($excelData)) {
                return redirect()->back()
                    ->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($excelData as $index => $row) {
                try {
                    if ($this->supplierModel->insert($data)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->supplierModel->errors());
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Import selesai. Berhasil: {$successCount}, Gagal: {$errorCount}";
            if (!empty($errors)) {
                $message .= "<br>Error details:<br>" . implode("<br>", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= "<br>... dan " . (count($errors) - 10) . " error lainnya";
                }
            }

            return redirect()->to(base_url('master/supplier'))
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        $filename = 'template_supplier.xlsx';
        $filepath = FCPATH . 'assets/templates/' . $filename;
        
        // Create template if not exists
        if (!file_exists($filepath)) {
            $templateDir = dirname($filepath);
            if (!is_dir($templateDir)) {
                mkdir($templateDir, 0777, true);
            }
            
            $headers = ['Nama,Alamat,Telepon,Email,Contact Person,Keterangan,Status\n'];
        $sampleData = [
            ['PT ABC,Jl. Sudirman No. 1,08123456789,abc@email.com,John Doe,Supplier elektronik,1\n'],
            ['CV XYZ,Jl. Thamrin No. 2,08123456788,xyz@email.com,Jane Smith,Supplier pakaian,1\n']
        ];
        $filepath = createExcelTemplate($headers, $sampleData, $filename);
        }
        
        return $this->response->download($filepath, null);
    }

    /**
     * Bulk delete suppliers
     */
    public function bulk_delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $itemIds = $this->request->getPost('item_ids');

        if (empty($itemIds) || !is_array($itemIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada item yang dipilih'
            ]);
        }

        try {
            $deletedCount = 0;
            $failedCount = 0;

            foreach ($itemIds as $id) {
                // Soft delete - set status_hps = 1
                $data = [
                    'status_hps' => '1',
                    'deleted_at' => date('Y-m-d H:i:s')
                ];
                
                if ($this->supplierModel->update($id, $data)) {
                    $deletedCount++;
                } else {
                    $failedCount++;
                }
            }

            if ($deletedCount > 0) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Berhasil menghapus {$deletedCount} supplier" . ($failedCount > 0 ? ", gagal {$failedCount} supplier" : "")
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus semua supplier yang dipilih'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
} 