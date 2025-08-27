<?= $this->extend('admin-lte-3/layout/main') ?>

<?= $this->section('title') ?>
    <?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Permintaan Refund</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('transaksi/refund') ?>">Permintaan Refund</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Permintaan Refund</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('transaksi/refund') ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (session()->getFlashdata('error')) : ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                    <?= session()->getFlashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>No. Refund:</strong></td>
                                            <td>#<?= $refundRequest->id ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                $statusText = '';
                                                switch ($refundRequest->status) {
                                                    case 'pending':
                                                        $statusClass = 'badge badge-warning';
                                                        $statusText = 'Menunggu Persetujuan';
                                                        break;
                                                    case 'approved':
                                                        $statusClass = 'badge badge-success';
                                                        $statusText = 'Disetujui';
                                                        break;
                                                    case 'rejected':
                                                        $statusClass = 'badge badge-danger';
                                                        $statusText = 'Ditolak';
                                                        break;
                                                }
                                                ?>
                                                <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Request:</strong></td>
                                            <td><?= date('d/m/Y H:i', strtotime($refundRequest->created_at)) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kasir:</strong></td>
                                            <td><?= $refundRequest->cashier_name ?? 'N/A' ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>No. Nota:</strong></td>
                                            <td><?= $refundRequest->transaction_no ?? 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Transaksi:</strong></td>
                                            <td>Rp <?= number_format($refundRequest->transaction_amount ?? 0, 0, ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jumlah Refund:</strong></td>
                                            <td><strong>Rp <?= number_format($refundRequest->amount, 0, ',', '.') ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Transaksi:</strong></td>
                                            <td><?= $refundRequest->transaction_date ? date('d/m/Y H:i', strtotime($refundRequest->transaction_date)) : 'N/A' ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user"></i> Informasi Pelanggan</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Nama:</strong></td>
                                            <td><?= $refundRequest->customer_name ?? 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Alamat:</strong></td>
                                            <td><?= $refundRequest->customer_address ?? 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Telepon:</strong></td>
                                            <td><?= $refundRequest->customer_phone ?? 'N/A' ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-comment"></i> Alasan Refund</h5>
                                    <div class="alert alert-info">
                                        <?= nl2br(htmlspecialchars($refundRequest->reason)) ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($refundRequest->status !== 'pending') : ?>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-check-circle"></i> Informasi Approval</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="150"><strong>Status:</strong></td>
                                                <td>
                                                    <?php if ($refundRequest->status === 'approved') : ?>
                                                        <span class="badge badge-success">Disetujui</span>
                                                    <?php else : ?>
                                                        <span class="badge badge-danger">Ditolak</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Oleh:</strong></td>
                                                <td><?= $refundRequest->approved_by ? 'Superadmin' : 'N/A' ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tanggal:</strong></td>
                                                <td><?= $refundRequest->approved_at ? date('d/m/Y H:i', strtotime($refundRequest->approved_at)) : 'N/A' ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($refundRequest->status === 'rejected' && $refundRequest->rejection_reason) : ?>
                                            <h5><i class="fas fa-times-circle"></i> Alasan Penolakan</h5>
                                            <div class="alert alert-danger">
                                                <?= nl2br(htmlspecialchars($refundRequest->rejection_reason)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-4">
                                <a href="<?= base_url('transaksi/refund') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                </a>
                                
                                <?php if ($refundRequest->status === 'pending' && $this->ionAuth->inGroup('superadmin')) : ?>
                                    <a href="<?= base_url('transaksi/refund/approve/' . $refundRequest->id) ?>" 
                                       class="btn btn-success" 
                                       onclick="return confirm('Apakah Anda yakin ingin menyetujui permintaan refund ini?')">
                                        <i class="fas fa-check"></i> Setujui
                                    </a>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Reject Modal -->
<?php if ($refundRequest->status === 'pending' && $this->ionAuth->inGroup('superadmin')) : ?>
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('transaksi/refund/reject/' . $refundRequest->id) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Permintaan Refund</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" 
                                  placeholder="Berikan alasan penolakan yang jelas" required></textarea>
                        <small class="form-text text-muted">Alasan penolakan wajib diisi</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
