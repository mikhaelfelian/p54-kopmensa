<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-04
 * 
 * Purchase Transaction List View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Pembelian</h3>
        <div class="card-tools">
            
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>No. Faktur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>No. PO</th>
                    <th>Total</th>
                    <th>Status PPN</th>
                    <th>Status Bayar</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transaksi)) : ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data</td>
                    </tr>
                <?php else : ?>
                    <?php 
                    $startNumber = ($currentPage - 1) * $perPage;
                    foreach ($transaksi as $index => $row) : 
                    ?>
                        <tr>
                            <td><?= $startNumber + $index + 1 ?></td>
                            <td><?= esc($row->no_nota) ?></td>
                            <td><?= date('d/m/Y', strtotime($row->created_at)) ?></td>
                            <td><?= esc($row->supplier) ?></td>
                            <td><?= esc($row->no_po) ?></td>
                            <td class="text-right">
                                <?= number_format($row->jml_gtotal, 2, ',', '.') ?>
                            </td>
                            <td>
                                <?php
                                $ppnStatus = [
                                    '0' => '<span class="badge badge-secondary">Non PPN</span>',
                                    '1' => '<span class="badge badge-info">Tambah PPN</span>',
                                    '2' => '<span class="badge badge-primary">Include PPN</span>'
                                ];
                                echo $ppnStatus[$row->status_ppn] ?? '';
                                ?>
                            </td>
                            <td>
                                <?php
                                $paymentStatus = [
                                    '0' => '<span class="badge badge-warning">Belum Lunas</span>',
                                    '1' => '<span class="badge badge-success">Lunas</span>'
                                ];
                                echo $paymentStatus[$row->status_bayar] ?? '';
                                ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= base_url("transaksi/beli/detail/{$row->id}") ?>" 
                                       class="btn btn-default btn-sm" 
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($row->status_bayar != '1') : ?>
                                        <a href="<?= base_url("transaksi/beli/edit/{$row->id}") ?>" 
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
        <?= $pager->links('transbeli', 'adminlte_pagination') ?>
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