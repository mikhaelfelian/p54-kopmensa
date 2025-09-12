<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : View for displaying transfer/mutasi data.
 * This file represents the transfer index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <form action="<?= base_url('gudang/transfer') ?>" method="get">
                            <div class="input-group input-group-sm">
                                <input type="text" name="keyword" class="form-control rounded-0" value="<?= $keyword ?? '' ?>" placeholder="Cari...">
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-primary rounded-0" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="<?= base_url('gudang/transfer/create') ?>" class="btn btn-success btn-sm rounded-0">
                            <i class="fas fa-plus"></i> Tambah Transfer
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">No.</th>
                            <th>No. Nota</th>
                            <th>Tipe</th>
                            <th>Gudang Asal</th>
                            <th>Gudang Tujuan</th>
                            <th>Tanggal</th>
                            <th>Status Nota</th>
                            <th>Status Terima</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transfers)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transfers as $key => $row): ?>
                                <tr>
                                    <td class="text-center"><?= (($currentPage - 1) * $perPage) + $key + 1 ?>.</td>
                                    <td>
                                        <?= $row->no_nota ?? '-' ?>
                                        <?php if (!empty($row->kode_nota_dpn) || !empty($row->kode_nota_blk)): ?>
                                            <?= br() ?>
                                            <small>
                                                <?= $row->kode_nota_dpn ?? '' ?>
                                                <?= !empty($row->kode_nota_dpn) && !empty($row->kode_nota_blk) ? ' - ' : '' ?>
                                                <?= $row->kode_nota_blk ?? '' ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($row->tipe == '0') {
                                            // Draft, not covered by statusMutasi
                                            $label = 'Draft';
                                            $badge = 'secondary';
                                        } else {
                                            $mutasi = statusMutasi($row->tipe);
                                            $label = $mutasi['label'] ?? 'Unknown';
                                            $badge = $mutasi['badge'] ?? 'secondary';
                                        }
                                        ?>
                                        <span class="badge badge-<?= $badge ?>">
                                            <?= $label ?>
                                        </span>
                                    </td>
                                    <td><?= $row->gudang_asal_name ?? '-' ?></td>
                                    <td><?= $row->gudang_tujuan_name ?? '-' ?></td>
                                    <td>
                                        <?php if (!empty($row->tgl_masuk) && $row->tgl_masuk != '0000-00-00'): ?>
                                            <?= date('d/m/Y', strtotime($row->tgl_masuk)) ?>
                                        <?php endif; ?>
                                        <?php if (!empty($row->tgl_keluar) && $row->tgl_keluar != '0000-00-00'): ?>
                                            <?= br() ?>
                                            <?= date('d/m/Y', strtotime($row->tgl_keluar)) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
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
                                        <span class="badge badge-<?= $statusNotaColors[$row->status_nota] ?? 'secondary' ?>">
                                            <?= $statusNotaLabels[$row->status_nota] ?? 'Unknown' ?>
                                        </span>
                                    </td>
                                    <td>
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
                                        <span class="badge badge-<?= $statusTerimaColors[$row->status_terima] ?? 'secondary' ?>">
                                            <?= $statusTerimaLabels[$row->status_terima] ?? 'Unknown' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url("gudang/transfer/detail/{$row->id}") ?>"
                                                class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($row->status_nota == '0'): ?>
                                            <a href="<?= base_url("gudang/transfer/edit/{$row->id}") ?>"
                                                class="btn btn-warning btn-sm rounded-0">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url("gudang/transfer/input/{$row->id}") ?>"
                                                class="btn btn-success btn-sm rounded-0"
                                                data-toggle="tooltip" 
                                                title="Input Item">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('transfer', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
<?= $this->endSection() ?> 