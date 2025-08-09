<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying stock report detail
 * This file represents the stock report detail view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye mr-1"></i> Detail Stok
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/stock') ?>" class="btn btn-default btn-sm rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Item Information -->
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Kode Item</strong></td>
                                <td>: <?= $stock->item_kode ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Item</strong></td>
                                <td>: <?= $stock->item_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Kategori</strong></td>
                                <td>: <?= $stock->kategori_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Merk</strong></td>
                                <td>: <?= $stock->merk_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Deskripsi</strong></td>
                                <td>: <?= $stock->deskripsi ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Gudang</strong></td>
                                <td>: <?= $stock->gudang_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Stok</strong></td>
                                <td>: 
                                    <?php if (($stock->stok ?? 0) > 0): ?>
                                        <span class="badge badge-success"><?= number_format($stock->stok ?? 0, 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">0</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Satuan</strong></td>
                                <td>: <?= $stock->satuan_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Harga Beli</strong></td>
                                <td>: <?= number_format($stock->harga_beli ?? 0, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Harga Jual</strong></td>
                                <td>: <?= number_format($stock->harga_jual ?? 0, 0, ',', '.') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Stock History -->
                <h5><i class="fas fa-history mr-1"></i> Riwayat Stok</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Referensi</th>
                                <th class="text-center">Stok Sebelum</th>
                                <th class="text-center">Perubahan</th>
                                <th class="text-center">Stok Sesudah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($history)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada riwayat stok</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($history as $index => $hist): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($hist->tgl_hist)) ?></td>
                                        <td>
                                            <?php 
                                            $typeClass = 'badge-secondary';
                                            $typeText = 'Lainnya';
                                            
                                            if ($hist->tipe == 'IN') {
                                                $typeClass = 'badge-success';
                                                $typeText = 'Masuk';
                                            } elseif ($hist->tipe == 'OUT') {
                                                $typeClass = 'badge-danger';
                                                $typeText = 'Keluar';
                                            } elseif ($hist->tipe == 'ADJ') {
                                                $typeClass = 'badge-warning';
                                                $typeText = 'Penyesuaian';
                                            }
                                            ?>
                                            <span class="badge <?= $typeClass ?>"><?= $typeText ?></span>
                                        </td>
                                        <td><?= $hist->referensi ?? '-' ?></td>
                                        <td class="text-center"><?= number_format($hist->stok_sebelum ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <?php if (($hist->perubahan ?? 0) > 0): ?>
                                                <span class="text-success">+<?= number_format($hist->perubahan ?? 0, 0, ',', '.') ?></span>
                                            <?php else: ?>
                                                <span class="text-danger"><?= number_format($hist->perubahan ?? 0, 0, ',', '.') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= number_format($hist->stok_sesudah ?? 0, 0, ',', '.') ?></td>
                                        <td><?= $hist->keterangan ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Information -->
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-cubes"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Stok Saat Ini</span>
                                <span class="info-box-number"><?= number_format($stock->stok ?? 0, 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-money-bill-wave"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Nilai</span>
                                <span class="info-box-number"><?= number_format(($stock->stok ?? 0) * ($stock->harga_beli ?? 0), 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Margin</span>
                                <span class="info-box-number"><?= number_format(($stock->harga_jual ?? 0) - ($stock->harga_beli ?? 0), 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
