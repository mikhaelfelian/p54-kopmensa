<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Detail view for Sales Return transactions
 * This file represents the View.
 */

helper('form');
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                    <?php if ($retur->status_retur == '0'): ?>
                        <a href="<?= base_url('transaksi/retur/jual/edit/' . $retur->id) ?>" class="btn btn-warning btn-sm rounded-0">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('transaksi/retur/jual') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Return Header Information -->
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>No. Retur</strong></td>
                                <td width="5%">:</td>
                                <td><?= esc($retur->no_retur) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Retur</strong></td>
                                <td>:</td>
                                <td><?= date('d/m/Y', strtotime($retur->tgl_masuk)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>No. Penjualan</strong></td>
                                <td>:</td>
                                <td><?= esc($retur->no_nota ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Pelanggan</strong></td>
                                <td>:</td>
                                <td><?= esc($retur->customer_nama ?? 'N/A') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Tipe Retur</strong></td>
                                <td width="5%">:</td>
                                <td>
                                    <?php if ($retur->retur_type === 'refund'): ?>
                                        <span class="badge badge-info badge-lg">
                                            <i class="fas fa-money-bill-wave"></i> Refund
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-success badge-lg">
                                            <i class="fas fa-exchange-alt"></i> Tukar Barang
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>:</td>
                                <td>
                                    <?php if ($retur->status_retur == '1'): ?>
                                        <span class="badge badge-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Draft</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>User</strong></td>
                                <td>:</td>
                                <td><?= esc($retur->username ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat</strong></td>
                                <td>:</td>
                                <td><?= date('d/m/Y H:i', strtotime($retur->created_at)) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if ($retur->keterangan): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <strong>Catatan:</strong> <?= esc($retur->keterangan) ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Return Items -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-list"></i> Detail Item Retur
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="12%">Kode</th>
                                                <th>Produk</th>
                                                <th width="8%">Qty</th>
                                                <th width="10%">Satuan</th>
                                                <th width="12%">Harga</th>
                                                <th width="12%">Subtotal</th>
                                                <th width="8%">Tipe</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($retur->items)): ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                                        <p class="text-muted">Tidak ada item retur</p>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php 
                                                $no = 1;
                                                $totalRetur = 0;
                                                $totalExchange = 0;
                                                foreach ($retur->items as $item): 
                                                    $isExchange = $item->jml_satuan < 0;
                                                    $qty = abs($item->jml_satuan);
                                                    $subtotal = $qty * $item->harga;
                                                    
                                                    if ($isExchange) {
                                                        $totalExchange += $subtotal;
                                                    } else {
                                                        $totalRetur += $subtotal;
                                                    }
                                                ?>
                                                    <tr class="<?= $isExchange ? 'table-success' : '' ?>">
                                                        <td><?= $no++ ?></td>
                                                        <td><?= esc($item->kode) ?></td>
                                                        <td>
                                                            <strong><?= esc($item->produk) ?></strong>
                                                            <?php if ($isExchange): ?>
                                                                <br><small class="text-success">(Item Pengganti)</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= $qty ?></td>
                                                                                                <td><?= esc($item->satuan ?? '-') ?></td>
                                        <td class="text-right"><?= format_angka_rp($item->harga) ?></td>
                                        <td class="text-right"><?= format_angka_rp($subtotal) ?></td>
                                                        <td>
                                                            <?php if ($isExchange): ?>
                                                                <span class="badge badge-success">Tukar</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-info">Retur</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Ringkasan Retur</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="60%"><strong>Total Retur:</strong></td>
                                        <td class="text-right">
                                            <strong><?= format_angka_rp($totalRetur) ?></strong>
                                        </td>
                                    </tr>
                                    
                                    <?php if ($retur->retur_type === 'exchange'): ?>
                                    <tr>
                                        <td><strong>Total Tukar:</strong></td>
                                        <td class="text-right">
                                            <strong><?= format_angka_rp($totalExchange) ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><hr></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>
                                                <?php 
                                                $balance = $totalRetur - $totalExchange;
                                                echo $balance > 0 ? 'Kembali:' : ($balance < 0 ? 'Kurang:' : 'Selisih:');
                                                ?>
                                            </strong>
                                        </td>
                                        <td class="text-right">
                                            <strong class="<?= $balance > 0 ? 'text-success' : ($balance < 0 ? 'text-danger' : '') ?>">
                                                <?= format_angka_rp(abs($balance)) ?>
                                            </strong>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    
                                    <tr>
                                        <td colspan="2"><hr></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Grand Total:</strong></td>
                                        <td class="text-right">
                                            <strong class="text-primary">
                                                <?= format_angka_rp($totalRetur) ?>
                                            </strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Log -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-history"></i> Log Transaksi
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="time-label">
                                        <span class="bg-info"><?= date('d M Y', strtotime($retur->created_at)) ?></span>
                                    </div>
                                    
                                    <div>
                                        <i class="fas fa-plus bg-success"></i>
                                        <div class="timeline-item">
                                            <span class="time">
                                                <i class="fas fa-clock"></i> <?= date('H:i', strtotime($retur->created_at)) ?>
                                            </span>
                                            <h3 class="timeline-header">
                                                Retur dibuat oleh <?= esc($retur->username) ?>
                                            </h3>
                                            <div class="timeline-body">
                                                Retur penjualan dengan nomor <strong><?= esc($retur->no_retur) ?></strong> 
                                                telah dibuat dengan tipe 
                                                <strong><?= $retur->retur_type === 'refund' ? 'Refund' : 'Tukar Barang' ?></strong>.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if ($retur->status_retur == '1'): ?>
                                    <div>
                                        <i class="fas fa-check bg-success"></i>
                                        <div class="timeline-item">
                                            <span class="time">
                                                <i class="fas fa-clock"></i> <?= date('H:i', strtotime($retur->updated_at)) ?>
                                            </span>
                                            <h3 class="timeline-header">
                                                Retur diselesaikan
                                            </h3>
                                            <div class="timeline-body">
                                                Retur telah diproses dan diselesaikan.
                                                <?php if ($retur->retur_type === 'refund'): ?>
                                                    Pelanggan telah menerima refund sebesar 
                                                    <strong><?= format_angka_rp($totalRetur) ?></strong>.
                                                <?php else: ?>
                                                    Pelanggan telah menukar barang sesuai dengan kesepakatan.
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <i class="fas fa-clock bg-gray"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="<?= base_url('transaksi/retur/jual') ?>" class="btn btn-secondary rounded-0">
                                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <?php if ($retur->status_retur == '0'): ?>
                                            <a href="<?= base_url('transaksi/retur/jual/edit/' . $retur->id) ?>" 
                                               class="btn btn-warning rounded-0">
                                                <i class="fas fa-edit"></i> Edit Retur
                                            </a>
                                        <?php endif; ?>
                                        
                                        <button type="button" class="btn btn-info rounded-0" onclick="printRetur()">
                                            <i class="fas fa-print"></i> Cetak
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #dee2e6;
    left: 31px;
    margin: 0;
    border-radius: 2px;
}

.timeline > div {
    position: relative;
    margin: 0 0 20px 0;
    clear: both;
}

.timeline > div > .timeline-item {
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 3px;
    margin-top: 0;
    background: #fff;
    color: #545454;
    margin-left: 60px;
    margin-right: 15px;
    padding: 10px;
    position: relative;
    border-left: 3px solid #007bff;
}

.timeline > div > .timeline-item > .time {
    color: #999;
    float: right;
    padding: 10px;
    font-size: 12px;
}

.timeline > div > .timeline-item > .timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 5px 0;
    font-size: 16px;
    line-height: 1.1;
}

.timeline > div > .timeline-item > .timeline-body {
    padding: 10px 0;
    margin: 0;
}

.timeline > div > i {
    position: absolute;
    left: 18px;
    top: 0;
    width: 30px;
    height: 30px;
    border: 0;
    border-radius: 50%;
    text-align: center;
    line-height: 30px;
    font-size: 15px;
    color: #fff;
}

.time-label > span {
    font-weight: 600;
    color: #fff;
    border-radius: 4px;
    display: inline-block;
    padding: 5px 10px;
}

.badge-lg {
    font-size: 0.9em;
    padding: 0.5rem 0.75rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function printRetur() {
    // You can implement print functionality here
    // For now, we'll just show a message
    toastr.info('Fitur cetak akan segera tersedia');
    
    // Alternatively, you can open a print-friendly page:
    // window.open('<?= base_url('transaksi/retur/jual/print/' . $retur->id) ?>', '_blank');
}

// Auto hide alerts
$(document).ready(function() {
    $('.alert').delay(5000).fadeOut();
});
</script>
<?= $this->endSection() ?> 