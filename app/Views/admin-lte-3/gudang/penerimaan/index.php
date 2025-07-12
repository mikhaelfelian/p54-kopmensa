<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-29
 * 
 * Purchase Receiving List View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-truck-loading mr-1"></i> Penerimaan Barang
        </h3>
        <div class="card-tools">
            <span class="badge badge-info">Transaksi Siap Diterima</span>
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
                    <th>Status Terima</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)) : ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada transaksi siap diterima</td>
                    </tr>
                <?php else : ?>
                    <?php 
                    $startNumber = ($currentPage - 1) * $perPage;
                    foreach ($transactions as $index => $row) : 
                    ?>
                        <tr>
                            <td><?= $startNumber + $index + 1 ?></td>
                            <td>
                                <strong><?= esc($row->no_nota) ?></strong>
                                <br>
                                <small class="text-muted">ID: <?= $row->id ?></small>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row->tgl_masuk)) ?></td>
                            <td>
                                <?= esc($row->supplier_nama ?? $row->supplier) ?>
                                <?php if (!empty($row->supplier_nama)): ?>
                                    <br><small class="text-muted"><?= esc($row->supplier) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row->no_po)): ?>
                                    <span class="badge badge-info"><?= esc($row->no_po) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <strong><?= number_format($row->jml_gtotal ?? 0, 2, ',', '.') ?></strong>
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
                                $receiveStatus = [
                                    '0' => '<span class="badge badge-warning">Belum Diterima</span>',
                                    '1' => '<span class="badge badge-success">Sudah Diterima</span>',
                                    '2' => '<span class="badge badge-danger">Ditolak</span>'
                                ];
                                $statusKey = $row->status_terima ?? '0';
                                echo $receiveStatus[$statusKey] ?? $receiveStatus['0'];
                                ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= base_url("gudang/terima/{$row->id}") ?>" 
                                       class="btn btn-success btn-sm" 
                                       title="Terima Barang">
                                        <i class="fas fa-check"></i> Terima
                                    </a>
                                    <a href="<?= base_url("transaksi/beli/detail/{$row->id}") ?>" 
                                       class="btn btn-info btn-sm" 
                                       title="Detail Transaksi">
                                        <i class="fas fa-eye"></i>
                                    </a>
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

<!-- Info Card -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-1"></i> Informasi
                </h3>
            </div>
            <div class="card-body">
                <p class="mb-1">
                    <strong>Status Transaksi:</strong>
                </p>
                <ul class="mb-0">
                    <li><span class="badge badge-warning">Belum Diterima</span> - Transaksi sudah diproses, siap untuk diterima</li>
                    <li><span class="badge badge-success">Sudah Diterima</span> - Barang sudah diterima dan stok sudah diupdate</li>
                    <li><span class="badge badge-danger">Ditolak</span> - Barang ditolak karena tidak sesuai spesifikasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Add hover effect to table rows
    $('table tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );
    
    // Confirm receiving action
    $('.btn-success').on('click', function(e) {
        const href = $(this).attr('href');
        const noNota = $(this).closest('tr').find('td:eq(1) strong').text();
        
        Swal.fire({
            title: 'Terima Barang?',
            text: `Apakah anda yakin ingin menerima barang untuk faktur ${noNota}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Terima!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});
</script>
<?= $this->endSection() ?> 