<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-17
 * 
 * Voucher Trash View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/voucher') ?>" class="btn btn-sm btn-secondary rounded-0">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="button" class="btn btn-sm btn-danger rounded-0" onclick="deletePermanentAll()">
                            <i class="fas fa-trash"></i> Hapus Permanen Semua
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <h3 class="card-title">Arsip Voucher</h3>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <!-- <th width="5%">
                                    <input type="checkbox" id="select-all-trash" onchange="toggleAllTrash()">
                                </th> -->
                                <th width="5%">No</th>
                                <th width="15%">Kode</th>
                                <th width="10%">Jumlah</th>
                                <th width="10%">Jenis</th>
                                <th width="15%">Nominal</th>
                                <th width="10%">Status</th>
                                <th width="15%">Tanggal Hapus</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($vouchers)): ?>
                                <?php $no = 1;
                                foreach ($vouchers as $voucher): ?>
                                    <tr>
                                        <!-- <td>
                                            <input type="checkbox" class="trash-checkbox" value="<?= $voucher->id ?>">
                                        </td> -->
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($voucher->kode) ?></td>
                                        <td><?= esc($voucher->jml) ?></td>
                                        <td><?= esc($voucher->jenis_voucher == 'nominal' ? 'Nominal' : 'Persen') ?></td>
                                        <td><?= number_format($voucher->nominal, 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $voucher->status == '1' ? 'success' : 'danger' ?>">
                                                <?= $voucher->status == '1' ? 'Aktif' : 'Tidak Aktif' ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($voucher->deleted_at)) ?></td>
                                        <td>
                                            <a href="<?= base_url('master/voucher/restore/' . $voucher->id) ?>"
                                                class="btn btn-sm btn-success rounded-0"
                                                onclick="return confirm('Yakin ingin memulihkan voucher ini?')">
                                                <i class="fas fa-trash-restore"></i>
                                            </a>
                                            <a href="<?= base_url('master/voucher/delete_permanent/' . $voucher->id) ?>"
                                                class="btn btn-sm btn-danger rounded-0"
                                                onclick="return confirm('Yakin ingin menghapus permanen voucher ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data voucher yang dihapus</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAllTrash() {
        const selectAll = document.getElementById('select-all-trash');
        const checkboxes = document.querySelectorAll('.trash-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }

    function restoreAll() {
        const checkboxes = document.querySelectorAll('.trash-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Pilih voucher yang akan dipulihkan');
            return;
        }

        if (confirm(`Yakin ingin memulihkan ${checkboxes.length} voucher?`)) {
            const ids = Array.from(checkboxes).map(cb => cb.value);
            // Implement bulk restore functionality
            console.log('Restore IDs:', ids);
        }
    }

    function deletePermanentAll() {
        const checkboxes = document.querySelectorAll('.trash-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Pilih voucher yang akan dihapus permanen');
            return;
        }

        if (confirm(`Yakin ingin menghapus permanen ${checkboxes.length} voucher?`)) {
            const ids = Array.from(checkboxes).map(cb => cb.value);
            // Implement bulk permanent delete functionality
            console.log('Delete Permanent IDs:', ids);
        }
    }
</script>
<?= $this->endSection() ?>