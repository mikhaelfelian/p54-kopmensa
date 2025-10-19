<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-20
 * Github : github.com/mikhaelfelian
 * description : View for displaying customer detail data
 * This file represents the View for customer details.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Detail Data Pelanggan</h3>
                <div class="card-tools"></div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="control-label">Kode</label>
                    <div class="form-control rounded-0 bg-light" readonly>
                        <?= esc($pelanggan->kode) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Kode</label>
                    <div class="form-control rounded-0 bg-light" readonly>
                        <?= esc($pelanggan->kode) ?>
                    </div>
                </div>
                <?php
                    // Ambil username dari Ion Auth berdasarkan $pelanggan->id_user
                    $ionAuthUsername = '-';
                    if (!empty($pelanggan->id_user)) {
                        $ionAuthModel = new \IonAuth\Models\IonAuthModel();
                        $ionAuthUser = $ionAuthModel->user($pelanggan->id_user)->row();
                        if ($ionAuthUser && !empty($ionAuthUser->username)) {
                            $ionAuthUsername = $ionAuthUser->username;
                        }
                    }
                ?>
                <div class="form-group">
                    <label class="control-label">Username</label>
                    <div class="form-control rounded-0 bg-light" readonly>
                        <?= esc($ionAuthUsername) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Nama</label>
                    <div class="form-control rounded-0 bg-light" readonly>
                        <?= esc($pelanggan->nama) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">No. Telp</label>
                    <div class="form-control rounded-0 bg-light" readonly>
                        <?= esc($pelanggan->no_telp) ?: '-' ?>
            </div>
        </div>

                <div class="form-group">
                    <label class="control-label">Alamat</label>
                    <div class="form-control rounded-0 bg-light" readonly style="min-height: 80px;">
                        <?= nl2br(esc($pelanggan->alamat)) ?>
    </div>
            </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Kota</label>
                            <div class="form-control rounded-0 bg-light" readonly>
                                <?= esc($pelanggan->kota) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Provinsi</label>
                            <div class="form-control rounded-0 bg-light" readonly>
                                <?= esc($pelanggan->provinsi) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Tipe</label>
                            <div class="form-control rounded-0 bg-light" readonly>
                                <?php
                                $tipeLabels = [
                                    '1' => 'Anggota',
                                    '2' => 'Umum'
                                ];
                                echo $tipeLabels[$pelanggan->tipe] ?? '-';
                                ?>
                            </div>
                        </div>
                    </div>                                
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <div class="form-control rounded-0 bg-light" readonly>
                                <span class="badge badge-<?= ($pelanggan->status == '1') ? 'success' : 'danger' ?>">
                                    <?= ($pelanggan->status == '1') ? 'Aktif' : 'Non - Aktif' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        <a href="<?= base_url('master/customer') ?>" class="btn btn-primary btn-flat">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                    </div>
                    <div class="col-lg-6 text-right">
                        <a href="<?= base_url("master/customer/edit/{$pelanggan->id}") ?>" 
                           class="btn btn-warning btn-flat">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                        <a href="<?= base_url("master/customer/delete/{$pelanggan->id}") ?>"
                           class="btn btn-danger btn-flat"
                       onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
    
    <!-- Contact Person Section - Show if customer type is Instansi/Swasta -->
    <?php if ($pelanggan->tipe > 1): ?>
    <div class="col-md-6">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Data Kontak</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addContactModal">
                        <i class="fas fa-plus"></i> Tambah Kontak
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($contacts)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Nama</th>
                            <th>HP</th>
                            <th>Jabatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $key => $contact): ?>
                        <tr>
                            <td class="text-center"><?= $key + 1 ?></td>
                            <td><?= esc($contact->nama) ?></td>
                            <td><?= esc($contact->no_hp) ?: '-' ?></td>
                            <td><?= esc($contact->jabatan) ?></td>
                            <td class="text-center">
                                <a href="<?= base_url("master/customer/delete_contact/{$contact->id}") ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Apakah anda yakin ingin menghapus kontak ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <p>Belum ada data kontak</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Purchase History Section -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Riwayat Pembelian</h3>
                <div class="card-tools">
                    <span class="badge badge-info">Total: <?= count($transactions ?? []) ?> Transaksi</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($transactions)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>No. Nota</th>
                                <th>Tanggal</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Pembayaran</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $key => $trx): ?>
                            <tr>
                                <td class="text-center"><?= $key + 1 ?></td>
                                <td><strong><?= esc($trx->no_nota) ?></strong></td>
                                <td><?= date('d/m/Y H:i', strtotime($trx->tgl_masuk)) ?></td>
                                <td class="text-right">Rp <?= number_format($trx->jml_gtotal, 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <?php
                                    $statusLabels = [
                                        '0' => '<span class="badge badge-secondary">Draft</span>',
                                        '1' => '<span class="badge badge-success">Selesai</span>',
                                        '2' => '<span class="badge badge-danger">Batal</span>',
                                        '3' => '<span class="badge badge-warning">Retur</span>',
                                        '4' => '<span class="badge badge-info">Pending</span>'
                                    ];
                                    echo $statusLabels[$trx->status] ?? '<span class="badge badge-secondary">-</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $paymentLabels = [
                                        '0' => '<span class="badge badge-warning">Belum Lunas</span>',
                                        '1' => '<span class="badge badge-success">Lunas</span>'
                                    ];
                                    echo $paymentLabels[$trx->status_bayar] ?? '<span class="badge badge-secondary">-</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url("transaksi/jual?search={$trx->no_nota}") ?>" 
                                       class="btn btn-info btn-sm" 
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th colspan="3" class="text-right">Total Pembelian:</th>
                                <th class="text-right">
                                    Rp <?= number_format(array_sum(array_column($transactions, 'jml_gtotal')), 0, ',', '.') ?>
                                </th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <p>Belum ada riwayat pembelian</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Add Contact Modal -->
<?php if ($pelanggan->tipe > 1): ?>
<div class="modal fade" id="addContactModal" tabindex="-1" role="dialog" aria-labelledby="addContactModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?= form_open('master/customer/store_contact') ?>
            <?= form_hidden('id_pelanggan', $pelanggan->id) ?>
            <div class="modal-header">
                <h5 class="modal-title" id="addContactModalLabel">Tambah Kontak</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama*</label>
                    <?= form_input([
                        'name' => 'nama',
                        'class' => 'form-control rounded-0',
                        'placeholder' => 'Isikan nama CP ...',
                        'required' => true
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <?= form_input([
                        'name' => 'no_hp',
                        'class' => 'form-control rounded-0',
                        'placeholder' => 'Isikan nomor telepon CP ...'
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Jabatan*</label>
                    <?= form_input([
                        'name' => 'jabatan',
                        'class' => 'form-control rounded-0',
                        'placeholder' => 'Isikan Jabatan ...',
                        'required' => true
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?> 