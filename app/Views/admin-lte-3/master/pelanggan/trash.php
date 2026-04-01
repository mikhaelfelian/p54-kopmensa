<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-20
 * Github : github.com/mikhaelfelian
 * description : Trash view for customer (pelanggan) data
 * This file represents the Pelanggan Trash View.
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/customer') ?>" class="btn btn-sm btn-secondary rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="button" id="restore-all-btn" class="btn btn-sm btn-success rounded-0">
                    <i class="fas fa-trash-restore"></i> Pulihkan Semua
                </button>
                <button type="button" id="delete-all-permanent-btn" class="btn btn-sm btn-danger rounded-0">
                    <i class="fas fa-trash"></i> Hapus Permanen Semua
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <?= form_open('master/customer/trash', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="text-left">Kode</th>
                        <th class="text-left">Nama</th>
                        <th class="text-left">Alamat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>
                            <?= form_input([
                                'name' => 'search',
                                'value' => $search ?? '',
                                'class' => 'form-control form-control-sm rounded-0',
                                'placeholder' => 'Cari...'
                            ]) ?>
                        </th>
                        <th></th>
                        <th></th>
                        <th class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-filter"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pelanggan)): ?>
                        <?php
                        $no = ($perPage * ($currentPage - 1)) + 1;
                        foreach ($pelanggan as $pelanggan):
                            ?>
                            <tr>
                                <td class="text-center" width="3%"><?= $no++ ?>.</td>
                                <td width="15%"><?= esc($pelanggan->kode) ?></td>
                                <td width="40%"><?= esc($pelanggan->nama) ?></td>
                                <td width="30%"><?= esc($pelanggan->alamat) ?></td>
                                <td class="text-center" width="12%">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/customer/restore/{$pelanggan->id}") ?>"
                                            class="btn btn-success btn-sm rounded-0"
                                            onclick="return confirm('Yakin ingin memulihkan pelanggan ini?')">
                                            <i class="fas fa-trash-restore"></i> Pulihkan
                                        </a>
                                        <a href="<?= base_url("master/customer/delete_permanent/{$pelanggan->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Yakin ingin menghapus permanen pelanggan ini? Tindakan ini tidak dapat dibatalkan.')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= form_close() ?>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('pelanggan', 'adminlte_pagination') ?>
    </div>
</div>

<?= $this->section('js') ?>
<script>
(function () {
    function postJson(url, onDone) {
        var fd = new FormData();
        fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd,
            credentials: 'same-origin'
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.csrfHash) {
                var m = document.querySelector('meta[name="csrf-token"]');
                if (m) { m.setAttribute('content', res.csrfHash); }
            }
            onDone(res);
        })
        .catch(function () {
            onDone({ success: false, message: 'Permintaan gagal' });
        });
    }

    document.getElementById('restore-all-btn').addEventListener('click', function () {
        if (!confirm('Pulihkan semua data di arsip?')) return;
        var btn = this;
        btn.disabled = true;
        postJson('<?= base_url('master/customer/restore_all') ?>', function (res) {
            btn.disabled = false;
            if (res.success) {
                if (typeof toastr !== 'undefined') { toastr.success(res.message); } else { alert(res.message); }
                setTimeout(function () { window.location.reload(); }, 800);
            } else {
                if (typeof toastr !== 'undefined') { toastr.error(res.message); } else { alert(res.message); }
            }
        });
    });

    document.getElementById('delete-all-permanent-btn').addEventListener('click', function () {
        if (!confirm('Semua data di arsip akan dihapus secara permanen. Lanjutkan?')) return;
        var btn = this;
        btn.disabled = true;
        postJson('<?= base_url('master/customer/delete_all_permanent') ?>', function (res) {
            btn.disabled = false;
            if (res.success) {
                if (typeof toastr !== 'undefined') { toastr.success(res.message); } else { alert(res.message); }
                setTimeout(function () { window.location.reload(); }, 800);
            } else {
                if (typeof toastr !== 'undefined') { toastr.error(res.message); } else { alert(res.message); }
            }
        });
    });
})();
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
