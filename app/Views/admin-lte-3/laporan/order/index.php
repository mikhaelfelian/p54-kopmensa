<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-30
 * Github: github.com/mikhaelfelian
 * Description: View for displaying order reports
 * This file represents the order report index view.
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
                    <i class="fas fa-chart-bar mr-1"></i> Laporan Pesanan
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
                <form method="get" action="<?= base_url('laporan/order') ?>" id="filterForm" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Tanggal</label>
                            <input type="text" id="date_range" class="form-control form-control-sm" 
                                   value="<?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?>" 
                                   placeholder="Pilih Tanggal">
                            <input type="hidden" name="start_date" id="start_date" value="<?= $startDate ?>">
                            <input type="hidden" name="end_date" id="end_date" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Supplier</label>
                            <select name="id_supplier" id="id_supplier" class="form-control form-control-sm">
                                <option value="">Semua Supplier</option>
                                <?php foreach ($supplierList as $supplier): ?>
                                    <option value="<?= $supplier->id ?>" <?= ($idSupplier ?? '') == $supplier->id ? 'selected' : '' ?>>
                                        <?= $supplier->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="0" <?= ($status ?? '') == '0' ? 'selected' : '' ?>>Draft</option>
                                <option value="1" <?= ($status ?? '') == '1' ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                                <option value="2" <?= ($status ?? '') == '2' ? 'selected' : '' ?>>Disetujui</option>
                                <option value="3" <?= ($status ?? '') == '3' ? 'selected' : '' ?>>Ditolak</option>
                                <option value="4" <?= ($status ?? '') == '4' ? 'selected' : '' ?>>Diterima</option>
                                <option value="5" <?= ($status ?? '') == '5' ? 'selected' : '' ?>>Selesai</option>
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
                                <h3><?= format_angka($totalOrders) ?></h3>
                                <p>Total Pesanan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= format_angka($totalDraft) ?></h3>
                                <p>Draft</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= format_angka($totalApproved) ?></h3>
                                <p>Disetujui</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?= format_angka($totalCompleted) ?></h3>
                                <p>Selesai</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>No. PO</th>
                                <th>Supplier</th>
                                <th>Pembuat</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data pesanan</td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $transBeliPOModel = new \App\Models\TransBeliPOModel();
                                foreach ($orders as $index => $order): 
                                    $statusLabel = $transBeliPOModel->getStatusLabel($order->status ?? '0');
                                    $fullName = trim(($order->user_first_name ?? '') . ' ' . ($order->user_last_name ?? ''));
                                    $userName = $fullName ?: ($order->user_username ?? '-');
                                ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= date('d/m/Y', strtotime($order->tgl_masuk)) ?></td>
                                        <td>
                                            <a href="<?= base_url('laporan/order/detail/' . $order->id) ?>" class="text-primary">
                                                <?= $order->no_nota ?>
                                            </a>
                                        </td>
                                        <td><?= $order->supplier_nama ?? '-' ?></td>
                                        <td><?= $userName ?></td>
                                        <td>
                                            <?php 
                                            $statusClass = 'secondary';
                                            if ($order->status == '0') $statusClass = 'warning';
                                            elseif (in_array($order->status, ['2', '4', '5'])) $statusClass = 'success';
                                            elseif ($order->status == '3') $statusClass = 'danger';
                                            elseif ($order->status == '1') $statusClass = 'info';
                                            ?>
                                            <span class="badge badge-<?= $statusClass ?>"><?= $statusLabel ?></span>
                                        </td>
                                        <td><?= substr($order->keterangan ?? '-', 0, 50) ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= base_url('laporan/order/detail/' . $order->id) ?>" 
                                                   class="btn btn-info btn-sm rounded-0" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('laporan/order/detail_items/' . $order->id) ?>" 
                                                   class="btn btn-secondary btn-sm rounded-0" title="Detail Item">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                                <a href="<?= base_url('laporan/order/print_invoice/' . $order->id) ?>" 
                                                   target="_blank"
                                                   class="btn btn-warning btn-sm rounded-0" title="Print Invoice">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
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
            id_supplier: $('#id_supplier').val() || '',
            status: $('#status').val() || ''
        }).toString();

        $('#exportExcelBtn').attr('href', '<?= base_url('laporan/order/export_excel') ?>?' + params);
        $('#exportPdfBtn').attr('href', '<?= base_url('laporan/order/export_pdf') ?>?' + params);
    }

    // Update export links when filters change
    $('#id_supplier, #status').on('change', updateExportLinks);

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

