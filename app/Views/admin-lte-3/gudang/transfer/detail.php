<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : View for displaying transfer/mutasi detail.
 * This file represents the transfer detail view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <!-- Left: Umum / Produk untuk ditransfer -->
        <div class="col-lg-8 mb-3">
            <div class="card card-default rounded-0">
            <div class="card-header">
                    <h3 class="card-title">Data Item</h3>
                    <div class="card-tools">

                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th style="width:60px;"></th>
                                    <th>Item</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($details)): ?>
                                    <?php foreach ($details as $key => $detail): ?>
                                        <tr>
                                            <td><?= $key + 1 ?></td>
                                            <td>
                                                <?php if (!empty($detail->foto)): ?>
                                                    <div style="width:48px;height:48px;background:#f4f6f9;border-radius:6px;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                                        <img src="<?= base_url($detail->foto) ?>" alt="Foto Item" style="max-width:100%;max-height:100%;">
                                                    </div>
                                                <?php else: ?>
                                                    <div style="width:48px;height:48px;background:#f4f6f9;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                                        <i class="fas fa-image text-muted" style="font-size:22px;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold"><?= $detail->item ?? '-' ?></div>
                                                <div class="text-muted small"><?= $detail->kode ?? '-' ?></div>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold"><?= $detail->jml ?? 0 ?></span>
                                                <span class="text-muted small"> <?= $detail->satuan ?? '' ?></span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold">
                                                    <?= !empty($detail->keterangan) ? esc($detail->keterangan) : '-' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Belum ada item yang ditambahkan ke transfer ini.<br>
                                            <?php if ($transfer->status_nota == '0' || $transfer->status_nota == '1'): ?>
                                                <a href="<?= base_url("gudang/transfer/input/{$transfer->id}") ?>" 
                                                   class="btn btn-success btn-sm mt-2 rounded-0">
                                                    <i class="fas fa-plus"></i> Tambah Item
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-primary rounded-0">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <div class="text-muted small">
                            Total Item: <span class="font-weight-bold"><?= !empty($details) ? count($details) : 0 ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Right: Rincian -->
        <div class="col-lg-4 mb-3">
            <div class="card card-default rounded-0">
                <div class="card-header">
                    <h3 class="card-title">Rincian</h3>
                    <div class="card-tools">

                    </div>
                </div>
                <div class="card-body pt-3 pb-2">
                    <div class="mb-2">
                        <div class="text-muted small">Jenis Transfer</div>
                        <div class="font-weight-bold">
                            <?php $tipe = tipeMutasi($transfer->tipe); ?>
                            <?= $tipe['label'] ?? '-' ?>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">No. Nota</div>
                        <div class="font-weight-bold"><?= $transfer->no_nota ?? '-' ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Tanggal Transfer</div>
                        <div class="font-weight-bold"><?= date('d/m/Y', strtotime($transfer->tgl_masuk)) ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Dari</div>
                        <div class="font-weight-bold"><?= $gudangAsalName ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Kepada Penerima</div>
                        <div class="font-weight-bold"><?= $gudangTujuanName ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Status Nota</div>
                        <?php $statusNota = statusNota($transfer->status_nota); ?>
                        <span class="badge badge-<?= $statusNota['badge'] ?>">
                            <?= $statusNota['label'] ?>
                        </span>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Status Terima</div>
                        <?php
                        $statusTerimaLabels = [
                            '0' => 'Belum',
                            '1' => 'Terima',
                            '2' => 'Tolak'
                        ];
                        $statusTerimaColors = [
                            '0' => 'secondary',
                            '1' => 'success',
                            '2' => 'danger'
                        ];
                        ?>
                        <span class="badge badge-<?= $statusTerimaColors[$transfer->status_terima] ?? 'secondary' ?>">
                            <?= $statusTerimaLabels[$transfer->status_terima] ?? 'Unknown' ?>
                        </span>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Dibuat Oleh</div>
                        <div class="font-weight-bold"><?= $transfer->user_name ?? 'Unknown User' ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Keterangan</div>
                        <div class="font-weight-bold"><?= $transfer->keterangan ?: '-' ?></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex flex-row-reverse">
                    <?php if ($transfer->status_nota == '0'): ?>
                        <a href="<?= base_url("gudang/transfer/edit/{$transfer->id}") ?>" 
                           class="btn btn-warning btn-sm rounded-0 ml-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="<?= base_url("gudang/transfer/input/{$transfer->id}") ?>" 
                           class="btn btn-success btn-sm rounded-0 ml-2">
                            <i class="fas fa-plus"></i> Input Item
                        </a>
                        <button type="button" class="btn btn-danger btn-sm rounded-0 ml-2" 
                                onclick="deleteTransfer(<?= $transfer->id ?>)">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    <?php elseif ($transfer->status_nota == '1'): ?>
                        <a href="<?= base_url("gudang/transfer/input/{$transfer->id}") ?>" 
                           class="btn btn-success btn-sm rounded-0 ml-2">
                            <i class="fas fa-plus"></i> Input Item
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-primary btn-sm rounded-0 ml-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteTransfer(id) {
    if (confirm('Apakah Anda yakin ingin menghapus transfer ini?')) {
        window.location.href = '<?= base_url('gudang/transfer/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?> 