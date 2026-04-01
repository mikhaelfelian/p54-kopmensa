<?php
/**
 * PO trash list (soft-deleted draft POs)
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <a href="<?= base_url('transaksi/po') ?>" class="btn btn-sm btn-secondary rounded-0">
            <i class="fas fa-arrow-left"></i> Kembali ke PO
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>No. PO</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($po_list)): ?>
                        <?php foreach ($po_list as $po): ?>
                            <tr>
                                <td><?= esc($po->no_nota ?? '') ?></td>
                                <td><?= esc($po->supplier ?? '') ?></td>
                                <td><?= esc($transBeliPOModel->getStatusLabel((int) ($po->status ?? 0))) ?></td>
                                <td>
                                    <?php if ((int) ($po->status ?? -1) === 0): ?>
                                        <a href="<?= base_url('transaksi/po/restore/' . $po->id) ?>"
                                           class="btn btn-sm btn-success rounded-0"
                                           onclick="return confirm('Pulihkan PO ini?')">Pulihkan</a>
                                        <a href="<?= base_url('transaksi/po/delete-permanent/' . $po->id) ?>"
                                           class="btn btn-sm btn-danger rounded-0"
                                           onclick="return confirm('Hapus permanen? Tidak dapat dibatalkan.')">Hapus permanen</a>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada PO di sampah</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($po_list) && isset($pager)): ?>
            <div class="card-footer"><?= $pager->links('po', 'default_full') ?></div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
