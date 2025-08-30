<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-28
 * Github: github.com/mikhaelfelian
 * Description: Category Report view for Petty Cash Management
 * This file represents the View.
 */

helper('form');
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tags"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <button type="button" class="btn btn-success btn-sm rounded-0" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="<?= base_url('transaksi/petty/category-report') ?>" class="form-inline">
                            <div class="form-group mr-2">
                                <label class="mr-2">Dari Tanggal:</label>
                                <input type="date" name="date_from" value="<?= $filters['date_from'] ?>" class="form-control form-control-sm">
                            </div>
                            <div class="form-group mr-2">
                                <label class="mr-2">Sampai Tanggal:</label>
                                <input type="date" name="date_to" value="<?= $filters['date_to'] ?>" class="form-control form-control-sm">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Category Report Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="categoryReportTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Jumlah Transaksi</th>
                                <th>Total Kas Masuk</th>
                                <th>Total Kas Keluar</th>
                                <th>Saldo Netto</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $totalIn = 0;
                            $totalOut = 0;
                            $totalTransactions = 0;
                            
                            if (!empty($categoryReport)) : 
                                foreach ($categoryReport as $cat) : 
                                    $totalIn += $cat['total_in'] ?? 0;
                                    $totalOut += $cat['total_out'] ?? 0;
                                    $totalTransactions += $cat['transaction_count'] ?? 0;
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <span class="badge badge-info"><?= $cat['category_name'] ?? 'Tanpa Kategori' ?></span>
                                    </td>
                                    <td class="text-center"><?= $cat['transaction_count'] ?? 0 ?></td>
                                    <td class="text-success text-right"><?= number_format($cat['total_in'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-danger text-right"><?= number_format($cat['total_out'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-right font-weight-bold">
                                        <?php 
                                        $netto = ($cat['total_in'] ?? 0) - ($cat['total_out'] ?? 0);
                                        $class = $netto >= 0 ? 'text-success' : 'text-danger';
                                        ?>
                                        <span class="<?= $class ?>"><?= number_format($netto, 0, ',', '.') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $totalCategory = ($cat['total_in'] ?? 0) + ($cat['total_out'] ?? 0);
                                        $percentage = $totalCategory > 0 ? (($totalCategory / ($totalIn + $totalOut)) * 100) : 0;
                                        ?>
                                        <span class="badge badge-secondary"><?= number_format($percentage, 1) ?>%</span>
                                    </td>
                                </tr>
                            <?php 
                                endforeach; 
                            endif; 
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-dark font-weight-bold">
                                <td colspan="2">TOTAL</td>
                                <td class="text-center"><?= $totalTransactions ?></td>
                                <td class="text-success text-right"><?= number_format($totalIn, 0, ',', '.') ?></td>
                                <td class="text-danger text-right"><?= number_format($totalOut, 0, ',', '.') ?></td>
                                <td class="text-right">
                                    <?php 
                                    $grandTotal = $totalIn - $totalOut;
                                    $class = $grandTotal >= 0 ? 'text-success' : 'text-danger';
                                    ?>
                                    <span class="<?= $class ?>"><?= number_format($grandTotal, 0, ',', '.') ?></span>
                                </td>
                                <td class="text-center">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Summary Cards -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-tags"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Kategori</span>
                                <span class="info-box-number"><?= count($categoryReport) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-arrow-down"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Kas Masuk</span>
                                <span class="info-box-number"><?= number_format($totalIn, 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-arrow-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Kas Keluar</span>
                                <span class="info-box-number"><?= number_format($totalOut, 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Saldo Netto</span>
                                <span class="info-box-number"><?= number_format($grandTotal, 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
.info-box {
    display: block;
    min-height: 80px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 3px;
    margin-bottom: 15px;
}
.info-box-icon {
    border-top-left-radius: 3px;
    border-bottom-left-radius: 3px;
    display: block;
    float: left;
    height: 80px;
    width: 80px;
    text-align: center;
    font-size: 40px;
    line-height: 80px;
    background: rgba(0,0,0,0.2);
    color: #fff;
}
.info-box-content {
    padding: 5px 10px;
    margin-left: 80px;
}
.info-box-text {
    display: block;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#categoryReportTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[5, "desc"]], // Sort by netto amount
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});

function exportToExcel() {
    // Create a temporary form to submit the export request
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= base_url('transaksi/petty/export-category-report') ?>';
    
    // Add date filters
    var dateFrom = document.createElement('input');
    dateFrom.type = 'hidden';
    dateFrom.name = 'date_from';
    dateFrom.value = '<?= $filters['date_from'] ?>';
    form.appendChild(dateFrom);
    
    var dateTo = document.createElement('input');
    dateTo.type = 'hidden';
    dateTo.name = 'date_to';
    dateTo.value = '<?= $filters['date_to'] ?>';
    form.appendChild(dateTo);
    
    // Add CSRF token
    var csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '<?= csrf_token() ?>';
    csrf.value = '<?= csrf_hash() ?>';
    form.appendChild(csrf);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
<?= $this->endSection() ?>
