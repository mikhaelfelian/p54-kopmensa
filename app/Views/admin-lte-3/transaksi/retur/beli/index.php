<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-25
 * Github : github.com/mikhaelfelian
 * description : View for listing purchase returns (retur pembelian)
 * This file represents the View.
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">Data Retur Pembelian</h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/retur/beli/create') ?>" class="btn btn-primary btn-sm rounded-0">
                <i class="fas fa-plus mr-1"></i> Buat Retur
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th>No. Nota Retur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>No. Nota Asal</th>
                    <th>Total</th>
                    <th>Status PPN</th>
                    <th>Status Retur</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($returns ? $returns : [])) : ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data</td>
                    </tr>
                <?php else : ?>
                    <?php 
                    $startNumber = ((isset($currentPage) ? $currentPage : 1) - 1) * (isset($perPage) ? $perPage : 10);
                    foreach ($returns as $index => $row) : 
                    ?>
                        <tr>
                            <td class="text-center"><?= $startNumber + $index + 1 ?></td>
                            <td><?= esc(isset($row->no_nota_retur) ? $row->no_nota_retur : '') ?></td>
                            <td><?= date('d/m/Y', strtotime(isset($row->tgl_retur) ? $row->tgl_retur : (isset($row->created_at) ? $row->created_at : ''))) ?></td>
                            <td><?= esc(isset($row->supplier) ? $row->supplier : '') ?></td>
                            <td><?= esc(isset($row->no_nota_asal) ? $row->no_nota_asal : '') ?></td>
                            <td class="text-right">
                                <?= number_format(isset($row->jml_total) ? $row->jml_total : 0, 2, ',', '.') ?>
                            </td>
                            <td>
                                <?php
                                $ppnStatus = [
                                    '0' => '<span class="badge badge-secondary">Non PPN</span>',
                                    '1' => '<span class="badge badge-info">Dengan PPN</span>',
                                    '2' => '<span class="badge badge-primary">PPN Ditangguhkan</span>'
                                ];
                                echo isset($ppnStatus[isset($row->status_ppn) ? $row->status_ppn : '0']) ? $ppnStatus[isset($row->status_ppn) ? $row->status_ppn : '0'] : '';
                                ?>
                            </td>
                            <td>
                                <?php
                                $returStatus = [
                                    '0' => '<span class="badge badge-warning">Draft</span>',
                                    '1' => '<span class="badge badge-success">Selesai</span>'
                                ];
                                echo isset($returStatus[isset($row->status_retur) ? $row->status_retur : '0']) ? $returStatus[isset($row->status_retur) ? $row->status_retur : '0'] : '';
                                ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= base_url("transaksi/retur/beli/" . (isset($row->id) ? $row->id : 1)) ?>" 
                                       class="btn btn-default btn-sm" 
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ((isset($row->status_retur) ? $row->status_retur : '0') != '1') : ?>
                                        <a href="<?= base_url("transaksi/retur/beli/edit/" . (isset($row->id) ? $row->id : 1)) ?>" 
                                           class="btn btn-default btn-sm" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        <?php if (isset($pager)): ?>
            <?= $pager->links('retursbeli', 'adminlte_pagination') ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Delete confirmation
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        })
    });
});
</script>
<?= $this->endSection() ?> 