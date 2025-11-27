<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Sales Transaction Cashier Interface View
 * This file represents the View.
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<?php $isSuperAdmin = session()->get('group_id') == 1; ?>
<!-- CSRF Token -->
<?= csrf_field() ?>

<!-- Summary Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= number_format($totalSales, 0, ',', '.') ?></h3>
                <p>Total Penjualan Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $totalTransactions ?></h3>
                <p>Transaksi Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= count($customers) ?></h3>
                <p>Total Pelanggan</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= count($platforms) ?></h3>
                <p>Platform Pembayaran</p>
            </div>
            <div class="icon">
                <i class="fas fa-credit-card"></i>
            </div>
        </div>
    </div>
</div>

<!-- Cashier Interface -->
<div class="row">
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cash-register"></i> Kasir - Transaksi Penjualan
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('transaksi/jual/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Transaksi Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari nota/pelanggan..."
                            value="<?= $search ?>">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="statusFilter">
                            <option value="">Semua Status</option>
                            <?php foreach ($statusOptions as $key => $value): ?>
                                <option value="<?= $key ?>" <?= $status == $key ? 'selected' : '' ?>><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="dateFrom" value="<?= $dateFrom ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="dateTo" value="<?= $dateTo ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-secondary" id="searchBtn">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="resetBtn">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>No. Nota</th>
                                <th>Pelanggan</th>
                                <th class="text-right">Total</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data transaksi</td>
                                </tr>
                            <?php else: ?>
                                <?php
                                $startNumber = ($currentPage - 1) * $perPage;
                                foreach ($transactions as $index => $row):
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $startNumber + $index + 1 ?></td>
                                        <td>
                                            <strong><?= esc(isset($row->no_nota) ? $row->no_nota : 'Unknown') ?></strong><br />
                                            <small class="text-muted"><?= tgl_indo6($row->created_at) ?></small><br />
                                            <small class="text-muted"><?= esc($user->username) ?></small><br/>
                                            <?= status_trx($row->status ?? '0'); ?>
                                        </td>
                                        <td>
                                            <?php
                                            // Find Pelanggan
                                            $customerName = 'Umum';
                                            if ($row->id_pelanggan) {
                                                foreach ($customers as $customer) {
                                                    if ($customer->id == $row->id_pelanggan) {
                                                        $customerName = isset($customer->nama) ? $customer->nama : 'Umum';
                                                        break;
                                                    }
                                                }
                                            }
                                            echo '' . esc($customerName) . '';
                                            ?>
                                        </td>
                                        <td class="text-right">
                                            <?php $rowStatusRetur = $row->status_retur ?? '0'; ?>
                                            <strong>Rp
                                                <?= number_format(isset($row->jml_gtotal) ? $row->jml_gtotal : 0, 0, ',', '.') ?></strong>
                                            <?= br().status_bayar($row->status_bayar ?? '0'); ?>
                                            <?php if ($rowStatusRetur === '1'): ?>
                                                <?= br() ?><span class="badge badge-success">Retur Disetujui</span>
                                            <?php elseif ($rowStatusRetur === '2'): ?>
                                                <?= br() ?><span class="badge badge-danger">Retur Ditolak</span>
                                            <?php elseif ($rowStatusRetur === '0' && (float)($row->jml_retur ?? 0) != 0): ?>
                                                <?= br() ?><span class="badge badge-warning">Retur Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-sm"
                                                    onclick="viewTransaction(<?= $row->id ?>)" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm"
                                                    onclick="showPrintOptions('jual', <?= $row->id ?>)" title="Print">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                                <?php if (isset($row->status) && $row->status == '0'): ?>
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        onclick="editTransaction(<?= $row->id ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (isset($row->status) && $row->status == '1'): ?>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="showReturModal(<?= $row->id ?>)" title="Retur">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (akses_kasir() != TRUE && (float)($row->jml_retur ?? 0) != 0): ?>
                                                    <button type="button" class="btn btn-outline-success btn-sm"
                                                        onclick="approveRetur(<?= $row->id ?>)" title="Approve Retur">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="rejectRetur(<?= $row->id ?>)" title="Reject Retur">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    <?= $pager->links('transjual', 'adminlte_pagination') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Sidebar -->
    <div class="col-md-3">
        <div class="card shadow-sm rounded-0 border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 rounded-0 pb-2 pt-3">
                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size:1.25rem;">
                    <i class="fas fa-bolt text-warning mr-2"></i> Aksi Cepat
                </h3>
            </div>
            <div class="card-body p-3 rounded-0">
                <div class="d-flex flex-column gap-3">
                    <button type="button"
                        class="btn btn-primary btn-lg rounded-0 py-3 mb-2 shadow-sm text-left d-flex align-items-center"
                        onclick="openSO()">
                        <i class="fas fa-plus fa-lg mr-3"></i>
                        <span class="font-weight-bold" style="font-size:1.1rem;">Transaksi Baru</span>
                    </button>
                    <button type="button"
                        class="btn btn-success btn-lg rounded-0 py-3 mb-2 shadow-sm text-left d-flex align-items-center"
                        onclick="openCashier()">
                        <i class="fas fa-cash-register fa-lg mr-3"></i>
                        <span class="font-weight-bold" style="font-size:1.1rem;">Buka Kasir</span>
                    </button>
                    <button type="button"
                        class="btn btn-info btn-lg rounded-0 py-3 mb-2 shadow-sm text-left d-flex align-items-center"
                        onclick="viewReports()">
                        <i class="fas fa-chart-bar fa-lg mr-3"></i>
                        <span class="font-weight-bold" style="font-size:1.1rem;">Laporan</span>
                    </button>
                    <a href="<?= base_url('transaksi/retur/jual/exchange') ?>"
                        class="btn btn-warning btn-lg rounded-0 py-3 shadow-sm text-left d-flex align-items-center mb-2">
                        <i class="fas fa-exchange-alt fa-lg mr-3"></i>
                        <span class="font-weight-bold" style="font-size:1.1rem;">Retur Tukar</span>
                    </a>
                    <a href="<?= base_url('transaksi/refund') ?>"
                        class="btn btn-danger btn-lg rounded-0 py-3 shadow-sm text-left d-flex align-items-center">
                        <i class="fas fa-money-bill-wave fa-lg mr-3"></i>
                        <span class="font-weight-bold" style="font-size:1.1rem;">Retur Refund</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock"></i> Transaksi Terbaru
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php
                    $recentTransactions = array_slice($transactions, 0, 5);
                    foreach ($recentTransactions as $row):
                        ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?= esc(isset($row->no_nota) ? $row->no_nota : 'Unknown') ?></h6>
                                <small><?= isset($row->created_at) ? date('H:i', strtotime($row->created_at)) : '-' ?></small>
                            </div>
                            <p class="mb-1">Rp
                                <?= number_format(isset($row->jml_gtotal) ? $row->jml_gtotal : 0, 0, ',', '.') ?>
                            </p>
                            <small>
                                <?php
                                $statusBadges = [
                                    '0' => '<span class="badge badge-secondary">Draft</span>',
                                    '1' => '<span class="badge badge-success">Selesai</span>',
                                    '2' => '<span class="badge badge-danger">Batal</span>',
                                    '3' => '<span class="badge badge-warning">Retur</span>',
                                    '4' => '<span class="badge badge-info">Pending</span>'
                                ];
                                echo $statusBadges[isset($row->status) ? $row->status : '0'] ?? '';
                                ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Transaction Modal -->
<div class="modal fade" id="newTransactionModal" tabindex="-1" role="dialog" aria-labelledby="newTransactionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newTransactionModalLabel">
                    <i class="fas fa-plus"></i> Transaksi Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newTransactionForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_nota">No. Nota</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="no_nota" name="no_nota" readonly>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="generateNotaNumber()">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_pelanggan">Pelanggan</label>
                                <select class="form-control select2" id="id_pelanggan" name="id_pelanggan">
                                    <option value="">Pilih Pelanggan</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?= $customer->id ?>">
                                            <?= esc(isset($customer->nama) ? $customer->nama : 'Unknown') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_sales">Sales</label>
                                <select class="form-control select2" id="id_sales" name="id_sales">
                                    <option value="">Pilih Sales</option>
                                    <?php foreach ($sales as $sale): ?>
                                        <option value="<?= $sale->id ?>">
                                            <?= esc(isset($sale->nama) ? $sale->nama : 'Unknown') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_gudang">Gudang</label>
                                <select class="form-control select2" id="id_gudang" name="id_gudang">
                                    <option value="">Pilih Gudang</option>
                                    <?php foreach ($warehouses as $warehouse): ?>
                                        <option value="<?= $warehouse->id ?>">
                                            <?= esc(isset($warehouse->gudang) ? $warehouse->gudang : 'Unknown') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="createTransaction()">
                    <i class="fas fa-save"></i> Buat Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Detail Modal -->
<div class="modal fade" id="transactionDetailModal" tabindex="-1" role="dialog"
    aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionDetailModalLabel">
                    <i class="fas fa-eye"></i> Detail Transaksi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="transactionDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="printBtn" style="display: none;">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Retur Confirmation Modal -->
<div class="modal fade" id="returConfirmModal" tabindex="-1" role="dialog"
    aria-labelledby="returConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="returConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Retur Transaksi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                </div>
                <p>Apakah Anda yakin ingin melakukan retur untuk transaksi ini?</p>
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check text-danger"></i> Transaksi akan dibatalkan</li>
                    <li><i class="fas fa-check text-danger"></i> Stok akan dikembalikan untuk item yang stockable</li>
                    <li><i class="fas fa-check text-danger"></i> Semua kolom keuangan akan dihitung ulang</li>
                </ul>
                <div class="mt-3 p-3 bg-light rounded">
                    <strong>No. Nota:</strong> <span id="returModalNota">-</span><br>
                    <strong>Total:</strong> <span id="returModalTotal">-</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmReturBtn">
                    <i class="fas fa-undo"></i> Ya, Proses Retur
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        // Generate nota number on page load
        generateNotaNumber();

        // Bind retur modal confirm button
        $('#confirmReturBtn').on('click', function() {
            processRetur();
        });

        // Reset transaction ID when retur modal is closed
        $('#returConfirmModal').on('hidden.bs.modal', function() {
            returTransactionId = null;
        });

        // Search functionality
        $('#searchBtn').on('click', function () {
            performSearch();
        });

        // Reset search
        $('#resetBtn').on('click', function () {
            $('#searchInput').val('');
            $('#statusFilter').val('');
            $('#dateFrom').val('');
            $('#dateTo').val('');
            performSearch();
        });

        // Enter key on search input
        $('#searchInput').on('keypress', function (e) {
            if (e.which == 13) {
                performSearch();
            }
        });
    });

    function performSearch() {
        const search = $('#searchInput').val();
        const status = $('#statusFilter').val();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();

        let url = '<?= base_url('transaksi/jual') ?>?';
        const params = new URLSearchParams();

        if (search) params.append('search', search);
        if (status) params.append('status', status);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);

        window.location.href = url + params.toString();
    }

    function generateNotaNumber() {
        $.ajax({
            url: '<?= base_url('transaksi/jual/generate-nota') ?>',
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    $('#no_nota').val(response.nota_number);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error generating nota number:', error);
                if (xhr.status === 401) {
                    toastr.error('Sesi Anda telah berakhir. Silakan login ulang.');
                    setTimeout(function () {
                        window.location.href = '<?= base_url('auth/login') ?>';
                    }, 2000);
                }
            }
        });
    }

    function viewTransaction(id) {
        $.ajax({
            url: '<?= base_url('transaksi/jual/get-details') ?>/' + id,
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    $('#transactionDetailContent').html(generateTransactionDetailHTML(response));
                    $('#transactionDetailModal').modal('show');
                } else {
                    toastr.error('Gagal memuat detail transaksi');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading transaction details:', error);
                if (xhr.status === 401) {
                    toastr.error('Sesi Anda telah berakhir. Silakan login ulang.');
                    setTimeout(function () {
                        window.location.href = '<?= base_url('auth/login') ?>';
                    }, 2000);
                }
            }
        });
    }

    function generateTransactionDetailHTML(data) {
        const transaction = data.transaction;
        const details = data.details;
        const platforms = data.platforms;

        let html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Informasi Transaksi</h6>
                <table class="table table-sm">
                    <tr><td>No. Nota</td><td>: <strong>${transaction.no_nota}</strong></td></tr>
                    <tr><td>Tanggal</td><td>: ${new Date(transaction.created_at).toLocaleString('id-ID')}</td></tr>
                    <tr><td>Status</td><td>: ${getStatusBadge(transaction.status)}</td></tr>
                    <tr><td>Status Bayar</td><td>: ${getPaymentStatusBadge(transaction.status_bayar)}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Total</h6>
                <table class="table table-sm">
                    <tr><td>Subtotal</td><td class="text-right">Rp ${numberFormat(transaction.jml_subtotal)}</td></tr>
                    <tr><td>Diskon</td><td class="text-right">Rp ${numberFormat(transaction.jml_diskon)}</td></tr>
                    <tr><td>PPN</td><td class="text-right">Rp ${numberFormat(transaction.jml_ppn)}</td></tr>
                    <tr><td><strong>Grand Total</strong></td><td class="text-right"><strong>Rp ${numberFormat(transaction.jml_gtotal)}</strong></td></tr>
                </table>
            </div>
        </div>
        <hr>
        <h6>Detail Item</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>`;

        details.forEach((detail, index) => {
            // Extract variant information if available
            let productDisplay = detail.produk || detail.nama_item || 'Unknown Item';
            let variantInfo = '';
            
            // Check if variant information is available
            if (detail.nama_varian) {
                // Variant is already matched and available
                variantInfo = `<br><small class="text-muted"><i class="fas fa-tag"></i> Varian: ${detail.nama_varian}${detail.kode_varian ? ' (' + detail.kode_varian + ')' : ''}</small>`;
            } else if (detail.produk && detail.nama_item) {
                // Try to extract variant from produk field (format: "Item Name - Variant Name")
                const produk = detail.produk;
                const itemName = detail.nama_item;
                
                // Check if produk contains " - " which indicates variant
                if (produk.includes(' - ') && produk !== itemName) {
                    const parts = produk.split(' - ');
                    if (parts.length > 1) {
                        const variantName = parts.slice(1).join(' - '); // Join in case variant name contains " - "
                        variantInfo = `<br><small class="text-muted"><i class="fas fa-tag"></i> Varian: ${variantName}</small>`;
                        // Use the base item name for display
                        productDisplay = itemName;
                    }
                }
            }
            
            html += `
            <tr>
                <td>${index + 1}</td>
                <td>${productDisplay}${variantInfo}</td>
                <td>${detail.jml} ${detail.satuan || detail.nama_satuan || 'PCS'}</td>
                <td class="text-right">Rp ${numberFormat(detail.harga)}</td>
                <td class="text-right">Rp ${numberFormat(detail.subtotal)}</td>
            </tr>`;
        });

        html += `
                </tbody>
            </table>
        </div>`;

        if (platforms && platforms.length > 0) {
            html += `
        <hr>
        <h6>Platform Pembayaran</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>`;

            platforms.forEach(platform => {
                const displayName = platform.nama_platform || platform.platform || '-';
                const kode = platform.platform_kode || platform.kode || '';
                const platformLabel = kode ? `${kode} / ${displayName}` : displayName;

                html += `
                <tr>
                    <td>${platformLabel}</td>
                    <td class="text-right">Rp ${numberFormat(platform.nominal)}</td>
                    <td>${platform.keterangan || platform.keterangan_platform || '-'}</td>
                </tr>`;
            });

            html += `
                </tbody>
            </table>
        </div>`;
        }

        const voucherInfo = resolveVoucherInfo(transaction, platforms);
        if (voucherInfo) {
            html += `
        <hr>
        <h6>Voucher</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Jenis</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${voucherInfo.code}</td>
                        <td>${voucherInfo.type}</td>
                        <td class="text-right">${voucherInfo.value}</td>
                    </tr>
                </tbody>
            </table>
        </div>`;
        }

        return html;
    }

    function getStatusBadge(status) {
        const badges = {
            '0': '<span class="badge badge-secondary">Draft</span>',
            '1': '<span class="badge badge-success">Selesai</span>',
            '2': '<span class="badge badge-danger">Batal</span>',
            '3': '<span class="badge badge-warning">Retur</span>',
            '4': '<span class="badge badge-info">Pending</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">Unknown</span>';
    }

    function getPaymentStatusBadge(status) {
        const badges = {
            '0': '<span class="badge badge-warning">Belum Lunas</span>',
            '1': '<span class="badge badge-success">Lunas</span>',
            '2': '<span class="badge badge-danger">Kurang</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">Unknown</span>';
    }

    function numberFormat(number) {
        return new Intl.NumberFormat('id-ID').format(number || 0);
    }

    function formatPercent(number) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(number || 0);
    }

    function resolveVoucherInfo(transaction, platforms = []) {
        if (!transaction) {
            return null;
        }

        const code = transaction.voucher_code && transaction.voucher_code !== '' ? transaction.voucher_code : '-';
        const voucherPayment = (platforms || []).find(platform => (platform.jenis_voucher && platform.jenis_voucher !== ''));
        const amount = parseNumericValue(transaction.voucher_discount_amount ?? (voucherPayment?.nominal ?? 0));
        const percent = parseNumericValue(transaction.voucher_discount ?? (voucherPayment?.platform_persen ?? 0));
        const rawType = (voucherPayment?.jenis_voucher || transaction.voucher_type || '').toString().trim().toLowerCase();

        const hasNominal = amount > 0;
        const hasPercent = percent > 0;

        if (!code && !hasNominal && !hasPercent && !rawType) {
            return null;
        }

        const percentKeywords = ['persen', 'percent', 'percentage', '%'];
        const nominalKeywords = ['nominal', 'amount', 'fixed', 'rupiah'];

        let resolvedType = 'NOMINAL';
        let displayValue = 'Rp ' + numberFormat(amount);

        if (rawType && percentKeywords.includes(rawType)) {
            resolvedType = 'PERSEN';
            displayValue = `${formatPercentValue(hasPercent ? percent : amount)}%`;
        } else if (rawType && nominalKeywords.includes(rawType)) {
            resolvedType = 'NOMINAL';
            displayValue = `Rp ${numberFormat(amount)}`;
        } else if (hasPercent && !hasNominal) {
            resolvedType = 'PERSEN';
            displayValue = `${formatPercentValue(percent)}%`;
        } else if (hasNominal && !hasPercent) {
            resolvedType = 'NOMINAL';
            displayValue = `Rp ${numberFormat(amount)}`;
        } else if (hasPercent) {
            resolvedType = 'PERSEN';
            displayValue = `${formatPercentValue(percent)}%`;
        }

        return {
            code,
            type: resolvedType,
            value: displayValue
        };
    }

    function formatPercentValue(value) {
        const numeric = parseFloat(value) || 0;
        const formatted = numeric.toFixed(2).replace(/\.?0+$/, '');
        return formatted === '' ? '0' : formatted.replace('.', ',');
    }

    function parseNumericValue(value) {
        if (value === null || value === undefined) {
            return 0;
        }
        if (typeof value === 'number') {
            return value;
        }

        const stringValue = value.toString().trim();
        if (stringValue === '') {
            return 0;
        }

        const normalized = stringValue
            .replace(/[^0-9,.-]/g, '')
            .replace(/\./g, '')
            .replace(',', '.');

        const parsed = parseFloat(normalized);
        return isNaN(parsed) ? 0 : parsed;
    }

    function createTransaction() {
        const formData = {
            no_nota: $('#no_nota').val(),
            id_pelanggan: $('#id_pelanggan').val(),
            id_sales: $('#id_sales').val(),
            id_gudang: $('#id_gudang').val()
        };

        // Basic validation
        if (!formData.no_nota) {
            toastr.error('No. Nota harus diisi');
            return;
        }

        // Here you would typically submit the form to create a new transaction
        // For now, we'll just close the modal and show a success message
        $('#newTransactionModal').modal('hide');
        toastr.success('Transaksi baru berhasil dibuat');

        // Reload the page to show the new transaction
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    function editTransaction(id) {
        // Redirect to edit page or open edit modal
        window.location.href = '<?= base_url('transaksi/jual/edit') ?>/' + id;
    }

    // Store transaction ID for retur
    let returTransactionId = null;

    function showReturModal(id) {
        returTransactionId = id;
        
        // Fetch transaction details to show in modal
        $.ajax({
            url: '<?= base_url('transaksi/jual/get-details') ?>/' + id,
            type: 'GET',
            success: function (response) {
                if (response.success && response.transaction) {
                    const transaction = response.transaction;
                    $('#returModalNota').text(transaction.no_nota || '-');
                    $('#returModalTotal').text('Rp ' + formatCurrency(transaction.jml_gtotal || 0));
                    $('#returConfirmModal').modal('show');
                } else {
                    toastr.error('Gagal memuat detail transaksi');
                }
            },
            error: function () {
                // Still show modal even if details fail to load
                $('#returModalNota').text('-');
                $('#returModalTotal').text('-');
                $('#returConfirmModal').modal('show');
            }
        });
    }

    function processRetur() {
        if (!returTransactionId) {
            toastr.error('ID transaksi tidak valid');
            return;
        }

        // Disable button during processing
        const $btn = $('#confirmReturBtn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

        $.ajax({
            url: '<?= base_url('transaksi/jual/retur') ?>/' + returTransactionId,
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            success: function (response) {
                if (response.success) {
                    $('#returConfirmModal').modal('hide');
                    toastr.success(response.message || 'Retur berhasil diajukan dan menunggu persetujuan.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    $btn.prop('disabled', false).html('<i class="fas fa-undo"></i> Ya, Proses Retur');
                    toastr.error(response.message || 'Gagal memproses retur');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error processing retur:', error);
                $btn.prop('disabled', false).html('<i class="fas fa-undo"></i> Ya, Proses Retur');
                let errorMessage = 'Gagal memproses retur';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
                if (xhr.status === 401) {
                    setTimeout(function () {
                        window.location.href = '<?= base_url('auth/login') ?>';
                    }, 2000);
                }
            }
        });
    }

    function submitReturDecision(url, defaultSuccessMessage) {
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message || defaultSuccessMessage);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    toastr.error(response.message || 'Gagal memproses permintaan');
                }
            },
            error: function (xhr, status, error) {
                console.error('Retur approval error:', error);
                let errorMessage = 'Gagal memproses permintaan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
                if (xhr.status === 401) {
                    setTimeout(function () {
                        window.location.href = '<?= base_url('auth/login') ?>';
                    }, 2000);
                }
            }
        });
    }

    function approveRetur(id) {
        if (!confirm('Setujui permintaan retur untuk transaksi ini?')) {
            return;
        }
        submitReturDecision('<?= base_url('transaksi/jual/retur/approve') ?>/' + id, 'Retur berhasil disetujui.');
    }

    function rejectRetur(id) {
        if (!confirm('Tolak permintaan retur untuk transaksi ini?')) {
            return;
        }
        submitReturDecision('<?= base_url('transaksi/jual/retur/reject') ?>/' + id, 'Retur berhasil ditolak.');
    }

    // Global variables
    let currentTransactionData = null;

    function openSO() {
        // Redirect to sales order creation page
        window.location.href = '<?= base_url('transaksi/jual/create') ?>';
    }

    function openCashier() {
        // Redirect to cashier page
        window.location.href = '<?= base_url('transaksi/jual/cashier') ?>';
    }

    function generateReceiptHTML(transactionData) {
        const { no_nota, customer_name, customer_type, items, subtotal, discount, voucher, ppn, total, payment_methods, date, outlet } = transactionData;

        let itemsHTML = '';
        if (items && items.length > 0) {
            items.forEach(item => {
                itemsHTML += `
                    <div class="item">
                        <div>${item.name}</div>
                        <div>${item.quantity} x ${formatCurrency(item.price)} = ${formatCurrency(item.total)}</div>
                    </div>
                `;
            });
        } else {
            itemsHTML = '<div class="item">No items available</div>';
        }

        let paymentHTML = '';
        if (payment_methods && payment_methods.length > 0) {
            payment_methods.forEach(pm => {
                const methodName = pm.type === '1' ? 'Tunai' : pm.type === '2' ? 'Non Tunai' : 'Piutang';
                paymentHTML += `<div>${methodName}: ${formatCurrency(pm.amount)}</div>`;
            });
        }

        return `
            <div class="receipt">
                <div class="header">
                    <h3>KOPMENSA</h3>
                    <div>${outlet || 'OUTLET'}</div>
                    <div>${date || new Date().toLocaleString('id-ID')}</div>
                    <div>No: ${no_nota || 'DRAFT'}</div>
                </div>
                
                <div class="divider"></div>
                
                <div class="customer">
                    <div>Customer: ${customer_name || 'UMUM'}</div>
                    <div>Type: ${customer_type || 'UMUM'}</div>
                </div>
                
                <div class="divider"></div>
                
                <div class="items">
                    ${itemsHTML}
                </div>
                
                <div class="divider"></div>
                
                <div class="summary">
                    <div>Subtotal: ${formatCurrency(subtotal || 0)}</div>
                    ${discount > 0 ? `<div>Diskon: ${discount}%</div>` : ''}
                    ${voucher ? `<div>Voucher: ${voucher}</div>` : ''}
                    <div>PPN (${ppn || 11}%): ${formatCurrency((subtotal || 0) * (ppn || 11) / 100)}</div>
                    <div class="total">TOTAL: ${formatCurrency(total || 0)}</div>
                </div>
                
                ${paymentHTML ? `
                    <div class="divider"></div>
                    <div class="payment">
                        <div><strong>Pembayaran:</strong></div>
                        ${paymentHTML}
                    </div>
                ` : ''}
                
                <div class="divider"></div>
                
                <div class="footer">
                    <div>Terima kasih atas kunjungan Anda</div>
                    <div>Barang yang sudah dibeli tidak dapat dikembalikan</div>
                    <div>Powered by Kopmensa System</div>
                </div>
            </div>
        `;
    }

    function formatCurrency(amount) {
        return `Rp ${numberFormat(amount)}`;
    }

    function viewReports() {
        // Redirect to reports page
        window.location.href = '<?= base_url('transaksi/jual/reports') ?>';
    }

    function viewReturns() {
        // Redirect to returns page
        window.location.href = '<?= base_url('transaksi/jual/returns') ?>';
    }
</script>

<!-- Include Print Options Modal -->
<?= $this->include('admin-lte-3/components/print_options_modal') ?>

<?= $this->endSection() ?>