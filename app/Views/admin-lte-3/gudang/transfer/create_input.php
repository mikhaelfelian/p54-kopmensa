<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-14
 * Github : github.com/mikhaelfelian
 * description : View for inputting items to transfer data.
 * This file represents the transfer create input view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <div class="card card-default rounded-0">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-box-open"></i> Form Input Stok</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label for="kode" class="col-sm-3 col-form-label">Kode <i class="text-danger">*</i></label>
                    <div class="col-sm-9">
                        <input type="text" name="kode" value="" id="kode" class="form-control pull-right rounded-0" placeholder="Inputkan Kode / Nama Item ...">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="item" class="col-sm-3 col-form-label">Item</label>
                    <div class="col-sm-9">
                        <input type="text" name="item" value="" id="item" class="form-control pull-right rounded-0" placeholder="Inputkan Nama Item ..." readonly="readonly">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="stok" class="col-sm-3 col-form-label"><i><small>Stok Gudang</small></i></label>
                    <div class="col-sm-2">
                        <input type="text" name="stok" value="" id="stok" class="form-control pull-right text-center rounded-0" disabled="disabled">
                    </div>
                    <div class="col-sm-4">
                        <input type="text" name="st" value="" id="st" class="form-control pull-right text-left rounded-0" disabled="disabled">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="jml" class="col-sm-3 col-form-label">Jml</label>
                    <div class="col-sm-2">
                        <input type="number" name="jml" value="1" id="jml" class="form-control pull-right text-center rounded-0" placeholder="Jml ...">
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
                        <button type="button" class="btn btn-info btn-flat" id="btn_test_toastr">
                            <i class="fa fa-bell"></i> Test Toastr
                        </button>
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
    
    <div class="col-md-6">
        <div class="card card-default rounded-0">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Transfer</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">No. Transfer</label>
                    <div class="col-sm-8">
                        <input type="text" value="<?= $transfer->no_nota ?>" class="form-control rounded-0" readonly>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Tanggal</label>
                    <div class="col-sm-8">
                        <input type="text" value="<?= date('d/m/Y', strtotime($transfer->tgl_masuk)) ?>" class="form-control rounded-0" readonly>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Tipe</label>
                    <div class="col-sm-8">
                        <input type="text" value="<?= $transfer->tipe == '1' ? 'Transfer/Mutasi' : ($transfer->tipe == '2' ? 'Stok Masuk' : 'Stok Keluar') ?>" class="form-control rounded-0" readonly>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Status</label>
                    <div class="col-sm-8">
                        <input type="text" value="<?= $transfer->status_nota == '0' ? 'Draft' : 'Selesai' ?>" class="form-control rounded-0" readonly>
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
                <h3 class="card-title"><i class="fas fa-boxes-stacked"></i> Data Item Transfer</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table_items">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-left">Item</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Satuan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center">Tidak Ada Data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-danger btn-flat">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
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
    // Toastr configuration
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: false,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
    };

    // Flash messages
    <?php if (session()->getFlashdata('success')): ?>
        toastr.success('<?= session()->getFlashdata('success') ?>');
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        toastr.error('<?= session()->getFlashdata('error') ?>');
    <?php endif; ?>

    <?php if (session()->getFlashdata('warning')): ?>
        toastr.warning('<?= session()->getFlashdata('warning') ?>');
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
        toastr.info('<?= session()->getFlashdata('info') ?>');
    <?php endif; ?>

    // Test toastr on page load
    toastr.info('Input item page loaded successfully!');

    // Test toastr button
    $('#btn_test_toastr').on('click', function() {
        toastr.success('Toastr is working!');
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