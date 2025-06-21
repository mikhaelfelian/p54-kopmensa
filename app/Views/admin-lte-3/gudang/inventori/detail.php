<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-07-15
 * Github : github.com/mikhaelfelian
 * description : View for displaying item stock details.
 * This file represents the inventory detail view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Data Item</h3>
                    <div class="card-tools">

                    </div>
                </div>
                <div class="card-body table-responsive">
                    <div class="form-group ">
                        <label class="control-label">Kode</label>
                        <input type="text" value="<?= $item->kode ?? '' ?>" class="form-control rounded-0" readonly>
                    </div>
                    <div class="form-group ">
                        <label class="control-label">Item</label>
                        <input type="text" value="<?= $item->item ?? '' ?>" class="form-control rounded-0" readonly>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="control-label">Jumlah</label>
                            <input type="text" value="<?= $item->jumlah ?? 0 ?>" class="form-control text-right rounded-0" readonly>
                        </div>
                        <div class="col-8">
                            <div class="form-group ">
                                <label class="control-label">Satuan</label>
                                <select class="form-control rounded-0" disabled>
                                    <option value="">- Pilih -</option>
                                    <option value="<?= $item->id_satuan ?? '' ?>" selected=""><?= $item->id_satuan ?? 'PCS' ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="<?= base_url('gudang/stok') ?>" class="btn btn-primary btn-flat">« Kembali</a>
                        </div>
                        <div class="col-lg-6 text-right">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Inventori per Outlet</h3>
                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <form action="https://simrs.esensia.co.id/gudang/set_stok_update_gd.php" autocomplete="off"
                        method="post" accept-charset="utf-8">
                        <input type="hidden" name="medkit_tokens" value="f9944a5c80bc3376cebd8b2b0822be98">

                        <input type="hidden" name="id"
                            value="MjcxODMxYTNhYjVhMmE2YjgwOGFmMTVmYTM4YjA0MjU3M2RhNDEyNjI1ZDc1NjkxYzI1YjVmMmJkYWFmYTBkOWVmMjEwMWQ3OTg3MGM4ZWE5NGI0Y2Q2OWNiZmM1MjU0MWMwZDA2NjMxNjdmMjAzNjA4ODBlZDg5NzdkZjI3YzNNa2FidS9XRWpvTHlnQXZ3RkpMSnpmekVndWJId0Uva0V3RWtVb2pJZWtNPQ--">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Outlet</th>
                                    <th class="text-center"></th>
                                    <th colspan="4" class="text-left">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($outlets as $outlet): ?>
                                <tr>
                                    <th><?= $outlet->nama ?></th>
                                    <th>:</th>
                                    <td class="text-right" style="width: 120px;">
                                        <input type="text" name="jml[<?= $outlet->id_outlet ?>]" value="<?= $outlet->jml ?? 0 ?>" id="jml"
                                            class="form-control rounded-0">
                                    </td>
                                    <td class="text-left">PCS</td>
                                    <td class="text-left">
                                        <button type="submit" class="btn btn-primary btn-flat"><i
                                                class="fa fa-save"></i></button>
                                    </td>
                                    <td class="text-left">
                                        <?php if ($outlet->status == '1'): ?>
                                            <label class="badge badge-success">Utama</label>
                                        <?php else: ?>
                                            <label class="badge badge-secondary">Tidak Aktif</label>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default rounded-0">
                <div class="card-header">
                    <h3 class="card-title">Data Mutasi Stok</h3>
                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Gudang</th>
                                <th class="text-right">Jml</th>
                                <th>Satuan</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-group ">
                                        <select name="filter_gd" class="form-control rounded-0">
                                            <option value="">- [Pilih] -</option>
                                            <option value="1">
                                                Gd. Bawah</option>
                                            <option value="2">
                                                Gd. Atas</option>
                                        </select>
                                    </div>
                                </td>
                                <td style="width: 100px;">
                                    <input type="number" name="filter_jml" class="form-control rounded-0"
                                        placeholder="Jumlah" value="">
                                </td>
                                <td style="width: 100px;"></td>
                                <td>
                                    <input type="text" name="filter_ket" class="form-control rounded-0"
                                        placeholder="Keterangan" value="">
                                </td>
                                <td>
                                    <select name="filter_status" class="form-control rounded-0">
                                        <option value="">- [Semua] -</option>
                                        <option value="1">Stok Masuk Pembelian</option>
                                        <option value="2">Stok Masuk</option>
                                        <option value="3">Stok Masuk Retur Jual</option>
                                        <option value="4">Stok Keluar Penjualan</option>
                                        <option value="5">Stok Keluar Retur Beli</option>
                                        <option value="6">SO (Stock Opname)</option>
                                        <option value="7">Stok Keluar</option>
                                        <option value="8">Mutasi Antar Gudang</option>
                                        <option value="9">Adjust stok</option>
                                    </select>
                                </td>
                                <td><button type="submit" class="btn btn-primary btn-flat rounded-0"><i
                                            class="fa fa-search"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">

                        </div>
                        <div class="col-lg-6 text-right">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<?= $this->endSection() ?>