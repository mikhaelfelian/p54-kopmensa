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
                                    <option value="<?= $item->satuan_id ?? '' ?>" selected=""><?= $item->satuan ?? '' ?></option>
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
                                <tr>
                                    <th>Pojok Seduh</th>
                                    <th>:</th>
                                    <td class="text-right" style="width: 120px;">
                                        <input type="text" name="jml[2060]" value="1" id="jml"
                                            class="form-control rounded-0">
                                    </td>
                                    <td class="text-left">PCS</td>
                                    <td class="text-left">
                                        <button type="submit" class="btn btn-primary btn-flat"><i
                                                class="fa fa-save"></i></button>
                                    </td>
                                    <td class="text-left"><label class="badge badge-success">Utama</label></td>
                                </tr>
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
                        <ul class="pagination pagination-sm float-right">
                            <ul class="pagination pagination-sm">
                                <li class="page-item active"><a href="#" class="page-link">1</a></li>
                                <li class="page-item"><a
                                        href="https://simrs.esensia.co.id/gudang/data_stok_tambah.php?id=MjcxODMxYTNhYjVhMmE2YjgwOGFmMTVmYTM4YjA0MjU3M2RhNDEyNjI1ZDc1NjkxYzI1YjVmMmJkYWFmYTBkOWVmMjEwMWQ3OTg3MGM4ZWE5NGI0Y2Q2OWNiZmM1MjU0MWMwZDA2NjMxNjdmMjAzNjA4ODBlZDg5NzdkZjI3YzNNa2FidS9XRWpvTHlnQXZ3RkpMSnpmekVndWJId0Uva0V3RWtVb2pJZWtNPQ--&amp;halaman=15"
                                        class="page-link" data-ci-pagination-page="2">2</a></li>
                                <li class="page-item"><a
                                        href="https://simrs.esensia.co.id/gudang/data_stok_tambah.php?id=MjcxODMxYTNhYjVhMmE2YjgwOGFmMTVmYTM4YjA0MjU3M2RhNDEyNjI1ZDc1NjkxYzI1YjVmMmJkYWFmYTBkOWVmMjEwMWQ3OTg3MGM4ZWE5NGI0Y2Q2OWNiZmM1MjU0MWMwZDA2NjMxNjdmMjAzNjA4ODBlZDg5NzdkZjI3YzNNa2FidS9XRWpvTHlnQXZ3RkpMSnpmekVndWJId0Uva0V3RWtVb2pJZWtNPQ--&amp;halaman=30"
                                        class="page-link" data-ci-pagination-page="3">3</a></li>
                                <li class="page-item"><a
                                        href="https://simrs.esensia.co.id/gudang/data_stok_tambah.php?id=MjcxODMxYTNhYjVhMmE2YjgwOGFmMTVmYTM4YjA0MjU3M2RhNDEyNjI1ZDc1NjkxYzI1YjVmMmJkYWFmYTBkOWVmMjEwMWQ3OTg3MGM4ZWE5NGI0Y2Q2OWNiZmM1MjU0MWMwZDA2NjMxNjdmMjAzNjA4ODBlZDg5NzdkZjI3YzNNa2FidS9XRWpvTHlnQXZ3RkpMSnpmekVndWJId0Uva0V3RWtVb2pJZWtNPQ--&amp;halaman=15"
                                        class="page-link" data-ci-pagination-page="2" rel="next">›</a></li>
                            </ul>
                        </ul>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Gudang</th>
                                <th class="text-right">Jml</th>
                                <th>Satuan</th>
                                <th>Keterangan</th>
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <form method="get" action="https://simrs.esensia.co.id/gudang/data_stok_tambah.php"
                            autocomplete="off"></form>
                        <input type="hidden" name="id"
                            value="MjcxODMxYTNhYjVhMmE2YjgwOGFmMTVmYTM4YjA0MjU3M2RhNDEyNjI1ZDc1NjkxYzI1YjVmMmJkYWFmYTBkOWVmMjEwMWQ3OTg3MGM4ZWE5NGI0Y2Q2OWNiZmM1MjU0MWMwZDA2NjMxNjdmMjAzNjA4ODBlZDg5NzdkZjI3YzNNa2FidS9XRWpvTHlnQXZ3RkpMSnpmekVndWJId0Uva0V3RWtVb2pJZWtNPQ--">

                        <input type="hidden" name="id_produk"
                            value="YzcyMDJmNzZiNzRjODA4ODA4Nzg1ZDgwYzY3MmE0MjRkZDc4MDAxNGFkODgwM2Q3ZWU4NmM5Y2ZkODQxY2Y3ZmRkNmZlMDkyODVhNjljOWE0OTFlNTE0YmI0YmViYjllZDQxOWI3NWRhNjBkZDM4MmVmZDcwZmIzMGRkYTNkYTFieGF6YWpaU2tzY1JDVjlZOHhmUGp0L2ovVzdVK01pQUEyRkFKTys2ZDFBPQ--">

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

                        <tbody>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Atas<br>
                                    <small><i>DESI SUCI LESTARI</i></small><br>
                                    <small><i>06-11-2023 11:32</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Pembelian <a
                                        href="https://simrs.esensia.co.id/transaksi/trans_beli_det.php?id=YzVhYWQyZTU3ZDUzZGRlMDU4NjcyNzI4MjM1ZmUzMWU1MTBhM2M5YjUyMjcwNTMzNDNmM2I4MzI1YjE4YTg1OWZmMTM1MDM5Y2YwOTczZTlhZTE4NDIxNjMzMjlmYWExNWJmNTY5ZDVhZmQyZDA2NzI2NDI1YzRkYjYxNzk4OGNTMVRMRkh1KzRCUTY5N1MxQm00RDZtWWZNWW02OHI2TmNNem9CcXdOcEFnPQ--"
                                        target="_blank">02-1040317</a> </td>
                                <td>
                                    <label class="badge badge-success">Stok Masuk</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Bawah<br>
                                    <small><i>RIDA YATUL ARI FADLINA, S. Farm</i></small><br>
                                    <small><i>15-11-2023 20:21</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    TN. MUHAMMAD TAUFIQY NUR </td>
                                <td>
                                    <label class="badge badge-info">Stok Keluar</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Bawah<br>
                                    <small><i>TIM B</i></small><br>
                                    <small><i>04-12-2023 15:20</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    0 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Stok Opname 00464 </td>
                                <td>
                                    <label class="badge badge-warning">Stok Opname</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Bawah<br>
                                    <small><i>TIM C</i></small><br>
                                    <small><i>04-12-2023 15:58</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    0 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Stok Opname 00476 </td>
                                <td>
                                    <label class="badge badge-warning">Stok Opname</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Atas<br>
                                    <small><i>TIM A</i></small><br>
                                    <small><i>04-12-2023 17:42</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    0 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Stok Opname 00498 </td>
                                <td>
                                    <label class="badge badge-warning">Stok Opname</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Atas<br>
                                    <small><i>DESI SUCI LESTARI</i></small><br>
                                    <small><i>07-12-2023 09:14</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Pembelian <a
                                        href="https://simrs.esensia.co.id/transaksi/trans_beli_det.php?id=MDFmNGExZjEzMTQzMWUwYzM1ZDQ4OTYzNzA4MDEzYTBkM2ZhYjExMDMwZDgxZDE5MWI4NmQyYzlmM2E4NTIyMDBmM2YzNzdiMzM5OTZmYTc5NzVmZmFlMjMzNTcwNTRmODExOWNjZDczZmIzZTJlNDVlNzQ0NzEyZDJlYzU2MmNPZEo3eDVnN2ZhZ2xEODFiTUZyRmRjRjhvdTZpaWJVSmpsMlBSd0lmSGhzPQ--"
                                        target="_blank">02-1043703</a> </td>
                                <td>
                                    <label class="badge badge-success">Stok Masuk</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Bawah<br>
                                    <small><i>RIDA YATUL ARI FADLINA, S. Farm</i></small><br>
                                    <small><i>13-12-2023 19:34</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    TN. MUHAMMAD TAUFIQY NUR </td>
                                <td>
                                    <label class="badge badge-info">Stok Keluar</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Atas<br>
                                    <small><i>DESI SUCI LESTARI</i></small><br>
                                    <small><i>23-12-2023 08:58</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Pembelian <a
                                        href="https://simrs.esensia.co.id/transaksi/trans_beli_det.php?id=N2EyZTNjODZiZGQ3ODMzZWI0MTkzNWY4OTAzNjAwMzUwYjk2ZjY3MjhmMTc1MWJkYTcwNmI3ZTM5MGNhOGMxNDFjZTY3ODIzYzkzM2FkNmViZWNlMjk3ODY3Zjg4YmJjYjQzYTE5YzI2YjUwZGYyNGYzOWNhZDU1OGUwMDYyNWZkTlhWSUljNGhMRzk3UGxrU0pkdHBmSkUza3U0b2haZ25MMjh1OXM0T0kwPQ--"
                                        target="_blank">02-1045476</a> </td>
                                <td>
                                    <label class="badge badge-success">Stok Masuk</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Bawah<br>
                                    <small><i>NOVIA IKA WULANDARI, S.M</i></small><br>
                                    <small><i>17-01-2024 20:23</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    TN. MUHAMMAD TAUFIQY NUR </td>
                                <td>
                                    <label class="badge badge-info">Stok Keluar</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Atas<br>
                                    <small><i>DESI SUCI LESTARI</i></small><br>
                                    <small><i>29-01-2024 10:17</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Pembelian <a
                                        href="https://simrs.esensia.co.id/transaksi/trans_beli_det.php?id=NDAzODM2M2U3ZDdmZTNkMjIyNTE0N2U0YmY3YWM0NmQwZGY3NjRlYmJkOTNiZGFhNTJlMjdkM2M1NDQwNGQ0NmZjMDMyMTI3ZWNkNzdmMjcxZjdjYmU3MDQxMDViZDhkMTE4Yzk1Mzg2NTllZjIxZjc3OTIzMTYwMzRhOTQ2MjM2THNGcDdWUll0Q2JqTFREWE1xNnRHZ2lUMlUvNGdGVktYWVZJUmt6b1lvPQ--"
                                        target="_blank">02-1049071</a> </td>
                                <td>
                                    <label class="badge badge-success">Stok Masuk</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Atas<br>
                                    <small><i>NARULITA NOOR WIDYA, S.E</i></small><br>
                                    <small><i>03-02-2024 09:01</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Mutasi stok antar gudang [<a
                                        href="https://simrs.esensia.co.id/gudang/trans_mutasi_det.php?id=MjgxMDcwMjc2MWVkN2E2MGQwZGMzMDkzMmQzNDVjODlmOGIzMWVkMDNkOTllYTNlNWEzODUzMzZjYjA2OGZlZDUxZmVmODRiMWNmOGM3NDljZmQ2MWMzYTM3ZThmMWJhNTI0YjQ3NzhmNzNkMjgzZDI5YmFlZGRmYzM3OGZlMDNyTnhJeXI4blJZbnM3YWdYaWVBNlVhdFdGdjlsVk12Z1gwVzR6enZndThnPQ--">#00116</a>]
                                </td>
                                <td>
                                    <label class="badge badge-warning">Mutasi</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Bawah<br>
                                    <small><i>TIM A</i></small><br>
                                    <small><i>05-03-2024 09:37</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Stok Opname 00510 </td>
                                <td>
                                    <label class="badge badge-warning">Stok Opname</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Atas<br>
                                    <small><i>TIM C</i></small><br>
                                    <small><i>05-03-2024 09:56</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    0 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Stok Opname 00513 </td>
                                <td>
                                    <label class="badge badge-warning">Stok Opname</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Bawah<br>
                                    <small><i>JUNITA PUTRI SONITA</i></small><br>
                                    <small><i>06-04-2024 20:54</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    TN. MUHAMMAD TAUFIQY NUR </td>
                                <td>
                                    <label class="badge badge-info">Stok Keluar</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 350px;">
                                    Gd. Atas<br>
                                    <small><i>DESI SUCI LESTARI</i></small><br>
                                    <small><i>19-04-2024 17:25</i></small>
                                </td>
                                <td style="width: 100px;" class="text-right">
                                    1 </td>
                                <td style="width: 150px;">
                                </td>
                                <td style="width: 600px;">
                                    Pembelian <a
                                        href="https://simrs.esensia.co.id/transaksi/trans_beli_det.php?id=OGZjYTU2YzFiNmQ5MTVjMjI4MTgyM2IxNDI0M2FmMTkxMDVkYjA5YTJlYjI2MDQ4YWIwNGIwZTQwNWRlNTc4NzFmNWI2NDY0YmNkMGQ5NTJjMmE2NzQ1MGZiODNmYjVlOTU0ZTE1ZGY2YTAyYWIxN2NlNjk0ZGU3NzE4MjBlMTJOQzdGSjlieW8xNHUzSmtiNzMyTHVYc2VEVG8zcUQ0ZUtabEM1WnRheEljPQ--"
                                        target="_blank">02-1058817</a><br><small><i>[2K80YDA]</i></small> </td>
                                <td>
                                    <label class="badge badge-success">Stok Masuk</label>
                                </td>
                                <td>
                                    <!--<label class="label label-default" ><i class="fa fa-remove"></i> Hapus</label>-->
                                </td>
                            </tr>
                            <!-- <tr>
                                            <th colspan="4" class="text-right">Total Stok Opname</th>
                                            <td class="text-right">3</td>
                                            <td colspan="4" class="text-left"></td>
                                        </tr> -->
                            <tr>
                                <th colspan="4" class="text-right">Total Stok Masuk</th>
                                <td class="text-right">3</td>
                                <td colspan="4" class="text-left"></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Total Transfer Stok</th>
                                <td class="text-right">2</td>
                                <td colspan="4" class="text-left"></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Total Stok Keluar</th>
                                <td class="text-right">2</td>
                                <td colspan="4" class="text-left"></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Sisa Stok</th>
                                <td class="text-right">1</td>
                                <td colspan="4" class="text-left"></td>
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