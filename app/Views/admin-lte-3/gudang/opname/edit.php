<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-12
 * Github : github.com/mikhaelfelian
 * description : View for editing stock opname data.
 * This file represents the opname edit view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">                  
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Edit Stok Opname</h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/opname') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <div class="row">
                    <div class="col-md-6">
                        <?= form_open(base_url("gudang/opname/update/{$opname->id}"), ['id' => 'opname_form', 'autocomplete' => 'off']) ?>
                        
                        <div class="form-group">
                            <label class="control-label">Tanggal</label>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                                <?= form_input([
                                    'id' => 'tgl',
                                    'name' => 'tgl_masuk',
                                    'class' => 'form-control text-middle',
                                    'style' => 'vertical-align: middle;',
                                    'type' => 'date',
                                    'value' => isset($opname->tgl_masuk) ? $opname->tgl_masuk : date('Y-m-d')
                                ]) ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="gudang">Gudang <i class="text-danger">*</i></label>
                            <select name="id_gudang" class="form-control rounded-0">
                                <option value="">- Pilih Gudang -</option>
                                <?php foreach ($gudang as $gd): ?>
                                    <option value="<?= $gd->id ?>" <?= ($gd->id == $opname->id_gudang) ? 'selected' : '' ?>>
                                        <?= $gd->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label">Keterangan</label>
                            <?= form_textarea([
                                'id' => 'keterangan',
                                'name' => 'keterangan',
                                'class' => 'form-control rounded-0 text-middle',
                                'style' => 'vertical-align: middle; height: 200px;',
                                'placeholder' => 'Inputkan keterangan opname...',
                                'value' => $opname->keterangan
                            ]) ?>
                        </div>
                        
                        <div class="text-right">
                            <a href="<?= base_url('gudang/opname') ?>" class="btn btn-warning btn-flat">
                                <i class="fa fa-undo"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-flat">
                                <i class="fa fa-save"></i> Update
                            </button>
                        </div>
                        <?= form_close() ?>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Informasi</h5>
                            <ul>
                                <li>Hanya opname dengan status "Draft" yang dapat diedit</li>
                                <li>Setelah opname diproses, data tidak dapat diubah</li>
                                <li>Perubahan gudang akan mempengaruhi item yang sudah diinput</li>
                            </ul>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Status Opname</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Status:</strong> 
                                    <?php if ($opname->status == '0'): ?>
                                        <span class="badge badge-warning">Draft</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Selesai</span>
                                    <?php endif ?>
                                </p>
                                <p><strong>Reset:</strong> 
                                    <?php if ($opname->reset == '0'): ?>
                                        <span class="badge badge-info">Belum Diproses</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Sudah Diproses</span>
                                    <?php endif ?>
                                </p>
                                <p><strong>Dibuat:</strong> <?= date('d/m/Y H:i', strtotime($opname->created_at)) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize date picker
    $('#tgl').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });
});
</script>
<?= $this->endSection() ?> 