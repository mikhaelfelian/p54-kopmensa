<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying sales reports
 * This file represents the sales report index view.
 */
?>

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
                    <i class="fas fa-chart-bar mr-1"></i> Laporan Penjualan
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/sale/export_excel') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_gudang=<?= $idGudang ?>&id_pelanggan=<?= $idPelanggan ?>&id_platform=<?= $idPlatform ?? '' ?>"
                        id="exportExcelBtn" class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                    <a href="<?= base_url('laporan/sale/export_pdf') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_gudang=<?= $idGudang ?>&id_pelanggan=<?= $idPelanggan ?>&id_platform=<?= $idPlatform ?? '' ?>"
                        id="exportPdfBtn" class="btn btn-danger btn-sm rounded-0">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/sale') ?>" id="filterForm" class="mb-4">
                    <input type="hidden" name="start_date" id="start_date" value="<?= $startDate ?>">
                    <input type="hidden" name="end_date" id="end_date" value="<?= $endDate ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Periode</label>
                            <input type="text" id="date_range" class="form-control form-control-sm"
                                placeholder="Pilih Periode"
                                value="<?= $startDate && $endDate ? date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)) : '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Outlet</label>
                            <select name="id_gudang" class="form-control form-control-sm">
                                <option value="">Semua Outlet</option>
                                <?php foreach ($gudangList as $gudang): ?>
                                    <option value="<?= $gudang->id ?>" <?= $idGudang == $gudang->id ? 'selected' : '' ?>>
                                        <?= $gudang->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Pelanggan</label>
                            <select name="id_pelanggan" class="form-control form-control-sm">
                                <option value="">Semua Pelanggan</option>
                                <?php
                                // Read user list from Ion Auth where tipe = '2'
                                $ionAuth = new \IonAuth\Libraries\IonAuth();
                                $pelangganUsers = $ionAuth->where('tipe', '2')->users()->result();
                                foreach ($pelangganUsers as $pelanggan):
                                ?>
                                    <option value="<?= $pelanggan->id ?>" <?= $idPelanggan == $pelanggan->id ? 'selected' : '' ?>>
                                        <?= isset($pelanggan->nama) ? $pelanggan->nama : (isset($pelanggan->first_name) ? $pelanggan->first_name : 'Pelanggan ' . $pelanggan->id) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Platform</label>
                            <select name="id_platform" class="form-control form-control-sm">
                                <option value="">Semua Platform</option>
                                <?php foreach ($platformList as $platform): ?>
                                    <option value="<?= $platform->id ?>" <?= ($idPlatform ?? '') == $platform->id ? 'selected' : '' ?>>
                                        <?= esc($platform->platform) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= format_angka($totalTransactions) ?></h3>
                                <p>Total Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= format_angka($totalSales) ?></h3>
                                <p>Total Penjualan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= $totalTransactions > 0 ? format_angka($totalSales / $totalTransactions) : 0 ?></h3>
                                <p>Rata-rata per Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= count($sales) ?></h3>
                                <p>Data Ditemukan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-list"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="3%">No</th>
                                <th width="8%">Tanggal</th>
                                <th width="10%">No. Nota</th>
                                <th width="10%">Outlet</th>
                                <th width="12%">Pelanggan</th>
                                <th width="10%">Kasir</th>
                                <th width="8%">Shift</th>
                                <th width="5%" class="text-center">Item</th>
                                <?php if (!empty($platforms)): ?>
                                    <?php foreach ($platforms as $platform): ?>
                                        <th class="text-right"><?= esc($platform->platform) ?></th>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php if (!empty($vouchers)): ?>
                                    <?php foreach ($vouchers as $voucher): ?>
                                        <th class="text-right"><?= esc($voucher->kode ?? 'Voucher ' . $voucher->id) ?></th>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <th class="text-right">Subtotal</th>
                                <th class="text-right">Retur</th>
                                <th width="8%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sales)): ?>
                                <tr>
                                    <td colspan="<?= 11 + (count($platforms ?? []) + count($vouchers ?? [])) ?>" class="text-center">Tidak ada data penjualan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sales as $index => $sale): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($sale->tgl_masuk)) ?></td>
                                        <td><?= esc($sale->no_nota ?? '-') ?></td>
                                        <td><?= esc($sale->gudang_nama ?? '-') ?></td>
                                        <td><?= esc($sale->pelanggan_nama ?? 'Umum') ?><?= !empty($sale->pelanggan_kode) ? ' (' . esc($sale->pelanggan_kode) . ')' : '' ?></td>
                                        <td><?= esc($sale->user_full_name ?? $sale->username ?? '-') ?></td>
                                        <td><?= esc($sale->shift_nama ?? '-') ?></td>
                                        <td class="text-center"><?= (int)($sale->total_items ?? 0) ?></td>
                                        <?php if (!empty($platforms)): ?>
                                            <?php foreach ($platforms as $platform): ?>
                                                <td class="text-right"><?= format_angka($sale->platform_amounts[$platform->id] ?? 0) ?></td>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <?php if (!empty($vouchers)): ?>
                                            <?php foreach ($vouchers as $voucher): ?>
                                                <td class="text-right"><?= format_angka($sale->voucher_amounts[$voucher->id] ?? 0) ?></td>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <td class="text-right"><?= format_angka($sale->jml_gtotal ?? 0) ?></td>
                                        <td class="text-right"><?= format_angka($sale->jml_retur ?? 0) ?></td>
                                        <td>
                                            <a href="<?= base_url('laporan/sale/detail/' . $sale->id) ?>" 
                                               class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($sales)): ?>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="<?= 8 + (count($platforms ?? []) + count($vouchers ?? [])) ?>" class="text-right">TOTAL</th>
                                    <th class="text-right"><?= format_angka($totalSales) ?></th>
                                    <th class="text-right"><?= format_angka($totalRetur ?? 0) ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
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

    const startMoment = startInput.value ? moment(startInput.value, 'YYYY-MM-DD') : moment();
    const endMoment = endInput.value ? moment(endInput.value, 'YYYY-MM-DD') : moment();

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

    function updateExportLinks() {
        const params = new URLSearchParams({
            start_date: startInput.value || '',
            end_date: endInput.value || '',
            id_gudang: $('select[name="id_gudang"]').val() || '',
            id_pelanggan: $('select[name="id_pelanggan"]').val() || '',
            id_platform: $('select[name="id_platform"]').val() || ''
        }).toString();

        $('#exportExcelBtn').attr('href', '<?= base_url('laporan/sale/export_excel') ?>?' + params);
        $('#exportPdfBtn').attr('href', '<?= base_url('laporan/sale/export_pdf') ?>?' + params);
    }

    $('select[name="id_gudang"], select[name="id_pelanggan"], select[name="id_platform"]').on('change', updateExportLinks);

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

    updateExportLinks();
});
</script>
<?= $this->endSection() ?>
