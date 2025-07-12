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
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Detail Transfer/Mutasi</h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-sm btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>No. Nota</strong></td>
                                <td>: <?= $transfer->no_nota ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Transfer</strong></td>
                                <td>: <?= date('d/m/Y', strtotime($transfer->tgl_masuk)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tipe Transfer</strong></td>
                                <td>: 
                                    <?php
                                    $tipeLabels = [
                                        '0' => 'Draft',
                                        '1' => 'Pindah Gudang',
                                        '2' => 'Stok Masuk',
                                        '3' => 'Stok Keluar'
                                    ];
                                    $tipeColors = [
                                        '0' => 'secondary',
                                        '1' => 'info',
                                        '2' => 'success',
                                        '3' => 'warning'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $tipeColors[$transfer->tipe] ?? 'secondary' ?>">
                                        <?= $tipeLabels[$transfer->tipe] ?? 'Unknown' ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Gudang Asal</strong></td>
                                <td>: <?= $gudangAsalName ?></td>
                            </tr>
                            <tr>
                                <td><strong>Gudang Tujuan</strong></td>
                                <td>: <?= $gudangTujuanName ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status Nota</strong></td>
                                <td>: 
                                    <?php
                                    $statusNotaLabels = [
                                        '0' => 'Draft',
                                        '1' => 'Pending',
                                        '2' => 'Diproses',
                                        '3' => 'Selesai',
                                        '4' => 'Dibatalkan'
                                    ];
                                    $statusNotaColors = [
                                        '0' => 'secondary',
                                        '1' => 'warning',
                                        '2' => 'info',
                                        '3' => 'success',
                                        '4' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $statusNotaColors[$transfer->status_nota] ?? 'secondary' ?>">
                                        <?= $statusNotaLabels[$transfer->status_nota] ?? 'Unknown' ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status Terima</strong></td>
                                <td>: 
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
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat Oleh</strong></td>
                                <td>: <?= $transfer->user_name ?? 'Unknown User' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>: <?= $transfer->keterangan ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="text-right">
                            <?php if ($transfer->status_nota == '0'): ?>
                                <a href="<?= base_url("gudang/transfer/edit/{$transfer->id}") ?>" 
                                   class="btn btn-warning btn-sm rounded-0">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="<?= base_url("gudang/transfer/input/{$transfer->id}") ?>" 
                                   class="btn btn-success btn-sm rounded-0">
                                    <i class="fas fa-plus"></i> Input Item
                                </a>
                                <button type="button" class="btn btn-danger btn-sm rounded-0" 
                                        onclick="deleteTransfer(<?= $transfer->id ?>)">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            <?php elseif ($transfer->status_nota == '1'): ?>
                                <a href="<?= base_url("gudang/transfer/input/{$transfer->id}") ?>" 
                                   class="btn btn-success btn-sm rounded-0">
                                    <i class="fas fa-plus"></i> Input Item
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($details)): ?>
                    <hr>
                    <h5><i class="fas fa-boxes"></i> Detail Item Transfer</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="50" class="text-center">No.</th>
                                    <th>Kode Item</th>
                                    <th>Nama Item</th>
                                    <th>Satuan</th>
                                    <th class="text-center">Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $key => $detail): ?>
                                    <tr>
                                        <td class="text-center"><?= $key + 1 ?>.</td>
                                        <td><?= $detail->kode ?? '-' ?></td>
                                        <td><?= $detail->item ?? '-' ?></td>
                                        <td><?= $detail->satuan ?? '-' ?></td>
                                        <td class="text-center"><?= number_format($detail->jml, 2) ?></td>
                                        <td><?= $detail->keterangan ?: '-' ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <hr>
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Informasi!</h5>
                        <p>Belum ada item yang ditambahkan ke transfer ini.</p>
                        <?php if ($transfer->status_nota == '0' || $transfer->status_nota == '1'): ?>
                            <a href="<?= base_url("gudang/transfer/input/{$transfer->id}") ?>" 
                               class="btn btn-success btn-sm rounded-0">
                                <i class="fas fa-plus"></i> Tambah Item
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
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