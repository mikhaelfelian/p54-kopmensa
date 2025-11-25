<?= $this->extend(theme_path('main')) ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/daterangepicker/daterangepicker.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">Laporan Penjualan Item</h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="#" id="exportExcelBtn"
                   class="btn btn-sm btn-success rounded-0">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="#" id="exportPdfBtn"
                   class="btn btn-sm btn-danger rounded-0">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filter Form -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter"></i> Filter Laporan</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <input type="hidden" name="start_date" id="start_date" value="<?= $startDate ?>">
                    <input type="hidden" name="end_date" id="end_date" value="<?= $endDate ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Periode:</label>
                            <input type="text" id="date_range" class="form-control form-control-sm"
                                   placeholder="Pilih Periode"
                                   value="<?= $startDate && $endDate ? date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)) : '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Outlet:</label>
                            <select name="id_gudang" id="id_gudang" class="form-control form-control-sm">
                                <option value="">Semua Outlet</option>
                                <?php foreach ($gudangList as $gudang): ?>
                                    <option value="<?= $gudang->id ?>" <?= ($idGudang ?? '') == $gudang->id ? 'selected' : '' ?>>
                                        <?= esc($gudang->nama) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Urutkan:</label>
                            <select name="sort_by" id="sort_by" class="form-control form-control-sm">
                                <option value="total_qty" <?= ($sortBy ?? 'total_qty') == 'total_qty' ? 'selected' : '' ?>>Qty Terjual</option>
                                <option value="total_amount" <?= ($sortBy ?? '') == 'total_amount' ? 'selected' : '' ?>>Total Revenue</option>
                                <option value="total_transactions" <?= ($sortBy ?? '') == 'total_transactions' ? 'selected' : '' ?>>Jumlah Transaksi</option>
                                <option value="item" <?= ($sortBy ?? '') == 'item' ? 'selected' : '' ?>>Nama Item</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Urutan:</label>
                            <select name="sort_order" id="sort_order" class="form-control form-control-sm">
                                <option value="DESC" <?= ($sortOrder ?? 'DESC') == 'DESC' ? 'selected' : '' ?>>Tertinggi</option>
                                <option value="ASC" <?= ($sortOrder ?? '') == 'ASC' ? 'selected' : '' ?>>Terendah</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-sm btn-info">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="<?= base_url('laporan/item-sale') ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= format_angka($summary['total_items']) ?></h3>
                        <p>Total Item Terjual</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= format_angka($summary['total_quantity_sold']) ?></h3>
                        <p>Total Qty Terjual</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= format_angka($summary['total_revenue']) ?></h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= format_angka($summary['total_transactions']) ?></h3>
                        <p>Total Transaksi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?= format_angka($summary['total_ppn']) ?></h3>
                                <p>Total PPN (<?= $ppnRate ?? $summary['ppn_rate'] ?? '' ?>%)</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-percent"></i>
                            </div>
                        </div>
                    </div>
        </div>

        <!-- Item Sales Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="bg-primary">
                    <tr>
                        <th width="3%">No</th>
                        <th width="10%">Kode</th>
                        <th width="25%">Nama Item</th>
                        <th width="8%">Satuan</th>
                        <th width="10%" class="text-center">Qty Terjual</th>
                        <th width="12%" class="text-right">Total Revenue</th>
                        <th width="12%" class="text-right">Total PPN</th>
                        <th width="8%" class="text-center">Status PPN</th>
                        <th width="8%" class="text-right">Rata-rata Harga</th>
                        <th width="8%" class="text-center">Transaksi</th>
                        <th width="6%">Rank</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($itemSales)): ?>
                        <?php $no = 1; foreach ($itemSales as $item): ?>
                            <tr>
                                <td class="text-center"><?= $no ?></td>
                                <td><strong><?= esc($item->kode) ?></strong></td>
                                <td>
                                    <strong><?= esc($item->item) ?></strong>
                                    <?php if (!empty($item->barcode)): ?>
                                        <br><small class="text-muted">Barcode: <?= esc($item->barcode) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($item->satuan ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge badge-primary"><?= format_angka($item->total_qty) ?></span>
                                </td>
                                <td class="text-right">
                                    <strong><?= format_angka($item->total_amount) ?></strong>
                                </td>
                                <td class="text-right">
                                    <strong><?= format_angka($item->ppn_value ?? 0) ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php if (($item->status_ppn ?? '0') === '1'): ?>
                                        <span class="badge badge-success">Include</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Non PPN</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <?= format_angka($item->avg_price) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info"><?= $item->total_transactions ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($no <= 3): ?>
                                        <span class="badge badge-<?= $no == 1 ? 'warning' : ($no == 2 ? 'secondary' : 'success') ?>">
                                            #<?= $no ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">#<?= $no ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $no++; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="py-3">
                                    <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Tidak ada data penjualan item untuk periode yang dipilih</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/daterangepicker/daterangepicker.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const dateRangeInput = $('#date_range');
    const startMoment = startInput.value ? moment(startInput.value, 'YYYY-MM-DD') : moment().startOf('month');
    const endMoment = endInput.value ? moment(endInput.value, 'YYYY-MM-DD') : moment().endOf('month');

    dateRangeInput.daterangepicker({
        startDate: startMoment,
        endDate: endMoment,
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
    }, function (start, end) {
        startInput.value = start.format('YYYY-MM-DD');
        endInput.value = end.format('YYYY-MM-DD');
        updateExportLinks();
    });

    function buildQuery() {
        const params = new URLSearchParams({
            start_date: startInput.value || '',
            end_date: endInput.value || '',
            id_gudang: $('#id_gudang').val() || '',
            sort_by: $('#sort_by').val() || '',
            sort_order: $('#sort_order').val() || ''
        });
        return params.toString();
    }

    function updateExportLinks() {
        const query = buildQuery();
        $('#exportExcelBtn').attr('href', '<?= base_url('laporan/item-sale/export_excel') ?>?' + query);
        $('#exportPdfBtn').attr('href', '<?= base_url('laporan/item-sale/export_pdf') ?>?' + query);
    }

    $('#id_gudang, #sort_by, #sort_order').on('change', updateExportLinks);
    updateExportLinks();

    $('#filterForm').on('submit', function () {
        const range = dateRangeInput.val();
        if (range) {
            const dates = range.split(' - ');
            if (dates.length === 2) {
                startInput.value = moment(dates[0], 'DD/MM/YYYY').format('YYYY-MM-DD');
                endInput.value = moment(dates[1], 'DD/MM/YYYY').format('YYYY-MM-DD');
            }
        }
    });
});
</script>
<?= $this->endSection() ?>