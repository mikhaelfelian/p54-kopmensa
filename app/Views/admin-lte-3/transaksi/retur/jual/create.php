<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Create view for Sales Return transactions
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
                    <i class="fas fa-plus"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('transaksi/retur/jual') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <?= form_open('transaksi/retur/jual/store', ['id' => 'form-retur']) ?>
            <div class="card-body">
                <!-- Alert Messages -->
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Hidden Fields -->
                <input type="hidden" name="retur_type" value="<?= $retur_type ?>">
                <input type="hidden" name="id_gudang" value="1">
                
                <!-- Return Header Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_penjualan">Pilih Transaksi Penjualan <span class="text-danger">*</span></label>
                            <select class="form-control rounded-0 select2" name="id_penjualan" id="id_penjualan" required>
                                <option value="">-- Pilih Transaksi --</option>
                                <?php foreach ($sales_transactions as $sale): ?>
                                    <option value="<?= $sale->id ?>" 
                                            data-customer="<?= $sale->id_pelanggan ?>"
                                            data-total="<?= $sale->jml_gtotal ?>"
                                            data-nota="<?= $sale->no_nota ?>">
                                        <?= $sale->no_nota ?> - <?= $sale->customer_nama ?> - <?= format_angka_rp($sale->jml_gtotal) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tgl_retur">Tanggal Retur <span class="text-danger">*</span></label>
                            <input type="date" class="form-control rounded-0" 
                                   name="tgl_retur" id="tgl_retur" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_pelanggan">Pelanggan <span class="text-danger">*</span></label>
                            <select class="form-control rounded-0 select2" name="id_pelanggan" id="id_pelanggan" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer->id ?>"><?= esc($customer->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea class="form-control rounded-0" name="catatan" id="catatan" rows="2" 
                                      placeholder="Catatan retur (opsional)"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Return Type Information -->
                <div class="row">
                    <div class="col-12">
                        <div class="alert <?= $retur_type === 'refund' ? 'alert-info' : 'alert-success' ?>">
                            <h5>
                                <?php if ($retur_type === 'refund'): ?>
                                    <i class="fas fa-money-bill-wave"></i> Retur dengan Refund
                                <?php else: ?>
                                    <i class="fas fa-exchange-alt"></i> Retur dengan Tukar Barang
                                <?php endif; ?>
                            </h5>
                            <p class="mb-0">
                                <?php if ($retur_type === 'refund'): ?>
                                    Pelanggan akan menerima pengembalian uang untuk produk yang diretur.
                                <?php else: ?>
                                    Pelanggan akan menukar produk yang diretur dengan produk lain.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Return Items Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-list"></i> Item yang Diretur
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="sales-items-container" style="display: none;">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="sales-items-table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">
                                                        <input type="checkbox" id="select-all-items">
                                                    </th>
                                                    <th>Produk</th>
                                                    <th width="10%">Qty Jual</th>
                                                    <th width="12%">Harga</th>
                                                    <th width="10%">Qty Retur</th>
                                                    <th width="12%">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sales-items-body">
                                                <!-- Items will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="no-sales-selected" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <p>Pilih transaksi penjualan terlebih dahulu untuk melihat item yang bisa diretur</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exchange Items Section (only for exchange type) -->
                <?php if ($retur_type === 'exchange'): ?>
                <div class="row" id="exchange-section" style="display: none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-plus"></i> Item Pengganti
                                </h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-exchange-item">
                                        <i class="fas fa-plus"></i> Tambah Item
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="exchange-items-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Produk</th>
                                                <th width="10%">Qty</th>
                                                <th width="12%">Harga</th>
                                                <th width="12%">Subtotal</th>
                                                <th width="8%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="exchange-items-body">
                                            <!-- Exchange items will be added here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Summary Section -->
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Ringkasan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-6">Total Retur:</div>
                                    <div class="col-6 text-right">
                                        <strong>Rp <span id="total-retur">0</span></strong>
                                    </div>
                                </div>
                                
                                <?php if ($retur_type === 'exchange'): ?>
                                <div class="row mb-2">
                                    <div class="col-6">Total Tukar:</div>
                                    <div class="col-6 text-right">
                                        <strong>Rp <span id="total-exchange">0</span></strong>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <strong>
                                            <span id="balance-label">Selisih:</span>
                                        </strong>
                                    </div>
                                    <div class="col-6 text-right">
                                        <strong>
                                            <span id="balance-amount" class="text-success">Rp 0</span>
                                        </strong>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Hidden fields for totals - handled in JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status_retur">Status Retur</label>
                            <select class="form-control rounded-0" name="status_retur" id="status_retur">
                                <option value="0">Draft</option>
                                <option value="1">Selesai</option>
                            </select>
                            <small class="text-muted">Pilih "Selesai" jika retur langsung diproses</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-6">
                        <a href="<?= base_url('transaksi/retur/jual') ?>" class="btn btn-secondary rounded-0">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                    <div class="col-6 text-right">
                        <button type="submit" class="btn btn-primary rounded-0" id="submit-btn">
                            <i class="fas fa-save"></i> Simpan Retur
                        </button>
                    </div>
                </div>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Item Selection Modal (for exchange) -->
<?php if ($retur_type === 'exchange'): ?>
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Item Pengganti</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control" id="search-items" placeholder="Cari produk...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="available-items-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Produk</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="available-items-body">
                            <!-- Available items will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
.select2-container .select2-selection--single {
    height: 38px !important;
    border-radius: 0 !important;
}

.table th, .table td {
    vertical-align: middle;
}

.exchange-item-row {
    background-color: #f8f9fa;
}

#balance-amount.text-danger {
    color: #dc3545 !important;
}

#balance-amount.text-success {
    color: #28a745 !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
const returType = '<?= $retur_type ?>';
let exchangeItemCounter = 0;
let availableItems = [];

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });
    
    // Load available items for exchange
    <?php if ($retur_type === 'exchange'): ?>
    loadAvailableItems();
    <?php endif; ?>
    
    // Event handlers
    $('#id_penjualan').on('change', function() {
        const salesId = $(this).val();
        const customerId = $(this).find(':selected').data('customer');
        
        if (salesId) {
            $('#id_pelanggan').val(customerId).trigger('change');
            loadSalesItems(salesId);
        } else {
            clearSalesItems();
        }
    });
    
    // Select all items checkbox
    $('#select-all-items').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.item-checkbox').prop('checked', isChecked);
        $('.item-checkbox').trigger('change');
    });
    
    // Item quantity change
    $(document).on('change', '.retur-qty', function() {
        const row = $(this).closest('tr');
        const maxQty = parseInt($(this).attr('max'));
        let qty = parseInt($(this).val()) || 0;
        
        if (qty > maxQty) {
            qty = maxQty;
            $(this).val(qty);
            toastr.warning('Quantity retur tidak boleh lebih dari quantity penjualan');
        }
        
        const harga = parseFloat(row.find('.item-price').val()) || 0;
        const subtotal = qty * harga;
        
        row.find('.retur-subtotal').text(formatCurrency(subtotal));
        
        calculateTotals();
    });
    
    // Exchange item handlers
    <?php if ($retur_type === 'exchange'): ?>
    $('#add-exchange-item').on('click', function() {
        $('#itemModal').modal('show');
    });
    
    $('#search-items').on('input', function() {
        const search = $(this).val().toLowerCase();
        filterAvailableItems(search);
    });
    
    $(document).on('change', '.exchange-qty, .exchange-price', function() {
        const row = $(this).closest('tr');
        const qty = parseInt(row.find('.exchange-qty').val()) || 0;
        const price = parseFloat(row.find('.exchange-price').val()) || 0;
        const subtotal = qty * price;
        
        row.find('.exchange-subtotal').text(formatCurrency(subtotal));
        calculateTotals();
    });
    
    $(document).on('click', '.remove-exchange-item', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });
    <?php endif; ?>
    
    // Form submission
    $('#form-retur').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return false;
        }
        
        // Collect return items
        const returItems = [];
        $('.item-checkbox:checked').each(function() {
            const row = $(this).closest('tr');
            const qty = parseInt(row.find('.retur-qty').val()) || 0;
            
            if (qty > 0) {
                returItems.push({
                    id_item: row.find('.item-id').val(),
                    id_satuan: row.find('.item-satuan').val(),
                    kode: row.find('.item-code').val(),
                    produk: row.find('.item-name').val(),
                    qty: qty,
                    satuan: row.find('.item-satuan-name').val(),
                    harga: parseFloat(row.find('.item-price').val()) || 0,
                    subtotal: qty * (parseFloat(row.find('.item-price').val()) || 0)
                });
            }
        });
        
        // Add return items to form
        returItems.forEach((item, index) => {
            Object.keys(item).forEach(key => {
                $('<input>').attr({
                    type: 'hidden',
                    name: `retur_items[${index}][${key}]`,
                    value: item[key]
                }).appendTo(this);
            });
        });
        
        <?php if ($retur_type === 'exchange'): ?>
        // Collect exchange items
        const exchangeItems = [];
        $('#exchange-items-body tr').each(function() {
            const row = $(this);
            const qty = parseInt(row.find('.exchange-qty').val()) || 0;
            
            if (qty > 0) {
                exchangeItems.push({
                    id_item: row.find('.exchange-item-id').val(),
                    id_satuan: row.find('.exchange-satuan').val(),
                    kode: row.find('.exchange-code').val(),
                    produk: row.find('.exchange-name').val(),
                    qty: qty,
                    satuan: row.find('.exchange-satuan-name').val(),
                    harga: parseFloat(row.find('.exchange-price').val()) || 0,
                    subtotal: qty * (parseFloat(row.find('.exchange-price').val()) || 0)
                });
            }
        });
        
        // Add exchange items to form
        exchangeItems.forEach((item, index) => {
            Object.keys(item).forEach(key => {
                $('<input>').attr({
                    type: 'hidden',
                    name: `exchange_items[${index}][${key}]`,
                    value: item[key]
                }).appendTo(this);
            });
        });
        <?php endif; ?>
        
        // Submit form
        this.submit();
    });
});

function loadSalesItems(salesId) {
    $.get(`<?= base_url('transaksi/retur/jual/sales-items/') ?>${salesId}`, function(response) {
        if (response.success) {
            displaySalesItems(response.items);
            $('#sales-items-container').show();
            $('#no-sales-selected').hide();
            
            <?php if ($retur_type === 'exchange'): ?>
            $('#exchange-section').show();
            <?php endif; ?>
        }
    });
}

function displaySalesItems(items) {
    let html = '';
    
    items.forEach(item => {
        html += `
            <tr>
                <td>
                    <input type="checkbox" class="item-checkbox">
                    <input type="hidden" class="item-id" value="${item.id_item}">
                    <input type="hidden" class="item-satuan" value="${item.id_satuan || ''}">
                    <input type="hidden" class="item-code" value="${item.kode}">
                    <input type="hidden" class="item-name" value="${item.produk}">
                    <input type="hidden" class="item-satuan-name" value="${item.satuan || ''}">
                    <input type="hidden" class="item-price" value="${item.harga}">
                </td>
                <td>
                    <strong>${item.produk}</strong><br>
                    <small class="text-muted">${item.kode}</small>
                </td>
                <td>${item.jml_satuan}</td>
                <td>Rp ${formatCurrency(item.harga)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm retur-qty" 
                           min="0" max="${item.jml_satuan}" value="0">
                </td>
                <td class="retur-subtotal">Rp 0</td>
            </tr>
        `;
    });
    
    $('#sales-items-body').html(html);
}

function clearSalesItems() {
    $('#sales-items-container').hide();
    $('#no-sales-selected').show();
    $('#exchange-section').hide();
    $('#sales-items-body').empty();
}

<?php if ($retur_type === 'exchange'): ?>
function loadAvailableItems() {
    $.post('<?= base_url('transaksi/retur/jual/search-items') ?>', {search: ''}, function(response) {
        if (response.success) {
            availableItems = response.items;
            displayAvailableItems(availableItems);
            console.log('Loaded items:', response.items.length, 'items');
        } else {
            console.error('Failed to load items:', response);
            alert('Gagal memuat data produk: ' + (response.error || 'Unknown error'));
        }
    }).fail(function(xhr, status, error) {
        console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
        alert('Error: ' + error + ' (Status: ' + xhr.status + ')');
    });
}

function displayAvailableItems(items) {
    let html = '';
    
    items.forEach(item => {
        html += `
            <tr>
                <td>${item.kode}</td>
                <td>${item.nama}</td>
                <td>${item.stok || 0}</td>
                <td>Rp ${formatCurrency(item.harga_jual)}</td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm" 
                            onclick="selectExchangeItem(${item.id}, '${item.kode}', '${item.nama.replace(/'/g, "\\'")}', ${item.harga_jual}, '${item.satuan || ''}', ${item.id_satuan || 0})">
                        Pilih
                    </button>
                </td>
            </tr>
        `;
    });
    
    $('#available-items-body').html(html);
}

function filterAvailableItems(search) {
    const filtered = availableItems.filter(item => 
        item.kode.toLowerCase().includes(search) || 
        item.nama.toLowerCase().includes(search)
    );
    displayAvailableItems(filtered);
}

function selectExchangeItem(id, kode, nama, harga, satuan, idSatuan) {
    exchangeItemCounter++;
    
    const html = `
        <tr class="exchange-item-row">
            <td>
                <strong>${nama}</strong><br>
                <small class="text-muted">${kode}</small>
                <input type="hidden" class="exchange-item-id" value="${id}">
                <input type="hidden" class="exchange-code" value="${kode}">
                <input type="hidden" class="exchange-name" value="${nama}">
                <input type="hidden" class="exchange-satuan" value="${idSatuan}">
                <input type="hidden" class="exchange-satuan-name" value="${satuan}">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm exchange-qty" 
                       min="1" value="1">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm exchange-price" 
                       value="${harga}" step="0.01">
            </td>
            <td class="exchange-subtotal">Rp ${formatCurrency(harga)}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-exchange-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#exchange-items-body').append(html);
    $('#itemModal').modal('hide');
    calculateTotals();
}
<?php endif; ?>

function calculateTotals() {
    let totalRetur = 0;
    let totalExchange = 0;
    
    // Calculate return total
    $('.item-checkbox:checked').each(function() {
        const row = $(this).closest('tr');
        const qty = parseInt(row.find('.retur-qty').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        totalRetur += qty * price;
    });
    
    <?php if ($retur_type === 'exchange'): ?>
    // Calculate exchange total
    $('#exchange-items-body tr').each(function() {
        const row = $(this);
        const qty = parseInt(row.find('.exchange-qty').val()) || 0;
        const price = parseFloat(row.find('.exchange-price').val()) || 0;
        totalExchange += qty * price;
    });
    
    // Calculate balance
    const balance = totalRetur - totalExchange;
    $('#total-exchange').text(formatCurrency(totalExchange));
    
    if (balance > 0) {
        $('#balance-label').text('Kembali:');
        $('#balance-amount').removeClass('text-danger').addClass('text-success').text('Rp ' + formatCurrency(balance));
    } else if (balance < 0) {
        $('#balance-label').text('Kurang:');
        $('#balance-amount').removeClass('text-success').addClass('text-danger').text('Rp ' + formatCurrency(Math.abs(balance)));
    } else {
        $('#balance-label').text('Selisih:');
        $('#balance-amount').removeClass('text-danger text-success').text('Rp 0');
    }
    
    // Store totals in JavaScript variables for form submission
    <?php else: ?>
    // Store total for refund type
    <?php endif; ?>
    
    $('#total-retur').text(formatCurrency(totalRetur));
}

function validateForm() {
    if (!$('#id_penjualan').val()) {
        toastr.error('Pilih transaksi penjualan terlebih dahulu');
        return false;
    }
    
    if (!$('#id_pelanggan').val()) {
        toastr.error('Pilih pelanggan terlebih dahulu');
        return false;
    }
    
    if ($('.item-checkbox:checked').length === 0) {
        toastr.error('Pilih minimal satu item untuk diretur');
        return false;
    }
    
    let hasValidQty = false;
    $('.item-checkbox:checked').each(function() {
        const row = $(this).closest('tr');
        const qty = parseInt(row.find('.retur-qty').val()) || 0;
        if (qty > 0) {
            hasValidQty = true;
        }
    });
    
    if (!hasValidQty) {
        toastr.error('Masukkan quantity yang valid untuk item yang dipilih');
        return false;
    }
    
    return true;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(Math.round(amount || 0));
}

// Auto hide alerts
$(document).ready(function() {
    $('.alert').delay(5000).fadeOut();
});
</script>
<?= $this->endSection() ?> 