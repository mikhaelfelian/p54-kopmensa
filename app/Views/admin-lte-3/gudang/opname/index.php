<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : View for displaying stock opname data.
 * This file represents the opname index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Data Stok Opname</h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/opname/create') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Tambah Opname
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Tgl Opname</th>
                            <th>User</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                        <?= form_open(base_url('gudang/opname'), ['autocomplete' => 'off']) ?>
                        <tr>
                            <th class="text-center"></th>
                            <th>
                                <?= form_input([
                                    'id' => 'tgl',
                                    'name' => 'tgl',
                                    'class' => 'form-control rounded-0',
                                    'placeholder' => 'Isikan Tgl ...',
                                    'type' => 'date',
                                    'value' => $tgl ?? ''
                                ]) ?>
                            </th>
                            <th></th>
                            <th>
                                <?= form_input([
                                    'id' => 'ket',
                                    'name' => 'ket',
                                    'class' => 'form-control rounded-0',
                                    'placeholder' => 'Isikan Keterangan ...',
                                    'value' => $ket ?? ''
                                ]) ?>
                            </th>
                            <th>
                                <button type="submit" class="btn btn-primary btn-flat">
                                    <i class="fa fa-search-plus"></i> Filter
                                </button>
                                <a href="<?= base_url('gudang/opname') ?>" class="btn btn-secondary btn-flat">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                            </th>
                        </tr>
                        <?= form_close() ?>
                    </thead>
                    <tbody>
                        <?php if (empty($opname)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($opname as $key => $row): ?>
                                <tr>
                                    <td style="width: 50px;" class="text-center">
                                        <?= (($currentPage - 1) * $perPage) + $key + 1 ?>.
                                    </td>
                                    <td style="width: 100px;" class="text-left">
                                        <?= date('d/m/Y', strtotime($row->created_at)) ?>
                                    </td>
                                    <td style="width: 250px;" class="text-left">
                                        <?= $row->user_name ?>
                                    </td>
                                    <td style="width: 350px;" class="text-left">
                                        <?= $row->keterangan ?? '-' ?>
                                        <br>
                                        <small class="text-muted">
                                            <?php if ($row->status == '0'): ?>
                                                <span class="badge badge-warning">Draft</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Selesai</span>
                                            <?php endif ?>
                                            
                                            <?php if ($row->reset == '0'): ?>
                                                <span class="badge badge-info">Belum Diproses</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Sudah Diproses</span>
                                            <?php endif ?>
                                        </small>
                                    </td>
                                    <td style="width: 100px;" class="text-left">
                                        <div class="btn-group">
                                            <a href="<?= base_url("gudang/opname/detail/{$row->id}") ?>"
                                                class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($row->status == '0'): ?>
                                                <a href="<?= base_url("gudang/opname/edit/{$row->id}") ?>"
                                                    class="btn btn-warning btn-sm rounded-0">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url("gudang/opname/input/{$row->id}") ?>"
                                                    class="btn btn-success btn-sm rounded-0" data-toggle="tooltip"
                                                    title="Input Item">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                <a href="<?= base_url("gudang/opname/delete/{$row->id}") ?>"
                                                    class="btn btn-danger btn-sm rounded-0" 
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')"
                                                    data-toggle="tooltip"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('opname', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Initialize date picker
        $('#tgl').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
<?= $this->endSection() ?>