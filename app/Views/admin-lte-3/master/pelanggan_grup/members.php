<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-23
 * Github : github.com/mikhaelfelian
 * description : View for managing customer group members
 * This file represents the View for managing group members.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Group Info Header -->
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users"></i> Kelola Member Grup: <strong><?= esc($grup->grup) ?></strong>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('master/customer-group') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali ke List Grup
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Member</span>
                                <span class="info-box-number"><?= count($currentMembers) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-user-plus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pelanggan Tersedia</span>
                                <span class="info-box-number"><?= count($availableCustomers) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">
                            <small>
                                <strong>Deskripsi:</strong> <?= esc($grup->deskripsi ?: 'Tidak ada deskripsi') ?><br>
                                <strong>Status:</strong>
                                <span class="badge badge-<?= $grup->status == '1' ? 'success' : 'danger' ?>">
                                    <?= $grup->status == '1' ? 'Aktif' : 'Non-Aktif' ?>
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Current Members -->
    <div class="col-md-5">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-check"></i> Member Saat Ini
                    <span class="badge badge-light ml-2"><?= count($currentMembers) ?></span>
                </h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($currentMembers)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-users fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Belum ada member dalam grup ini</p>
                        <small>Gunakan panel kanan untuk menambahkan member</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="sticky-top bg-success text-white">
                                <tr>
                                    <th width="10%">No</th>
                                    <th>Nama</th>
                                    <th width="15%">Telepon</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($currentMembers as $member): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td>
                                            <strong><?= esc($member->nama) ?></strong>
                                            <?php if (isset($member->no_telp) && $member->no_telp): ?>
                                                <br><small class="text-muted"><?= esc($member->no_telp) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= esc($member->no_telp ?? '-') ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm rounded-0"
                                                onclick="removeMember(<?= $grup->id ?>, <?= $member->id_pelanggan ?>, '<?= esc($member->nama) ?>')"
                                                title="Hapus dari Grup">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <!-- Available Customers -->
    <div class="col-md-7">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-plus"></i> Tambah Member Baru
                    <span class="badge badge-light ml-2"><?= count($availableCustomers) ?></span>
                </h3>
            </div>
            <div class="card-body">
                <?php if (empty($availableCustomers)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-user-plus fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Semua pelanggan sudah dalam grup ini</p>
                        <small>Tidak ada pelanggan tersedia untuk ditambahkan</small>
                    </div>
                <?php else: ?>
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" id="searchCustomer" class="form-control rounded-0"
                                    placeholder="Cari nama atau telepon pelanggan..." value="<?= esc($search) ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary rounded-0" type="button"
                                        onclick="window.performSearch()">
                                        Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="filterStatus" class="form-control rounded-0" onchange="window.performSearch()">
                                <option value="">Semua Status</option>
                                <option value="1" <?= $status == '1' ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= $status == '0' ? 'selected' : '' ?>>Non-Aktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <strong>Pilih Semua</strong> (<span id="selectedCount">0</span> terpilih)
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-primary btn-sm rounded-0"
                                onclick="addBulkMembers(<?= $grup->id ?>)">
                                <i class="fas fa-users"></i> Tambah Terpilih
                            </button>
                            <button type="button" class="btn btn-success btn-sm rounded-0"
                                onclick="addAllVisibleCustomers(<?= $grup->id ?>)">
                                <i class="fas fa-plus-circle"></i> Tambah Semua
                            </button>
                        </div>
                    </div>

                    <!-- Customer Table -->
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-striped table-hover mb-0" id="customerTable">
                            <thead class="sticky-top bg-white text-black">
                                <tr>
                                    <th width="5%" class="text-center align-middle">
                                        <input type="checkbox" id="selectAllTable" class="form-check-input">
                                    </th>
                                    <th>Nama Pelanggan</th>
                                    <th width="15%">Telepon</th>
                                    <th width="10%">Status</th>
                                    <th width="12%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($availableCustomers)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                                            <p class="mb-0">Tidak ada pelanggan tersedia</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($availableCustomers as $customer): ?>
                                        <tr class="customer-row" data-name="<?= strtolower($customer->nama) ?>"
                                            data-phone="<?= strtolower($customer->no_telp ?? '') ?>"
                                            data-status="<?= $customer->status ?? '1' ?>">
                                            <td class="text-center">
                                                <input type="checkbox" class="customer-checkbox form-check-input"
                                                    value="<?= $customer->id ?>">
                                            </td>
                                            <td>
                                                <strong><?= esc($customer->nama) ?></strong>
                                                <?php if (isset($customer->no_telp) && $customer->no_telp): ?>
                                                    <br><small class="text-muted"><?= esc($customer->no_telp) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= esc($customer->no_telp ?? '-') ?>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge badge-<?= ($customer->status ?? '1') == '1' ? 'success' : 'danger' ?>">
                                                    <?= ($customer->status ?? '1') == '1' ? 'Aktif' : 'Non-Aktif' ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-success btn-sm rounded-0"
                                                    onclick="addMember(<?= $grup->id ?>, <?= $customer->id ?>, '<?= esc($customer->nama) ?>')"
                                                    title="Tambah ke Grup">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination and Info -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Menampilkan <?= count($availableCustomers) ?> dari <?= $totalAvailable ?> pelanggan
                                (Halaman <?= $currentPage ?> dari <?= ceil($totalAvailable / $perPage) ?>)
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-0"
                                onclick="exportSelectedCustomers()">
                                <i class="fas fa-download"></i> Export Terpilih
                            </button>
                        </div>
                    </div>

                    <!-- Pagination Controls -->
                    <?php if ($totalAvailable > $perPage): ?>
                        <div class="row mt-2">
                            <div class="col-12">
                                <nav aria-label="Customer pagination">
                                    <ul class="pagination pagination-sm justify-content-center mb-0">
                                        <!-- Previous Page -->
                                        <?php if ($currentPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?= $currentPage - 1 ?>&search=<?= esc($search) ?>&status=<?= esc($status) ?>">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <!-- Page Numbers -->
                                        <?php
                                        $totalPages = ceil($totalAvailable / $perPage);
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($totalPages, $currentPage + 2);

                                        if ($startPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=1&search=<?= esc($search) ?>&status=<?= esc($status) ?>">1</a>
                                            </li>
                                            <?php if ($startPage > 2): ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                                <a class="page-link"
                                                    href="?page=<?= $i ?>&search=<?= esc($search) ?>&status=<?= esc($status) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($endPage < $totalPages): ?>
                                            <?php if ($endPage < $totalPages - 1): ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            <?php endif; ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?= $totalPages ?>&search=<?= esc($search) ?>&status=<?= esc($status) ?>"><?= $totalPages ?></a>
                                            </li>
                                        <?php endif; ?>

                                        <!-- Next Page -->
                                        <?php if ($currentPage < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?= $currentPage + 1 ?>&search=<?= esc($search) ?>&status=<?= esc($status) ?>">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Memproses...</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<style>
    /* Custom styling for professional look */
    .info-box {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .info-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .card-header {
        border-radius: 8px 8px 0 0 !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;
    }

    .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }

    .form-control {
        border-radius: 6px;
        border: 1px solid #ddd;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .badge {
        border-radius: 12px;
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    .sticky-top {
        z-index: 1020;
    }

    .customer-row {
        transition: all 0.2s ease;
    }

    .customer-row:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Loading animation */
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }

        .table-responsive {
            font-size: 0.9rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    }

    /* Custom scrollbar */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

<?= $this->section('scripts') ?>
<script>


    // Define all functions at GLOBAL scope - outside any wrapper functions
    window.updateSelectedCount = function () {
        var count = $('.customer-checkbox:checked').length;
        $('#selectedCount').text(count);
    };

    window.addMember = function (groupId, customerId, customerName) {
        if (confirm('Apakah Anda yakin ingin menambahkan "' + customerName + '" ke grup ini?')) {
            window.window.showLoading();

            $.ajax({
                url: '<?= base_url('master/customer-group/addMember') ?>',
                type: 'POST',
                data: {
                    id_grup: groupId,
                    id_pelanggan: customerId
                },
                dataType: 'json',
                success: function (response) {
                    window.hideLoading();
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    window.hideLoading();
                    toastr.error('Terjadi kesalahan server');
                }
            });
        }
    };

    window.removeMember = function (groupId, customerId, customerName) {
        if (confirm('Apakah Anda yakin ingin menghapus "' + customerName + '" dari grup ini?')) {
            window.showLoading();

            $.ajax({
                url: '<?= base_url('master/customer-group/removeMember') ?>',
                type: 'POST',
                data: {
                    id_grup: groupId,
                    id_pelanggan: customerId
                },
                dataType: 'json',
                success: function (response) {
                    window.hideLoading();
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    window.hideLoading();
                    toastr.error('Terjadi kesalahan server');
                }
            });
        }
    };

    window.addBulkMembers = function (groupId) {
        var selectedCustomers = [];
        $('.customer-checkbox:checked').each(function () {
            selectedCustomers.push($(this).val());
        });

        if (selectedCustomers.length === 0) {
            toastr.warning('Pilih pelanggan yang akan ditambahkan');
            return;
        }

        if (confirm('Apakah Anda yakin ingin menambahkan ' + selectedCustomers.length + ' pelanggan ke grup ini?')) {
            window.showLoading();

            $.ajax({
                url: '<?= base_url('master/customer-group/addBulkMembers') ?>',
                type: 'POST',
                data: {
                    id_grup: groupId,
                    customer_ids: selectedCustomers
                },
                dataType: 'json',
                success: function (response) {
                    window.hideLoading();
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    window.hideLoading();
                    toastr.error('Terjadi kesalahan server');
                }
            });
        }
    };

    window.addAllVisibleCustomers = function (groupId) {
        var totalCustomers = $('.customer-checkbox').length;

        if (totalCustomers === 0) {
            toastr.warning('Tidak ada pelanggan yang tersedia');
            return;
        }

        if (confirm('Apakah Anda yakin ingin menambahkan SEMUA ' + totalCustomers + ' pelanggan di halaman ini ke grup ini?')) {
            window.showLoading();

            var allCustomerIds = [];
            $('.customer-checkbox').each(function () {
                allCustomerIds.push($(this).val());
            });

            $.ajax({
                url: '<?= base_url('master/customer-group/addBulkMembers') ?>',
                type: 'POST',
                data: {
                    id_grup: groupId,
                    customer_ids: allCustomerIds
                },
                dataType: 'json',
                success: function (response) {
                    window.hideLoading();
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    window.hideLoading();
                    toastr.error('Terjadi kesalahan server');
                }
            });
        }
    };

    window.exportSelectedCustomers = function () {
        var selectedCustomers = [];
        $('.customer-checkbox:checked').each(function () {
            var row = $(this).closest('tr');
            selectedCustomers.push({
                nama: row.find('td:eq(1)').text().trim(),
                telepon: row.find('td:eq(2)').text().trim(),
                status: row.find('td:eq(3)').text().trim()
            });
        });

        if (selectedCustomers.length === 0) {
            toastr.warning('Pilih pelanggan yang akan di-export');
            return;
        }

        // Create CSV content
        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Nama,Telepon,Status\n";

        selectedCustomers.forEach(function (customer) {
            csvContent += customer.nama + "," + customer.telepon + "," + customer.status + "\n";
        });

        // Download CSV
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "pelanggan_terpilih_grup_<?= $grup->grup ?>.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        toastr.success('Export berhasil: ' + selectedCustomers.length + ' pelanggan');
    };

    window.showLoading = function () {
        $('#loadingModal').modal('show');
    };

    window.hideLoading = function () {
        $('#loadingModal').modal('hide');
    };

    window.performSearch = function () {
        var searchTerm = $('#searchCustomer').val();
        var filterStatus = $('#filterStatus').val();

        // Build search URL with current parameters
        var currentUrl = new URL(window.location);
        currentUrl.searchParams.set('page', '1'); // Reset to first page
        if (searchTerm) currentUrl.searchParams.set('search', searchTerm);
        if (filterStatus) currentUrl.searchParams.set('status', filterStatus);

        // Redirect to search results
        window.location.href = currentUrl.toString();
    };




    function searchCustomers() {
        var searchTerm = $('#searchCustomer').val().toLowerCase();
        var filterStatus = $('#filterStatus').val();

        $('.customer-row').each(function () {
            var row = $(this);
            var name = row.data('name');
            var phone = row.data('phone');
            var status = row.data('status');

            var matchesSearch = name.includes(searchTerm) || phone.includes(searchTerm);
            var matchesFilter = filterStatus === '' || status === filterStatus;

            if (matchesSearch && matchesFilter) {
                row.show();
            } else {
                row.hide();
            }
        });

        updateVisibleCount();
        updateSelectAllState();
    }

    function filterCustomers() {
        searchCustomers();
    }

    function updateVisibleCount() {
        var visibleCount = $('.customer-row:visible').length;
        $('#visibleCount').text(visibleCount);
    }

    function updateSelectAllState() {
        var totalCheckboxes = $('.customer-checkbox:visible').length;
        var checkedCheckboxes = $('.customer-checkbox:visible:checked').length;
        $('#selectAllTable').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
    }





    // Initialize when document is ready
    $(document).ready(function () {
        // Select all functionality - only using selectAllTable
        $(document).on('change', '#selectAllTable', function () {
            var isChecked = $(this).is(':checked');
            $('.customer-checkbox').prop('checked', isChecked);
            window.updateSelectedCount();
        });

        // Update select all when individual checkboxes change - using event delegation
        $(document).on('change', '.customer-checkbox', function () {
            if (!$(this).is(':checked')) {
                $('#selectAllTable').prop('checked', false);
            } else {
                var totalCheckboxes = $('.customer-checkbox').length;
                var checkedCheckboxes = $('.customer-checkbox:checked').length;
                $('#selectAllTable').prop('checked', totalCheckboxes === checkedCheckboxes);
            }
            window.updateSelectedCount();
        });

        // Initialize
        window.updateSelectedCount();

        // Handle search on Enter key
        $('#searchCustomer').keypress(function (e) {
            if (e.which == 13) {
                window.performSearch();
            }
        });

        // Handle filter change
        $('#filterStatus').change(function () {
            window.performSearch();
        });
    });
</script>
<?= $this->endSection() ?>