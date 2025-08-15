<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/voucher/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Voucher
                        </a>
                    </div>
                    <div class="col-md-6">
                        <?= form_open('', ['method' => 'get', 'class' => 'float-right']) ?>
                        <div class="input-group input-group-sm">
                            <?= form_input([
                                'name' => 'keyword',
                                'class' => 'form-control rounded-0',
                                'value' => $keyword ?? '',
                                'placeholder' => 'Cari voucher...'
                            ]) ?>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-primary rounded-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            
            <!-- Summary Dashboard -->
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= number_format($summary['total']) ?></h3>
                                <p>Total Voucher</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($summary['active']) ?></h3>
                                <p>Voucher Aktif</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?= number_format($summary['nominal']) ?></h3>
                                <p>Voucher Nominal</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($summary['percentage']) ?></h3>
                                <p>Voucher Persen</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kode Voucher</th>
                            <th>Jenis</th>
                            <th>Nominal</th>
                            <th>Jumlah</th>
                            <th>Terpakai</th>
                            <th>Maksimal</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($vouchers)): ?>
                            <?php foreach ($vouchers as $key => $voucher): ?>
                                <tr>
                                    <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                    <td>
                                        <span class="badge badge-secondary"><?= esc($voucher->kode) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($voucher->jenis_voucher === 'nominal'): ?>
                                            <span class="badge badge-primary">Nominal</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">Persen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($voucher->jenis_voucher === 'nominal'): ?>
                                            Rp <?= number_format($voucher->nominal) ?>
                                        <?php else: ?>
                                            <?= $voucher->nominal ?>%
                                        <?php endif; ?>
                                    </td>
                                    <td><?= number_format($voucher->jml) ?></td>
                                    <td>
                                        <span class="badge badge-info"><?= number_format($voucher->jml_keluar) ?></span>
                                    </td>
                                    <td><?= number_format($voucher->jml_max) ?></td>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y', strtotime($voucher->tgl_masuk)) ?> - 
                                            <?= date('d/m/Y', strtotime($voucher->tgl_keluar)) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php 
                                        $today = date('Y-m-d');
                                        $isExpired = $voucher->tgl_keluar < $today;
                                        $isNotStarted = $voucher->tgl_masuk > $today;
                                        $isFull = $voucher->jml_keluar >= $voucher->jml_max;
                                        ?>
                                        
                                        <?php if ($voucher->status == '0'): ?>
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        <?php elseif ($isExpired): ?>
                                            <span class="badge badge-danger">Kadaluarsa</span>
                                        <?php elseif ($isNotStarted): ?>
                                            <span class="badge badge-warning">Belum Aktif</span>
                                        <?php elseif ($isFull): ?>
                                            <span class="badge badge-dark">Habis</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('master/voucher/detail/' . $voucher->id) ?>" 
                                               class="btn btn-sm btn-info rounded-0" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('master/voucher/edit/' . $voucher->id) ?>" 
                                               class="btn btn-sm btn-warning rounded-0" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($voucher->jml_keluar == 0): ?>
                                                <a href="<?= base_url('master/voucher/delete/' . $voucher->id) ?>" 
                                                   class="btn btn-sm btn-danger rounded-0" 
                                                   onclick="return confirm('Yakin ingin menghapus voucher ini?')" 
                                                   title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Belum ada data voucher
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <?php if (!empty($vouchers)): ?>
                <div class="card-footer">
                    <?= $pager->links('voucher', 'default_full') ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?= $this->endSection() ?>