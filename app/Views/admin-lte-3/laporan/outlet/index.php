<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying outlet reports
 * This file represents the outlet report index view.
 */
?>

<?php helper('angka'); ?>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css">

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.7/js/responsive.bootstrap4.min.js"></script>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i> Laporan Outlet
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/outlet/export_excel') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_outlet=<?= $idOutlet ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/outlet') ?>" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Outlet</label>
                            <select name="id_outlet" class="form-control form-control-sm">
                                <option value="">Semua Outlet</option>
                                <?php foreach ($outletList as $outlet): ?>
                                    <option value="<?= $outlet->id ?>" <?= $idOutlet == $outlet->id ? 'selected' : '' ?>>
                                        <?= $outlet->nama ?>
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
                                <h3><?= format_angka($totalOutlets) ?></h3>
                                <p>Total Outlet</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-store"></i>
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
                                <h3><?= format_angka($totalTransactions) ?></h3>
                                <p>Total Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= format_angka($totalItems) ?></h3>
                                <p>Total Item</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outlet Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table mr-1"></i> Data Outlet
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="outletTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Nama Outlet</th>
                                        <th width="15%">Total Transaksi</th>
                                        <th width="15%">Total Penjualan</th>
                                        <th width="15%">Rata-rata Penjualan</th>
                                        <th width="10%">Pelanggan Unik</th>
                                        <th width="10%">Total Item</th>
                                        <th width="10%">Total Stok</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($outletDetails as $detail): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td>
                                                <strong><?= $detail['outlet']['nama'] ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info"><?= format_angka($detail['sales']['total_transactions'] ?? 0) ?></span>
                                            </td>
                                            <td class="text-right">
                                                <strong class="text-success">Rp <?= format_angka($detail['sales']['total_sales'] ?? 0) ?></strong>
                                            </td>
                                            <td class="text-right">
                                                Rp <?= format_angka($detail['sales']['avg_sales'] ?? 0) ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-warning"><?= format_angka($detail['sales']['unique_customers'] ?? 0) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-primary"><?= format_angka($detail['stock']['total_items'] ?? 0) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= ($detail['stock']['total_stock'] > 0) ? 'success' : 'danger' ?>">
                                                    <?= format_angka($detail['stock']['total_stock'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('laporan/outlet/detail/' . $detail['outlet']['id']) ?>" 
                                                       class="btn btn-info btn-sm" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            onclick="showTopItems(<?= $detail['outlet']['id'] ?>)" title="Top Items">
                                                        <i class="fas fa-trophy"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-sm" 
                                                            onclick="showTransactionDetails(<?= $detail['outlet']['id'] ?>)" title="Detail Transaksi">
                                                        <i class="fas fa-list"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="thead-dark">
                                    <tr>
                                        <th colspan="2" class="text-center">TOTAL</th>
                                        <th class="text-center"><?= format_angka($totalTransactions) ?></th>
                                        <th class="text-right">Rp <?= format_angka($totalSales) ?></th>
                                        <th class="text-right">Rp <?= format_angka($totalSales > 0 ? ($totalSales / $totalTransactions) : 0) ?></th>
                                        <th class="text-center">-</th>
                                        <th class="text-center"><?= format_angka($totalItems) ?></th>
                                        <th class="text-center">-</th>
                                        <th class="text-center">-</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Transaction Detail List -->
                <div class="card" id="transactionDetailCard" style="display: none;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-1"></i> Detail Transaksi Outlet
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="hideTransactionDetails()">
                                <i class="fas fa-times mr-1"></i> Tutup
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="transactionDetailTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">No Nota</th>
                                        <th width="15%">Tanggal</th>
                                        <th width="20%">Pelanggan</th>
                                        <th width="15%">Sales</th>
                                        <th width="15%">Total</th>
                                        <th width="15%">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionDetailBody">
                                    <!-- Transaction details will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Items Modal -->
                <div class="modal fade" id="topItemsModal" tabindex="-1" role="dialog" aria-labelledby="topItemsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="topItemsModalLabel">
                                    <i class="fas fa-trophy mr-1"></i> Top 5 Item Terjual
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="topItemsModalBody">
                                <!-- Content will be loaded here -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#outletTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        },
        pageLength: 25,
        order: [[2, 'desc']], // Sort by Total Transaksi descending
        columnDefs: [
            { orderable: false, targets: [0, 8] } // Disable sorting for No and Aksi columns
        ]
    });

    // Auto-submit form when filters change
    $('select[name="id_outlet"]').change(function() {
        $('form').submit();
    });
});

// Function to show top items in modal
function showTopItems(outletId) {
    // Find the outlet data
    const outletData = <?= json_encode($outletDetails) ?>;
    const outlet = outletData.find(o => o.outlet.id == outletId);
    
    if (outlet && outlet.top_items && outlet.top_items.length > 0) {
        let modalContent = `
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Item</th>
                            <th class="text-center">Qty Terjual</th>
                            <th class="text-right">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        outlet.top_items.forEach((item, index) => {
            modalContent += `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td><strong>${item.item_nama || '-'}</strong></td>
                    <td class="text-center">
                        <span class="badge badge-info">${formatNumber(item.total_qty || 0)}</span>
                    </td>
                    <td class="text-right">
                        <strong class="text-success">Rp ${formatNumber(item.total_value || 0)}</strong>
                    </td>
                </tr>
            `;
        });
        
        modalContent += `
                    </tbody>
                </table>
            </div>
        `;
        
        $('#topItemsModalBody').html(modalContent);
        $('#topItemsModal').modal('show');
    } else {
        $('#topItemsModalBody').html('<div class="alert alert-info">Tidak ada data item terjual untuk outlet ini.</div>');
        $('#topItemsModal').modal('show');
    }
}

// Function to show transaction details
function showTransactionDetails(outletId) {
    const outletData = <?= json_encode($outletDetails) ?>;
    const outlet = outletData.find(o => o.outlet.id == outletId);

    if (outlet && outlet.transaction_details && outlet.transaction_details.length > 0) {
        let tableContent = '';
        
        outlet.transaction_details.forEach((detail, index) => {
            tableContent += `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td><strong>${detail.no_nota || '-'}</strong></td>
                    <td>${detail.tanggal || '-'}</td>
                    <td>${detail.pelanggan_nama || '-'}</td>
                    <td>${detail.sales_nama || '-'}</td>
                    <td class="text-right">
                        <strong class="text-success">Rp ${formatNumber(detail.total_transaksi || 0)}</strong>
                    </td>
                    <td>
                        <span class="badge badge-info">${detail.status_transaksi || '-'}</span>
                    </td>
                </tr>
            `;
        });
        
        $('#transactionDetailBody').html(tableContent);
        $('#transactionDetailCard').show();
        
        // Scroll to transaction details
        $('#transactionDetailCard')[0].scrollIntoView({ behavior: 'smooth' });
    } else {
        $('#transactionDetailBody').html('<tr><td colspan="7" class="text-center"><div class="alert alert-info">Tidak ada detail transaksi untuk outlet ini.</div></td></tr>');
        $('#transactionDetailCard').show();
    }
}

// Function to hide transaction details
function hideTransactionDetails() {
    $('#transactionDetailCard').hide();
}

// Helper function to format numbers
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}
</script>
