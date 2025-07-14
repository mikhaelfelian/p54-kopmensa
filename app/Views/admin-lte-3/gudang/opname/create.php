<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : View for creating stock opname data.
 * This file represents the opname create view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">                  
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Form Stok Opname</h3>
                <div class="card-tools">

                </div>
            </div>
            <div class="card-body table-responsive">
                <div class="row">
                    <div class="col-md-5">
                        <?= form_open(base_url('gudang/opname/store'), ['id' => 'opname_form', 'autocomplete' => 'off']) ?>
                        
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
                                    'value' => date('Y-m-d')
                                ]) ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="opname_type">Tipe Opname <i class="text-danger">*</i></label>
                            <select name="opname_type" id="opname_type" class="form-control rounded-0" required>
                                <option value="">- Pilih Tipe Opname -</option>
                                <option value="gudang">Opname Gudang</option>
                                <option value="outlet">Opname Outlet</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="gudang_section" style="display: none;">
                            <label for="gudang">Gudang <i class="text-danger">*</i></label>
                            <select name="id_gudang" id="id_gudang" class="form-control rounded-0">
                                <option value="">- Pilih Gudang -</option>
                                <?php foreach ($gudang as $gd): ?>
                                    <option value="<?= $gd->id ?>">
                                        <?= $gd->gudang ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group" id="outlet_section" style="display: none;">
                            <label for="outlet">Outlet <i class="text-danger">*</i></label>
                            <select name="id_outlet" id="id_outlet" class="form-control rounded-0">
                                <option value="">- Pilih Outlet -</option>
                                <?php foreach ($outlet as $ot): ?>
                                    <option value="<?= $ot->id ?>">
                                        <?= $ot->nama ?>
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
                                'placeholder' => 'Inputkan keterangan opname...'
                            ]) ?>
                        </div>
                        
                        <div class="text-right">
                            <a href="<?= base_url('gudang/opname') ?>" class="btn btn-warning btn-flat">
                                <i class="fa fa-undo"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-flat">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                        <?= form_close() ?>
                    </div>
                    <div class="col-md-8">
                        <!-- Additional content can be added here -->
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
    
    // Handle opname type selection
    $('#opname_type').on('change', function() {
        const selectedType = $(this).val();
        
        // Hide all sections first
        $('#gudang_section, #outlet_section').hide();
        
        // Clear previous selections
        $('#id_gudang, #id_outlet').val('').removeAttr('required');
        
        // Show relevant section based on selection
        if (selectedType === 'gudang') {
            $('#gudang_section').fadeIn(300);
            $('#id_gudang').attr('required', true);
        } else if (selectedType === 'outlet') {
            $('#outlet_section').fadeIn(300);
            $('#id_outlet').attr('required', true);
        }
    });
    
    // Form validation
    $('#opname_form').on('submit', function(e) {
        const opnameType = $('#opname_type').val();
        
        if (!opnameType) {
            e.preventDefault();
            alert('Silakan pilih tipe opname terlebih dahulu!');
            $('#opname_type').focus();
            return false;
        }
        
        if (opnameType === 'gudang' && !$('#id_gudang').val()) {
            e.preventDefault();
            alert('Silakan pilih gudang!');
            $('#id_gudang').focus();
            return false;
        }
        
        if (opnameType === 'outlet' && !$('#id_outlet').val()) {
            e.preventDefault();
            alert('Silakan pilih outlet!');
            $('#id_outlet').focus();
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?> 