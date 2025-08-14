<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Create Sales Transaction Form View
 * This file represents the View.
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-plus"></i> Buat Transaksi Penjualan
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/jual') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="salesForm" method="POST" action="<?= base_url('transaksi/jual/store') ?>">
            <?= csrf_field() ?>
            
            <!-- Transaction Details Section -->
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <!-- Customer -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_pelanggan">Pelanggan *</label>
                                <select class="form-control select2" id="id_pelanggan" name="id_pelanggan">
                                    <option value="">Pilih Pelanggan</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?= $customer->id ?>"><?= esc(isset($customer->nama) ? $customer->nama : 'Unknown') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Order Number -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_nota">Nomor Order</label>
                                <input type="text" class="form-control" id="no_nota" name="no_nota" value="- Auto -" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Order Date -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tgl_order">Tanggal Order *</label>
                                <input type="datetime-local" class="form-control" id="tgl_order" name="tgl_order" 
                                       value="<?= date('Y-m-d\TH:i') ?>" required>
                            </div>
                        </div>

                        <!-- Customer Reference -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_ref_pelanggan">Nomor Ref. Pelanggan</label>
                                <input type="text" class="form-control" id="no_ref_pelanggan" name="no_ref_pelanggan">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Delivery Date -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tgl_pengiriman">Tanggal Pengiriman</label>
                                <input type="date" class="form-control" id="tgl_pengiriman" name="tgl_pengiriman">
                            </div>
                        </div>

                        <!-- Outlet -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="outlet">Outlet *</label>
                                <select class="form-control select2" id="outlet" name="outlet" required>
                                    <option value="">Pilih Outlet</option>
                                    <option value="1" selected>Pojok Seduh</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Warehouse -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_gudang">Gudang *</label>
                                <select class="form-control select2" id="id_gudang" name="id_gudang" required>
                                    <option value="">Pilih Gudang</option>
                                    <?php foreach ($warehouses as $warehouse): ?>
                                        <option value="<?= $warehouse->id ?>"><?= esc(isset($warehouse->gudang) ? $warehouse->gudang : 'Unknown') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Price Includes Tax -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga Termasuk Pajak</label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <label class="btn btn-outline-primary active">
                                        <input type="radio" name="harga_include_pajak" value="0" checked> Tidak
                                    </label>
                                    <label class="btn btn-outline-primary">
                                        <input type="radio" name="harga_include_pajak" value="1"> Ya
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Display -->
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h4 class="card-title">Total</h4>
                        </div>
                        <div class="card-body">
                            <h2 class="text-center" id="displayTotal">Rp 0,00</h2>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Products Table -->
            <div class="row">
                <div class="col-12">
                    <h5>Produk</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="productsTable">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Produk</th>
                                    <th width="10%">Qty</th>
                                    <th width="15%">Harga</th>
                                    <th width="15%">Diskon</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="10%">Pajak</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <!-- Products will be added here dynamically -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-2">
                        <button type="button" class="btn btn-primary" id="addProductRow">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>
                        <button type="button" class="btn btn-danger" id="removeAllRows">
                            <i class="fas fa-times"></i> Hapus Semua Baris
                        </button>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Additional Information and Summary -->
            <div class="row">
                <!-- Left Column - Messages and Notes -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pesan_pelanggan">Pesan untuk Pelanggan</label>
                        <textarea class="form-control" id="pesan_pelanggan" name="pesan_pelanggan" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="catatan">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="4"></textarea>
                    </div>
                </div>

                <!-- Right Column - Summary -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-6">Subtotal:</div>
                                <div class="col-6 text-right">
                                    <span id="subtotalDisplay">Rp 0,00</span>
                                    <input type="hidden" id="subtotal" name="subtotal" value="0">
                                    <input type="hidden" id="jml_subtotal" name="jml_subtotal" value="0">
                                    <input type="hidden" id="jml_total" name="jml_total" value="0">
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6">Diskon:</div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" id="diskonPercent" placeholder="%" step="0.01">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm mt-1">
                                        <input type="number" class="form-control" id="diskonAmount" placeholder="0" step="0.01">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <input type="hidden" id="diskon" name="diskon" value="0">
                                    <input type="hidden" id="jml_diskon" name="jml_diskon" value="0">
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="includeTax" checked>
                                        <label class="form-check-label" for="includeTax">
                                            Pajak
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                    <span id="taxDisplay">Rp 0,00</span>
                                    <input type="hidden" id="ppn" name="ppn" value="0">
                                    <input type="hidden" id="jml_ppn" name="jml_ppn" value="0">
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6">Penyesuaian:</div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" id="penyesuaian" name="penyesuaian" value="0" step="0.01">
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6">Voucher:</div>
                                <div class="col-6">
                                    <input type="text" class="form-control form-control-sm" id="voucher_code" name="voucher_code" placeholder="Kode voucher">
                                    <small class="text-muted" id="voucher_info"></small>
                                    <input type="hidden" id="voucher_discount" name="voucher_discount" value="0">
                                    <input type="hidden" id="voucher_id" name="voucher_id" value="">
                                    <input type="hidden" id="voucher_type" name="voucher_type" value="">
                                    <input type="hidden" id="voucher_discount_amount" name="voucher_discount_amount" value="0">
                                </div>
                            </div>
                            
                            <div class="row mb-2" id="voucherDiscountRow" style="display: none;">
                                <div class="col-6">Potongan Voucher:</div>
                                <div class="col-6 text-right">
                                    <span id="voucherDiscountDisplay">Rp 0</span>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6">Metode Bayar:</div>
                                <div class="col-6">
                                    <select class="form-control form-control-sm" id="metode_bayar" name="metode_bayar">
                                        <option value="">Pilih metode bayar</option>
                                        <option value="tunai">Tunai</option>
                                        <option value="kartu">Kartu Debit/Credit</option>
                                        <option value="transfer">Transfer Bank</option>
                                        <option value="ewallet">E-Wallet</option>
                                        <option value="qris">QRIS</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6">Platform Pembayaran:</div>
                                <div class="col-6">
                                    <select class="form-control form-control-sm" id="id_platform" name="id_platform">
                                        <option value="">Pilih Platform</option>
                                        <?php foreach ($platforms as $platform): ?>
                                            <option value="<?= $platform->id ?>"><?= esc($platform->platform) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>                            
                            
                            <!-- Hidden platform data for controller -->
                            <input type="hidden" id="platform_data" name="platforms" value="">                            
                            <hr>                            
                            <div class="row">
                                <div class="col-6"><strong>Total:</strong></div>
                                <div class="col-6 text-right">
                                    <strong><span id="grandTotalDisplay">Rp 0,00</span></strong>
                                    <input type="hidden" id="jml_gtotal" name="jml_gtotal" value="0">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6"><label for="jumlah_bayar"><strong>Jumlah Bayar:</strong></label></div>
                                <div class="col-6 text-right">
                                    <input type="number" class="form-control form-control-sm text-right" id="jumlah_bayar" name="jumlah_bayar" value="0" min="0" step="0.01" placeholder="Masukkan jumlah pembayaran">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6"><label for="jml_kembali"><strong>Jumlah Kembali:</strong></label></div>
                                <div class="col-6 text-right">
                                    <input type="text" class="form-control form-control-sm text-right" id="jml_kembali" name="jml_kembali" value="0" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Footer Actions -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="print_order" name="print_order" value="1" checked>
                        <label class="form-check-label" for="print_order">
                            Print Order
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="print_surat_jalan" name="print_surat_jalan" value="1">
                        <label class="form-check-label" for="print_surat_jalan">
                            Print Surat Jalan
                        </label>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-secondary" onclick="history.back()">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog">
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
                    <input type="text" class="form-control" id="productSearch" placeholder="Cari produk...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="productListTable">
                                                    <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Produk (Kategori - Merk)</th>
                                    <th>Harga Jual</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        <tbody>
                            <!-- Products will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Generate order number
    generateOrderNumber();

    // Add first product row
    addProductRow();

    // Event listeners
    $('#addProductRow').on('click', addProductRow);
    $('#removeAllRows').on('click', removeAllRows);
    $('#diskonPercent').on('input', calculateDiscount);
    $('#diskonAmount').on('input', calculateDiscount);
    $('#penyesuaian').on('input', calculateTotal);
    $('#includeTax').on('change', calculateTotal);

    // Product search
    $('#productSearch').on('input', function() {
        searchProducts($(this).val());
    });
    
    // Voucher validation
    $('#voucher_code').on('blur', function() {
        validateVoucher($(this).val());
    });
    
            // Clear voucher when input is cleared
        $('#voucher_code').on('input', function() {
            if (!$(this).val()) {
                clearVoucher();
            }
        });
        
        // Recalculate totals when discount inputs change
        $('#diskonPercent, #diskonAmount').on('input', function() {
            calculateDiscount();
        });
        
        // Recalculate totals when adjustment changes
        $('#penyesuaian').on('input', function() {
            calculateTotal();
        });
        
        // Recalculate totals when tax checkbox changes
        $('#includeTax').on('change', function() {
            calculateTotal();
    });
});

let productRowCounter = 0;
let currentEditingRow = null;

function generateOrderNumber() {
    $.ajax({
        url: '<?= base_url('transaksi/jual/generate-nota') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#no_nota').val(response.nota_number);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error generating order number:', error);
            if (xhr.status === 401) {
                toastr.error('Sesi Anda telah berakhir. Silakan login ulang.');
                setTimeout(function() {
                    window.location.href = '<?= base_url('auth/login') ?>';
                }, 2000);
            }
        }
    });
}

function addProductRow() {
    productRowCounter++;
    const rowHtml = `
        <tr id="productRow_${productRowCounter}">
            <td>${productRowCounter}</td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control product-select" placeholder="Pilih produk..." readonly>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" onclick="openProductModal(${productRowCounter})">
                            <i class="fas fa-search"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearProduct(${productRowCounter})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="items[${productRowCounter}][id_item]" class="product-id">
                <input type="hidden" name="items[${productRowCounter}][produk]" class="product-name">
                <input type="hidden" name="items[${productRowCounter}][kode]" class="product-code">
                <input type="hidden" name="items[${productRowCounter}][id_satuan]" class="product-satuan-id" value="">
                <input type="hidden" name="items[${productRowCounter}][id_kategori]" class="product-kategori-id" value="">
                <input type="hidden" name="items[${productRowCounter}][id_merk]" class="product-merk-id" value="">
                <input type="hidden" name="items[${productRowCounter}][satuan]" class="product-satuan" value="">
                <input type="hidden" name="items[${productRowCounter}][keterangan]" class="product-keterangan" value="">
                <input type="hidden" name="items[${productRowCounter}][harga_beli]" class="product-harga-beli" value="0">
                <input type="hidden" name="items[${productRowCounter}][qty_satuan]" class="product-qty-satuan" value="1">
                <input type="hidden" name="items[${productRowCounter}][disk1]" class="product-disk1" value="0">
                <input type="hidden" name="items[${productRowCounter}][disk2]" class="product-disk2" value="0">
                <input type="hidden" name="items[${productRowCounter}][disk3]" class="product-disk3" value="0">
                <input type="hidden" name="items[${productRowCounter}][potongan]" class="product-potongan" value="0">
            </td>
            <td>
                <input type="number" class="form-control product-qty" name="items[${productRowCounter}][qty]" 
                       value="1" min="1" step="1" onchange="calculateRowTotal(${productRowCounter})">
            </td>
            <td>
                <input type="number" class="form-control product-price" name="items[${productRowCounter}][harga]" 
                       value="0" min="0" step="0.01" readonly>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" class="form-control product-discount" name="items[${productRowCounter}][diskon]" 
                           value="0" min="0" max="100" step="0.01" onchange="calculateRowTotal(${productRowCounter})">
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <small class="text-muted discount-amount">Rp 0,00</small>
            </td>
            <td>
                <input type="number" class="form-control product-total" name="items[${productRowCounter}][jumlah]" 
                       value="0" readonly>
            </td>
            <td>
                <select class="form-control product-tax" name="items[${productRowCounter}][pajak]">
                    <option value="0">Non PPN</option>
                    <option value="1">PPN 11%</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeProductRow(${productRowCounter})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#productsTableBody').append(rowHtml);
    calculateTotal();
}

function removeProductRow(rowId) {
    $(`#productRow_${rowId}`).remove();
    renumberRows();
    calculateTotal();
}

function removeAllRows() {
    $('#productsTableBody').empty();
    productRowCounter = 0;
    addProductRow();
    clearVoucher();
    calculateTotal();
}

function renumberRows() {
    $('#productsTableBody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

function openProductModal(rowId) {
    currentEditingRow = rowId;
    $('#productModal').modal('show');
    $('#productSearch').val('').focus();
    loadProducts();
}

function clearProduct(rowId) {
    $(`#productRow_${rowId} .product-select`).val('');
    $(`#productRow_${rowId} .product-id`).val('');
    $(`#productRow_${rowId} .product-name`).val('');
    $(`#productRow_${rowId} .product-code`).val('');
    $(`#productRow_${rowId} .product-price`).val('0');
    calculateRowTotal(rowId);
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
            if (xhr.status === 401) {
                toastr.error('Sesi Anda telah berakhir. Silakan login ulang.');
                setTimeout(function() {
                    window.location.href = '<?= base_url('auth/login') ?>';
                }, 2000);
            }
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
            warehouse_id: $('#id_gudang').val()
        },
        success: function(response) {
            if (response.items) {
                displayProducts(response.items);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error searching products:', error);
            if (xhr.status === 401) {
                toastr.error('Sesi Anda telah berakhir. Silakan login ulang.');
                setTimeout(function() {
                    window.location.href = '<?= base_url('auth/login') ?>';
                }, 2000);
            }
        }
    });
}

function displayProducts(products) {
    let html = '';
    products.forEach(function(product) {
        const itemName = (product.item || product.nama || product.produk || '-').replace(/'/g, "\\'");
        const category = (product.kategori || '-').replace(/'/g, "\\'");
        const brand = (product.merk || '-').replace(/'/g, "\\'");
        const price = product.harga_jual || product.harga || 0;
        const stock = product.stok || 0;
        const productCode = (product.kode || '-').replace(/'/g, "\\'");
        
        html += `
            <tr>
                <td>${product.kode || '-'}</td>
                <td>
                    <strong>${product.item || product.nama || product.produk || '-'}</strong><br>
                    <small class="text-muted">
                        Kategori: ${product.kategori || '-'} | Merk: ${product.merk || '-'}
                    </small>
                </td>
                <td>Rp ${numberFormat(price)}</td>
                <td>${stock}</td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm" onclick="selectProduct(${product.id}, '${itemName}', '${productCode}', ${price}, '${category}', '${brand}')">
                        Pilih
                    </button>
                </td>
            </tr>
        `;
    });
    
    $('#productListTable tbody').html(html);
}

function selectProduct(productId, productName, productCode, productPrice, category, brand) {
    if (currentEditingRow) {
        // Display product name with category and brand info
        const displayName = `${productName} (${category} - ${brand})`;
        $(`#productRow_${currentEditingRow} .product-select`).val(displayName);
        $(`#productRow_${currentEditingRow} .product-id`).val(productId);
        $(`#productRow_${currentEditingRow} .product-name`).val(productName);
        $(`#productRow_${currentEditingRow} .product-code`).val(productCode);
        $(`#productRow_${currentEditingRow} .product-price`).val(productPrice);
        
        // Set additional fields with default values
        $(`#productRow_${currentEditingRow} .product-satuan`).val('PCS');
        $(`#productRow_${currentEditingRow} .product-kategori-id`).val('');
        $(`#productRow_${currentEditingRow} .product-merk-id`).val('');
        $(`#productRow_${currentEditingRow} .product-satuan-id`).val('');
        $(`#productRow_${currentEditingRow} .product-keterangan`).val('');
        $(`#productRow_${currentEditingRow} .product-harga-beli`).val(0);
        $(`#productRow_${currentEditingRow} .product-qty-satuan`).val(1);
        $(`#productRow_${currentEditingRow} .product-disk1`).val(0);
        $(`#productRow_${currentEditingRow} .product-disk2`).val(0);
        $(`#productRow_${currentEditingRow} .product-disk3`).val(0);
        $(`#productRow_${currentEditingRow} .product-potongan`).val(0);
        
        calculateRowTotal(currentEditingRow);
        $('#productModal').modal('hide');
    }
}

function calculateRowTotal(rowId) {
    const qty = parseFloat($(`#productRow_${rowId} .product-qty`).val()) || 0;
    const price = parseFloat($(`#productRow_${rowId} .product-price`).val()) || 0;
    const discount = parseFloat($(`#productRow_${rowId} .product-discount`).val()) || 0;
    
    const subtotal = qty * price;
    const discountAmount = subtotal * (discount / 100);
    const total = subtotal - discountAmount;
    
    $(`#productRow_${rowId} .product-total`).val(total);
    $(`#productRow_${rowId} .discount-amount`).text(`Rp ${numberFormat(discountAmount)}`);
    
    calculateTotal();
}

function calculateDiscount() {
    const subtotal = parseFloat($('#subtotal').val()) || 0;
    const discountPercent = parseFloat($('#diskonPercent').val()) || 0;
    const discountAmount = parseFloat($('#diskonAmount').val()) || 0;
    
    if (discountPercent > 0) {
        const calculatedAmount = subtotal * (discountPercent / 100);
        $('#diskonAmount').val(calculatedAmount.toFixed(2));
        $('#diskon').val(discountPercent);
        $('#jml_diskon').val(calculatedAmount);
    } else if (discountAmount > 0) {
        const calculatedPercent = (discountAmount / subtotal) * 100;
        $('#diskonPercent').val(calculatedPercent.toFixed(2));
        $('#diskon').val(calculatedPercent);
        $('#jml_diskon').val(discountAmount);
    } else {
        $('#diskon').val(0);
        $('#jml_diskon').val(0);
    }
    
    calculateTotal();
}

function calculateTotal() {
    let subtotal = 0;
    
    // Calculate subtotal from products
    $('.product-total').each(function() {
        subtotal += parseFloat($(this).val()) || 0;
    });
    
    $('#subtotal').val(subtotal);
    $('#jml_subtotal').val(subtotal);
    $('#subtotalDisplay').text(`Rp ${numberFormat(subtotal)}`);
    
    // Calculate discount
    const discountAmount = parseFloat($('#jml_diskon').val()) || 0;
    const afterDiscount = subtotal - discountAmount;
    
    // Set jml_total (total after discount, before tax)
    $('#jml_total').val(afterDiscount);
    
    // Calculate voucher discount
    const voucherType = $('#voucher_type').val();
    let voucherDiscountAmount = 0;

    if (voucherType === 'persen') {
    const voucherDiscountPercent = parseFloat($('#voucher_discount').val()) || 0;
        voucherDiscountAmount = afterDiscount * (voucherDiscountPercent / 100);
    } else if (voucherType === 'nominal') {
        voucherDiscountAmount = parseFloat($('#voucher_discount_amount').val()) || 0;
        if (voucherDiscountAmount > afterDiscount) {
            voucherDiscountAmount = afterDiscount;
        }
    }
    const afterVoucherDiscount = afterDiscount - voucherDiscountAmount;

    // Show/hide voucher discount row
    if (voucherDiscountAmount > 0) {
        $('#voucherDiscountRow').show();
        $('#voucherDiscountDisplay').text(`Rp ${numberFormat(voucherDiscountAmount)}`);
    } else {
        $('#voucherDiscountRow').hide();
    }
    
    // Calculate tax
    let taxAmount = 0;
    if ($('#includeTax').is(':checked')) {
        taxAmount = afterVoucherDiscount * 0.11; // 11% PPN
        $('#ppn').val(11);
        $('#jml_ppn').val(taxAmount);
    } else {
        $('#ppn').val(0);
        $('#jml_ppn').val(0);
    }
    
    // Calculate adjustment
    const adjustment = parseFloat($('#penyesuaian').val()) || 0;
    
    // Calculate grand total
    const grandTotal = afterVoucherDiscount + taxAmount + adjustment;
    
    $('#grandTotalDisplay').text(`Rp ${numberFormat(grandTotal)}`);
    $('#displayTotal').text(`Rp ${numberFormat(grandTotal)}`);
    $('#jml_gtotal').val(grandTotal);
    $('#taxDisplay').text(`Rp ${numberFormat(taxAmount)}`);
}

function numberFormat(number) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(number || 0);
}

function clearVoucher() {
    $('#voucher_info').text('').removeClass('text-success text-danger');
    $('#voucher_discount').val(0);
    $('#voucher_id').val('');
    $('#voucher_type').val('');
    $('#voucher_discount_amount').val(0);
    $('#voucherDiscountRow').hide();
    calculateTotal();
}

function validateVoucher(voucherCode) {
    if (!voucherCode) {
        $('#voucher_info').text('').removeClass('text-success text-danger');
        $('#voucher_discount').val(0);
        $('#voucher_id').val('');
        $('#voucher_type').val('');
        $('#voucher_discount_amount').val(0);
        $('#voucherDiscountRow').hide();
        calculateTotal();
        return;
    }
    
    $.ajax({
        url: '<?= base_url('transaksi/jual/validate-voucher') ?>',
        type: 'POST',
        data: { voucher_code: voucherCode },
        success: function(response) {
            if (response.valid) {
                let displayText = '';
                if (response.jenis_voucher === 'persen') {
                    displayText = `Voucher valid: ${response.discount}%`;
                } else if (response.jenis_voucher === 'nominal') {
                    displayText = `Voucher valid: Rp ${numberFormat(response.discount_amount)}`;
                }
                $('#voucher_info').text(displayText).removeClass('text-danger').addClass('text-success');
                $('#voucher_discount').val(response.discount);
                $('#voucher_id').val(response.voucher_id);
                $('#voucher_type').val(response.jenis_voucher);
                $('#voucher_discount_amount').val(response.discount_amount);
                calculateTotal();
            } else {
                $('#voucher_info').text(response.message || 'Voucher tidak valid').removeClass('text-success').addClass('text-danger');
                $('#voucher_discount').val(0);
                $('#voucher_id').val('');
                $('#voucher_type').val('');
                $('#voucher_discount_amount').val(0);
                $('#voucherDiscountRow').hide();
                calculateTotal();
            }
        },
        error: function() {
            $('#voucher_info').text('Error validasi voucher').removeClass('text-success').addClass('text-danger');
            $('#voucher_discount').val(0);
            $('#voucher_id').val('');
            $('#voucher_type').val('');
            $('#voucher_discount_amount').val(0);
            $('#voucherDiscountRow').hide();
            calculateTotal();
        }
    });
}

// Form validation
$('#salesForm').on('submit', function(e) {
    // Ensure all totals are calculated before submission
    calculateTotal();
    
    const warehouse = $('#id_gudang').val();
    const orderDate = $('#tgl_order').val();
    
    if (!warehouse) {
        e.preventDefault();
        toastr.error('Gudang harus dipilih');
        return false;
    }
    
    if (!orderDate) {
        e.preventDefault();
        toastr.error('Tanggal order harus diisi');
        return false;
    }
    
    // Check if at least one product is added
    let hasProducts = false;
    $('.product-id').each(function() {
        if ($(this).val()) {
            hasProducts = true;
            return false;
        }
    });
    
    if (!hasProducts) {
        e.preventDefault();
        toastr.error('Minimal satu produk harus ditambahkan');
        return false;
    }
    
    // Validate required total fields
    const subtotal = parseFloat($('#jml_subtotal').val()) || 0;
    const total = parseFloat($('#jml_total').val()) || 0;
    const discount = parseFloat($('#jml_diskon').val()) || 0;
    const grandTotal = parseFloat($('#jml_gtotal').val()) || 0;
    
    if (subtotal <= 0) {
        e.preventDefault();
        toastr.error('Subtotal tidak boleh kosong atau 0');
        return false;
    }
    
    if (total <= 0) {
        e.preventDefault();
        toastr.error('Total setelah diskon tidak boleh kosong atau 0');
        return false;
    }
    
    if (grandTotal <= 0) {
        e.preventDefault();
        toastr.error('Total tidak boleh kosong atau 0');
        return false;
    }
    
    // Add voucher fields to form data
    const voucherCode = $('#voucher_code').val();
    if (voucherCode) {
        // Add voucher fields to the form
        if (!$('#voucher_id').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'voucher_id',
                value: $('#voucher_id').val()
            }).appendTo('#salesForm');
        }
        if (!$('#voucher_type').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'voucher_type',
                value: $('#voucher_type').val()
            }).appendTo('#salesForm');
        }
        if (!$('#voucher_discount_amount').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'voucher_discount_amount',
                value: $('#voucher_discount_amount').val()
            }).appendTo('#salesForm');
        }
    }
    
    // Add platform data
    const platformId = $('#id_platform').val();
    if (platformId) {
        const platformData = [{
            id_platform: platformId,
            platform: $('#id_platform option:selected').text(),
            nominal: parseFloat($('#jml_gtotal').val()) || 0,
            keterangan: 'Pembayaran via ' + $('#id_platform option:selected').text()
        }];
        $('#platform_data').val(JSON.stringify(platformData));
    }
    
    return true;
});
</script>
<?= $this->endSection() ?> 