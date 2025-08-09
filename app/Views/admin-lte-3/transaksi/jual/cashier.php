<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Cashier Interface for Sales Transactions
 * This file represents the View.
 */

helper('form');
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>

<div class="row">
    <!-- Left Column - Product Selection and Cart -->
    <div class="col-md-8">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shopping-cart"></i> Kasir - Transaksi Penjualan
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-secondary btn-sm" id="newTransaction">
                        <i class="fas fa-plus"></i> Transaksi Baru
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Product Search -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" id="productSearch" placeholder="Scan barcode atau ketik nama produk...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="searchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control rounded-0" id="outletSelect">
                            <option value="">Pilih Outlet</option>
                            <?php foreach ($outlets as $outlet): ?>
                                <option value="<?= $outlet->id ?>"><?= esc($outlet->nama) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Product List -->
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-hover" id="productListTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Kode</th>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Products will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Cart -->
                <div class="mt-3">
                    <h5>Keranjang Belanja</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="cartTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Produk</th>
                                    <th width="80">Qty</th>
                                    <th width="120">Harga</th>
                                    <th width="120">Total</th>
                                    <th width="80">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody">
                                <!-- Cart items will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Payment -->
    <div class="col-md-4">
        <div class="card rounded-0 mb-3">
            <div class="card-header bg-default">
                <h5 class="card-title mb-0">5 Transaksi Terakhir</h5>
            </div>
            <div class="card-body p-2">
                <ul class="list-group list-group-flush" id="lastTransactionsList" style="max-height: 120px; overflow-y: auto;">
                    <?php foreach ($lastTransactions as $transaction): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="font-weight-bold"><?= $transaction->no_nota ?></span>
                                <br>
                                <small class="text-muted"><?= $transaction->customer_name ?></small>
                                <br/>
                                <small class="text-muted"><?= tgl_indo6($transaction->created_at) ?></small>
                            </div>
                            <div class="d-flex flex-column align-items-end">
                                <span class="badge badge-success badge-pill mb-1">Rp <?= number_format($transaction->jml_gtotal, 0, ',', '.') ?></span>
                                <button type="button" class="btn btn-sm btn-info" onclick="viewTransaction(<?= $transaction->id ?>)" title="Lihat Detail">
                                    <i class="fa fa-eye"></i> View
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="card rounded-0">
            <div class="card-header">
                <h4 class="card-title">Pembayaran</h4>
            </div>
            <div class="card-body">
                <!-- Customer Selection -->
                <div class="form-group">
                    <label for="customerSelect">Pelanggan</label>
                    <select class="form-control rounded-0 select2" id="customerSelect">
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer->id ?>"><?= esc($customer->nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Payment Summary -->
                <div class="border rounded p-3 mb-3">
                    <div class="row mb-2">
                        <div class="col-6">Subtotal:</div>
                        <div class="col-6 text-right">
                            <span id="subtotalDisplay">Rp 0</span>
                        </div>
                    </div>
                    
                        <div class="row mb-2">
                            <div class="col-6">Diskon:</div>
                            <div class="col-6">
                                <?= form_input([
                                    'type'        => 'number',
                                    'class'       => 'form-control form-control-sm rounded-0',
                                    'id'          => 'discountPercent',
                                    'placeholder' => '%',
                                    'step'        => '0.01'
                                ]); ?>
                            </div>
                        </div>
                    
                    <div class="row mb-2">
                        <div class="col-6">Voucher:</div>
                        <div class="col-6">
                            <?= form_input([
                                'type'        => 'text',
                                'class'       => 'form-control form-control-sm rounded-0',
                                'id'          => 'voucherCode',
                                'placeholder' => 'Kode voucher'
                            ]); ?>
                            <small class="text-muted" id="voucherInfo"></small>
                            <input type="hidden" id="voucherDiscount" name="voucherDiscount" value="0">
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6">PPN (11%):</div>
                        <div class="col-6 text-right">
                            <span id="taxDisplay">Rp 0</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-6"><strong>Total:</strong></div>
                        <div class="col-6 text-right">
                            <strong><span id="grandTotalDisplay">Rp 0</span></strong>
                        </div>
                    </div>
                </div>

                <!-- Multiple Payment Methods -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Pembayaran</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm rounded-0" id="addPaymentMethod">
                                <i class="fas fa-plus"></i> Tambah Metode
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <div id="paymentMethods">
                            <!-- Payment methods will be added here -->
                        </div>
                        
                        <!-- Payment Summary -->
                        <div class="row mt-3 p-2 bg-light">
                            <div class="col-6">
                                <strong>Total Tagihan:</strong><br>
                                <span id="grandTotalPayment">Rp 0</span>
                            </div>
                            <div class="col-6">
                                <strong>Total Bayar:</strong><br>
                                <span id="totalPaidAmount">Rp 0</span>
                            </div>
                        </div>
                        
                        <div class="row mt-2 p-2" id="remainingPayment" style="background-color: #ffe6e6;">
                            <div class="col-12">
                                <strong>Sisa Bayar:</strong>
                                <span id="remainingAmount" class="text-danger">Rp 0</span>
                            </div>
                        </div>
                        
                        <div class="row mt-2 p-2" id="changePayment" style="background-color: #e6ffe6; display: none;">
                            <div class="col-12">
                                <strong>Kembalian:</strong>
                                <span id="changeAmount" class="text-success">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="btn btn-success btn-block rounded-0" id="completeTransaction">
                            <i class="fas fa-check"></i> Proses
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-danger btn-block rounded-0" id="cancelTransaction">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaksi Selesai</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Transaksi Berhasil!</h4>
                    <p>Total: <strong id="finalTotal">Rp 0,00</strong></p>
                    <p>Metode Bayar: <strong id="finalPaymentMethod">-</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="printReceipt">
                    <i class="fas fa-print"></i> Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Variant Selection Modal -->
<div class="modal fade" id="variantModal" tabindex="-1" role="dialog" aria-labelledby="variantModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="variantModalLabel">Pilih Varian Produk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="variantList"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
/* Select2 rounded-0 style */
.select2-container .select2-selection--single {
    height: 36px !important; /* Sesuaikan dengan tinggi input */
    display: flex;
    align-items: center; /* Ini akan membuat teks di tengah */
    vertical-align: middle;
    padding-left: 10px;
    border-radius: 0px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: normal !important; /* Pastikan tidak fix ke line-height tinggi */
    padding-left: 0px !important;
    padding-right: 0px !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
// Global variables
let cart = [];
let currentTransactionId = null;
let paymentMethods = [];
let paymentCounter = 0;

$(document).ready(function() {
    // Initialize Select2 for customer dropdown
    $('#customerSelect').select2({
        placeholder: 'Pilih pelanggan...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#customerSelect').parent()
    });
    
    // Initialize
    loadProducts();
    addPaymentMethod(); // Add first payment method by default
    
    // Event listeners
    $('#productSearch').on('input', function() {
        searchProducts($(this).val());
    });
    
    $('#searchBtn').on('click', function() {
        searchProducts($('#productSearch').val());
    });
    
    $('#discountPercent').on('input', calculateTotal);
    $('#voucherCode').on('blur', function() {
        validateVoucher($(this).val());
    });
    
    // Payment method event listeners
    $('#addPaymentMethod').on('click', addPaymentMethod);
    $(document).on('click', '.remove-payment', removePaymentMethod);
    $(document).on('input', '.payment-amount', calculatePaymentTotals);
    $(document).on('change', '.payment-platform', calculatePaymentTotals);
    
    $('#completeTransaction').on('click', completeTransaction);
    $('#newTransaction').on('click', newTransaction);
    $('#holdTransaction').on('click', holdTransaction);
    $('#cancelTransaction').on('click', cancelTransaction);
    $('#printReceipt').on('click', printReceipt);
    
    // Auto clear form when modal is closed
    $('#completeModal').on('hidden.bs.modal', function() {
        clearTransactionForm();
    });
    
    // Enter key to search
    $('#productSearch').on('keypress', function(e) {
        if (e.which === 13) {
            searchProducts($(this).val());
        }
    });
});

// Payment Methods Functions
function addPaymentMethod() {
    paymentCounter++;
    const platforms = <?= json_encode($platforms) ?>;
    
    let platformOptions = '<option value="">Pilih Platform</option>';
    platforms.forEach(platform => {
        platformOptions += `<option value="${platform.id}">${platform.platform}</option>`;
    });
    
    const paymentHtml = `
        <div class="payment-method-row border rounded p-2 mb-2" data-payment-id="${paymentCounter}">
            <div class="row">
                <div class="col-md-4">
                    <label>Metode Bayar</label>
                    <select class="form-control form-control-sm rounded-0 payment-type" name="payments[${paymentCounter}][type]">
                        <option value="">Pilih metode</option>
                        <option value="1">Tunai</option>
                        <option value="2">Non Tunai</option>
                        <option value="3">Piutang QR</option>
                        <option value="4">Piutang TTD</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Platform</label>
                    <select class="form-control form-control-sm rounded-0 payment-platform" name="payments[${paymentCounter}][platform_id]">
                        ${platformOptions}
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Jumlah</label>
                    <input type="number" class="form-control form-control-sm rounded-0 payment-amount" 
                           name="payments[${paymentCounter}][amount]" placeholder="0" step="100" min="0">
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm rounded-0 remove-payment d-block" 
                            data-payment-id="${paymentCounter}" ${paymentCounter === 1 ? 'style="display: none !important;"' : ''}>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2" style="display: none;">
                <div class="col-12">
                    <input type="text" class="form-control form-control-sm rounded-0" 
                           name="payments[${paymentCounter}][reference]" placeholder="No. Referensi (opsional)">
                </div>
            </div>
        </div>
    `;
    
    $('#paymentMethods').append(paymentHtml);
    calculatePaymentTotals();
}

function removePaymentMethod() {
    const paymentId = $(this).data('payment-id');
    $(`.payment-method-row[data-payment-id="${paymentId}"]`).remove();
    calculatePaymentTotals();
}

function calculatePaymentTotals() {
    let totalPaid = 0;
    // Remove dots (thousand separators) before parsing grand total
    const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/\./g, '').replace(/[^\d,-]/g, '').replace(',', '.')) || 0;

    $('.payment-amount').each(function() {
        // Allow user to input with dots as thousand separator, remove them for calculation
        let val = $(this).val();
        if (typeof val === 'string') {
            val = val.replace(/\./g, '').replace(',', '.');
        }
        const amount = parseFloat(val) || 0;
        totalPaid += amount;
    });

    // Update displays with formatted currency (showing dots as thousand separator)
    $('#grandTotalPayment').text(formatCurrency(grandTotal));
    $('#totalPaidAmount').text(formatCurrency(totalPaid));

    const remaining = grandTotal - totalPaid;

    if (remaining > 0) {
        $('#remainingAmount').text(formatCurrency(remaining));
        $('#remainingPayment').show();
        $('#changePayment').hide();
    } else if (remaining < 0) {
        $('#changeAmount').text(formatCurrency(Math.abs(remaining)));
        $('#remainingPayment').hide();
        $('#changePayment').show();
    } else {
        $('#remainingPayment').hide();
        $('#changePayment').hide();
    }
}

function loadProducts() {
    $.ajax({
        url: '<?= base_url('transaksi/jual/search-items') ?>',
        type: 'GET',
        success: function(response) {
            if (response.items) {
                displayProducts(response.items);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading products:', error);
        }
    });
}

function searchProducts(query) {
    if (query.length < 2) {
        loadProducts();
        return;
    }

    $.ajax({
        url: '<?= base_url('transaksi/jual/search-items') ?>',
        type: 'POST',
        data: {
            search: query,
            warehouse_id: $('#warehouseSelect').val()
        },
        success: function(response) {
            if (response.items) {
                displayProducts(response.items);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error searching products:', error);
        }
    });
}

function displayProducts(products) {
    let html = '';
    products.forEach(function(product) {
        const itemName = product.item || product.nama || product.produk || '-';
        const category = product.kategori || '-';
        const brand = product.merk || '-';
        const price = product.harga_jual || product.harga || 0;
        const stock = product.stok || 0;
        const supplier = '[' + product.supplier + ']' || '-';
        
        html += `
            <tr>
                <td>${product.kode || '-'}</td>
                <td>
                    <strong>${itemName}</strong><br>
                    <small class="text-muted">${category} - ${brand}</small><br>
                    ${product.supplier ? `<small class="text-muted">[${product.supplier}]</small>` : ''}
                </td>
                <td>Rp ${numberFormat(price)}</td>
                <td>${stock}</td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm" onclick="checkVariant(${product.id}, '${itemName.replace(/'/g, "\\'")}', '${product.kode}', ${price})">
                        <i class="fas fa-plus"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    $('#productListTable tbody').html(html);
}

// Function to check for variants and handle add to cart
function checkVariant(productId, productName, productCode, price) {
    $.get('<?= base_url('transaksi/jual/get_variants') ?>/' + productId, function(response) {
        if (response.success && response.variants && response.variants.length > 0) {
            // Show modal with variants
            let variantHtml = '<div class="list-group">';
            response.variants.forEach(function(variant) {
                variantHtml += `
                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="selectVariantToCart(${productId}, '${productName.replace(/'/g, "\\'")}', '${productCode}', ${variant.id}, '${variant.nama.replace(/'/g, "\\'")}', ${variant.harga_jual_value || 0})">
                        <span>
                            <strong>${variant.nama}</strong><br>
                            <small>Kode: ${variant.kode}</small>
                        </span>
                        <span class="badge badge-primary badge-pill">Rp ${numberFormat(variant.harga_jual_value || 0)}</span>
                    </button>
                `;
            });
            variantHtml += '</div>';
            $('#variantList').html(variantHtml);
            $('#variantModal').modal('show');
        } else {
            // No variants, add directly
            addToCart(productId, productName, productCode, price);
        }
    }, 'json');
}

// Function to add selected variant to cart
function selectVariantToCart(productId, productName, productCode, variantId, variantName, variantPrice) {
    addToCart(productId + '-' + variantId, productName + ' - ' + variantName, productCode, variantPrice);
    $('#variantModal').modal('hide');
}

function addToCart(productId, productName, productCode, price) {
    // Check if product already in cart
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
        existingItem.total = existingItem.quantity * existingItem.price;
    } else {
        cart.push({
            id: productId,
            name: productName,
            code: productCode,
            price: price,
            quantity: 1,
            total: price
        });
    }
    
    updateCartDisplay();
    calculateTotal();
    $('#productSearch').val('').focus();
}

function updateCartDisplay() {
    let html = '';
    cart.forEach(function(item, index) {
        html += `
            <tr>
                <td>${item.name}</td>
                <td>
                    <div class="d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-1 me-1" style="min-width:32px;" onclick="updateQuantity(${index}, -1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input 
                            type="number" 
                            class="form-control form-control-sm text-center mx-1" 
                            value="${item.quantity}" 
                            min="1" 
                            style="width: 50px; height: 32px; padding: 0 4px; box-shadow: none;"
                            onchange="updateQuantityInput(${index}, this.value)"
                        >
                        <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-1 ms-1" style="min-width:32px;" onclick="updateQuantity(${index}, 1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </td>
                <td class="text-right">Rp ${numberFormat(item.price)}</td>
                <td class="text-right">Rp ${numberFormat(item.total)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    $('#cartTableBody').html(html);
}

function updateQuantity(index, change) {
    cart[index].quantity = Math.max(1, cart[index].quantity + change);
    cart[index].total = cart[index].quantity * cart[index].price;
    updateCartDisplay();
    calculateTotal();
}

function updateQuantityInput(index, value) {
    cart[index].quantity = Math.max(1, parseInt(value) || 1);
    cart[index].total = cart[index].quantity * cart[index].price;
    updateCartDisplay();
    calculateTotal();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
    calculateTotal();
}

function calculateTotal() {
    let subtotal = 0;
    cart.forEach(function(item) {
        subtotal += item.total;
    });
    
    $('#subtotalDisplay').text(`Rp ${numberFormat(subtotal)}`);
    
    // Calculate discount
    const discountPercent = parseFloat($('#discountPercent').val()) || 0;
    const discountAmount = subtotal * (discountPercent / 100);
    const afterDiscount = subtotal - discountAmount;
    
    // Calculate voucher discount
    const voucherDiscountPercent = parseFloat($('#voucherDiscount').val()) || 0;
    const voucherDiscountAmount = afterDiscount * (voucherDiscountPercent / 100);
    const afterVoucherDiscount = afterDiscount - voucherDiscountAmount;
    
    // Calculate tax
    const taxAmount = afterVoucherDiscount * 0.11; // 11% PPN
    
    // Calculate grand total
    const grandTotal = afterVoucherDiscount + taxAmount;
    
    $('#taxDisplay').text(`Rp ${numberFormat(taxAmount)}`);
    $('#grandTotalDisplay').text(`Rp ${numberFormat(grandTotal)}`);
    
    // Update payment totals when grand total changes
    calculatePaymentTotals();
}

function validateVoucher(voucherCode) {
    if (!voucherCode) {
        $('#voucherInfo').text('').removeClass('text-success text-danger');
        return;
    }
    
    $.ajax({
        url: '<?= base_url('transaksi/jual/validate-voucher') ?>',
        type: 'POST',
        data: { 
            voucher_code: voucherCode
        },
        success: function(response) {
            if (response.valid) {
                $('#voucherInfo').text('Voucher valid: ' + response.discount + '%').removeClass('text-danger').addClass('text-success');
                $('#voucherDiscount').val(response.discount);
                calculateTotal();
            } else {
                $('#voucherInfo').text('Voucher tidak valid').removeClass('text-success').addClass('text-danger');
                $('#voucherDiscount').val(0);
                calculateTotal();
            }
        },
        error: function() {
            $('#voucherInfo').text('Error validasi voucher').removeClass('text-success').addClass('text-danger');
            $('#voucherDiscount').val(0);
            calculateTotal();
        }
    });
}

// Currency formatting function
function formatCurrency(amount) {
    return `Rp ${numberFormat(amount)}`;
}

function completeTransaction() {
    if (cart.length === 0) {
        toastr.error('Keranjang belanja kosong');
        return;
    }

    const outletId = $('#outletSelect').val();
    if (!outletId) {
        toastr.error('Outlet belum dipilih');
        return;
    }
    
    // Validate payment methods
    const paymentMethods = [];
    let totalPaymentAmount = 0;
    let hasValidPayment = false;
    
    $('.payment-method-row').each(function() {
        const type = $(this).find('.payment-type').val();
        const platformId = $(this).find('.payment-platform').val();
        const amount = parseFloat($(this).find('.payment-amount').val()) || 0;
        const reference = $(this).find('input[name*="[reference]"]').val();
        
        if (type && amount > 0) {
            hasValidPayment = true;
            paymentMethods.push({
                type: type,
                platform_id: platformId,
                amount: amount,
                reference: reference
            });
            totalPaymentAmount += amount;
        }
    });
    
    if (!hasValidPayment) {
        toastr.error('Minimal harus ada satu metode pembayaran dengan jumlah > 0');
        return;
    }
    
    const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/[^\d]/g, '')) || 0;
    
    if (totalPaymentAmount < grandTotal) {
        toastr.error(`Jumlah bayar (${formatCurrency(totalPaymentAmount)}) kurang dari total (${formatCurrency(grandTotal)})`);
        return;
    }
    
    // Prepare transaction data
    const transactionData = {
        cart: cart,
        customer_id: $('#customerSelect').val() || null,
        warehouse_id: $('#outletSelect').val() || null,
        discount_percent: parseFloat($('#discountPercent').val()) || 0,
        voucher_code: $('#voucherCode').val() || null,
        voucher_discount: parseFloat($('#voucherDiscount').val()) || 0,
        payment_methods: paymentMethods,
        total_amount_received: totalPaymentAmount,
        grand_total: grandTotal
    };
    
    // Show loading state
    $('#completeTransaction').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
    

    
    // Send transaction to server
    $.ajax({
        url: '<?= base_url('transaksi/jual/process-transaction') ?>',
        type: 'POST',
        data: transactionData,
        success: function(response) {
            if (response.success) {
                // Check if payment method includes Piutang (value='3')
                const hasPiutang = paymentMethods.some(pm => pm.type === '3');
                
                if (hasPiutang) {
                    // Store transaction info for QR scanner
                    window.lastTransaction = {
                        id: response.transaction_id,
                        no_nota: response.no_nota,
                        total: response.total,
                        change: response.change,
                        payment_type: 'piutang'
                    };
                    
                    // Redirect to mobile QR scanner page
                    window.open('<?= base_url('transaksi/jual/qr-scanner') ?>/' + response.transaction_id, '_blank');
                    toastr.success('Transaksi Piutang berhasil! Arahkan ke halaman scan QR.');
                    
                    // Clear form for next transaction
                    clearTransactionForm();
                } else {
                    // Normal transaction completion
                    $('#finalTotal').text(`Rp ${numberFormat(response.total)}`);
                    
                    // Build payment methods summary
                    let paymentSummary = '';
                    paymentMethods.forEach(pm => {
                        const methodName = pm.type === '1' ? 'Tunai' : pm.type === '2' ? 'Non Tunai' : 'Piutang';
                        paymentSummary += `${methodName}: ${formatCurrency(pm.amount)}<br>`;
                    });
                    $('#finalPaymentMethod').html(paymentSummary);
                    $('#completeModal').modal('show');
                    
                    // Store transaction info for receipt printing
                    window.lastTransaction = {
                        id: response.transaction_id,
                        no_nota: response.no_nota,
                        total: response.total,
                        change: response.change
                    };
                    
                    toastr.success(response.message);
                }
            } else {
                toastr.error(response.message || 'Gagal memproses transaksi');
            }
        },
        error: function(xhr, status, error) {
            console.error('Transaction error:', error);
            toastr.error('Terjadi kesalahan saat memproses transaksi');
        },
        complete: function() {
            // Reset button state
            $('#completeTransaction').prop('disabled', false).html('<i class="fas fa-check"></i> Selesai');
        }
    });
}

function newTransaction() {
    cart = [];
    updateCartDisplay();
    calculateTotal();
    $('#customerSelect').val('');

    $('#discountPercent').val('');
    $('#voucherCode').val('');
    $('#voucherInfo').text('');
    $('#paymentMethod').val('');
    $('#amountReceived').val('');
    $('#productSearch').val('').focus();
}

function clearTransactionForm() {
    // Clear cart
    cart = [];
    updateCartDisplay();
    
    // Reset customer selection
    $('#customerSelect').val('').trigger('change');
    
    // Clear discount and voucher fields
    $('#discountPercent').val('');
    $('#voucherCode').val('');
    $('#voucherInfo').text('').removeClass('text-success text-danger');
    $('#voucherDiscount').val('0');
    
    // Reset payment methods
    $('#paymentMethods').empty();
    paymentMethods = [];
    paymentCounter = 0;
    addPaymentMethod(); // Add first payment method by default
    
    // Clear product search
    $('#productSearch').val('');
    
    // Recalculate totals
    calculateTotal();
    
    // Focus on product search for next transaction
    setTimeout(function() {
        $('#productSearch').focus();
    }, 500);
    
    // Show success message
    toastr.success('Form berhasil direset untuk transaksi baru');
}

function holdTransaction() {
    // Save current transaction to session/localStorage for later retrieval
    const transactionData = {
        cart: cart,
        customer: $('#customerSelect').val(),
        discount: $('#discountPercent').val(),
        voucher: $('#voucherCode').val(),
        paymentMethod: $('#paymentMethod').val()
    };
    
    localStorage.setItem('heldTransaction', JSON.stringify(transactionData));
    toastr.success('Transaksi ditahan');
    newTransaction();
}

function cancelTransaction() {
    if (confirm('Yakin ingin membatalkan transaksi ini?')) {
        newTransaction();
    }
}

function printReceipt() {
    // Implement receipt printing logic
    toastr.success('Struk berhasil dicetak');
    $('#completeModal').modal('hide');
    newTransaction();
}

function numberFormat(number) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(Math.round(number || 0));
}

function viewTransaction(transactionId) {
    // Redirect to the main transaction list with a filter for this specific transaction
    window.open('<?= base_url('transaksi/jual') ?>?search=' + transactionId, '_blank');
}
</script>
<?= $this->endSection() ?> 