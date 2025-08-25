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
                                            <?= form_open(base_url('master/customer-group/removeMember'), ['style' => 'display: inline;']) ?>
                                            <input type="hidden" name="id_grup" value="<?= $grup->id ?>">
                                            <input type="hidden" name="id_pelanggan" value="<?= $member->id_pelanggan ?>">
                                            <button type="submit" class="btn btn-danger btn-sm rounded-0"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus <?= esc($member->nama) ?> dari grup ini?')"
                                                title="Hapus dari Grup">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                            <?= form_close() ?>
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
                <!-- Search and Filter Form -->
                <form method="GET" action="<?= current_url() ?>">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" name="search" class="form-control rounded-0"
                                    placeholder="Cari nama atau telepon pelanggan..." value="<?= esc($search) ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary rounded-0" type="submit">
                                        Cari
                                    </button>
                                    <a href="<?= current_url() ?>" class="btn btn-outline-secondary rounded-0">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-control rounded-0" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="1" <?= $status == '1' ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= $status == '0' ? 'selected' : '' ?>>Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                </form>

                    <!-- Bulk Actions -->
                <?= form_open(base_url('master/customer-group/addBulkMembers'), ['id' => 'bulkForm', 'onsubmit' => 'return validateBulkForm()']) ?>
                <input type="hidden" name="id_grup" value="<?= $grup->id ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                            <label class="form-check-label" for="selectAllTable">
                                <strong>Pilih Semua</strong> (<span id="selectedCount">0</span> terpilih)
                            </label>
                        </div>
                        </div>
                        <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary btn-sm rounded-0" id="bulkAddBtn" disabled>
                                <i class="fas fa-users"></i> Tambah Terpilih
                            </button>
                        <button type="button" class="btn btn-success btn-sm rounded-0" id="addAllBtn">
                                <i class="fas fa-plus-circle"></i> Tambah Semua
                            </button>
                        </div>
                    </div>

                    <!-- Customer Table -->
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-striped table-hover mb-0">
                            <thead class="sticky-top bg-white text-black">
                                <tr>
                                <th class="text-center align-middle" width="5%">
                                    <label style="margin:0; cursor:pointer;">
                                        <input type="checkbox" id="selectAllTable" class="form-check-input">
                                    </label>
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
                                    <tr>
                                            <td class="text-center">
                                            <input type="checkbox" name="customer_ids[]"
                                                class="customer-checkbox form-check-input" value="<?= $customer->id ?>">
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
                                            <form method="POST" action="<?= base_url('master/customer-group/addMember') ?>"
                                                style="display: inline;">
                                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                                                <input type="hidden" name="id_grup" value="<?= $grup->id ?>">
                                                <input type="hidden" name="id_pelanggan" value="<?= $customer->id ?>">
                                                <button type="submit" class="btn btn-success btn-sm rounded-0"
                                                    onclick="return confirm('Apakah Anda yakin ingin menambahkan <?= esc($customer->nama) ?> ke grup ini?')"
                                                    title="Tambah ke Grup">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?= form_close() ?>

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
                                        <?php if ($currentPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                href="<?= current_url() ?>?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php
                                        $startPage = max(1, $currentPage - 2);
                                    $endPage = min(ceil($totalAvailable / $perPage), $currentPage + 2);

                                        if ($startPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                href="<?= current_url() ?>?page=1&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>">1</a>
                                            </li>
                                            <?php if ($startPage > 2): ?>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                        <?php endif; ?>

                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                                <a class="page-link"
                                                href="<?= current_url() ?>?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                    <?php if ($endPage < ceil($totalAvailable / $perPage)): ?>
                                        <?php if ($endPage < ceil($totalAvailable / $perPage) - 1): ?>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                href="<?= current_url() ?>?page=<?= ceil($totalAvailable / $perPage) ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>"><?= ceil($totalAvailable / $perPage) ?></a>
                                            </li>
                                        <?php endif; ?>

                                    <?php if ($currentPage < ceil($totalAvailable / $perPage)): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                href="<?= current_url() ?>?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($search) ?>">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add All Modal -->
<div class="modal fade" id="addAllModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Semua Pelanggan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="<?= base_url('master/customer-group/addBulkMembers') ?>">
                <div class="modal-body">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <input type="hidden" name="id_grup" value="<?= $grup->id ?>">

                    <?php foreach ($availableCustomers as $customer): ?>
                        <input type="hidden" name="customer_ids[]" value="<?= $customer->id ?>">
                    <?php endforeach; ?>

                    <p>Apakah Anda yakin ingin menambahkan SEMUA <?= count($availableCustomers) ?> pelanggan ke grup
                        ini?</p>
                    <p class="text-muted">Ini akan menambahkan semua pelanggan yang tersedia di halaman ini.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Tambah Semua</button>
            </div>
            </form>
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

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
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
(function () {
  function getItemCbs() {
    return Array.from(document.querySelectorAll('.customer-checkbox'));
  }

  function updateUI() {
    const items   = getItemCbs();
    const checked = items.filter(cb => cb.checked).length;

    const counter = document.getElementById('selectedCount');
    if (counter) counter.textContent = checked;

    const bulkBtn = document.getElementById('bulkAddBtn');
    if (bulkBtn) bulkBtn.disabled = checked === 0;

    const master = document.getElementById('selectAllTable');
    if (master) {
      const all  = items.length > 0 && checked === items.length;
      const none = checked === 0;
      master.indeterminate = !(all || none) && items.length > 0;
      master.checked = all;
    }
  }

  function setAllItems(checked) {
    getItemCbs().forEach(cb => cb.checked = checked);
    updateUI();
  }

  // === BIND LANGSUNG KE ELEMEN (tanpa delegator) ===
  function bind() {
    const master = document.getElementById('selectAllTable');
    if (master) {
      // pastikan semua jalur event tertangkap
      ['change','click','input'].forEach(ev =>
        master.addEventListener(ev, e => setAllItems(master.checked))
      );
    }

    // bind semua checkbox item
    getItemCbs().forEach(cb => {
      ['change','click','input'].forEach(ev =>
        cb.addEventListener(ev, updateUI)
      );
    });

    updateUI();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bind);
  } else {
    bind();
  }

  // Ekspor & validasi tetap sama
  window.exportSelectedCustomers = function () {
    const items = getItemCbs().filter(cb => cb.checked);
    if (items.length === 0) { alert('Pilih pelanggan yang akan diexport.'); return; }
    let csv = 'data:text/csv;charset=utf-8,';
    csv += 'Nama,Telepon,Status,Kode\n';
    items.forEach(cb => {
      const tr = cb.closest('tr');
      const tds = tr ? Array.from(tr.querySelectorAll('td')) : [];
      const nama    = (tds[1]?.textContent || '').trim().replace(/\s+/g, ' ');
      const telepon = (tds[2]?.textContent || '').trim().replace(/\s+/g, ' ');
      const status  = (tds[3]?.textContent || '').trim().replace(/\s+/g, ' ');
      const kode    = '';
      csv += `"${nama}","${telepon}","${status}","${kode}"\n`;
    });
    const a = document.createElement('a');
    a.href = encodeURI(csv);
    a.download = 'pelanggan_terpilih.csv';
    document.body.appendChild(a); a.click(); a.remove();
  };

  window.validateBulkForm = function () {
    const selected = getItemCbs().filter(cb => cb.checked).length;
    if (selected === 0) { alert('Pilih pelanggan yang akan ditambahkan ke grup.'); return false; }
    return confirm('Apakah Anda yakin ingin menambahkan ' + selected + ' pelanggan ke grup ini?');
  };
})();
</script>
<?= $this->endSection() ?>