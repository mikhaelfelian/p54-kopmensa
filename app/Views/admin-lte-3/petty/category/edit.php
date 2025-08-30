<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-28
 * Github: github.com/mikhaelfelian
 * Description: Edit view for Petty Cash Categories
 * This file represents the View.
 */

helper('form');
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('transaksi/petty/category') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
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

                <?php if (session()->getFlashdata('errors')) : ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                        <ul>
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('transaksi/petty/category/update/' . $category->id) ?>" method="post" id="editCategoryForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode">Kode Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-0" id="kode" name="kode" 
                                       value="<?= old('kode', $category->kode) ?>" 
                                       placeholder="Contoh: MKN, MNM, DLL" required maxlength="10">
                                <small class="form-text text-muted">Kode unik untuk kategori (2-10 karakter)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-0" id="nama" name="nama" 
                                       value="<?= old('nama', $category->nama) ?>" 
                                       placeholder="Contoh: Makanan, Minuman, Lain-lain" required maxlength="100">
                                <small class="form-text text-muted">Nama lengkap kategori (3-100 karakter)</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control rounded-0" id="deskripsi" name="deskripsi" rows="3" 
                                  placeholder="Deskripsi singkat tentang kategori ini (opsional)" maxlength="255"><?= old('deskripsi', $category->deskripsi) ?></textarea>
                        <small class="form-text text-muted">Deskripsi opsional untuk kategori</small>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control rounded-0" id="status" name="status">
                            <option value="1" <?= old('status', $category->is_active) == '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= old('status', $category->is_active) == '0' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                        <small class="form-text text-muted">Status aktifitas kategori</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="fas fa-save"></i> Update Kategori
                        </button>
                        <a href="<?= base_url('transaksi/petty/category') ?>" class="btn btn-secondary rounded-0">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Auto-uppercase for kode field
    $('#kode').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Form validation
    $('#editCategoryForm').on('submit', function(e) {
        var kode = $('#kode').val().trim();
        var nama = $('#nama').val().trim();
        
        if (kode.length < 2) {
            e.preventDefault();
            alert('Kode kategori minimal 2 karakter!');
            $('#kode').focus();
            return false;
        }
        
        if (kode.length > 10) {
            e.preventDefault();
            alert('Kode kategori maksimal 10 karakter!');
            $('#kode').focus();
            return false;
        }
        
        if (nama.length < 3) {
            e.preventDefault();
            alert('Nama kategori minimal 3 karakter!');
            $('#nama').focus();
            return false;
        }
        
        if (nama.length > 100) {
            e.preventDefault();
            alert('Nama kategori maksimal 100 karakter!');
            $('#nama').focus();
            return false;
        }
        
        // Confirm update
        if (!confirm('Apakah Anda yakin ingin mengupdate kategori ini?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>
