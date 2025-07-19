<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : View for inputting items to transfer/mutasi.
 * This file represents the transfer input view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<!-- CSRF Token -->
<?= csrf_field() ?>

<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Input Item Transfer</h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-sm btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="120"><strong>No. Nota</strong></td>
                                <td>: <?= $transfer->no_nota ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Gudang Asal</strong></td>
                                <td>: <?= $transfer->gudang_asal_name ?></td>
                            </tr>
                            <tr>
                                <td><strong>Gudang Tujuan</strong></td>
                                <td>: <?= $transfer->gudang_tujuan_name ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="120"><strong>Tipe</strong></td>
                                <td>: 
                                    <?php
                                    $tipeLabels = [
                                        '1' => 'Pindah Gudang',
                                        '2' => 'Stok Masuk',
                                        '3' => 'Stok Keluar'
                                    ];
                                    $tipeColors = [
                                        '1' => 'info',
                                        '2' => 'success',
                                        '3' => 'warning'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $tipeColors[$transfer->tipe] ?? 'secondary' ?>">
                                        <?= $tipeLabels[$transfer->tipe] ?? 'Unknown' ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php
                                    $statusNotaLabels = [
                                        '0' => 'Draft',
                                        '1' => 'Pending',
                                        '2' => 'Diproses',
                                        '3' => 'Selesai'
                                    ];
                                    $statusNotaColors = [
                                        '0' => 'secondary',
                                        '1' => 'warning',
                                        '2' => 'info',
                                        '3' => 'success'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $statusNotaColors[$transfer->status_nota] ?? 'secondary' ?>">
                                        <?= $statusNotaLabels[$transfer->status_nota] ?? 'Unknown' ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if ($transfer->status_nota == '3'): ?>
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
                        <p>Transfer ini sudah selesai dan tidak dapat ditambahkan item lagi.</p>
                    </div>
                <?php else: ?>
                    <?= form_open(base_url("gudang/transfer/process/{$transfer->id}"), ['id' => 'transferItemForm']) ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="mb-3">Produk</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="productTable">
                                        <thead>
                                            <tr>
                                                <th width="50" class="text-center">#</th>
                                                <th>Produk</th>
                                                <th width="100">Qty</th>
                                                <th width="120">Stok Tersedia</th>
                                                <th>Keterangan</th>
                                                <th width="80" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="product-row" data-row="1">
                                                <td class="text-center">1</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control product-select" placeholder="Pilih produk..." readonly>
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm product-search" data-row="1">
                                                                <i class="fas fa-search"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm product-clear" data-row="1">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="items[]" class="product-id">
                                                </td>
                                                <td>
                                                    <input type="number" name="quantities[]" class="form-control qty-input" value="1" min="1" step="1">
                                                </td>
                                                <td>
                                                    <span class="stock-display">-</span>
                                                    <input type="hidden" name="stock[]" class="stock-input">
                                                </td>
                                                <td>
                                                    <input type="text" name="notes[]" class="form-control deskripsi-input" placeholder="Keterangan...">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" id="addRow">
                                            <i class="fas fa-plus"></i> Tambah Baris
                                        </button>
                                        <button type="button" class="btn btn-danger" id="removeAllRows">
                                            <i class="fas fa-times"></i> Hapus Semua Baris
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-success rounded-0" id="btnProcess">
                                    <i class="fas fa-check"></i> Proses Transfer
                                </button>
                                <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-secondary rounded-0">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </div>
                    <?= form_close() ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Product Search Modal -->
<div class="modal fade" id="productSearchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Produk</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control" id="productSearchInput" placeholder="Cari produk...">
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="productSearchTable">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Deskripsi</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Product search results will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let rowCounter = 1;
    let currentRow = null;
    
    // Add new row
    $('#addRow').on('click', function() {
        rowCounter++;
        const newRow = `
            <tr class="product-row" data-row="${rowCounter}">
                <td class="text-center">${rowCounter}</td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control product-select" placeholder="Pilih produk..." readonly>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary btn-sm product-search" data-row="${rowCounter}">
                                <i class="fas fa-search"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm product-clear" data-row="${rowCounter}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="items[]" class="product-id">
                </td>
                <td>
                    <input type="number" name="quantities[]" class="form-control qty-input" value="1" min="1" step="1">
                </td>
                <td>
                    <span class="stock-display">-</span>
                    <input type="hidden" name="stock[]" class="stock-input">
                </td>
                <td>
                    <input type="text" name="notes[]" class="form-control deskripsi-input" placeholder="Keterangan...">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#productTable tbody').append(newRow);
        updateRowNumbers();
    });
    
    // Remove row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        updateRowNumbers();
    });
    
    // Remove all rows
    $('#removeAllRows').on('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus semua baris?')) {
            $('#productTable tbody').empty();
            rowCounter = 0;
            $('#addRow').click(); // Add one empty row
        }
    });
    
    // Product search
    $(document).on('click', '.product-search', function() {
        currentRow = $(this).data('row');
        $('#productSearchModal').modal('show');
        loadProducts();
    });
    
    // Product clear
    $(document).on('click', '.product-clear', function() {
        const row = $(this).data('row');
        $(`.product-row[data-row="${row}"] .product-select`).val('');
        $(`.product-row[data-row="${row}"] .product-id`).val('');
        $(`.product-row[data-row="${row}"] .stock-display`).text('-');
        $(`.product-row[data-row="${row}"] .stock-input`).val('');
    });
    
    // Load products for search
    function loadProducts(search = '') {
        $.ajax({
            url: '<?= base_url('publik/items') ?>',
            method: 'GET',
            data: { term: search },
            success: function(response) {
                let html = '';
                if (response && response.length > 0) {
                    response.forEach(function(item) {
                        html += `
                            <tr>
                                <td>${item.kode || '-'}</td>
                                <td>${item.item}</td>
                                <td>${item.deskripsi || '-'}</td>
                                <td>${item.jml_min || 0}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary select-product" 
                                            data-id="${item.id}" 
                                            data-nama="${item.item}" 
                                            data-stok="${item.jml_min || 0}">
                                        Pilih
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center">Tidak ada produk ditemukan</td></tr>';
                }
                $('#productSearchTable tbody').html(html);
            },
            error: function() {
                $('#productSearchTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat data produk</td></tr>');
            }
        });
    }
    
    // Select product from search
    $(document).on('click', '.select-product', function() {
        const productId = $(this).data('id');
        const productNama = $(this).data('nama');
        const productStok = $(this).data('stok');
        
        $(`.product-row[data-row="${currentRow}"] .product-select`).val(productNama);
        $(`.product-row[data-row="${currentRow}"] .product-id`).val(productId);
        $(`.product-row[data-row="${currentRow}"] .stock-display`).text(productStok);
        $(`.product-row[data-row="${currentRow}"] .stock-input`).val(productStok);
        
        $('#productSearchModal').modal('hide');
    });
    
    // Product search input
    $('#productSearchInput').on('input', function() {
        loadProducts($(this).val());
    });
    

    
    // Input change events
    $(document).on('input', '.qty-input', function() {
        const row = $(this).closest('tr').data('row');
        
        // Validate quantity against stock
        const qty = parseFloat($(this).val()) || 0;
        const stock = parseFloat($(`.product-row[data-row="${row}"] .stock-input`).val()) || 0;
        
        if (qty > stock) {
            alert('Jumlah transfer tidak boleh melebihi stok tersedia!');
            $(this).val(stock);
        }
    });
    
    // Update row numbers
    function updateRowNumbers() {
        $('#productTable tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
            $(this).attr('data-row', index + 1);
            $(this).find('.product-search, .product-clear').attr('data-row', index + 1);
        });
        rowCounter = $('#productTable tbody tr').length;
    }
    
    // Format number
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    // Form submission
    $('#transferItemForm').on('submit', function(e) {
        let hasProducts = false;
        $('.product-id').each(function() {
            if ($(this).val()) {
                hasProducts = true;
                return false;
            }
        });
        
        if (!hasProducts) {
            e.preventDefault();
            alert('Pilih minimal satu produk!');
            return false;
        }
        
        if (!confirm('Apakah Anda yakin ingin memproses transfer ini?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?> 