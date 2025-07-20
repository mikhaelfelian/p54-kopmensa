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

                <!-- Payment Method -->
                <div class="form-group">
                    <label for="paymentMethod">Metode Bayar</label>
                    <select class="form-control" id="paymentMethod">
                        <option value="">Pilih metode bayar</option>
                        <option value="tunai">Tunai</option>
                        <option value="kartu">Kartu Debit/Credit</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="ewallet">E-Wallet</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_platform">Platform Pembayaran</label>
                    <select class="form-control" id="id_platform" name="id_platform">
                        <option value="">Pilih Platform</option>
                        <?php foreach ($platforms as $platform): ?>
                            <option value="<?= $platform->id ?>"><?= esc($platform->platform) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Amount Received -->
                <div class="form-group">
                    <label for="amountReceived">Jumlah Bayar</label>
                    <input type="number" class="form-control" id="amountReceived" placeholder="0" step="100">
                </div>

                <!-- Change -->
                <div class="form-group">
                    <label>Kembalian</label>
                    <div class="form-control-plaintext" id="changeDisplay">Rp 0</div>
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
    
    $('#amountReceived').on('input', calculateChange);
    $('#paymentMethod').on('change', calculateChange);
    
    $('#completeTransaction').on('click', completeTransaction);
    $('#newTransaction').on('click', newTransaction);
    $('#holdTransaction').on('click', holdTransaction);
    $('#cancelTransaction').on('click', cancelTransaction);
    $('#printReceipt').on('click', printReceipt);
    
    // Enter key to search
    $('#productSearch').on('keypress', function(e) {
        if (e.which === 13) {
            searchProducts($(this).val());
        }
    });
});

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
                    <button type="button" class="btn btn-primary btn-sm" onclick="addToCart(${product.id}, '${itemName.replace(/'/g, "\\'")}', '${product.kode}', ${price})">
                        <i class="fas fa-plus"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    $('#productListTable tbody').html(html);
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
    
    calculateChange();
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

function calculateChange() {
    const grandTotalText = $('#grandTotalDisplay').text();
    // Remove all non-numeric characters including dots (thousands separators)
    const grandTotal = parseFloat(grandTotalText.replace(/[^\d]/g, '')) || 0;
    const amountReceived = parseFloat($('#amountReceived').val()) || 0;
    const change = amountReceived - grandTotal;
    
    $('#changeDisplay').text(`Rp ${numberFormat(change)}`);
}

function completeTransaction() {
    if (cart.length === 0) {
        toastr.error('Keranjang belanja kosong');
        return;
    }

    const paymentMethod = $('#paymentMethod').val();
    if (!paymentMethod) {
        toastr.error('Pilih metode pembayaran');
        return;
    }

    const outletId = $('#outletSelect').val();
    if (!outletId) {
        toastr.error('Outlet belum dipilih');
        return;
    }
    
    const amountReceived = parseFloat($('#amountReceived').val()) || 0;
    const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/[^\d]/g, '')) || 0;
    
    if (amountReceived < grandTotal) {
        toastr.error('Jumlah bayar kurang dari total');
        return;
    }
    
    // Prepare transaction data
    const transactionData = {
        cart: cart,
        customer_id: $('#customerSelect').val() || null,

        warehouse_id      : $('#outletSelect').val() || null,
        discount_percent  : parseFloat($('#discountPercent').val()) || 0,
        voucher_code      : $('#voucherCode').val() || null,
        voucher_discount  : parseFloat($('#voucherDiscount').val()) || 0,
        payment_method    : paymentMethod,
        platform_id       : $('#id_platform').val() || null,
        amount_received   : amountReceived,
        grand_total       : grandTotal
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
                // Show completion modal
                $('#finalTotal').text(`Rp ${numberFormat(response.total)}`);
                $('#finalPaymentMethod').text($('#paymentMethod option:selected').text());
                $('#completeModal').modal('show');
                
                // Store transaction info for receipt printing
                window.lastTransaction = {
                    id: response.transaction_id,
                    no_nota: response.no_nota,
                    total: response.total,
                    change: response.change
                };
                
                toastr.success(response.message);
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