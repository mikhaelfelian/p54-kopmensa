<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : View for creating transfer/mutasi data.
 * This file represents the transfer create view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <?= form_open(base_url('gudang/transfer/store'), ['id' => 'form_mutasi', 'autocomplete' => 'off']) ?>
        <?= csrf_field() ?>
        <?= form_hidden('gd_asal', '2') ?>
        <?= form_hidden('gd_tujuan', '1') ?>

        <div class="card card-default rounded-0">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-truck"></i> Form Mutasi Stok</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label for="tgl" class="col-sm-4 col-form-label">Tanggal <i class="text-danger">*</i></label>
                    <div class="col-sm-8">
                        <div class="input-group mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            </div>
                            <?= form_input([
                                'id' => 'tgl',
                                'name' => 'tgl_masuk',
                                'class' => 'form-control pull-right rounded-0',
                                'placeholder' => 'Inputkan tanggal ...',
                                'value' => date('d-m-Y'),
                                'type' => 'date'
                            ]) ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="tipe" class="col-sm-4 col-form-label">Tipe <i class="text-danger">*</i></label>
                    <div class="col-sm-8">
                        <select id="tipe" name="tipe" class="form-control rounded-0">
                            <option value="1" selected>Pindah Gudang</option>
                            <option value="2">Stok Masuk</option>
                            <option value="3">Stok Keluar</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="gd_asal" class="col-sm-4 col-form-label">Gudang Asal <i class="text-danger">*</i></label>
                    <div class="col-sm-8">
                        <select name="id_gd_asal" class="form-control rounded-0">
                            <option value="">- Pilih -</option>
                            <?php foreach ($gudang as $gd_asal): ?>
                                <option value="<?= $gd_asal->id ?>" <?= ($gd_asal->status_gd == '1' ? '' : 'selected') ?>>
                                    <?= $gd_asal->gudang . ($gd_asal->status_gd == '1' ? ' [Utama]' : '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="outlet_tujuan" class="col-sm-4 col-form-label">Outlet Tujuan</label>
                    <div class="col-sm-8">
                        <select name="id_outlet" class="form-control rounded-0">
                            <option value="">- Pilih -</option>
                            <?php foreach ($outlet as $out): ?>
                                <option value="<?= $out->id ?>">
                                    <?= $out->nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="keterangan" class="col-sm-4 col-form-label">Keterangan</label>
                    <div class="col-sm-8">
                        <?= form_textarea([
                            'id' => 'keterangan',
                            'name' => 'keterangan',
                            'class' => 'form-control pull-right rounded-0',
                            'rows' => '5',
                            'placeholder' => 'Inputkan keterangan / catatan ...'
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-danger btn-flat">
                            <i class="fas fa-remove"></i> Batal
                        </a>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary btn-flat">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?= form_close() ?>
    </div>
    
    <div class="col-md-6">
        <div class="card card-default rounded-0">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-box-open"></i> Form Input Stok</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label for="kode" class="col-sm-3 col-form-label">Kode <i class="text-danger">*</i></label>
                    <div class="col-sm-9">
                        <?= form_input([
                            'id' => 'kode',
                            'name' => 'kode',
                            'class' => 'form-control pull-right rounded-0',
                            'placeholder' => 'Inputkan Kode / Nama Item ...'
                        ]) ?>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="item" class="col-sm-3 col-form-label">Item</label>
                    <div class="col-sm-9">
                        <?= form_input([
                            'id' => 'item',
                            'name' => 'item',
                            'class' => 'form-control pull-right rounded-0',
                            'placeholder' => 'Inputkan Nama Item ...',
                            'readonly' => 'readonly'
                        ]) ?>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="stok" class="col-sm-3 col-form-label"><i><small>Stok Gudang</small></i></label>
                    <div class="col-sm-2">
                        <?= form_input([
                            'id' => 'stok',
                            'name' => 'stok',
                            'class' => 'form-control pull-right text-center rounded-0',
                            'disabled' => 'disabled'
                        ]) ?>
                    </div>
                    <div class="col-sm-4">
                        <?= form_input([
                            'id' => 'st',
                            'name' => 'st',
                            'class' => 'form-control pull-right text-left rounded-0',
                            'disabled' => 'disabled'
                        ]) ?>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="jml" class="col-sm-3 col-form-label">Jml</label>
                    <div class="col-sm-2">
                        <?= form_input([
                            'id' => 'jml',
                            'name' => 'jml',
                            'class' => 'form-control pull-right text-center rounded-0',
                            'placeholder' => 'Jml ...',
                            'value' => '1',
                            'type' => 'number'
                        ]) ?>
                    </div>
                    <div class="col-sm-4">
                        <select id="satuan" name="satuan" class="form-control rounded-0">
                            <option value="1">PCS</option>
                            <option value="2">BOX</option>
                            <option value="3">LUSIN</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary btn-flat" id="btn_tambah">
                            <i class="fa fa-plus"></i> Tambah
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-default rounded-0">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-boxes-stacked"></i> Data Item</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table_items">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-left">Item</th>
                            <th class="text-center">Gudang</th>
                            <th class="text-center">Stok Asal</th>
                            <th class="text-center">Jml Mutasi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center">Tidak Ada Data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-success btn-flat" id="btn_proses" style="display: none;">
                            <i class="fa fa-check-circle"></i> Proses
                        </button>
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
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true
    });
    
    $('#tgl_ed').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true
    });
    
    // Handle form submission
    $('#form_mutasi').on('submit', function(e) {
        e.preventDefault();
        
        // Add your form validation and submission logic here
        if (confirm('Simpan data transfer?')) {
            this.submit();
        }
    });
    
    // Handle add item button
    $('#btn_tambah').on('click', function() {
        // Add your logic to add items to the table
        alert('Fitur tambah item akan diimplementasikan');
    });
    
    // Handle process button
    $('#btn_proses').on('click', function() {
        // Add your logic to process the transfer
        alert('Fitur proses transfer akan diimplementasikan');
    });
});
</script>
<?= $this->endSection() ?> 