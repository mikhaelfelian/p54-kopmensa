<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-money-bill-wave"></i> Data Kas
        </h3>
        <div class="card-tools">
        </div>
    </div>
    <div class="card-body">
        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col-md-12">
                <a href="<?= base_url('transaksi/petty/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Input Kas
                </a>
                <a href="<?= base_url('transaksi/petty/category') ?>" class="btn btn-info">
                    <i class="fas fa-tags mr-2"></i>Kategori
                </a>
                <a href="<?= base_url('transaksi/petty/summary') ?>" class="btn btn-success">
                    <i class="fas fa-chart-bar mr-2"></i>Ringkasan
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>Filter
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('transaksi/petty') ?>">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="outlet_id">Outlet</label>
                                <select name="outlet_id" id="outlet_id" class="form-control">
                                    <option value="">Semua Outlet</option>
                                    <?php foreach ($outlets as $outlet): ?>
                                        <option value="<?= $outlet->id ?>" <?= ($filters['outlet_id'] == $outlet->id) ? 'selected' : '' ?>>
                                            <?= $outlet->nama ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="draft" <?= ($filters['status'] == 'draft') ? 'selected' : '' ?>>Draft
                                    </option>
                                    <option value="posted" <?= ($filters['status'] == 'posted') ? 'selected' : '' ?>>Posted
                                    </option>
                                    <option value="void" <?= ($filters['status'] == 'void') ? 'selected' : '' ?>>Void
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="direction">Jenis</label>
                                <select name="direction" id="direction" class="form-control">
                                    <option value="">Semua Jenis</option>
                                    <option value="IN" <?= ($filters['direction'] == 'IN') ? 'selected' : '' ?>>Masuk
                                    </option>
                                    <option value="OUT" <?= ($filters['direction'] == 'OUT') ? 'selected' : '' ?>>Keluar
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_from">Dari Tanggal</label>
                                <input type="date" name="date_from" id="date_from" class="form-control"
                                    value="<?= $filters['date_from'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_to">Sampai Tanggal</label>
                                <input type="date" name="date_to" id="date_to" class="form-control"
                                    value="<?= $filters['date_to'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search mr-2"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Petty Cash List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>Daftar Petty Cash
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Tanggal</th>
                                <th width="15%">Outlet</th>
                                <th width="15%">Kategori</th>
                                <th width="10%">Jenis</th>
                                <th width="12%">Nominal</th>
                                <th width="20%">Keterangan</th>
                                <th width="8%">Status</th>
                                <th width="10%">User</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pettyEntries)): ?>
                                <?php foreach ($pettyEntries as $index => $item): ?>
                                    <tr>
                                        <td><?= ($currentPage - 1) * $perPage + $index + 1 ?></td>
                                        <td><?= date('d/m/Y', strtotime($item->created_at)) ?></td>
                                        <td><?= $item->outlet_name ?? 'N/A' ?></td>
                                        <td>
                                            <?php if (isset($item->kategori_nama) && $item->kategori_nama): ?>
                                                <?= $item->kategori_nama ?>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($item->direction === 'IN'): ?>
                                                <span class="badge badge-success">Masuk</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Keluar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <strong><?= format_angka($item->amount, 0) ?></strong>
                                        </td>
                                        <td><?= $item->reason ?></td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'draft' => 'badge badge-warning',
                                                'posted' => 'badge badge-success',
                                                'void' => 'badge badge-secondary'
                                            ];
                                            $statusLabel = [
                                                'draft' => 'Draft',
                                                'posted' => 'Posted',
                                                'void' => 'Void'
                                            ];
                                            ?>
                                            <span class="<?= $statusClass[$item->status] ?? 'badge badge-secondary' ?>">
                                                <?= $statusLabel[$item->status] ?? ucfirst($item->status) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (isset($item->user_name) && $item->user_name): ?>
                                                <?= ($item->user_name ?? '') . ' ' . ($item->user_lastname ?? '') ?>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= base_url('transaksi/petty/show/' . $item->id) ?>"
                                                    class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($item->status === 'draft'): ?>
                                                    <a href="<?= base_url('transaksi/petty/edit/' . $item->id) ?>"
                                                        class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= base_url('transaksi/petty/approve/' . $item->id) ?>"
                                                        class="btn btn-sm btn-success" title="Approve"
                                                        onclick="return confirm('Apakah Anda yakin ingin approve petty cash ini?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="<?= base_url('transaksi/petty/delete/' . $item->id) ?>"
                                                        class="btn btn-sm btn-danger" title="Hapus"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus petty cash ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted">
                                        Tidak ada data petty cash
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalRecords > $perPage): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <nav>
                            <ul class="pagination">
                                <?php
                                $totalPages = ceil($totalRecords / $perPage);
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                ?>

                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="<?= base_url('transaksi/petty?page=' . ($currentPage - 1) . '&' . http_build_query(array_diff_key($filters, ['page' => '']))) ?>">
                                            Previous
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="<?= base_url('transaksi/petty?page=' . $i . '&' . http_build_query(array_diff_key($filters, ['page' => '']))) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="<?= base_url('transaksi/petty?page=' . ($currentPage + 1) . '&' . http_build_query(array_diff_key($filters, ['page' => '']))) ?>">
                                            Next
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Initialize Select2 for dropdowns
        $('#outlet_id, #status, #jenis').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
<?= $this->endSection() ?>