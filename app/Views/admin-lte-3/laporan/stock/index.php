<?= $this->extend(theme_path('main')) ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/daterangepicker/daterangepicker.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-boxes mr-1"></i> Laporan Stok Item
                </h3>
                <div class="card-tools">
                    <a href="#" id="exportExcelBtn" class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                    <a href="#" id="exportPdfBtn" class="btn btn-danger btn-sm rounded-0">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/stock') ?>" id="filterForm" class="mb-4">
                    <input type="hidden" name="start_date" id="start_date" value="<?= $startDate ?>">
                    <input type="hidden" name="end_date" id="end_date" value="<?= $endDate ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Periode Tanggal</label>
                            <input type="text" id="date_range" class="form-control form-control-sm" 
                                   placeholder="Pilih Periode Tanggal" 
                                   value="<?= $startDate && $endDate ? date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)) : '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Outlet</label>
                            <select name="gudang_id" class="form-control form-control-sm">
                                <option value="">Semua Outlet</option>
                                <?php foreach ($gudangList as $gudang): ?>
                                    <option value="<?= $gudang->id ?>" <?= ($selectedGudang ?? '') == $gudang->id ? 'selected' : '' ?>>
                                        <?= esc($gudang->nama) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Urutkan</label>
                            <select name="sort_by" class="form-control form-control-sm">
                                <option value="sisa" <?= ($sortBy ?? 'sisa') == 'sisa' ? 'selected' : '' ?>>Sisa Stok</option>
                                <option value="item" <?= ($sortBy ?? '') == 'item' ? 'selected' : '' ?>>Nama Item</option>
                                <option value="kode" <?= ($sortBy ?? '') == 'kode' ? 'selected' : '' ?>>Kode</option>
                                <option value="gudang" <?= ($sortBy ?? '') == 'gudang' ? 'selected' : '' ?>>Gudang</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Urutan</label>
                            <select name="sort_order" class="form-control form-control-sm">
                                <option value="DESC" <?= ($sortOrder ?? 'DESC') == 'DESC' ? 'selected' : '' ?>>Tertinggi</option>
                                <option value="ASC" <?= ($sortOrder ?? '') == 'ASC' ? 'selected' : '' ?>>Terendah</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="<?= base_url('laporan/stock') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-redo mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= format_angka($totalItems ?? count($stock)) ?></h3>
                                <p>Total Item</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= format_angka($totalStockQty ?? array_sum(array_map(function($item) { return (float)($item->sisa ?? 0); }, $stock))) ?></h3>
                                <p>Total Sisa Stok</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-warehouse"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= format_angka($totalStockValue ?? 0) ?></h3>
                                <p>Total Nilai Stok</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (isset($outOfStockCount)): ?>
                    <div class="alert alert-warning py-2 px-3 small mb-4">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Item stok habis/kosong: <strong><?= format_angka($outOfStockCount) ?></strong>
                    </div>
                <?php endif; ?>

                <!-- Stock Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Gudang</th>
                                <!-- <th class="text-right">SO</th> -->
                                <!-- <th class="text-right">Stok Masuk</th> -->
                                <!-- <th class="text-right">Stok Keluar</th> -->
                                <th class="text-right">Stok</th>
                                <th class="text-right">Nilai (Rp)</th>
                                <th>Status</th>
                                <!-- <th width="10%">Aksi</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stock)): ?>
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data stok</td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $rowNumber = 1;
                                foreach ($stock as $item): 
                                    $kode        = $item->kode        ?? 'Unknown';
                                    $itemName    = $item->item        ?? 'Unknown';
                                    $gudangName  = $item->gudang      ?? 'Unknown';
                                    $so          = (float)($item->so           ?? 0);
                                    $stokMasuk   = (float)($item->stok_masuk   ?? 0);
                                    $stokKeluar  = (float)($item->stok_keluar  ?? 0);
                                    $sisa        = (float)($item->sisa         ?? 0);
                                    $nilai       = (float)($item->harga_beli   ?? 0) * $sisa;
                                    $idItem      = (int)($item->id_item        ?? 0);
                                    $idGudang    = (int)($item->id_gudang      ?? 0);
                                ?>
                                    <tr>
                                        <td><?= $rowNumber++ ?></td>
                                        <td><strong><?= esc($kode) ?></strong></td>
                                        <td><?= esc($itemName) ?></td>
                                        <td><?= esc($gudangName) ?></td>
                                        <!-- <td class="text-right"><?= format_angka($so) ?></td> -->
                                        <!-- <td class="text-right"><?= format_angka($stokMasuk) ?></td> -->
                                        <!-- <td class="text-right"><?= format_angka($stokKeluar) ?></td> -->
                                        <td class="text-right">
                                            <?php 
                                            if ($sisa > 0) {
                                                echo '<span class="badge badge-success">' . format_angka($sisa) . '</span>';
                                            } elseif ($sisa == 0) {
                                                echo '<span class="badge badge-warning">' . format_angka($sisa) . '</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">' . format_angka($sisa) . '</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-right"><?= format_angka($nilai) ?></td>
                                        <td>
                                            <?php 
                                            if ($sisa > 0) {
                                                echo '<span class="badge badge-success">Ada Stok</span>';
                                            } elseif ($sisa == 0) {
                                                echo '<span class="badge badge-warning">Stok Kosong</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Stok Negatif</span>';
                                            }
                                            ?>
                                        </td>
                                        <!-- <td>
                                            <a href="<?= base_url('laporan/stock/detail/' . $idItem) ?>?gudang_id=<?= $idGudang ?>" 
                                               class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td> -->
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if (!empty($pagination) && ($pagination['last_page'] ?? 1) > 1): ?>
                        <?php
                            $currentPage = $pagination['current_page'] ?? 1;
                            $lastPage = $pagination['last_page'] ?? 1;
                            $queryString = !empty($baseQuery) ? $baseQuery . '&' : '';
                            $paginationBase = base_url('laporan/stock') . '?' . $queryString;
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        ?>
                        <nav aria-label="Stock pagination" class="mt-3">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $currentPage > 1 ? $paginationBase . 'page_stock=' . ($currentPage - 1) : '#' ?>" tabindex="-1">Previous</a>
                                </li>
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= $paginationBase . 'page_stock=' . $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $currentPage >= $lastPage ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $currentPage < $lastPage ? $paginationBase . 'page_stock=' . ($currentPage + 1) : '#' ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/daterangepicker/daterangepicker.js') ?>"></script>
<script>
$(document).ready(function() {
    // Initialize daterangepicker
    var startDate = '<?= $startDate ?>' ? moment('<?= $startDate ?>', 'YYYY-MM-DD') : moment().startOf('month');
    var endDate = '<?= $endDate ?>' ? moment('<?= $endDate ?>', 'YYYY-MM-DD') : moment().endOf('month');
    
    $('#date_range').daterangepicker({
        startDate: startDate,
        endDate: endDate,
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Terapkan',
            cancelLabel: 'Batal',
            fromLabel: 'Dari',
            toLabel: 'Sampai',
            customRangeLabel: 'Kustom',
            weekLabel: 'M',
            daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            firstDay: 1
        },
        opens: 'left',
        autoUpdateInput: true
    }, function(start, end, label) {
        // Update hidden inputs when date range changes
        $('#start_date').val(start.format('YYYY-MM-DD'));
        $('#end_date').val(end.format('YYYY-MM-DD'));
    });
    
    // Update hidden inputs on initial load
    $('#start_date').val(startDate.format('YYYY-MM-DD'));
    $('#end_date').val(endDate.format('YYYY-MM-DD'));
    
    // Update export links when date range changes
    function updateExportLinks() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var gudangId = $('select[name="gudang_id"]').val() || '';
        var sortBy = $('select[name="sort_by"]').val() || 'sisa';
        var sortOrder = $('select[name="sort_order"]').val() || 'DESC';
        
        var params = 'start_date=' + startDate + '&end_date=' + endDate + 
                     '&gudang_id=' + gudangId + '&sort_by=' + sortBy + '&sort_order=' + sortOrder;
        
        $('#exportExcelBtn').attr('href', '<?= base_url('laporan/stock/export_excel') ?>?' + params);
        $('#exportPdfBtn').attr('href', '<?= base_url('laporan/stock/export_pdf') ?>?' + params);
    }
    
    // Update export links when form fields change
    $('#date_range').on('apply.daterangepicker', function(ev, picker) {
        updateExportLinks();
    });
    
    $('select[name="gudang_id"], select[name="sort_by"], select[name="sort_order"]').on('change', function() {
        updateExportLinks();
    });
    
    // Initialize export links on page load
    updateExportLinks();
    
    // Handle form submission
    $('#filterForm').on('submit', function(e) {
        // Ensure hidden inputs are populated
        var range = $('#date_range').val();
        if (range) {
            var dates = range.split(' - ');
            if (dates.length === 2) {
                $('#start_date').val(moment(dates[0], 'DD/MM/YYYY').format('YYYY-MM-DD'));
                $('#end_date').val(moment(dates[1], 'DD/MM/YYYY').format('YYYY-MM-DD'));
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
