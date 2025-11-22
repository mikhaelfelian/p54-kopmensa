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
<?= $this->extend('admin-lte-3/layout/main_no_sidebar') ?>
<?= $this->section('content') ?>
<!-- Hidden CSRF token for AJAX requests -->
<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

<div class="row">
    <!-- Left Column - Product Selection and Grid -->
    <div class="col-lg-7">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="input-group" style="max-width: 400px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                            <button type="button" class="btn btn-outline-info" id="testSearch"
                                title="Test basic search">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="<?= base_url('transaksi/jual/cashier-data') ?>" class="btn btn-outline-primary ml-2"
                            title="Lihat Data Penjualan">
                            <i class="fas fa-list"></i> Data Penjualan
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Category Tabs -->
                <div class="mb-3">
                    <div class="category-tabs-container">
                        <ul class="nav nav-tabs" id="categoryTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab">
                                    All (<?= count($items) ?>)
                                </a>
                            </li>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="category-<?= $category->id ?>-tab" data-toggle="tab"
                                            href="#category-<?= $category->id ?>" role="tab"
                                            data-category-id="<?= $category->id ?>">
                                            <?= $category->kategori ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="nav-item">
                                    <span class="text-muted">No categories found</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="row" id="productGrid">
                    <!-- Products will be loaded here -->
                </div>

                <!-- Loading Indicator -->
                <div id="productLoading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat produk...</p>
                </div>

                <!-- Lanjutkan Button (Load More) -->
                <div id="loadMoreContainer" class="text-center mt-2" style="display: none;">
                    <button type="button" class="btn btn-primary btn-sm rounded-0" id="loadMoreProducts">
                        <i class="fas fa-arrow-down"></i> Lanjutkan
                    </button> 
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Order Management -->
    <div class="col-lg-5">
        <div class="card rounded-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <!-- Warehouse Selection -->
                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label">Outlet</label>
                            <?php if (akses_kasir()): ?>
                                <select class="form-control form-control-sm" id="warehouse_id" disabled>
                                    <?php
                                    $selectedOutlet = session('kasir_outlet');
                                    if (!empty($outlets)):
                                        foreach ($outlets as $outlet):
                                            ?>
                                            <option value="<?= $outlet->id ?>" <?= ($outlet->id == $selectedOutlet) ? 'selected' : '' ?>>
                                                <?= esc($outlet->nama) ?>
                                            </option>
                                            <?php
                                        endforeach;
                                    else:
                                        ?>
                                        <option value="" disabled>- Tidak ada outlet aktif --</option>
                                    <?php endif; ?>
                                </select>
                                <input type="hidden" name="warehouse_id" id="warehouse_id_hidden"
                                    value="<?= esc($selectedOutlet) ?>">
                            <?php else: ?>
                                <select class="form-control form-control-sm" id="warehouse_id">
                                    <option value="">Pilih Outlet</option>
                                    <?php if (!empty($outlets)): ?>
                                        <?php foreach ($outlets as $outlet): ?>
                                            <option value="<?= $outlet->id ?>"><?= esc($outlet->nama) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>- Tidak ada outlet aktif --</option>
                                    <?php endif; ?>
                                </select>
                            <?php endif; ?>
                            <?php if (empty($outlets)): ?>
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Tidak ada outlet dengan status aktif (status=1, status_otl=1)
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted">#Pesanan Baru</h6>
                        <div class="btn-group btn-group-toggle d-flex mb-2" data-toggle="buttons">
                            <label class="btn btn-outline-primary flex-fill active" id="btnCustomerUmum">
                                <input type="radio" name="customerType" id="customerTypeUmum" value="umum"
                                    autocomplete="off" checked> Umum
                            </label>
                            <label class="btn btn-outline-success flex-fill" id="btnCustomerAnggota">
                                <input type="radio" name="customerType" id="customerTypeAnggota" value="anggota"
                                    autocomplete="off"> Anggota
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <!-- Scan Customer Field (hidden by default) -->
                        <div class="form-group scan-anggota-field mb-3" id="scanAnggotaGroup" style="display: none;">
                            <label for="scanAnggota">Scan QR Code Customer</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" id="scanAnggota"
                                    placeholder="Scan QR code atau ketik nomor anggota saja">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="openQrScanner">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="searchAnggota">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                GUNAKAN DENGAN MEMASUKKAN KODE ANGGOTA SAJA
                            </small>

                            <!-- QR Scanner Modal -->
                            <div class="modal fade qr-scanner-modal rounded-0" id="qrScannerModal" tabindex="-1"
                                role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Scan QR Code Customer</h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <div id="qrScannerContainer" class="qr-scanner-container">
                                                <video id="qrVideo" width="100%" height="400"
                                                    style="border: 1px solid #ddd;"></video>
                                            </div>
                                            <div id="qrScannerStatus" class="qr-scanner-status mt-2">
                                                <p class="text-muted">Mengaktifkan kamera...</p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-info rounded-0 me-2" id="flipCamera">
                                                <i class="fas fa-sync-alt"></i> Flip Camera
                                            </button>
                                            <button type="button" class="btn btn-secondary rounded-0"
                                                data-dismiss="modal">Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="customerBlockAlert" class="mt-2" style="display: none;">
                                <div class="alert alert-danger">
                                    <i class="fas fa-ban"></i>
                                    <strong>Akun Customer Terblokir!</strong>
                                    <p class="mb-0">Customer ini hanya dapat melakukan pembayaran dengan metode Tunai.</p>
                                </div>
                            </div>

                            <div id="anggotaInfo" class="anggota-info mt-2" style="display: none;">
                                <div class="alert alert-info alert-sm">
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Informasi Customer:</strong>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <strong>Nama:</strong> <span id="anggotaNama"></span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Kode:</strong> <span id="anggotaKode"></span>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-12">
                                            <strong>Alamat:</strong> <span id="anggotaAlamat"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Selection -->
                <div class="form-group customer-type-radio mb-3">
                    <!-- Hidden fields for customer data -->
                    <input type="hidden" id="selectedCustomerId" name="selectedCustomerId" value="2">
                    <input type="hidden" id="selectedCustomerName" name="selectedCustomerName" value="">
                    <input type="hidden" id="selectedCustomerType" name="selectedCustomerType" value="umum">
                    <input type="hidden" id="selectedCustomerIsBlocked" name="selectedCustomerIsBlocked" value="0">

                    <!-- Cart Area -->
                    <div class="cart-container rounded-0">
                        <div class="cart-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Keranjang Belanja</h6>
                            <span class="badge badge-info d-flex align-items-center" style="font-size: 1rem;">
                                <span id="totalItemsCount" class="mr-1">0</span>
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                        </div>
                        <div class="cart-items" id="cartTableBody">
                            <!-- Cart items will be added here -->
                            <div class="empty-cart-message" id="emptyCartMessage">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                    <p class="mb-0">Keranjang belanja kosong</p>
                                </div>
                            </div>
                        </div>
                        <div class="cart-summary">
                            <div class="summary-row">
                                <span class="summary-label">DPP (Dasar Pengenaan Pajak):</span>
                                <span class="summary-value" id="dppDisplay">Rp 0</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">PPN (<span
                                        id="cartPpnPercent"><?= $Pengaturan->ppn ?></span>%):</span>
                                <span class="summary-value" id="taxDisplay">Rp 0</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label"><strong>Total:</strong></span>
                                <span class="summary-value" id="grandTotalDisplay"><strong>Rp 0</strong></span>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <!-- Payment Methods Button -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary btn-block rounded-0" id="openPaymentModal">
                            <i class="fas fa-credit-card"></i> Bayar
                        </button>
                    </div>

                    <!-- Draft List Button -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-info btn-block rounded-0" id="showDraftList">
                            <i class="fas fa-list"></i> Daftar Draft
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('transaksi/jual') ?>" class="btn btn-primary rounded-0">
                    &laquo; Kembali
                </a>
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
            </div>
        </div>
    </div>
</div>

<!-- Variant Selection Modal -->
<div class="modal fade" id="variantModal" tabindex="-1" role="dialog" aria-labelledby="variantModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="variantModalLabel">Pilih Varian Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="variantList">
                    <!-- Variants will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Draft List Modal -->
<div class="modal fade" id="draftListModal" tabindex="-1" role="dialog" aria-labelledby="draftListModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Draft Transaksi</h5>
                <div>
                    <button type="button" class="btn btn-info btn-sm me-2" onclick="printAllDrafts()">
                        <i class="fas fa-print"></i> Print All
                    </button>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="draftTable">
                        <thead>
                            <tr>
                                <th>No. Nota</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Outlet</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="draftTableBody">
                            <!-- Draft data will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <div id="draftLoading" class="text-center" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Loading...
                </div>
                <div id="draftEmpty" class="text-center text-muted" style="display: none;">
                    <i class="fas fa-inbox"></i> Tidak ada draft transaksi
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Methods Modal -->
<div class="modal fade" id="paymentMethodsModal" tabindex="-1" role="dialog" aria-labelledby="paymentMethodsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentMethodsModalLabel">
                    <i class="fas fa-credit-card"></i> Transaksi & Pembayaran
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Transaction Summary Section -->
                <div class="border rounded-0 p-3 mb-4">
                    <h6 class="mb-3">Ringkasan Transaksi</h6>
                    <div class="row mb-2">
                        <div class="col-6">Subtotal:</div>
                        <div class="col-6 text-right">
                            <span id="subtotalDisplay">Rp 0</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6">Diskon:</div>
                        <div class="col-6">
                            <div class="input-group input-group-sm">
                                <?= form_input([
                                    'type' => 'number',
                                    'class' => 'form-control form-control-sm rounded-0',
                                    'id' => 'discountAmount',
                                    'placeholder' => '0',
                                    'step' => '0.01'
                                ]); ?>
                                <div class="input-group-append">
                                    <select class="form-control form-control-sm rounded-0" id="discountType" style="border-left: 0;">
                                        <option value="nominal">Rp</option>
                                        <option value="percent">%</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-0" id="clearDiscount" title="Clear Discount">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-2" id="discountRow" style="display: none;">
                        <div class="col-6">Potongan Diskon:</div>
                        <div class="col-6 text-right">
                            <span id="discountDisplay">Rp 0</span>
                        </div>
                    </div>

                    <!-- Voucher functionality is now handled as a payment method -->
                </div>

                <!-- Payment Methods Section -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Daftar Metode Pembayaran</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-0" id="addPaymentMethod">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>

                <div id="paymentMethods">
                    <!-- Payment methods will be added here -->
                </div>
                
                <!-- Cash Options Section -->
                <div class="mt-3 pt-3 border-top">
                    <h6 class="mb-3">Uang Tunai Options</h6>
                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" id="uangPas">
                                <i class="fas fa-hand-holding-usd"></i> Uang Pas (Sesuai Total)
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-2">
                            <button type="button" class="btn btn-outline-success btn-sm w-100 cash-option" data-amount="1000">
                                Rp 1.000
                            </button>
                        </div>
                        <div class="col-4 mb-2">
                            <button type="button" class="btn btn-outline-success btn-sm w-100 cash-option" data-amount="10000">
                                Rp 10.000
                            </button>
                        </div>
                        <div class="col-4 mb-2">
                            <button type="button" class="btn btn-outline-success btn-sm w-100 cash-option" data-amount="20000">
                                Rp 20.000
                            </button>
                        </div>
                        <div class="col-4 mb-2">
                            <button type="button" class="btn btn-outline-success btn-sm w-100 cash-option" data-amount="50000">
                                Rp 50.000
                            </button>
                        </div>
                        <div class="col-4 mb-2">
                            <button type="button" class="btn btn-outline-success btn-sm w-100 cash-option" data-amount="100000">
                                Rp 100.000
                            </button>
                        </div>
                        <div class="col-4 mb-2">
                            <button type="button" class="btn btn-outline-success btn-sm w-100 cash-option" data-amount="500000">
                                Rp 500.000
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Voucher Summary -->
                <div id="voucherSummary" style="display: none;" class="mt-3 pt-3 border-top">
                    <h6 class="mb-2">Potongan Voucher</h6>
                    <div id="voucherDiscounts">
                        <!-- Voucher discounts will be displayed here -->
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>Total Potongan:</strong></div>
                        <div class="col-6 text-right">
                            <span id="totalVoucherDiscount" class="text-success">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="mt-4 pt-3 border-top">
                    <table class="table table-borderless mb-0" style="font-size: 1.2rem;" border="1">
                        <tbody>
                            <tr style="height: 32px;">
                                <td class="font-weight-bold py-1 align-middle" style="vertical-align: middle;">Total Bayar</td>
                                <td class="text-right font-weight-bold py-1 align-middle" style="vertical-align: middle;">
                                    <span id="grandTotalPayment" style="font-size: 1.2rem; font-weight: bold;">Rp 0</span>
                                </td>
                            </tr>
                            <tr id="remainingPayment" style="display: none; font-size: 1.2rem; height: 32px;">
                                <td class="font-weight-bold py-1 align-middle" style="vertical-align: middle;">Kurang</td>
                                <td class="text-right text-danger font-weight-bold py-1 align-middle" style="vertical-align: middle;">
                                    <span id="remainingAmount" style="font-size: 1.2rem; font-weight: bold;">Rp 0</span>
                                </td>
                            </tr>
                            <tr id="changePayment" style="display: none; font-size: 1.2rem; height: 32px;">
                                <td class="font-weight-bold py-1 align-middle" style="vertical-align: middle;">Kembalian</td>
                                <td class="text-right text-success font-weight-bold py-1 align-middle" style="vertical-align: middle;">
                                    <span id="changeAmount" style="font-size: 1.2rem; font-weight: bold;">Rp 0</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Action Buttons Row -->
                <div class="row w-100 mb-3">
                    <div class="col-3">
                        <button type="button" class="btn btn-success btn-block rounded-0" id="completeTransaction">
                            <i class="fas fa-check"></i> Proses
                        </button>
                    </div>
                    <div class="col-3">
                        <button type="button" class="btn btn-warning btn-block rounded-0" id="saveAsDraft">
                            <i class="fas fa-save"></i> Draft
                        </button>
                    </div>
                    <div class="col-3">
                        <button type="button" class="btn btn-info btn-block rounded-0" onclick="quickPrint()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                    <div class="col-3">
                        <button type="button" class="btn btn-danger btn-block rounded-0" id="cancelTransaction">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </div>

                <!-- Close Button -->
                <div class="w-100 text-center">
                    <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    /* Select2 rounded-0 style */
    .select2-container .select2-selection--single {
        height: 36px !important;
        /* Sesuaikan dengan tinggi input */
        display: flex;
        align-items: center;
        /* Ini akan membuat teks di tengah */
        vertical-align: middle;
        padding-left: 10px;
        border-radius: 0px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        /* Pastikan tidak fix ke line-height tinggi */
        padding-left: 0px !important;
        padding-right: 0px !important;
    }



    /* Cart styling */
    .cart-container {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }

    .cart-header {
        background: #343a40;
        color: white;
        padding: 15px 20px;
        border-bottom: 1px solid #ddd;
    }

    .cart-items {
        padding: 20px;
        min-height: 100px;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
        font-size: 16px;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item-left {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
    }

    .cart-item-qty {
        font-weight: bold;
        color: #007bff;
        min-width: 30px;
        text-align: center;
    }

    .cart-item-name {
        flex: 1;
        color: #333;
        font-weight: 500;
    }

    .cart-item-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .cart-item-subtotal {
        font-weight: bold;
        color: #28a745;
        min-width: 100px;
        text-align: right;
        font-size: 18px;
    }

    .cart-item-actions {
        display: flex;
        gap: 5px;
    }

    .cart-item-actions .btn {
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 4px;
    }

    .empty-cart-message {
        color: #6c757d;
    }

    .empty-cart-message i {
        opacity: 0.5;
    }

    .cart-summary {
        background: #f8f9fa;
        padding: 20px;
        border-top: 1px solid #ddd;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    .summary-row:last-child {
        border-bottom: none;
    }

    .summary-label {
        color: #666;
    }

    .summary-value {
        font-weight: bold;
        color: #333;
    }

    .total-bayar {
        background: #e9ecef;
        padding: 12px 15px;
        margin: 0 -20px -20px -20px;
        border-top: 2px solid #007bff;
    }

    .total-bayar .summary-label,
    .total-bayar .summary-value {
        font-size: 18px;
        color: #007bff;
    }

    #uangPas:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    #uangPas:active {
        transform: translateY(0);
    }

    /* Payment method styling */
    .payment-method-row {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .payment-method-row label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
        font-size: 12px;
    }



    .customer-type-radio {
        margin-bottom: 10px;
    }

    .customer-type-radio .form-check {
        margin-bottom: 5px;
    }

    .scan-anggota-field {
        border-left: 3px solid #007bff;
        padding-left: 15px;
        margin-top: 10px;
    }

    .customer-status-display {
        border-left: 3px solid #28a745;
        margin-bottom: 15px;
    }

    .anggota-info {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px;
        margin-top: 10px;
    }

    .qr-scanner-modal .modal-lg {
        max-width: 800px;
    }

    .qr-scanner-container {
        position: relative;
        background-color: #000;
        border-radius: 8px;
        overflow: hidden;
    }

    .qr-scanner-status {
        margin-top: 15px;
        padding: 10px;
        border-radius: 4px;
        background-color: #f8f9fa;
    }

    .qr-scanner-status .text-success {
        color: #28a745 !important;
    }

    .qr-scanner-status .text-danger {
        color: #dc3545 !important;
    }

    .qr-scanner-status .text-muted {
        color: #6c757d !important;
    }

    /* Flip Camera Button Styling */
    #flipCamera {
        transition: all 0.3s ease;
    }

    #flipCamera:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    #flipCamera:active {
        transform: scale(0.95);
    }

    #flipCamera i {
        margin-right: 5px;
    }

    /* Cart table scrollable styles */
    .cart-table-container {
        max-height: 400px;
        /* Height for approximately 5 items + header + footer */
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .cart-table-container::-webkit-scrollbar {
        width: 8px;
    }

    .cart-table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .cart-table-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .cart-table-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Ensure thead stays fixed */
    .cart-table-container thead th {
        position: sticky;
        top: 0;
        background: #343a40;
        color: white;
        z-index: 10;
    }

    /* Ensure tfoot stays fixed */
    .cart-table-container tfoot th,
    .cart-table-container tfoot td {
        position: sticky;
        bottom: 0;
        background: white;
        z-index: 10;
        border-top: 2px solid #dee2e6;
    }

    /* Add some spacing for better readability */
    .cart-table-container tbody tr:last-child {
        border-bottom: none;
    }

    /* Product Grid Styles */
    .product-grid-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }

    .product-image {
        margin-bottom: 10px;
        text-align: center;
    }

    .product-thumbnail {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }

    .product-info {
        text-align: center;
    }

    .product-grid-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-color: #007bff;
    }

    .product-grid-item .product-name {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 8px;
        color: #333;
    }

    .product-grid-item .product-price {
        font-size: 16px;
        color: #28a745;
        font-weight: bold;
    }

    .product-grid-item .product-category {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    /* Category Tabs */
    .category-tabs-container {
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
    }

    .category-tabs-container::-webkit-scrollbar {
        height: 6px;
    }

    .category-tabs-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .category-tabs-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .category-tabs-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .nav-tabs {
        flex-wrap: nowrap;
        min-width: max-content;
        border-bottom: 1px solid #dee2e6;
    }

    .nav-tabs .nav-item {
        flex-shrink: 0;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-size: 14px;
        padding: 8px 16px;
        white-space: nowrap;
    }

    .nav-tabs .nav-link.active {
        color: #007bff;
        background: none;
        border-bottom: 2px solid #007bff;
    }

    /* Right Panel Styling */
    .cart-area {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }

    /* Essential Performance */
    .product-grid-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-color: #007bff;
    }

    /* Smooth scrolling for categories */
    .category-tabs-container {
        -webkit-overflow-scrolling: touch;
    }

    /* Payment Methods Modal Styling */
    #paymentMethodsModal .modal-lg {
        max-width: 900px;
    }

    #paymentMethodsModal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    #paymentMethodsModal .payment-method-row {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

    /* Payment Note Field Styling */
    .payment-note {
        font-size: 0.875rem;
    }
    
    .payment-note::placeholder {
        color: #6c757d;
        font-style: italic;
    }


</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    // Global variables
    let cart = [];
    let paymentCounter = 1;
    let currentDraftId = null; // Store current draft ID when loading a draft
    let currentTransactionId = null;
    let paymentMethods = [];
    const PPN_PERCENTAGE = <?= $Pengaturan->ppn ?>; // Dynamic PPN from settings (included in price)
    
    // Logo path from Pengaturan
    const COMPANY_LOGO = '<?= !empty($Pengaturan->logo_header) ? base_url("public/file/app/" . $Pengaturan->logo_header) : (!empty($Pengaturan->logo) ? base_url("public/file/app/" . $Pengaturan->logo) : "") ?>';
    const COMPANY_NAME = '<?= esc($Pengaturan->judul_app ?? $Pengaturan->judul ?? "Perusahaan") ?>';

    // Global AJAX error handler for authentication issues
    $(document).ajaxError(function (event, xhr, settings) {
        if (xhr.status === 401) {
            // Authentication failed - show message and redirect to login
            toastr.error('Sesi Anda telah berakhir. Silakan login ulang.');
            setTimeout(function () {
                window.location.href = '<?= base_url('auth/login') ?>';
            }, 2000);
        }
    });

    // Session keep-alive function - ping server every 5 minutes to keep session active
    function keepSessionAlive() {
        $.ajax({
            url: '<?= base_url('transaksi/jual/refresh-session') ?>',
            type: 'GET',
            timeout: 5000,
            success: function (response) {
                if (response.success) {
                    // Session is still valid
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 401) {
                    // Session expired - redirect to login
                    toastr.error('Sesi Anda telah berakhir. Silakan login ulang.');
                    setTimeout(function () {
                        window.location.href = '<?= base_url('auth/login') ?>';
                    }, 2000);
                            } else {
                // Other errors - don't redirect
            }
            }
        });
    }

    // Start session keep-alive (every 5 minutes)
    setInterval(keepSessionAlive, 5 * 60 * 1000);

    $(document).ready(function () {
        // Initialize
        // Only load products if a warehouse is selected
        if ($('#warehouse_id').val()) {
            loadProducts();
        }

        // Initialize payment methods
        addPaymentMethod(); // Add first payment method by default

        // Initialize payment calculation
        calculatePaymentTotals();

        // Event listeners
        $('#productSearch').on('input', function () {
            searchProducts($(this).val());
        });

        $('#searchBtn').on('click', function () {
            searchProducts($('#productSearch').val());
        });

        // Warehouse selection change event
        $('#warehouse_id').on('change', function () {
            loadProducts();
            loadOutletPlatforms(); // Load platforms for selected outlet
        });

        // Barcode scanner integration
        let barcodeBuffer = '';
        let barcodeTimeout;
        let lastInputTime = 0;
        let inputCount = 0;
        let lastScannedBarcode = '';
        let lastScanTime = 0;
        let isBarcodeScan = false;

        $('#productSearch').on('input', function () {
            const currentTime = Date.now();
            const inputValue = $(this).val();
            inputCount++;

            // If input is very fast (typical of barcode scanner), treat it as a scan
            if (currentTime - lastInputTime < 100 && inputValue.length > 5) {
                isBarcodeScan = true;
                // This is likely a barcode scan - clear the timeout and set a new one
                clearTimeout(barcodeTimeout);
                barcodeTimeout = setTimeout(function () {
                    const warehouseId = $('#warehouse_id').val();
                    if (warehouseId) {
                        // Prevent duplicate scans of the same barcode within 2 seconds
                        if (inputValue !== lastScannedBarcode || (currentTime - lastScanTime) > 2000) {
                            lastScannedBarcode = inputValue;
                            lastScanTime = currentTime;
                            findProductByBarcode(inputValue, warehouseId);
                        }
                    }
                }, 300); // Wait 300ms after last input to confirm it's a complete scan
            }

            lastInputTime = currentTime;

            // Only handle manual search if it's NOT a barcode scan
            if (!isBarcodeScan && (inputCount === 1 || currentTime - lastInputTime > 500)) {
                searchProducts(inputValue);
            }
        });

        $('#productSearch').on('keypress', function (e) {
            if (e.which === 13) {
                // Enter key pressed - check if this is a barcode scan
                const scannedValue = $(this).val().trim();

                if (scannedValue.length > 0) {
                    // Check if warehouse is selected
                    const warehouseId = $('#warehouse_id').val();
                    if (!warehouseId) {
                        toastr.warning('Silakan pilih outlet terlebih dahulu');
                        return;
                    }

                    // Try to find product by barcode/code
                    findProductByBarcode(scannedValue, warehouseId);
                }
            }
        });

        // Reset input count when field is focused or cleared
        $('#productSearch').on('focus', function () {
            inputCount = 0;
            lastScannedBarcode = '';
            isBarcodeScan = false;
        });

        $('#productSearch').on('blur', function () {
            inputCount = 0;
            isBarcodeScan = false;
        });

        // Reset duplicate prevention when field is cleared
        $('#productSearch').on('input', function () {
            if ($(this).val() === '') {
                lastScannedBarcode = '';
                lastScanTime = 0;
                isBarcodeScan = false;
            }
        });

        // Manual search trigger (for Enter key and search button)
        $('#productSearch').on('keypress', function (e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                const searchValue = $(this).val().trim();
                if (searchValue.length > 0) {
                    isBarcodeScan = false; // Force manual search mode
                    searchProducts(searchValue);
                }
            }
        });

        // Manual session refresh button
        $('#refreshSession').on('click', function () {
            const $btn = $(this);
            const $icon = $btn.find('i');

            // Show loading state
            $btn.prop('disabled', true);
            $icon.removeClass('fa-sync-alt').addClass('fa-spinner fa-spin');

            // Call session refresh
            $.ajax({
                url: '<?= base_url('transaksi/jual/refresh-session') ?>',
                type: 'GET',
                timeout: 10000,
                success: function (response) {
                    if (response.success) {
                        toastr.success('Session berhasil diperbarui');
                        // Try to reload products to test if authentication is working
                        loadProducts();
                    } else {
                        toastr.error('Gagal memperbarui session');
                    }
                },
                error: function (xhr, status, error) {
                    if (xhr.status === 401) {
                        toastr.error('Session telah berakhir. Silakan login ulang.');
                        setTimeout(function () {
                            window.location.href = '<?= base_url('auth/login') ?>';
                        }, 2000);
                    } else {
                        toastr.error('Gagal memperbarui session: ' + error);
                    }
                },
                complete: function () {
                    // Reset button state
                    $btn.prop('disabled', false);
                    $icon.removeClass('fa-spinner fa-spin').addClass('fa-sync-alt');
                }
            });
        });

        $('#discountAmount').on('input', calculateTotal);
        $('#discountType').on('change', calculateTotal);
        
        // Clear discount button
        $('#clearDiscount').on('click', function() {
            clearDiscount();
        });

        // Voucher input handling
        $('#voucherCode').on('blur', function () {
            validateVoucher($(this).val());
        });

        // Clear voucher button
        $('#clearVoucher').on('click', function () {
            clearVoucher();
        });

        // Clear voucher when input is cleared manually
        $('#voucherCode').on('input', function () {
            if (!$(this).val()) {
                clearVoucher();
            }
        });

        // Allow Enter key to validate voucher
        $('#voucherCode').on('keypress', function (e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                validateVoucher($(this).val());
            }
        });

        // Payment method event listeners - use namespaced events to prevent duplicates
        $('#addPaymentMethod').off('click.payment').on('click.payment', addPaymentMethod);
        $(document).off('click.payment').on('click.payment', '.remove-payment', removePaymentMethod);
        $(document).off('input.payment').on('input.payment', '.payment-amount', calculatePaymentTotals);
        $(document).off('change.payment').on('change.payment', '.payment-platform', handlePaymentPlatformChange);
        
        // Add event handler for payment amount changes to ensure values are captured
        $(document).off('input.payment-capture').on('input.payment-capture', '.payment-amount', function() {
            const amount = parseFloat($(this).val()) || 0;
            const platformId = $(this).closest('.payment-method-row').find('.payment-platform').val();
            console.log('Payment amount changed:', { amount, platformId, paymentRow: $(this).closest('.payment-method-row').data('payment-id') });
        });



        // Clear any existing event handlers to prevent duplicates








        $('#completeTransaction').on('click', function (e) {
            const customerType = $('#selectedCustomerType').val();

            if (customerType === 'anggota' && !$('#selectedCustomerId').val()) {
                e.preventDefault();
                toastr.error('Silakan scan kartu anggota terlebih dahulu');
                $('#scanAnggota').focus();
                return false;
            }

            // Continue with normal transaction flow
            completeTransaction(false);
        });
        $('#saveAsDraft').on('click', function () { completeTransaction(true); });
        $('#newTransaction').on('click', newTransaction);
        $('#holdTransaction').on('click', holdTransaction);
        $('#cancelTransaction').on('click', cancelTransaction);
        $('#showDraftList').on('click', showDraftList);

        // Payment Methods Modal
        $('#openPaymentModal').on('click', function () {
            $('#paymentMethodsModal').modal('show');
        });
        
        // Initialize payment methods when modal opens
        $('#paymentMethodsModal').on('shown.bs.modal', function () {
            initializePaymentMethods();
            
            // Ensure default values are set after initialization
            setTimeout(() => {
                const firstPaymentRow = $('[data-payment-id="1"]');
                if (firstPaymentRow.length) {
                    firstPaymentRow.find('.payment-platform').val('1'); // Default to Cash
                    firstPaymentRow.find('.payment-amount').val('0'); // Default amount
                    firstPaymentRow.find('.payment-note').val(''); // Default empty note
                }
            }, 200);
        });

        // Confirm Payment Button
        $('#confirmPayment').on('click', function () {
            // Close modal and proceed with transaction
            $('#paymentMethodsModal').modal('hide');
            // You can add additional validation here if needed
            toastr.success('Pembayaran dikonfirmasi');
        });

        // Auto clear form when modal is closed
        $('#completeModal').on('hidden.bs.modal', function () {
            clearTransactionForm();
        });
        
        // Clear payment methods when payment modal is closed
        $('#paymentMethodsModal').on('hidden.bs.modal', function () {
            $('#paymentMethods').empty();
            paymentCounter = 0;
        });

        // Enter key to search
        $('#productSearch').on('keypress', function (e) {
            if (e.which === 13) {
                searchProducts($(this).val());
            }
        });

        // Customer type radio button change event
        $('input[name="customerType"]').on('change', function () {
            const customerType = $(this).val();
            $('#selectedCustomerType').val(customerType);

            if (customerType === 'anggota') {
                $('#scanAnggotaGroup').show();
                $('#scanAnggota').focus();
                // Clear any existing customer data
                $('#selectedCustomerId').val('');
                $('#selectedCustomerName').val('');
                $('#anggotaInfo').hide();
                $('#customerStatusDisplay').show();
                $('#customerTypeDisplay').text('Anggota');
                $('#customerInfoDisplay').hide();
            } else {
                $('#scanAnggotaGroup').hide();
                $('#scanAnggota').val('');
                $('#anggotaInfo').hide();
                // Clear customer data for umum
                $('#selectedCustomerId').val('');
                $('#selectedCustomerName').val('');
                $('#customerStatusDisplay').hide();
                $('#customerTypeDisplay').text('Umum');
                $('#customerInfoDisplay').hide();
            }
        });

        // Scan anggota input event
        $('#scanAnggota').on('keypress', function (e) {
            if (e.which === 13) {
                searchAnggota();
            }
        });

        // Search anggota button click
        $('#searchAnggota').on('click', function () {
            searchAnggota();
        });

        // Manual input button for anggota search
        $('#searchAnggota').on('click', function () {
            searchAnggota();
        });

        // Open QR Scanner button click
        $('#openQrScanner').on('click', function () {
            openQrScanner();
        });

        // Flip Camera button click
        $('#flipCamera').on('click', function () {
            flipCamera();
        });

        // Manual input button in QR scanner modal
        $('#manualInputBtn').on('click', function () {
            $('#qrScannerModal').modal('hide');
            $('#scanAnggota').focus();
        });

        // Test QR scan button
        $('#testQrScanBtn').on('click', function () {
            // Simulate a QR scan for testing
            const testData = {
                id_pelanggan: 'TEST001',
                nama: 'Test Anggota',
                nomor_kartu: 'TEST001'
            };
            handleQrScanResult(testData);
        });

        // Test manual QR button
        $('#testManualQrBtn').on('click', function () {
            // Test different QR code formats
            const testFormats = [
                { type: 'Plain text', data: 'MEMBER123' },
                { type: 'JSON with id_pelanggan', data: { id_pelanggan: 'MEMBER123', nama: 'John Doe' } },
                { type: 'JSON with id', data: { id: 'MEMBER456', nama: 'Jane Smith' } },
                { type: 'JSON with kartu', data: { kartu: 'CARD789', nama: 'Bob Wilson' } }
            ];

            const randomFormat = testFormats[Math.floor(Math.random() * testFormats.length)];
            handleQrScanResult(randomFormat.data);
        });

        // Test QR handling button
        $('#testQrBtn').on('click', function () {
            testQrHandling();
        });

        // QR Scanner modal events - use off() to prevent multiple bindings
        $('#qrScannerModal').off('shown.bs.modal hidden.bs.modal').on('shown.bs.modal', function () {
            // Modal is fully shown, scanner will be started by openQrScanner
        });

        $('#qrScannerModal').on('hidden.bs.modal', function () {
            stopQrScanner();
        });

        // Category tabs event listeners
        $('#categoryTabs .nav-link').on('click', function (e) {
            e.preventDefault();
            // Remove active class from all tabs
            $('#categoryTabs .nav-link').removeClass('active');
            // Add active class to clicked tab
            $(this).addClass('active');
            // Load products for selected category
            loadProductsByCategory(this.id);
        });

        // Load more products button
        $('#loadMoreProducts').on('click', function () {
            loadMoreProducts();
        });

        // Clear search button
        $('#clearSearch').on('click', function () {
            $('#productSearch').val('');
            isBarcodeScan = false; // Reset barcode scan flag
            loadProducts();
            toastr.info('Pencarian dibersihkan');
        });

        // Test search button
        $('#testSearch').on('click', function () {

            const warehouseId = $('#warehouse_id').val();
            if (!warehouseId) {
                toastr.warning('Silakan pilih outlet terlebih dahulu');
                return;
            }

            // Test basic search without category
            $.ajax({
                url: '<?= base_url('transaksi/jual/search-items') ?>',
                type: 'POST',
                data: {
                    search: '',
                    warehouse_id: warehouseId,
                    category_id: '',
                    limit: 5
                },
                dataType: 'json',
                success: function (response) {
                    if (response.items && response.items.length > 0) {
                        toastr.success('Test search successful! Found ' + response.items.length + ' products');
                        displayProducts(response.items);
                    } else {
                        toastr.info('Test search successful but no products found');
                    }
                },
                            error: function (xhr, status, error) {
                toastr.error('Test search failed: ' + error);
            }
            });
        });

        // Cash options buttons event handler
        $(document).on('click', '.cash-option', function() {
            const amount = parseFloat($(this).data('amount')) || 0;
            setCashAmount(amount, $(this));
        });
        
        // Add "Uang Pas" (exact amount) button functionality
        $(document).on('click', '#uangPas', function() {
            const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/[^\d]/g, '')) || 0;
            if (grandTotal > 0) {
                setCashAmount(grandTotal, $(this));
            } else {
                toastr.warning('Total transaksi belum ada');
            }
        });

    });

    // Helper function to set cash amount in payment methods
    function setCashAmount(amount, buttonElement) {
        if (amount <= 0) {
            toastr.warning('Jumlah tidak valid');
            return;
        }
        
        // Find the first cash payment method (platform_id = 1)
        let cashPaymentRow = null;
        $('.payment-method-row').each(function() {
            const platformId = $(this).find('.payment-platform').val();
            if (platformId === '1') { // Cash platform
                cashPaymentRow = $(this);
                return false; // Break loop
            }
        });
        
        // If no cash payment method exists, add one
        if (!cashPaymentRow) {
            addPaymentMethod();
            // Get the newly added payment method row
            cashPaymentRow = $('.payment-method-row').last();
            cashPaymentRow.find('.payment-platform').val('1'); // Set to cash
        }
        
        // Set the amount in the cash payment row
        if (cashPaymentRow) {
            cashPaymentRow.find('.payment-amount').val(amount);
            
            // Recalculate totals
            calculatePaymentTotals();
            
            // Show success message
            toastr.success(`Jumlah tunai Rp ${numberFormat(amount)} telah diset`);
            
            // Highlight the button briefly if provided
            if (buttonElement) {
                if (buttonElement.hasClass('btn-outline-success')) {
                    buttonElement.removeClass('btn-outline-success').addClass('btn-success');
                    setTimeout(() => {
                        buttonElement.removeClass('btn-success').addClass('btn-outline-success');
                    }, 1000);
                } else if (buttonElement.hasClass('btn-outline-primary')) {
                    buttonElement.removeClass('btn-outline-primary').addClass('btn-primary');
                    setTimeout(() => {
                        buttonElement.removeClass('btn-primary').addClass('btn-outline-primary');
                    }, 1000);
                }
            }
        } else {
            toastr.error('Gagal menambahkan pembayaran tunai');
        }
    }

    // Payment Methods Functions
    // Store outlet platforms globally
    let outletPlatforms = <?= json_encode($platforms ?? []) ?>;
    
    // Load platforms for selected outlet
    function loadOutletPlatforms() {
        const warehouseId = $('#warehouse_id').val();
        
        if (!warehouseId) {
            // Use default platforms if no outlet selected
            outletPlatforms = <?= json_encode($platforms ?? []) ?>;
            return;
        }

        $.ajax({
            url: '<?= base_url('transaksi/jual/get-outlet-platforms') ?>',
            type: 'POST',
            data: {
                warehouse_id: warehouseId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.platforms) {
                    outletPlatforms = response.platforms;
                    toastr.info(`Memuat ${outletPlatforms.length} platform pembayaran`);
                }
            },
            error: function() {
                // Fallback to default platforms on error
                outletPlatforms = <?= json_encode($platforms ?? []) ?>;
                console.log('Error loading outlet platforms, using defaults');
            }
        });
    }

    function addPaymentMethod() {
        paymentCounter++;

        // Add voucher as a payment method option
        let platformOptions = '<option value="">Pilih Platform</option>';
        if (outletPlatforms && outletPlatforms.length > 0) {
            outletPlatforms.forEach(platform => {
                platformOptions += `<option value="${platform.id}">${platform.platform}</option>`;
            });
        }

        const paymentHtml = `
        <div class="payment-method-row border rounded p-2 mb-2 rounded-0" data-payment-id="${paymentCounter}">
            <div class="row">
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
                <div class="col-md-4">
                    <label>Keterangan</label>
                    <input type="text" class="form-control form-control-sm rounded-0 payment-note" 
                           name="payments[${paymentCounter}][keterangan]" placeholder="Catatan pembayaran">
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm rounded-0 remove-payment d-block" 
                            data-payment-id="${paymentCounter}" ${paymentCounter === 1 ? 'style="display: none !important;"' : ''}>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

        $('#paymentMethods').append(paymentHtml);

        // Initialize the first payment method
        if (paymentCounter === 1) {
            // Set default platform for first payment method (Cash)
            const firstPaymentRow = $(`[data-payment-id="${paymentCounter}"]`);
            firstPaymentRow.find('.payment-platform').val('1'); // Default to Cash
            firstPaymentRow.find('.payment-amount').val('0'); // Default amount
            firstPaymentRow.find('.payment-note').val(''); // Default empty note
        }

        calculatePaymentTotals();
    }
    
    // Initialize payment methods with default options
    function initializePaymentMethods() {
        // Clear existing payment methods
        $('#paymentMethods').empty();
        paymentCounter = 0;
        
        // Add first payment method
        addPaymentMethod();
        
        // Ensure the first payment method has default values
        setTimeout(() => {
            const firstPaymentRow = $('[data-payment-id="1"]');
            if (firstPaymentRow.length) {
                firstPaymentRow.find('.payment-platform').val('1'); // Default to Cash
                firstPaymentRow.find('.payment-amount').val('0'); // Default amount
                firstPaymentRow.find('.payment-note').val(''); // Default empty note
            }
        }, 100);
    }

    // Handle payment platform change (including voucher)
    function handlePaymentPlatformChange() {
        const selectedPlatform = $(this).val();
        const paymentRow = $(this).closest('.payment-method-row');
        const amountInput = paymentRow.find('.payment-amount');
        
                if (selectedPlatform === '4') { // Hardcoded voucher value
            // Show voucher input instead of amount input
            amountInput.hide();
            // Find the label that contains "Jumlah" and change it to "Kode Voucher"
            paymentRow.find('label').filter(function() {
                return $(this).text().trim() === 'Jumlah';
            }).text('Kode Voucher');
            
            // Ensure amount input is hidden
            paymentRow.find('.payment-amount').hide();
            
            // Replace amount input with voucher code input
            if (!paymentRow.find('.voucher-code-input').length) {
                const voucherInput = $(`
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm rounded-0 voucher-code-input" 
                               name="payments[${paymentRow.data('payment-id')}][voucher_code]" 
                               placeholder="Masukkan kode voucher">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-0 clear-voucher" 
                                    title="Clear Voucher">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `);
                amountInput.after(voucherInput);
                
                // Add clear voucher functionality
                voucherInput.find('.clear-voucher').off('click.voucher').on('click.voucher', function() {
                    clearVoucher(paymentRow);
                });
            } else {
                // Show existing voucher input
                paymentRow.find('.voucher-code-input').closest('.input-group').show();
            }
            
            // Ensure voucher input is visible and amount input is hidden
            paymentRow.find('.voucher-code-input').closest('.input-group').show();
            paymentRow.find('.payment-amount').hide();
            
            // Add voucher validation and discount calculation
            paymentRow.find('.voucher-code-input').off('blur.voucher').on('blur.voucher', function() {
                const voucherCode = $(this).val().trim();
                if (voucherCode) {
                    validateVoucherCode(voucherCode, paymentRow);
                }
            });
            
            // Also validate on Enter key press
            paymentRow.find('.voucher-code-input').off('keypress.voucher').on('keypress.voucher', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    const voucherCode = $(this).val().trim();
                    if (voucherCode) {
                        validateVoucherCode(voucherCode, paymentRow);
                    }
                }
            });
        } else {
            // Show regular amount input for other platforms
            amountInput.show();
            // Find the label that contains "Kode Voucher" and change it back to "Jumlah"
            paymentRow.find('label').filter(function() { 
                return $(this).text().trim() === 'Kode Voucher'; 
            }).text('Jumlah');
            paymentRow.find('.voucher-code-input').closest('.input-group').hide();
            
            // Ensure amount input is visible and voucher input is hidden
            paymentRow.find('.payment-amount').show();
            paymentRow.find('.voucher-code-input').closest('.input-group').hide();
        }
        
        calculatePaymentTotals();
    }

    // Validate voucher code and calculate discount
    function validateVoucherCode(voucherCode, paymentRow) {
        // Show loading state
        const voucherInput = paymentRow.find('.voucher-code-input');
        const originalValue = voucherInput.val();
        voucherInput.prop('disabled', true).val('Validating...');
        
        $.ajax({
            url: '<?= base_url('transaksi/jual/validate-voucher') ?>',
            type: 'POST',
            data: {
                voucher_code: voucherCode
            },
            dataType: 'json',
            success: function(response) {
                if (response.valid) {
                    let discountAmount = 0;
                    let discountText = '';
                    const originalVoucherCode = voucherCode; // Store the original input value
                    
                    // Debug: Log response structure
                    console.log('Voucher validation response:', response);
                    
                    // Handle different voucher types
                    if (response.jenis_voucher === 'persen') {
                        // Calculate percentage discount based on grand total
                        const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/\./g, '').replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
                        discountAmount = (grandTotal * response.discount) / 100;
                        discountText = `${response.discount}%`;
                    } else if (response.jenis_voucher === 'nominal') {
                        discountAmount = response.discount_amount || 0;
                        discountText = `Rp ${numberFormat(response.discount_amount)}`;
                    }
                    
                    // Update the amount input with discount amount
                    paymentRow.find('.payment-amount').val(discountAmount);
                    
                    // Show success message
                    toastr.success(`Voucher valid! Potongan: ${discountText}`);
                    
                    // Add voucher info display
                    if (!paymentRow.find('.voucher-info').length) {
                        const voucherInfo = $(`
                            <small class="text-success voucher-info">
                                <i class="fas fa-check-circle"></i> Voucher: ${originalVoucherCode} - Potongan: ${discountText}
                            </small>
                        `);
                        paymentRow.find('.payment-amount').after(voucherInfo);
                    }
                    
                    // Store voucher data
                    paymentRow.data('voucher-id', response.voucher_id);
                    paymentRow.data('voucher-discount', discountAmount);
                    paymentRow.data('voucher-code', originalVoucherCode);
                    
                    // Safely store voucher type with validation
                    const voucherType = response.jenis_voucher || response.voucher_type || 'persen';
                    paymentRow.data('voucher-type', voucherType);
                    
                    console.log('Stored voucher type:', voucherType);
                    
                } else {
                    toastr.error(response.message || 'Voucher tidak valid');
                    voucherInput.val('');
                    paymentRow.find('.payment-amount').val(0);
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Gagal memvalidasi voucher');
                voucherInput.val(originalValue);
                paymentRow.find('.payment-amount').val(0);
            },
            complete: function() {
                voucherInput.prop('disabled', false);
                
                // If validation was successful, clear the "Validating..." text
                if (paymentRow.data('voucher-discount') > 0) {
                    voucherInput.val(paymentRow.data('voucher-code'));
                }
                
                calculatePaymentTotals();
            }
        });
    }
    
    // Clear voucher and reset payment row
    function clearVoucher(paymentRow) {
        // Clear voucher input
        paymentRow.find('.voucher-code-input').val('');
        
        // Reset amount input
        paymentRow.find('.payment-amount').val(0);
        
        // Remove voucher info display
        paymentRow.find('.voucher-info').remove();
        
        // Clear voucher data
        paymentRow.removeData(['voucher-id', 'voucher-discount', 'voucher-code', 'voucher-type']);
        
        // Show amount input again
        paymentRow.find('.payment-amount').show();
        // Find the label that contains "Kode Voucher" and change it back to "Jumlah"
        paymentRow.find('label').filter(function() { 
            return $(this).text().trim() === 'Kode Voucher'; 
        }).text('Jumlah');
        paymentRow.find('.voucher-code-input').closest('.input-group').hide();
        
        // Reset platform selection to empty
        paymentRow.find('.payment-platform').val('');
        
        // Update calculations
        calculatePaymentTotals();
        
        toastr.info('Voucher telah dibersihkan');
    }
    
    // Update voucher summary display
    function updateVoucherSummary(voucherDiscounts, totalDiscount) {
        const voucherSummary = $('#voucherSummary');
        const voucherDiscountsContainer = $('#voucherDiscounts');
        const totalVoucherDiscount = $('#totalVoucherDiscount');
        
        if (voucherDiscounts.length > 0) {
            // Show voucher summary
            voucherSummary.show();
            
            // Update voucher discounts list
            let discountsHtml = '';
            voucherDiscounts.forEach(voucher => {
                discountsHtml += `
                    <div class="row mb-1">
                        <div class="col-6">${voucher.code}</div>
                        <div class="col-6 text-right text-success">- Rp ${numberFormat(voucher.amount)}</div>
                    </div>
                `;
            });
            voucherDiscountsContainer.html(discountsHtml);
            
            // Update total discount
            totalVoucherDiscount.text(`- Rp ${numberFormat(totalDiscount)}`);
        } else {
            // Hide voucher summary
            voucherSummary.hide();
        }
    }

    function removePaymentMethod() {
        const paymentId = $(this).data('payment-id');
        const paymentRow = $(`.payment-method-row[data-payment-id="${paymentId}"]`);
        
        // Clear voucher data if this was a voucher payment
        if (paymentRow.data('voucher-id')) {
            paymentRow.removeData(['voucher-id', 'voucher-discount', 'voucher-code', 'voucher-type']);
        }
        
        paymentRow.remove();
        calculatePaymentTotals();
    }



    function calculatePaymentTotals() {
        let totalPaid = 0;
        // Remove dots (thousand separators) before parsing grand total
        const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/\./g, '').replace(/[^\d,-]/g, '').replace(',', '.')) || 0;

        // Calculate total paid (excluding vouchers)
        $('.payment-amount').each(function () {
            const paymentRow = $(this).closest('.payment-method-row');
            const platform = paymentRow.find('.payment-platform').val();
            
            // Only add to totalPaid if it's NOT a voucher
            if (platform !== '4') { // Hardcoded voucher value
                const amount = parseFloat($(this).val()) || 0;
                totalPaid += amount;
            }
        });
        
        // Handle voucher discounts (vouchers reduce the total amount to be paid)
        let totalVoucherDiscount = 0;
        let voucherDiscounts = [];
        $('.payment-platform').each(function() {
            if ($(this).val() === '4') { // Hardcoded voucher value
                const paymentRow = $(this).closest('.payment-method-row');
                const voucherDiscount = paymentRow.data('voucher-discount') || 0;
                const voucherCode = paymentRow.data('voucher-code') || paymentRow.find('.voucher-code-input').val();
                if (voucherDiscount > 0 && voucherCode) {
                    totalVoucherDiscount += voucherDiscount;
                    voucherDiscounts.push({
                        code: voucherCode,
                        amount: voucherDiscount
                    });
                }
            }
        });
        
        // Update voucher summary display
        updateVoucherSummary(voucherDiscounts, totalVoucherDiscount);
        
        // Apply voucher discount to the amount to be paid
        const adjustedGrandTotal = Math.max(0, grandTotal - totalVoucherDiscount);

        // Update displays with formatted currency (showing dots as thousand separator)
        if ($('#grandTotalPayment').length) {
            $('#grandTotalPayment').text(formatCurrency(adjustedGrandTotal));
        }
        if ($('#totalPaidAmount').length) {
            $('#totalPaidAmount').text(formatCurrency(totalPaid));
        }

        const remaining = adjustedGrandTotal - totalPaid;

        if (remaining > 0) {
            if ($('#remainingAmount').length) {
                $('#remainingAmount').text(formatCurrency(remaining));
                $('#remainingPayment').show();
                $('#changePayment').hide();
            }
        } else if (remaining < 0) {
            if ($('#changeAmount').length) {
                $('#changeAmount').text(formatCurrency(Math.abs(remaining)));
                $('#remainingPayment').hide();
                $('#changePayment').show();
            }
        } else {
            if ($('#remainingPayment').length && $('#changePayment').length) {
                $('#remainingPayment').hide();
                $('#changePayment').hide();
            }
        }
    }












    function loadProducts() {
        const warehouseId = $('#warehouse_id').val();

        if (!warehouseId) {
            $('#productGrid').html(`
                <div class="col-12 text-center text-muted">
                    <i class="fas fa-info-circle"></i> Silakan pilih outlet terlebih dahulu untuk melihat produk
                </div>
        `);
            return;
        }

        // Show loading indicator
        $('#productLoading').show();
        $('#productGrid').hide();
        $('#loadMoreContainer').hide();

        // Clear existing products
        $('#productGrid').empty();

        $.ajax({
            url: '<?= base_url('transaksi/jual/search-items') ?>',
            type: 'POST',
            data: {
                search: '',
                warehouse_id: warehouseId,
                category_id: '',
                limit: <?= $Pengaturan->pagination_limit ?? 20 ?>
            },
            dataType: 'json',
            success: function (response) {
                if (response.items && response.items.length > 0) {
                    displayProducts(response.items);

                    // Show load more button if there are more items
                    const paginationLimit = <?= $Pengaturan->pagination_limit ?? 20 ?>;
                    if (response.items.length >= paginationLimit) {
                        $('#loadMoreContainer').show();
                    }
                } else {
                    $('#productGrid').html('<div class="col-12 text-center text-muted"><i class="fas fa-info-circle"></i> Tidak ada produk tersedia</div>');
                }
            },
            error: function (xhr, status, error) {
                $('#productGrid').html(`
                    <div class="col-12 text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error memuat produk: ${error}
                    </div>
            `);
            },
            complete: function () {
                $('#productLoading').hide();
                $('#productGrid').show();
            }
        });
    }

    function searchProducts(query) {
        const warehouseId = $('#warehouse_id').val();

        if (!warehouseId) {
            toastr.warning('Silakan pilih outlet terlebih dahulu');
            return;
        }

        if (query.length < 2) {
            loadProducts();
            return;
        }

        // Show loading indicator
        $('#productLoading').show();
        $('#productGrid').hide();
        $('#loadMoreContainer').hide();

        // Clear existing products
        $('#productGrid').empty();

        // Force manual search mode
        isBarcodeScan = false;

        $.ajax({
            url: '<?= base_url('transaksi/jual/search-items') ?>',
            type: 'POST',
            data: {
                search: query,
                warehouse_id: warehouseId,
                category_id: '',
                limit: <?= $Pengaturan->pagination_limit ?? 20 ?>
            },
            dataType: 'json',
            success: function (response) {
                if (response.items && response.items.length > 0) {
                    displayProducts(response.items);

                    // Show load more button if there are more items
                    const paginationLimit = <?= $Pengaturan->pagination_limit ?? 20 ?>;
                    if (response.items.length >= paginationLimit) {
                        $('#loadMoreContainer').show();
                    }

                    // Show search results count
                    toastr.info(`Ditemukan ${response.items.length} produk untuk: "${query}"`);
                } else {
                    $('#productGrid').html('<div class="col-12 text-center text-muted"><i class="fas fa-search"></i> Tidak ada produk ditemukan untuk: "' + query + '"</div>');
                    toastr.warning(`Tidak ada produk ditemukan untuk: "${query}"`);
                }
            },
            error: function (xhr, status, error) {
                $('#productGrid').html(`
                    <div class="col-12 text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error mencari produk: ${error}
                    </div>
            `);
                toastr.error('Error mencari produk: ' + error);
            },
            complete: function () {
                $('#productLoading').hide();
                $('#productGrid').show();
            }
        });
    }

    /**
     * Find product by barcode/code and automatically add to cart
     * @param {string} barcode - The scanned barcode or product code
     * @param {string} warehouseId - The selected warehouse ID
     */
    function findProductByBarcode(barcode, warehouseId) {
        if (!barcode || !warehouseId) {
            return;
        }

        // Clean barcode input (remove carriage return, line feed, and extra spaces)
        barcode = barcode.replace(/[\r\n]/g, '').trim();

        if (!barcode) {
            return;
        }

        // Show loading state
        $('#productSearch').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('transaksi/jual/search-items') ?>',
            type: 'POST',
            data: {
                search: barcode,
                warehouse_id: warehouseId
            },
            success: function (response) {
                if (response.items && response.items.length > 0) {
                    const product = response.items[0]; // Get first matching product
                    const stock = product.stok || 0;
                    const statusStok = product.status_stok || '1';

                    // Store product stock info in cache
                    productStockCache[product.id] = {
                        stock: stock,
                        statusStok: statusStok
                    };

                    // Check stock before adding
                    const isStockable = (statusStok === '1' || statusStok === 1);
                    if (isStockable && stock <= 0) {
                        toastr.error('Stok habis! Produk tidak dapat dijual.');
                        $('#productSearch').val('');
                        return;
                    }

                    // Check if product already in cart by ID
                    const existingItemIndex = cart.findIndex(item => item.id === product.id);

                    if (existingItemIndex !== -1) {
                        // Product already exists - check stock before incrementing
                        if (isStockable && stock > 0) {
                            const newQuantity = cart[existingItemIndex].quantity + 1;
                            if (newQuantity > stock) {
                                toastr.error(`Stok tidak mencukupi! Stok tersedia: ${stock}`);
                                $('#productSearch').val('');
                                return;
                            }
                        }
                        cart[existingItemIndex].quantity += 1;
                        cart[existingItemIndex].total = cart[existingItemIndex].quantity * cart[existingItemIndex].price;
                        toastr.success(`Quantity ${product.item} ditambah: ${cart[existingItemIndex].quantity}`);
                    } else {
                        // Add new product to cart
                        const cartItem = {
                            id: product.id,
                            name: product.item,
                            code: product.kode,
                            price: product.harga_jual || 0,
                            quantity: 1,
                            total: product.harga_jual || 0,
                            supplier: product.supplier_nama || product.supplier || '',
                            stock: stock,
                            statusStok: statusStok
                        };

                        cart.push(cartItem);
                        toastr.success(`Produk ditambahkan: ${product.item}`);
                    }

                    // Update cart display and totals
                    updateCartDisplay();
                    calculateTotal();

                    // Also update payment totals
                    calculatePaymentTotals();

                    // Clear search field but don't focus to prevent mobile keyboard
                    $('#productSearch').val('');

                    // Reset barcode scan flag
                    isBarcodeScan = false;

                } else {
                    // Product not found
                    toastr.error(`Produk dengan barcode/kode ${barcode} tidak ditemukan`);
                    $('#productSearch').focus();
                    isBarcodeScan = false;
                }
            },
            error: function (xhr, status, error) {
                toastr.error('Gagal mencari produk: ' + error);
                $('#productSearch').focus();
                isBarcodeScan = false;
            },
            complete: function () {
                // Re-enable search field
                $('#productSearch').prop('disabled', false);
            }
        });
    }

    function displayProducts(products) {
        let html = '';
        const base_url = '<?= base_url() ?>';

        if (products && products.length > 0) {
            products.forEach(function (product) {
                const itemName = product.item || product.nama || product.produk || '-';
                const productCode = product.kode || product.barcode || '';
                const price = product.harga_jual || product.harga || 0;
                const stock = product.stok || 0;
                const description = product.deskripsi || '';
                const statusStok = product.status_stok || '1'; // Default to stockable

                // Determine if product should be blocked (stock = 0 and not non-stockable)
                const isBlocked = (stock <= 0 && statusStok === '1');
                const isNonStockable = (statusStok === '0');

                // Store product stock info in cache
                productStockCache[product.id] = {
                    stock: stock,
                    statusStok: statusStok
                };

                // Determine background color, opacity, and cursor
                let bgColor = '#f8f9fa';
                let opacity = 1;
                let cursorStyle = 'pointer';
                let onClickAction = `onclick="checkVariant(${product.id}, '${itemName.replace(/'/g, "\\'")}', '${product.kode}', ${price}, ${stock}, '${statusStok}')"`;

                if (isBlocked) {
                    bgColor = '#ffebee';
                    opacity = 0.5;
                    cursorStyle = 'not-allowed';
                    onClickAction = `onclick="toastr.error('Stok habis! Produk tidak dapat dijual.')"`;
                } else if (isNonStockable) {
                    bgColor = '#e8f5e9';
                }

                // Stock status logic
                let stockStatus = '';
                let stockClass = '';
                let stockBadgeClass = '';
                let stockBadgeText = '';
                
                if (isBlocked) {
                    stockStatus = 'Stok Habis';
                    stockClass = 'text-danger';
                    stockBadgeClass = 'badge-danger';
                    stockBadgeText = 'Stok Habis';
                } else if (stock <= 5 && statusStok === '1') {
                    stockStatus = 'Stok Rendah';
                    stockClass = 'text-warning';
                    stockBadgeClass = 'badge-warning';
                    stockBadgeText = 'Stok Rendah';
                } else if (isNonStockable) {
                    stockStatus = 'Non-Stockable';
                    stockClass = 'text-info';
                    stockBadgeClass = 'badge-info';
                    stockBadgeText = 'Non-Stockable';
                } else {
                    stockStatus = 'Stockable';
                    stockClass = 'text-success';
                    stockBadgeClass = 'badge-success';
                    stockBadgeText = 'Stockable';
                }

                // Get supplier name
                const supplierName = product.supplier_nama || product.supplier || '';
                
                // Product name with code (like in your image)
                const displayName = productCode ? `${itemName}-${productCode}` : itemName;

                // Determine stock display text and style
                let stockDisplay = isNonStockable ? 'Non-Stockable' : stock;
                let stockTextColor = '';
                if (stockClass === 'text-danger') stockTextColor = '#dc3545';
                else if (stockClass === 'text-warning') stockTextColor = '#ffc107';
                else if (stockClass === 'text-info') stockTextColor = '#17a2b8';
                else if (stockClass === 'text-success') stockTextColor = '#28a745';
                else stockTextColor = '#007bff';

                html += `
                <div class="col-12 mb-2">
                    <div class="product-grid-item border-bottom py-3 px-2" ${onClickAction} style="cursor: ${cursorStyle}; background: ${bgColor}; opacity: ${opacity};">
                        <div class="d-flex align-items-start">
                            <!-- Product Icon -->
                            <div class="product-icon me-3" style="flex-shrink: 0;">
                            ${product.foto
                        ? `<img src="${base_url.replace(/\/$/, '')}/${product.foto.replace(/^\//, '')}" alt="${itemName}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;" class="rounded-0" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="product-image-placeholder" style="width: 40px; height: 40px; background: #e9ecef; display: none; align-items: center; justify-content: center;">
                                        <i class="fas fa-cube text-muted" style="font-size: 18px;"></i>
                                    </div>`
                        : `<div class="product-image-placeholder" style="width: 40px; height: 40px; background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-cube text-muted" style="font-size: 18px;"></i>
                                    </div>`
                    }
                            </div>
                            
                            <!-- Product Info -->
                            <div class="product-info flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1" style="padding-left: 8px;">
                                        <div class="product-info-left" style="flex-grow: 1; text-align: left;">
                                            <div class="product-name" style="font-size: 14px; line-height: 1.3; color: #000; font-weight: 500; text-align: left;">
                                                ${displayName}
                                            </div>
                                            <div class="product-desc" style="font-size: 12px; color: #666; text-align: left;">
                                                ${description ? description : ''}
                                            </div>
                                            ${supplierName ? `<div class="product-supplier" style="font-size: 11px; color: #888; text-align: left; margin-top: 2px;">
                                                <i class="fas fa-truck"></i> Supplier: ${supplierName}
                                            </div>` : ''}
                                            <div class="d-flex align-items-center" style="margin-top: 4px;">
                                                <div class="product-stock" style="font-size: 11px; color: ${stockTextColor}; text-align: left;">
                                                    <i class="fas fa-boxes"></i> Stok: ${stockDisplay} PCS
                                                </div>
                                                <span class="badge ${stockBadgeClass} ml-2" style="font-size: 10px; padding: 2px 6px;">
                                                    ${stockBadgeText}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="product-price" style="font-size: 14px; font-weight: 500; color: #000; margin-left: 15px; text-align: right;">
                                            Rp ${numberFormat(price)}
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            });
        } else {
            html = `
            <div class="col-12 text-center text-muted py-4">
                <i class="fas fa-search fa-2x mb-2"></i>
                <p>Tidak ada produk ditemukan</p>
            </div>
            `;
        }

        $('#productGrid').html(html);
    }

    // Function to load products by category
    function loadProductsByCategory(categoryId) {
        const warehouseId = $('#warehouse_id').val();
        if (!warehouseId) {
            toastr.warning('Silakan pilih outlet terlebih dahulu');
            return;
        }

        // Extract category ID from the tab ID
        const categoryIdMatch = categoryId.match(/category-(\d+)-tab/);

        if (categoryIdMatch) {
            const actualCategoryId = categoryIdMatch[1];
            loadProductsByCategoryId(actualCategoryId);
        } else if (categoryId === 'all-tab') {
            loadProducts(); // Load all products
        }
    }

    // Function to load products by specific category ID with performance optimization
    function loadProductsByCategoryId(categoryId) {
        const warehouseId = $('#warehouse_id').val();
        if (!warehouseId) {
            toastr.warning('Silakan pilih outlet terlebih dahulu');
            return;
        }

        // Show loading indicator
        $('#productLoading').show();
        $('#productGrid').hide();
        $('#loadMoreContainer').hide();

        // Clear existing products
        $('#productGrid').empty();



        $.ajax({
            url: '<?= base_url('transaksi/jual/search-items') ?>',
            type: 'POST',
            data: {
                search: '',
                warehouse_id: warehouseId,
                category_id: categoryId,
                limit: <?= $Pengaturan->pagination_limit ?? 20 ?> // Use pagination limit from database settings
            },
            dataType: 'json',
            success: function (response) {
                if (response.items && response.items.length > 0) {
                    displayProducts(response.items);

                    // Show load more button if there are more items
                    const paginationLimit = <?= $Pengaturan->pagination_limit ?? 20 ?>;
                    if (response.items.length >= paginationLimit) {
                        $('#loadMoreContainer').show();
                    }
                } else {
                    $('#productGrid').html('<div class="col-12 text-center text-muted"><i class="fas fa-info-circle"></i> Tidak ada produk dalam kategori ini</div>');
                }
            },
            error: function (xhr, status, error) {
                if (status !== 'abort') {
                    toastr.error('Gagal memuat produk berdasarkan kategori');
                }
            },
            complete: function () {
                $('#productLoading').hide();
                $('#productGrid').show();
            }
        });
    }

    // Function to load more products (pagination)
    function loadMoreProducts() {
        const warehouseId = $('#warehouse_id').val();
        const activeTab = $('#categoryTabs .nav-link.active');
        const categoryId = activeTab.attr('id');

        if (!warehouseId) {
            toastr.warning('Silakan pilih outlet terlebih dahulu');
            return;
        }

        // Get current product count
        const currentCount = $('#productGrid .product-grid-item').length;

        // Show loading state
        $('#loadMoreProducts').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat...');

        // Get pagination limit from database settings
        const paginationLimit = <?= $Pengaturan->pagination_limit ?? 20 ?>;

        // Extract category ID properly
        let extractedCategoryId = '';
        if (categoryId === 'all-tab') {
            extractedCategoryId = '';
        } else if (categoryId && categoryId.startsWith('category-') && categoryId.endsWith('-tab')) {
            extractedCategoryId = categoryId.replace('category-', '').replace('-tab', '');
        }

        $.ajax({
            url: '<?= base_url('transaksi/jual/search-items') ?>',
            type: 'POST',
            data: {
                search: '',
                warehouse_id: warehouseId,
                category_id: extractedCategoryId,
                limit: paginationLimit,
                offset: currentCount
            },
            dataType: 'json',
            success: function (response) {
                if (response.items && response.items.length > 0) {
                    // Append new products to existing grid
                    const newProducts = response.items.map(product => {
                        const itemName = product.item || product.nama || product.produk || '-';
                        const category = product.kategori || '-';
                        const brand = product.merk || '-';
                        const price = product.harga_jual || product.harga || 0;
                        const stock = product.stok || 0;
                        const description = product.deskripsi || '';
                        const statusStok = product.status_stok || '1'; // Default to stockable
                        const base_url = '<?= base_url() ?>';

                        // Stock status logic (same as displayProducts)
                        const isNonStockable = (statusStok === '0');
                        const isBlocked = (stock <= 0 && statusStok === '1');
                        
                        // Store product stock info in cache
                        productStockCache[product.id] = {
                            stock: stock,
                            statusStok: statusStok
                        };
                        
                        let stockBadgeClass = '';
                        let stockBadgeText = '';
                        let stockDisplay = isNonStockable ? 'Non-Stockable' : stock;
                        let stockTextColor = '#007bff';
                        
                        if (isBlocked) {
                            stockBadgeClass = 'badge-danger';
                            stockBadgeText = 'Stok Habis';
                            stockTextColor = '#dc3545';
                        } else if (stock <= 5 && statusStok === '1') {
                            stockBadgeClass = 'badge-warning';
                            stockBadgeText = 'Stok Rendah';
                            stockTextColor = '#ffc107';
                        } else if (isNonStockable) {
                            stockBadgeClass = 'badge-info';
                            stockBadgeText = 'Non-Stockable';
                            stockTextColor = '#17a2b8';
                        } else {
                            stockBadgeClass = 'badge-success';
                            stockBadgeText = 'Stockable';
                            stockTextColor = '#28a745';
                        }

                        // Same design as the main product grid (displayProducts)
                        return `
                        <div class="col-12 mb-2">
                            <div class="product-grid-item border-bottom py-3 px-2" onclick="checkVariant(${product.id}, '${itemName.replace(/'/g, "\\'")}', '${product.kode}', ${price}, ${stock}, '${statusStok}')" style="cursor: pointer; background: #f8f9fa;">
                                <div class="d-flex align-items-start">
                                    <!-- Product Icon -->
                                    <div class="product-icon me-3" style="flex-shrink: 0;">
                                        ${product.foto
                                            ? `<img src="${base_url.replace(/\/$/, '')}/${product.foto.replace(/^\//, '')}" alt="${itemName}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;" class="rounded-0" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="product-image-placeholder" style="width: 40px; height: 40px; background: #e9ecef; display: none; align-items: center; justify-content: center;">
                                                    <i class="fas fa-cube text-muted" style="font-size: 18px;"></i>
                                                </div>`
                                            : `<div class="product-image-placeholder" style="width: 40px; height: 40px; background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-cube text-muted" style="font-size: 18px;"></i>
                                            </div>`
                                        }
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class="product-info flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1" style="padding-left: 8px;">
                                            <div class="product-info-left" style="flex-grow: 1; text-align: left;">
                                                <div class="product-name" style="font-size: 14px; line-height: 1.3; color: #000; font-weight: 500; text-align: left;">
                                                    ${product.kode ? `${itemName}-${product.kode}` : itemName}
                                                </div>
                                                <div class="product-desc" style="font-size: 12px; color: #666; text-align: left;">
                                                    ${description ? description : ''}
                                                </div>
                                                <div class="d-flex align-items-center" style="margin-top: 4px;">
                                                    <div class="product-stock" style="font-size: 11px; color: ${stockTextColor}; text-align: left;">
                                                        <i class="fas fa-boxes"></i> Stok: ${stockDisplay} PCS
                                                    </div>
                                                    <span class="badge ${stockBadgeClass} ml-2" style="font-size: 10px; padding: 2px 6px;">
                                                        ${stockBadgeText}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="product-price" style="font-size: 14px; font-weight: 500; color: #000; margin-left: 15px; text-align: right;">
                                                Rp ${numberFormat(price)}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                    }).join('');

                    $('#productGrid').append(newProducts);

                    // Hide load more button if we got fewer items than requested
                    if (response.items.length < paginationLimit) {
                        $('#loadMoreContainer').hide();
                    }
                } else {
                    // No more products
                    $('#loadMoreContainer').hide();
                    toastr.info('Tidak ada produk lagi untuk dimuat');
                }
            },
            error: function (xhr, status, error) {
                toastr.error('Gagal memuat produk tambahan');
            },
            complete: function () {
                // Reset button state
                $('#loadMoreProducts').prop('disabled', false).html('<i class="fas fa-arrow-down"></i> Lanjutkan');
            }
        });
    }

    // Function to check for variants and handle add to cart
    function checkVariant(productId, productName, productCode, price, stock, statusStok) {
        // Check stock first
        if (!checkStockBeforeAdd(productId, productName, productCode, price, stock, statusStok)) {
            return;
        }

        $.ajax({
            url: '<?= base_url('transaksi/jual/get_variants') ?>/' + productId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success && response.variants && response.variants.length > 0) {
                    // Show modal with variants
                    let variantHtml = '<div class="list-group">';
                    response.variants.forEach(function (variant) {
                        variantHtml += `
                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="selectVariantToCart(${productId}, '${productName.replace(/'/g, "\\'")}', '${productCode}', ${variant.id}, '${variant.nama.replace(/'/g, "\\'")}', ${variant.harga_jual || 0})">
                        <span>
                            <strong>${variant.nama}</strong><br>
                            <small>Kode: ${variant.kode}</small>
                        </span>
                        <span class="badge badge-primary badge-pill">Rp ${numberFormat(variant.harga_jual || 0)}</span>
                    </button>
                `;
                    });
                    variantHtml += '</div>';
                    $('#variantList').html(variantHtml);
                    $('#variantModal').modal('show');
                } else {
                    // No variants, add directly
                    addToCart(productId, productName, productCode, price, stock, statusStok);
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 401) {
                    // Authentication failed - redirect to login
                    toastr.error('Sesi Anda telah berakhir. Silakan login ulang.');
                    setTimeout(function () {
                        window.location.href = '<?= base_url('auth/login') ?>';
                    }, 2000);
                } else {
                    // Other errors - try to add product directly without variants
                    addToCart(productId, productName, productCode, price, stock, statusStok);
                }
            }
        });
    }

    // Function to add selected variant to cart
    function selectVariantToCart(productId, productName, productCode, variantId, variantName, variantPrice) {
        // Get stock info from cache for the base product
        const cachedProduct = productStockCache[productId];
        const stock = cachedProduct ? cachedProduct.stock : 0;
        const statusStok = cachedProduct ? cachedProduct.statusStok : '1';
        addToCart(productId + '-' + variantId, productName + ' - ' + variantName, productCode, variantPrice, stock, statusStok);
        $('#variantModal').modal('hide');
    }

    function addToCart(productId, productName, productCode, price, stock, statusStok) {
        // Get stock info from cache if not provided
        if (stock === undefined || statusStok === undefined) {
            const cachedProduct = productStockCache[productId];
            if (cachedProduct) {
                stock = cachedProduct.stock || 0;
                statusStok = cachedProduct.statusStok || '1';
            } else {
                // Default values if not in cache
                stock = 0;
                statusStok = '1';
            }
        }

        // Check stock before adding
        if (!checkStockBeforeAdd(productId, productName, productCode, price, stock, statusStok)) {
            return;
        }

        // Check if product already in cart
        const existingItem = cart.find(item => item.id === productId);

        if (existingItem) {
            // Check if adding more would exceed stock (for stockable items)
            const isStockable = (statusStok === '1' || statusStok === 1);
            if (isStockable && stock > 0) {
                const newQuantity = existingItem.quantity + 1;
                if (newQuantity > stock) {
                    toastr.error(`Stok tidak mencukupi! Stok tersedia: ${stock}`);
                    return;
                }
            }
            existingItem.quantity += 1;
            existingItem.total = existingItem.quantity * existingItem.price;

        } else {
            cart.push({
                id: productId,
                name: productName,
                code: productCode,
                price: price,
                quantity: 1,
                total: price,
                stock: stock,
                statusStok: statusStok
            });

        }

        updateCartDisplay();
        calculateTotal();
        $('#productSearch').val('');
    }

    function updateCartDisplay() {
        let html = '';
        let totalItems = 0;

        cart.forEach(function (item, index) {
            totalItems += item.quantity;
            html += `
            <div class="cart-item">
                <div class="cart-item-left">
                    <span class="cart-item-qty">${item.quantity}</span>
                    <div>
                        <span class="cart-item-name">${item.name}</span>
                        ${item.supplier ? `<br/><small style="font-size: 11px; color: #888;"><i class="fas fa-truck"></i> ${item.supplier}</small>` : ''}
                    </div>
                </div>
                <div class="cart-item-right">
                    <span class="cart-item-subtotal">Rp ${numberFormat(item.total)}</span>
                    <div class="cart-item-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity(${index}, -1)" title="Kurang">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity(${index}, 1)" title="Tambah">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeFromCart(${index})" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        });

        if (cart.length === 0) {
            $('#cartTableBody').html(`
                <div class="empty-cart-message" id="emptyCartMessage">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <p class="mb-0">Keranjang belanja kosong</p>
                        <small>Tambahkan produk untuk memulai transaksi</small>
                    </div>
                </div>
            `);
        } else {
            $('#cartTableBody').html(html);
        }
        $('#totalItemsCount').text(totalItems);
    }

    function updateQuantity(index, change) {
        const item = cart[index];
        const newQuantity = Math.max(1, item.quantity + change);
        
        // Check stock if item is stockable
        if (item.statusStok === '1' || item.statusStok === 1) {
            const availableStock = item.stock || 0;
            if (availableStock > 0 && newQuantity > availableStock) {
                toastr.error(`Stok tidak mencukupi! Stok tersedia: ${availableStock}`);
                return;
            }
        }
        
        cart[index].quantity = newQuantity;
        cart[index].total = cart[index].quantity * cart[index].price;
        updateCartDisplay();
        calculateTotal();
    }

    function updateQuantityInput(index, value) {
        const item = cart[index];
        const newQuantity = Math.max(1, parseInt(value) || 1);
        
        // Check stock if item is stockable
        if (item.statusStok === '1' || item.statusStok === 1) {
            const availableStock = item.stock || 0;
            if (availableStock > 0 && newQuantity > availableStock) {
                toastr.error(`Stok tidak mencukupi! Stok tersedia: ${availableStock}`);
                // Reset to current quantity
                $(`#quantityInput_${index}`).val(item.quantity);
                return;
            }
        }
        
        cart[index].quantity = newQuantity;
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
        cart.forEach(function (item) {
            subtotal += item.total;
        });

        $('#subtotalDisplay').text(`Rp ${numberFormat(subtotal)}`);

        // Calculate discount
        const discountType = $('#discountType').val();
        const discountValue = parseFloat($('#discountAmount').val()) || 0;
        let discountAmount = 0;
        
        if (discountType === 'percent') {
            // Percentage discount
            discountAmount = subtotal * (discountValue / 100);
        } else {
            // Nominal discount (fixed amount)
            discountAmount = discountValue;
            // Ensure discount doesn't exceed subtotal
            if (discountAmount > subtotal) {
                discountAmount = subtotal;
            }
        }
        
        const afterDiscount = subtotal - discountAmount;
        
        // Show/hide discount row and update display
        if (discountAmount > 0) {
            $('#discountRow').show();
            $('#discountDisplay').text(`Rp ${numberFormat(discountAmount)}`);
        } else {
            $('#discountRow').hide();
        }

        // Calculate voucher discount
        const voucherType = $('#voucherType').val();
        let voucherDiscountAmount = 0;

        if (voucherType === 'persen') {
            // Percentage voucher
            const voucherDiscountPercent = parseFloat($('#voucherDiscount').val()) || 0;
            voucherDiscountAmount = afterDiscount * (voucherDiscountPercent / 100);
        } else if (voucherType === 'nominal') {
            // Nominal voucher (fixed amount)
            voucherDiscountAmount = parseFloat($('#voucherDiscountAmount').val()) || 0;
            // Ensure voucher discount doesn't exceed the amount after regular discount
            if (voucherDiscountAmount > afterDiscount) {
                voucherDiscountAmount = afterDiscount;
            }
        }

        const afterVoucherDiscount = afterDiscount - voucherDiscountAmount;

        // Show/hide voucher discount row and update display
        if (voucherDiscountAmount > 0) {
            $('#voucherDiscountRow').show();
            $('#voucherDiscountDisplay').text(`Rp ${numberFormat(voucherDiscountAmount)}`);
        } else {
            $('#voucherDiscountRow').hide();
        }

        // Calculate DPP (Tax Base) - extract PPN from the subtotal
        const dppAmount = afterVoucherDiscount * (100 / (100 + PPN_PERCENTAGE)); // Calculate DPP from inclusive price

        // Calculate tax (PPN is included in the price, so we extract it)
        const taxAmount = afterVoucherDiscount * (PPN_PERCENTAGE / (100 + PPN_PERCENTAGE)); // Extract PPN from included price

        // Calculate grand total (subtotal already includes PPN, so grand total equals subtotal)
        const grandTotal = afterVoucherDiscount;

        $('#dppDisplay').text(`Rp ${numberFormat(dppAmount)}`);
        $('#taxDisplay').text(`Rp ${numberFormat(taxAmount)}`);
        $('#grandTotalDisplay').text(`Rp ${numberFormat(grandTotal)}`);

        // Update payment totals when grand total changes
        calculatePaymentTotals();
    }

    function clearVoucher() {
        $('#voucherCode').val('');
        $('#voucherInfo').text('').removeClass('text-success text-danger');
        $('#voucherDiscount').val(0);
        $('#voucherId').val('');
        $('#voucherType').val('');
        $('#voucherDiscountAmount').val(0);
        $('#voucherDiscountRow').hide();
        calculateTotal();
    }
    
    function clearDiscount() {
        $('#discountAmount').val('');
        $('#discountType').val('nominal');
        $('#discountRow').hide();
        calculateTotal();
    }

    function validateVoucher(voucherCode) {
        if (!voucherCode || voucherCode.trim() === '') {
            clearVoucher();
            return;
        }

        // Check if voucher is already applied
        const currentVoucherId = $('#voucherId').val();
        if (currentVoucherId && currentVoucherId !== '') {
            toastr.warning('Voucher sudah diterapkan. Silakan clear voucher terlebih dahulu.');
            return;
        }

        // Show loading state
        $('#voucherInfo').text('Validating voucher...').removeClass('text-success text-danger').addClass('text-info');

        $.ajax({
            url: '<?= base_url('transaksi/jual/validate-voucher') ?>',
            type: 'POST',
            data: {
                voucher_code: voucherCode
            },
            success: function (response) {
                if (response.valid) {
                    let displayText = '';
                    if (response.jenis_voucher === 'persen') {
                        displayText = `Voucher valid: ${response.discount}% diskon`;
                    } else if (response.jenis_voucher === 'nominal') {
                        displayText = `Voucher valid: Rp ${numberFormat(response.discount_amount)} diskon`;
                    }

                    // Add additional info if available
                    if (response.remaining_usage !== undefined) {
                        displayText += ` (Tersisa: ${response.remaining_usage} penggunaan)`;
                    }

                    $('#voucherInfo').text(displayText).removeClass('text-danger text-info').addClass('text-success');
                    $('#voucherDiscount').val(response.discount);
                    $('#voucherId').val(response.voucher_id);
                    $('#voucherType').val(response.jenis_voucher);
                    $('#voucherDiscountAmount').val(response.discount_amount);
                    calculateTotal();

                    // Show success message with voucher details
                    let successMsg = 'Voucher berhasil diterapkan';
                    if (response.jenis_voucher === 'persen') {
                        successMsg += ` - Diskon ${response.discount}%`;
                    } else if (response.jenis_voucher === 'nominal') {
                        successMsg += ` - Diskon Rp ${numberFormat(response.discount_amount)}`;
                    }
                    toastr.success(successMsg);
                } else {
                    $('#voucherInfo').text(response.message || 'Voucher tidak valid').removeClass('text-success text-info').addClass('text-danger');
                    clearVoucher();

                    // Show error message
                    toastr.error(response.message || 'Voucher tidak valid');
                }
            },
            error: function (xhr, status, error) {
                $('#voucherInfo').text('Error validasi voucher').removeClass('text-success text-info').addClass('text-danger');
                clearVoucher();

                // Show error message
                if (xhr.status === 0) {
                    toastr.error('Tidak dapat terhubung ke server');
                } else if (xhr.status === 500) {
                    toastr.error('Error server: ' + error);
                } else {
                    toastr.error('Error validasi voucher: ' + error);
                }
            }
        });
    }

    // Currency formatting function
    function formatCurrency(amount) {
        return `Rp ${numberFormat(amount)}`;
    }

    function numberFormat(number) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(Math.round(number || 0));
    }

    function completeTransaction(isDraft = false) {
        if (cart.length === 0) {
            toastr.error('Keranjang belanja kosong');
            return;
        }

        const outletId = $('#warehouse_id').val();
        if (!outletId) {
            toastr.error('Outlet belum dipilih');
            return;
        }

        // Calculate grand total (needed for both draft and completed transactions)
        const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/[^\d]/g, '')) || 0;
        
        // Calculate voucher discounts
        let totalVoucherDiscount = 0;
        $('.payment-platform').each(function() {
            if ($(this).val() === '4') { // Hardcoded voucher value
                const paymentRow = $(this).closest('.payment-method-row');
                const voucherDiscount = paymentRow.data('voucher-discount') || 0;
                if (voucherDiscount > 0) {
                    totalVoucherDiscount += voucherDiscount;
                }
            }
        });
        
        // Calculate adjusted grand total (after voucher discounts)
        const adjustedGrandTotal = Math.max(0, grandTotal - totalVoucherDiscount);

        // Initialize payment variables (needed for both draft and completed transactions)
        let paymentMethods = [];
        let totalPaymentAmount = 0;

        // If it's a draft, skip payment validation
        if (!isDraft) {
            // Check if voucher is applied and mark it as used
            const voucherId = $('#voucherId').val();
            if (voucherId && voucherId !== '') {
                // Voucher will be marked as used in the backend when transaction is completed
            }

            // Validate payment methods
            let hasValidPayment = false;

            $('.payment-method-row').each(function () {
                const platformId = $(this).find('.payment-platform').val();
                const amount = parseFloat($(this).find('.payment-amount').val()) || 0;

                console.log('Validating payment method:', { platformId, amount });

                if (platformId && amount > 0) {
                    // Determine payment type based on platform (matching backend expectations)
                    let paymentType = 'platform';
                    if (platformId === '4') { // Hardcoded voucher value
                        // Vouchers are NOT payment methods - they are discounts
                        // Don't add them to paymentMethods array
                        // They are already handled in the voucher discount calculation
                    } else if (platformId === '1') {
                        paymentType = '1'; // Backend expects '1' for cash
                        hasValidPayment = true;
                        const note = $(this).find('.payment-note').val() || '';
                        const paymentMethod = {
                            platform_id: platformId,
                            amount: amount,
                            type: paymentType,
                            keterangan: note
                        };
                        console.log('Adding cash payment method:', paymentMethod);
                        paymentMethods.push(paymentMethod);
                        totalPaymentAmount += amount;
                    } else if (platformId === '2') {
                        paymentType = '2'; // Backend expects '2' for transfer
                        hasValidPayment = true;
                        const note = $(this).find('.payment-note').val() || '';
                        const paymentMethod = {
                            platform_id: platformId,
                            amount: amount,
                            type: paymentType,
                            keterangan: note
                        };
                        console.log('Adding transfer payment method:', paymentMethod);
                        paymentMethods.push(paymentMethod);
                        totalPaymentAmount += amount;
                    } else if (platformId === '3') {
                        paymentType = '3'; // Backend expects '3' for credit/piutang
                        hasValidPayment = true;
                        const note = $(this).find('.payment-note').val() || '';
                        const paymentMethod = {
                            platform_id: platformId,
                            amount: amount,
                            type: paymentType,
                            keterangan: note
                        };
                        console.log('Adding credit payment method:', paymentMethod);
                        paymentMethods.push(paymentMethod);
                        totalPaymentAmount += amount;
                    } else {
                        // For other platform IDs from database
                        paymentType = 'platform';
                        hasValidPayment = true;
                        const note = $(this).find('.payment-note').val() || '';
                        const paymentMethod = {
                            platform_id: platformId,
                            amount: amount,
                            type: paymentType,
                            keterangan: note
                        };
                        console.log('Adding platform payment method:', paymentMethod);
                        paymentMethods.push(paymentMethod);
                        totalPaymentAmount += amount;
                    }
                }
            });

            if (!hasValidPayment) {
                toastr.error('Minimal harus ada satu platform pembayaran dengan jumlah > 0. Silakan isi jumlah pembayaran.');
                return;
            }

            if (totalPaymentAmount < adjustedGrandTotal) {
                toastr.error(`Jumlah bayar (${formatCurrency(totalPaymentAmount)}) kurang dari total (${formatCurrency(adjustedGrandTotal)})`);
                return;
            }
        } 
        // End of draft check

        // Prepare transaction data
        // Clean cart data to ensure it can be serialized
        const cleanCart = cart.map(item => ({
            id: item.id,
            name: item.name,
            quantity: parseInt(item.quantity) || 0,
            price: parseFloat(item.price) || 0,
            total: parseFloat(item.total) || 0,
            kode: item.kode || '',
            harga_beli: parseFloat(item.harga_beli) || 0,
            satuan: item.satuan || 'PCS',
            kategori: item.kategori || '',
            merk: item.merk || ''
        }));

        // Payment methods now include keterangan field for tbl_trans_jual_plat.keterangan

        // Collect voucher information from payment methods
        let voucherInfo = null;
        $('.payment-platform').each(function() {
            if ($(this).val() === '4') { // Hardcoded voucher value
                const paymentRow = $(this).closest('.payment-method-row');
                const voucherCode = paymentRow.data('voucher-code');
                const voucherDiscount = paymentRow.data('voucher-discount');
                const voucherId = paymentRow.data('voucher-id');
                const voucherType = paymentRow.data('voucher-type'); // Get voucher type from payment row data
                
                console.log('Voucher data found:', { voucherCode, voucherDiscount, voucherId, voucherType });
                
                if (voucherCode && voucherDiscount > 0) {
                    // Backend expects voucher_discount as percentage, not amount
                    // Calculate percentage: (discount amount / grand total) * 100
                    const voucherPercentage = (voucherDiscount / grandTotal) * 100;
                    
                    voucherInfo = {
                        voucher_code: voucherCode,
                        voucher_discount: voucherPercentage, // Send as percentage
                        voucher_id: voucherId,
                        voucher_type: voucherType || 'persen' // Use stored voucher type or default to 'persen'
                    };
                    
                    console.log('Voucher info prepared:', voucherInfo);
                }
            }
        });

        // Calculate discount percentage from amount
        const discountAmount = parseFloat($('#discountAmount').val()) || 0;
        const discountType = $('#discountType').val() || 'nominal';
        let discountPercent = 0;
        
        if (discountAmount > 0 && discountType === 'persen') {
            discountPercent = discountAmount;
        } else if (discountAmount > 0 && discountType === 'nominal') {
            // Convert nominal discount to percentage for backend
            discountPercent = (discountAmount / grandTotal) * 100;
        }

        // Get warehouse ID
        const warehouse_id = $('#warehouse_id').val();
        
        // Validate required fields
        if (!warehouse_id) {
            toastr.error('Gudang harus dipilih');
            return;
        }
        
        if (isDraft === false && (!paymentMethods || paymentMethods.length === 0)) {
            toastr.error('Metode pembayaran harus diisi');
            return;
        }
        
        // Calculate adjusted grand total after voucher discount
        const transactionAdjustedTotal = grandTotal - (voucherInfo ? voucherInfo.voucher_discount * grandTotal / 100 : 0);
        
        if (isDraft === false && totalPaymentAmount < transactionAdjustedTotal) {
            toastr.error(`Jumlah bayar (${formatCurrency(totalPaymentAmount)}) kurang dari total setelah voucher (${formatCurrency(transactionAdjustedTotal)})`);
            return;
        }

        // Get CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="csrf_token"]').val();
        
        const transactionData = {
            cart: cleanCart,
            customer_id: $('#selectedCustomerId').val() || null,
            customer_type: $('#selectedCustomerType').val() || 'umum',
            customer_name: $('#selectedCustomerName').val() || null,
            warehouse_id: warehouse_id,
            discount_percent: discountPercent, // Backend expects this field
            voucher_code: voucherInfo ? voucherInfo.voucher_code : null,
            voucher_discount: voucherInfo ? voucherInfo.voucher_discount : 0,
            voucher_id: voucherInfo ? voucherInfo.voucher_id : null,
            voucher_type: voucherInfo ? voucherInfo.voucher_type : null, // Add voucher type
            payment_methods: isDraft ? [] : paymentMethods,
            total_amount_received: isDraft ? 0 : totalPaymentAmount,
            grand_total: transactionAdjustedTotal, // Send adjusted total as grand_total
            is_draft: isDraft,
            draft_id: currentDraftId, // Include draft ID if converting from draft
            csrf_token: csrfToken // Add CSRF token
        };

        // Validate payment methods structure
        let hasInvalidPaymentMethod = false;
        
        // Debug: Log payment methods being sent
        console.log('Payment methods being sent:', paymentMethods);
        console.log('Payment methods length:', paymentMethods.length);
        
        // Also log the actual DOM elements to see what's there
        console.log('Payment method rows in DOM:', $('.payment-method-row').length);
        $('.payment-method-row').each(function(index) {
            const platformId = $(this).find('.payment-platform').val();
            const amount = $(this).find('.payment-amount').val();
            const note = $(this).find('.payment-note').val();
            console.log(`DOM Payment method ${index}:`, { platformId, amount, note, paymentId: $(this).data('payment-id') });
        });
        
        paymentMethods.forEach((pm, index) => {
            console.log(`Payment method ${index}:`, pm);
            
            if (!pm.type) {
                console.log(`Payment method ${index} missing type`);
                hasInvalidPaymentMethod = true;
            }
            if (!pm.platform_id) {
                console.log(`Payment method ${index} missing platform_id`);
                hasInvalidPaymentMethod = true;
            }
            if (typeof pm.amount === 'undefined') {
                console.log(`Payment method ${index} missing amount`);
                hasInvalidPaymentMethod = true;
            }
            // Note: keterangan is optional, so no validation needed
        });
        
        if (hasInvalidPaymentMethod) {
            toastr.error('Ada kesalahan dalam struktur metode pembayaran. Silakan coba lagi.');
            return;
        }
                
        // Show loading state
        const buttonId = isDraft ? '#saveAsDraft' : '#completeTransaction';
        const buttonText = isDraft ? 'Menyimpan Draft...' : 'Memproses...';
        $(buttonId).prop('disabled', true).html(`<i class="fas fa-spinner fa-spin"></i> ${buttonText}`);

        // Debug: Log transaction data being sent
        console.log('Transaction data being sent:', transactionData);
        
        // Send transaction to server
        $.ajax({
            url: '<?= base_url('transaksi/jual/process-transaction') ?>',
            type: 'POST',
            data: transactionData,
            success: function (response) {
                if (response.success) {
                    if (isDraft) {
                        // Draft transaction saved successfully
                        toastr.success('Draft transaksi berhasil disimpan!');

                        // Close payment methods modal
                        $('#paymentMethodsModal').modal('hide');

                        // Clear form for next transaction
                        clearTransactionForm();
                    } else {
                        // Close payment methods modal
                        $('#paymentMethodsModal').modal('hide');

                        // Normal transaction completion
                        $('#finalTotal').text(`Rp ${numberFormat(response.total)}`);

                        // Build payment methods summary
                        let paymentSummary = '';
                        paymentMethods.forEach(pm => {
                            let paymentLabel = 'Platform';
                            if (pm.type === '1') paymentLabel = 'Tunai';
                            else if (pm.type === '2') paymentLabel = 'Transfer';
                            else if (pm.type === '3') paymentLabel = 'Piutang';
                            else if (pm.type === 'platform') paymentLabel = 'Platform';
                            
                            const note = pm.keterangan ? ` (${pm.keterangan})` : '';
                            paymentSummary += `${paymentLabel}: ${formatCurrency(pm.amount)}${note}<br>`;
                        });
                        
                        // Add voucher information if present
                        if (voucherInfo) {
                            paymentSummary += `<br><strong>Voucher:</strong> ${voucherInfo.voucher_code} (-${formatCurrency(voucherInfo.voucher_discount)})<br>`;
                        }
                        
                        $('#finalPaymentMethod').html(paymentSummary);
                        $('#completeModal').modal('show');

                        toastr.success(response.message);
                    }
                } else {
                    toastr.error(response.message || 'Gagal memproses transaksi');
                }
            },
            error: function (xhr, status, error) {
                // Try to parse response for more details
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        toastr.error('Backend Error: ' + response.message);
                    } else if (response.error) {
                        toastr.error('Backend Error: ' + response.error);
                    } else {
                        toastr.error('Terjadi kesalahan saat memproses transaksi');
                    }
                } catch (e) {
                    toastr.error('Terjadi kesalahan saat memproses transaksi');
                }
            },
            complete: function () {
                // Reset button state
                $('#completeTransaction').prop('disabled', false).html('<i class="fas fa-check"></i> Proses');
                $('#saveAsDraft').prop('disabled', false).html('<i class="fas fa-save"></i> Draft');
            }
        });
    }

    function newTransaction() {
        cart = [];
        currentDraftId = null; // Clear current draft ID
        updateCartDisplay();
        calculateTotal();

        // Reset customer selection
        $('#customerTypeUmum').prop('checked', true).trigger('change');
        $('#selectedCustomerId').val('');
        $('#selectedCustomerName').val('');
        $('#selectedCustomerType').val('umum');
        $('#customerStatusDisplay').hide();
        $('#customerInfoDisplay').hide();
        $('#anggotaInfo').hide();

        $('#discountAmount').val('');
        $('#discountType').val('nominal');
        $('#discountRow').hide();
        $('#voucherCode').val('');
        $('#voucherInfo').text('');
        $('#voucherDiscountRow').hide();
        $('#paymentMethod').val('');
        // Clear payment amounts and notes in all payment method rows
        $('.payment-amount').val('');
        $('.payment-note').val('');
        // Clear voucher data from all payment rows
        $('.payment-method-row').each(function() {
            $(this).removeData(['voucher-id', 'voucher-discount', 'voucher-code', 'voucher-type']);
        });
        
        // Reset payment methods to default
        $('#paymentMethods').empty();
        paymentMethods = [];
        paymentCounter = 0;
        addPaymentMethod(); // Add first payment method by default
        
        // Ensure the first payment method has default values
        setTimeout(() => {
            const firstPaymentRow = $('[data-payment-id="1"]');
            if (firstPaymentRow.length) {
                firstPaymentRow.find('.payment-platform').val('1'); // Default to Cash
                firstPaymentRow.find('.payment-amount').val('0'); // Default amount
                firstPaymentRow.find('.payment-note').val(''); // Default empty note
            }
        }, 100);
        $('#productSearch').val('');

        // Reset warehouse selection and show message
        $('#warehouse_id').val('');
        $('#productListTable tbody').html(`
        <tr id="noWarehouseMessage">
            <td colspan="5" class="text-center text-muted">
                <i class="fas fa-info-circle"></i> Silakan pilih outlet terlebih dahulu untuk melihat produk
            </td>
        </tr>
    `);

        $('#productSearch').focus();
    }

    function clearTransactionForm() {
        // Clear cart
        cart = [];
        currentDraftId = null; // Clear current draft ID
        updateCartDisplay();

        // Reset customer selection
        $('#customerTypeUmum').prop('checked', true).trigger('change');
        $('#selectedCustomerId').val('');
        $('#selectedCustomerName').val('');
        $('#selectedCustomerType').val('umum');
        $('#customerStatusDisplay').hide();
        $('#customerInfoDisplay').hide();
        $('#anggotaInfo').hide();

        // Clear discount and voucher fields
        $('#discountAmount').val('');
        $('#discountType').val('nominal');
        $('#discountRow').hide();
        $('#voucherCode').val('');
        $('#voucherInfo').text('').removeClass('text-success text-danger');
        $('#voucherDiscount').val('0');
        $('#voucherId').val('');
        $('#voucherType').val('');
        $('#voucherDiscountAmount').val(0);
        $('#voucherDiscountRow').hide();
        
        // Clear voucher data from all payment rows
        $('.payment-method-row').each(function() {
            $(this).removeData(['voucher-id', 'voucher-discount', 'voucher-code', 'voucher-type']);
        });

        // Reset payment methods
        $('#paymentMethods').empty();
        paymentMethods = [];
        paymentCounter = 0;
        addPaymentMethod(); // Add first payment method by default
        
        // Ensure the first payment method has default values
        setTimeout(() => {
            const firstPaymentRow = $('[data-payment-id="1"]');
            if (firstPaymentRow.length) {
                firstPaymentRow.find('.payment-platform').val('1'); // Default to Cash
                firstPaymentRow.find('.payment-amount').val('0'); // Default amount
                firstPaymentRow.find('.payment-note').val(''); // Default empty note
            }
        }, 100);

        // Clear product search
        $('#productSearch').val('');

        // Reset warehouse selection and show message
        $('#warehouse_id').val('');
        $('#productListTable tbody').html(`
        <tr id="noWarehouseMessage">
            <td colspan="5" class="text-center text-muted">
                <i class="fas fa-info-circle"></i> Silakan pilih outlet terlebih dahulu untuk melihat produk
            </td>
        </tr>
    `);

        // Recalculate totals
        calculateTotal();

        // Don't focus on product search to prevent mobile keyboard
    }

    function holdTransaction() {
        // Save current transaction to session/localStorage for later retrieval
        const transactionData = {
            cart: cart,
            customer_id: $('#selectedCustomerId').val(),
            customer_type: $('#selectedCustomerType').val(),
            customer_name: $('#selectedCustomerName').val(),
            discount_amount: $('#discountAmount').val(),
            discount_type: $('#discountType').val(),
            voucher: $('#voucherCode').val(),
            paymentMethod: $('#paymentMethod').val()
        };

        localStorage.setItem('heldTransaction', JSON.stringify(transactionData));
        toastr.success('Transaksi ditahan');
        newTransaction();
    }

    function cancelTransaction() {
        if (confirm('Yakin ingin membatalkan transaksi ini?')) {
            currentDraftId = null; // Clear current draft ID
            newTransaction();
        }
    }

    /**
     * Generate receipt HTML content (dot matrix style, matches provided sample)
     */
    function generateReceiptHTML(transactionData) {
        // Destructure transaction data
        const {
            no_nota,
            customer_name,
            customer_type,
            items,
            subtotal,
            discount,
            voucher,
            ppn,
            total,
            payment_methods,
            date,
            outlet,
            user,
            cashier,
            sales_type = 'Normal'
        } = transactionData;

        // Helper for right-align numbers
        function padLeft(str, len) {
            str = String(str);
            return ' '.repeat(Math.max(0, len - str.length)) + str;
        }

        // Helper for left-align
        function padRight(str, len) {
            str = String(str);
            return str + ' '.repeat(Math.max(0, len - str.length));
        }

        // Format date (if not already formatted)
        let dateStr = date;
        if (date && date instanceof Date) {
            dateStr = date.toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }

        // Items block
        let itemsHTML = '';
        items.forEach(item => {
            // Item name (first line)
            itemsHTML += `<div style="font-family:monospace;">${padRight(item.name, 32)}</div>`;
            // Second line: variant/notes if any (not in sample, but can be added)
            // Third line: qty x price
            itemsHTML += `<div style="font-family:monospace;">
                ${item.quantity}x ${padLeft(numberFormat(item.price), 8)}
                ${padLeft(numberFormat(item.total), 16)}
            </div>`;
        });

        // Payment block - show all payment methods
        let paymentHTML = '';
        let totalPayment = 0;
        let change = 0;
        if (payment_methods && payment_methods.length > 0) {
            paymentHTML += `<div style="font-family:monospace;font-weight:bold;">Metode Pembayaran:</div>`;
            payment_methods.forEach(pm => {
                let methodName = pm.platform || pm.name || '';
                if (!methodName) {
                    // Fallback to type-based naming
                    if (pm.type === '1' || pm.type === 'cash') methodName = 'Tunai';
                    else if (pm.type === '2' || pm.type === 'non_tunai') methodName = 'Non Tunai';
                    else if (pm.type === '3' || pm.type === 'piutang' || pm.type === 'credit') methodName = 'Piutang';
                    else if (pm.type === 'voucher') methodName = 'Voucher';
                    else methodName = pm.type || 'Lainnya';
                }
                const amount = parseFloat(pm.amount || pm.nominal || 0);
                const notes = pm.keterangan || pm.notes || '';
                paymentHTML += `<div style="font-family:monospace;">
                    ${padRight(methodName, 16)}${padLeft(numberFormat(amount), 16)}
                </div>`;
                if (notes) {
                    paymentHTML += `<div style="font-family:monospace;font-size:11px;padding-left:4px;">${notes}</div>`;
                }
                totalPayment += amount;
            });
            change = totalPayment - total;
            if (change < 0) change = 0;
        } else {
            // Fallback if no payment methods provided
            paymentHTML += `<div style="font-family:monospace;">
                ${padRight('Tunai', 16)}${padLeft(numberFormat(total), 16)}
            </div>`;
            totalPayment = total;
            change = 0;
        }

        // Get member number from customer data if available
        const memberNumber = transactionData.customer_code || transactionData.no_anggota || '';
        const storeName = transactionData.store_name || transactionData.nama_toko || outlet || COMPANY_NAME;
        const paymentNotes = transactionData.payment_notes || transactionData.catatan || '';

        // Compose HTML
        return `
<div style="font-family:monospace; font-size:13px; max-width:300px; margin:auto;">
${COMPANY_LOGO ? `<div style="text-align:center;margin-bottom:8px;"><img src="${COMPANY_LOGO}" alt="Logo" style="max-height:60px;max-width:100%;object-fit:contain;"></div>` : ''}
<div style="text-align:center;font-weight:bold;margin-bottom:8px;">${storeName}</div>
<hr style="border:0;border-top:1px dashed #000;margin:4px 0;">
${padRight('No. Nota', 12)}: ${no_nota || '-'}<br>
${padRight('Tanggal', 12)}: ${dateStr || '-'}<br>
${padRight('Waktu', 12)}: ${dateStr ? dateStr.split(' ')[1] || '' : '-'}<br>
${padRight('Kasir', 12)}: ${cashier || user || '-'}<br>
${customer_name && customer_name !== 'Umum' ? `${padRight('Pelanggan', 12)}: ${customer_name}<br>` : ''}
${memberNumber ? `${padRight('No. Anggota', 12)}: ${memberNumber}<br>` : ''}
${paymentNotes ? `${padRight('Catatan', 12)}: ${paymentNotes}<br>` : ''}
<hr style="border:0;border-top:1px dashed #000;margin:4px 0;">
<div style="text-align:center;font-weight:bold;">** REPRINT BILL **</div>
<hr style="border:0;border-top:1px dashed #000;margin:4px 0;">
${itemsHTML}
<hr style="border:0;border-top:1px dashed #000;margin:4px 0;">
<div style="font-family:monospace;">
Total Item ${items.length}
</div>
<hr style="border:0;border-top:1px dashed #000;margin:4px 0;">
<div style="font-family:monospace;">
${padRight('Total', 8)}${padLeft(numberFormat(total), 24)}
</div>
${paymentHTML}
<div style="font-family:monospace;">
${padRight('Change', 8)}${padLeft(numberFormat(change), 24)}
</div>
<hr style="border:0;border-top:1px dashed #000;margin:4px 0;">
<div style="text-align:center;font-size:11px;margin-top:8px;">Terima Kasih</div>
</div>
        `;
    }

    /**
     * Quick print function for current transaction
     */
    function quickPrint() {
        if (cart.length === 0) {
            toastr.error('Tidak ada transaksi untuk dicetak');
            return;
        }

        // Show print options modal
        $('#printOptionsModal').modal('show');
    }

    /**
     * Print draft transaction
     */
    function printDraft(draftId) {
        // Get draft data and print
        $.ajax({
            url: '<?= base_url('transaksi/jual/get-draft/') ?>' + draftId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const draft = response.draft;
                    const transactionData = {
                        no_nota: draft.no_nota,
                        customer_name: draft.customer_name || 'Umum',
                        customer_type: draft.customer_type || 'umum',
                        items: draft.items,
                        subtotal: draft.total * (100 / (100 + PPN_PERCENTAGE)), // Calculate subtotal from total
                        discount_amount: draft.discount_amount || 0,
                        discount_type: draft.discount_type || 'nominal',
                        voucher: draft.voucher_code || '',
                        ppn: PPN_PERCENTAGE,
                        total: draft.total,
                        payment_methods: [],
                        date: new Date(draft.created_at).toLocaleString('id-ID'),
                        outlet: 'Draft'
                    };

                    // Show print options
                    $('#printOptionsModal').modal('show');
                    // Store draft data for printing
                    window.currentPrintData = transactionData;
                } else {
                    toastr.error('Gagal memuat data draft');
                }
            },
            error: function (xhr, status, error) {
                toastr.error('Gagal memuat data draft: ' + error);
            }
        });
    }

    function viewTransaction(transactionId) {
        // Redirect to the main transaction list with a filter for this specific transaction
        window.open('<?= base_url('transaksi/jual') ?>?search=' + transactionId, '_blank');
    }

    // Load available printers
    loadPrinters();

    // Printer functionality
    function loadPrinters() {
        // Check if printer select element exists
        if (!$('#printerSelect').length) {
            return; // Exit if printer select doesn't exist
        }

        $.ajax({
            url: '<?= base_url('pengaturan/printer') ?>',
            type: 'GET',
            success: function (response) {
                // Parse the HTML response to extract printer data
                const parser = new DOMParser();
                const doc = parser.parseFromString(response, 'text/html');
                const printerRows = doc.querySelectorAll('tbody tr');

                const printerSelect = $('#printerSelect');
                printerSelect.empty();
                printerSelect.append('<option value="">Gunakan Printer Default</option>');

                printerRows.forEach(function (row) {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 8) {
                        const printerId = row.querySelector('a[href*="/edit/"]')?.href.match(/\/edit\/(\d+)/)?.[1];
                        const printerName = cells[1]?.textContent?.trim();

                        if (printerId && printerName) {
                            printerSelect.append(`<option value="${printerId}">${printerName}</option>`);
                        }
                    }
                });
            },
            error: function (xhr, status, error) {
                // Silently handle 404 errors - printer endpoint may not exist
                if (xhr.status === 404) {
                    // Printer endpoint not found, silently ignore
                    return;
                }
                // For other errors, log but don't show to user
                console.log('Failed to load printers:', status);
            }
        });
    }

    function testPrinterConnection() {
        const selectedPrinter = $('#printerSelect').val();
        const $btn = $('#testPrinter');
        const $icon = $btn.find('i');

        if (!selectedPrinter) {
            toastr.warning('Pilih printer terlebih dahulu');
            return;
        }

        // Show loading state
        $btn.prop('disabled', true);
        $icon.removeClass('fa-plug').addClass('fa-spinner fa-spin');

        $.ajax({
            url: '<?= base_url('pengaturan/printer/test') ?>/' + selectedPrinter,
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    toastr.success('Test printer berhasil!');
                } else {
                    toastr.error('Test printer gagal: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                // Handle 404 errors gracefully
                if (xhr.status === 404) {
                    toastr.warning('Fitur printer tidak tersedia');
                } else {
                    toastr.error('Gagal melakukan test printer');
                }
            },
            complete: function () {
                // Reset button state
                $btn.prop('disabled', false);
                $icon.removeClass('fa-spinner fa-spin').addClass('fa-plug');
            }
        });
    }

    function getCurrentTransactionId() {
        // This should return the current transaction ID
        // For now, we'll use a placeholder
        return $('#currentTransactionId').val() || null;
    }

    // Search Anggota function
    function searchAnggota() {
        let kartuNumber = $('#scanAnggota').val().trim();

        if (!kartuNumber) {
            toastr.warning('Masukkan nomor kartu anggota atau scan QR code');
            return;
        }

        // Try to parse QR code data if it looks like JSON or contains id_pelanggan
        let customerId = null;

        // Check if the input looks like JSON data
        if (kartuNumber.startsWith('{') || kartuNumber.startsWith('[')) {
            try {
                const qrData = JSON.parse(kartuNumber);

                // Look for id_pelanggan in the QR data
                if (qrData.id_pelanggan) {
                    customerId = qrData.id_pelanggan;
                } else if (qrData.id) {
                    customerId = qrData.id;
                } else {
                    // If no id found, try to use the original input
                    customerId = kartuNumber;
                }
            } catch (e) {
                customerId = kartuNumber;
            }
        } else {
            // Plain text input (manual entry)
            customerId = kartuNumber;
        }

        // Show loading state
        $('#searchAnggota').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '<?= base_url('transaksi/jual/search-customer') ?>',
            type: 'GET',
            dataType: 'json',
            data: { q: customerId },
            success: function (response) {
                if (response && response.success && response.data && response.data.length > 0) {
                    const customer = response.data[0]; // Get first result

                    // Store customer data
                    $('#selectedCustomerId').val(customer.id);
                    $('#selectedCustomerName').val(customer.nama);
                    
                    // Check block status
                    const isBlocked = customer.status_blokir == '1' || customer.status_blokir == 1;
                    $('#selectedCustomerIsBlocked').val(isBlocked ? '1' : '0');

                    // Show/hide block alert
                    if (isBlocked) {
                        $('#customerBlockAlert').show();
                    } else {
                        $('#customerBlockAlert').hide();
                    }

                    // Show customer info in the display section
                    $('#displayCustomerName').text(customer.nama);
                    $('#displayCustomerCard').text(customer.kode || customer.id_user || customerId);
                    $('#customerInfoDisplay').show();

                    // Show detailed customer info below
                    $('#anggotaNama').text(customer.nama || '-');
                    $('#anggotaKode').text(customer.kode || customer.id_user || customerId || '-');
                    $('#anggotaAlamat').text(customer.alamat || '-');
                    $('#anggotaInfo').show();

                    // Clear scan input
                    $('#scanAnggota').val('');

                    // Show success message
                    toastr.success('Customer ditemukan: ' + customer.nama);
                } else {
                    toastr.error('Customer tidak ditemukan');
                    $('#customerInfoDisplay').hide();
                    $('#anggotaInfo').hide();
                    $('#selectedCustomerId').val('');
                    $('#selectedCustomerName').val('');
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 401) {
                    toastr.error('Session telah berakhir. Silakan login ulang.');
                    setTimeout(function () {
                        window.location.href = '<?= base_url('auth/login') ?>';
                    }, 2000);
                } else if (xhr.status === 404) {
                    toastr.error('Anggota tidak ditemukan');
                    $('#customerInfoDisplay').hide();
                    $('#selectedCustomerId').val('');
                    $('#selectedCustomerName').val('');
                } else {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr.statusText || error || 'Error');
                    toastr.error('Gagal mencari anggota: ' + msg);
                }
            },
            complete: function () {
                // Reset button state
                $('#searchAnggota').prop('disabled', false).html('<i class="fas fa-qrcode"></i>');
            }
        });
    }

    // Event listeners for printer modal
    $(document).ready(function () {
        $('#testPrinter').on('click', testPrinterConnection);
    });

    // Note: loadPrinters() is already called above, no need to call again

    // Manual customer search function (searches tbl_m_pelanggan)
    function searchAnggota() {
        let searchTerm = $('#scanAnggota').val().trim();

        if (!searchTerm) {
            toastr.warning('Masukkan nomor kode anggota atau scan QR code customer');
            return;
        }

        // Show loading state
        $('#searchAnggota').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '<?= base_url('transaksi/jual/search-customer') ?>',
            type: 'GET',
            dataType: 'json',
            data: { q: searchTerm },
            success: function (response) {
                if (response && response.success && response.data) {
                    let customer;
                    
                    // Handle both single customer object and array of customers
                    if (Array.isArray(response.data)) {
                        if (response.data.length > 0) {
                            customer = response.data[0]; // Get first result
                        } else {
                            toastr.error('Customer tidak ditemukan');
                            $('#customerInfoDisplay').hide();
                            $('#anggotaInfo').hide();
                            $('#selectedCustomerId').val('');
                            $('#selectedCustomerName').val('');
                            return;
                        }
                    } else {
                        customer = response.data; // Single customer object
                    }

                    // Store customer data
                    $('#selectedCustomerId').val(customer.id);
                    $('#selectedCustomerName').val(customer.nama);
                    
                    // Check block status
                    const isBlocked = customer.status_blokir == '1' || customer.status_blokir == 1;
                    $('#selectedCustomerIsBlocked').val(isBlocked ? '1' : '0');

                    // Show/hide block alert
                    if (isBlocked) {
                        $('#customerBlockAlert').show();
                    } else {
                        $('#customerBlockAlert').hide();
                    }

                    // Show customer info in the display section
                    $('#displayCustomerName').text(customer.nama);
                    $('#displayCustomerCard').text(customer.kode || customer.id_user || searchTerm);
                    $('#customerInfoDisplay').show();

                    // Show detailed customer info below
                    $('#anggotaNama').text(customer.nama || '-');
                    $('#anggotaKode').text(customer.kode || customer.id_user || searchTerm || '-');
                    $('#anggotaAlamat').text(customer.alamat || '-');
                    $('#anggotaInfo').show();

                    // Clear scan input
                    $('#scanAnggota').val('');

                    // Show success message
                    toastr.success('Customer ditemukan: ' + customer.nama);
                } else {
                    toastr.error('Customer tidak ditemukan');
                    $('#customerInfoDisplay').hide();
                    $('#anggotaInfo').hide();
                    $('#selectedCustomerId').val('');
                    $('#selectedCustomerName').val('');
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 401) {
                    toastr.error('Session telah berakhir. Silakan login ulang.');
                    setTimeout(function () {
                        window.location.href = '<?= base_url('auth/login') ?>';
                    }, 2000);
                } else if (xhr.status === 404) {
                    toastr.error('Customer tidak ditemukan');
                    $('#customerInfoDisplay').hide();
                    $('#anggotaInfo').hide();
                    $('#selectedCustomerId').val('');
                    $('#selectedCustomerName').val('');
                } else {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr.statusText || error || 'Error');
                    toastr.error('Gagal mencari customer: ' + msg);
                }
            },
            complete: function () {
                // Reset button state
                $('#searchAnggota').prop('disabled', false).html('<i class="fas fa-qrcode"></i>');
            }
        });
    }

    // QR Scanner Functions
    let qrScanner = null;
    let qrStream = null;
    let currentCameraFacing = 'environment'; // 'environment' for back camera, 'user' for front camera

    function openQrScanner() {
        // Reset scanner state first
        qrScanner = false;
        if (qrStream) {
            stopQrScanner();
        }

        // Reset camera facing to back camera by default
        currentCameraFacing = 'environment';

        // Update button text
        $('#flipCamera').html('<i class="fas fa-sync-alt"></i> Front Camera');

        // Show modal first
        $('#qrScannerModal').modal('show');

        // Wait for modal to be fully visible before starting scanner
        $('#qrScannerModal').on('shown.bs.modal', function () {
            // Small delay to ensure modal is fully rendered
            setTimeout(() => {
                startQrScanner();
            }, 600);
        });
    }

    function startQrScanner() {
        const video = document.getElementById('qrVideo');
        const status = document.getElementById('qrScannerStatus');

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            status.innerHTML = '<p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Kamera tidak didukung di browser ini</p>';
            return;
        }

        status.innerHTML = '<p class="text-muted"><i class="fas fa-spinner fa-spin"></i> Mengaktifkan kamera...</p>';

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: currentCameraFacing,
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        })
            .then(function (stream) {
                qrStream = stream;
                video.srcObject = stream;

                // Wait for video to be ready before playing
                video.onloadedmetadata = function () {
                    // Handle video play with proper error handling
                    const playPromise = video.play();
                    if (playPromise !== undefined) {
                        playPromise
                            .then(function () {
                                status.innerHTML = '<p class="text-success"><i class="fas fa-camera"></i> Kamera aktif. Arahkan ke QR code</p>';
                                // Start QR code detection
                                startQrDetection(video);
                            })
                            .catch(function (err) {
                                if (err.name === 'AbortError') {
                                    status.innerHTML = '<p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Video diinterupsi, mencoba lagi...</p>';
                                    // Retry after a short delay
                                    setTimeout(() => {
                                        if (qrScanner && video.srcObject) {
                                            video.play().then(() => {
                                                status.innerHTML = '<p class="text-success"><i class="fas fa-camera"></i> Kamera aktif. Arahkan ke QR code</p>';
                                                startQrDetection(video);
                                                                                    }).catch(retryErr => {
                                            status.innerHTML = '<p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Gagal memulai video</p>';
                                        });
                                        }
                                    }, 500);
                                } else {
                                    status.innerHTML = '<p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Kamera aktif tapi ada masalah dengan video</p>';
                                    // Still try to start detection
                                    startQrDetection(video);
                                }
                            });
                    }
                };
            })
            .catch(function (err) {
                let errorMessage = 'Gagal mengaktifkan kamera';

                if (err.name === 'NotAllowedError') {
                    errorMessage = 'Izin kamera ditolak. Silakan izinkan akses kamera.';
                } else if (err.name === 'NotFoundError') {
                    errorMessage = 'Kamera tidak ditemukan.';
                } else if (err.name === 'NotReadableError') {
                    errorMessage = 'Kamera sedang digunakan aplikasi lain.';
                }

                status.innerHTML = '<p class="text-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMessage + '</p>';


            });
    }

    function startQrDetection(video) {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d', { willReadFrequently: true });

        function scanFrame() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.height = video.videoHeight;
                canvas.width = video.videoWidth;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // QR code detection using jsQR library
                detectQrCode(canvas, context);
            }

            if (qrScanner) {
                requestAnimationFrame(scanFrame);
            }
        }

        qrScanner = true;
        scanFrame();
    }

    function detectQrCode(canvas, context) {
        // QR code detection using jsQR library
        if (typeof jsQR === 'undefined') {
            return;
        }

        try {
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);

            if (code && code.data) {
                // Stop scanning to prevent multiple detections
                stopQrScanner();

                // Handle the QR scan result directly with the raw data
                // Let handleQrScanResult handle all the parsing logic
                handleQrScanResult(code.data);
            }
        } catch (error) {
            // QR detection error
        }
    }

    function stopQrScanner() {
        // Stop the scanning loop
        if (qrScanner) {
            qrScanner = false;
        }

        // Stop all camera tracks
        if (qrStream) {
            try {
                qrStream.getTracks().forEach(track => {
                    if (track.readyState === 'live') {
                        track.stop();
                    }
                });
                qrStream = null;
            } catch (error) {
                // Error stopping camera tracks
            }
        }

        // Clear video source safely
        const video = document.getElementById('qrVideo');
        if (video) {
            try {
                // Pause video first
                if (!video.paused) {
                    video.pause();
                }

                // Clear source
                if (video.srcObject) {
                    video.srcObject = null;
                }

                // Reset video element
                video.load();

                // Remove event listeners
                video.onloadedmetadata = null;
                video.oncanplay = null;

            } catch (error) {
                // Error clearing video source
            }
        }

        // Update status
        const status = document.getElementById('qrScannerStatus');
        if (status) {
            status.innerHTML = '<p class="text-muted">Kamera dinonaktifkan</p>';
        }
    }

    // Function to flip camera between front and back
    function flipCamera() {
        if (!qrStream) {
            toastr.warning('Kamera belum aktif');
            return;
        }

        // Toggle camera facing mode
        currentCameraFacing = currentCameraFacing === 'environment' ? 'user' : 'environment';

        // Update button text
        const buttonText = currentCameraFacing === 'environment' ? 'Front Camera' : 'Back Camera';
        $('#flipCamera').html(`<i class="fas fa-sync-alt"></i> ${buttonText}`);

        // Stop current stream
        stopQrScanner();

        // Restart scanner with new camera
        setTimeout(() => {
            startQrScanner();
        }, 500);

        toastr.info(`Switched to ${currentCameraFacing === 'environment' ? 'back' : 'front'} camera`);
    }

    // Function to handle QR code scan result (called by QR library)
    function handleQrScanResult(qrData) {
        // Close the scanner modal
        $('#qrScannerModal').modal('hide');

        // Extract customer ID from QR data
        let customerId = null;
        let customerName = null;

        // Handle different QR data formats - be more flexible
        if (typeof qrData === 'string') {
            // Plain text QR code - try to extract any meaningful data
            const trimmedData = qrData.trim();

            // Check if it's a JSON string that wasn't parsed
            if (trimmedData.startsWith('{') || trimmedData.startsWith('[')) {
                try {
                    const parsedData = JSON.parse(trimmedData);

                    // Extract customer ID from parsed JSON
                    if (parsedData.id_pelanggan) {
                        customerId = parsedData.id_pelanggan;
                        customerName = parsedData.nama;
                    } else if (parsedData.id) {
                        customerId = parsedData.id;
                        customerName = parsedData.nama;
                    } else if (parsedData.kartu) {
                        customerId = parsedData.kartu;
                        customerName = parsedData.nama;
                    } else if (parsedData.nomor_kartu) {
                        customerId = parsedData.nomor_kartu;
                        customerName = parsedData.nama;
                    } else if (parsedData.code) {
                        customerId = parsedData.code;
                        customerName = parsedData.name;
                    } else {
                        // If no specific field found, use the first non-empty string value
                        for (let key in parsedData) {
                            if (typeof parsedData[key] === 'string' && parsedData[key].trim() !== '') {
                                customerId = parsedData[key].trim();
                                break;
                            }
                        }
                    }
                } catch (e) {
                    customerId = trimmedData;
                }
            } else {
                // Regular plain text
                customerId = trimmedData;
            }
        } else if (qrData && typeof qrData === 'object') {
            // Try multiple possible field names
            if (qrData.id_pelanggan) {
                customerId = qrData.id_pelanggan;
                customerName = qrData.nama;
            } else if (qrData.id) {
                customerId = qrData.id;
                customerName = qrData.nama;
            } else if (qrData.kartu) {
                customerId = qrData.kartu;
                customerName = qrData.nama;
            } else if (qrData.nomor_kartu) {
                customerId = qrData.nomor_kartu;
                customerName = qrData.nama;
            } else if (qrData.code) {
                customerId = qrData.code;
                customerName = qrData.name;
            } else if (qrData.customer_id) {
                customerId = qrData.customer_id;
                customerName = qrData.customer_name || qrData.name;
            } else if (qrData.member_id) {
                customerId = qrData.member_id;
                customerName = qrData.member_name || qrData.name;
            } else {
                // If no specific field found, use the first non-empty string value
                for (let key in qrData) {
                    if (typeof qrData[key] === 'string' && qrData[key].trim() !== '') {
                        customerId = qrData[key].trim();
                        break;
                    }
                }
            }
        }

        if (customerId && customerId.toString().trim() !== '') {
            // Set the scanned data in the input field
            $('#scanAnggota').val(customerId);

            // If we have customer name, set it directly
            if (customerName) {
                $('#selectedCustomerName').val(customerName);
                $('#displayCustomerName').text(customerName);
                $('#displayCustomerCard').text(customerId);
                $('#customerInfoDisplay').show();

                // Show detailed anggota info below (with placeholder data)
                $('#anggotaNama').text(customerName);
                $('#anggotaKode').text(customerId);
                $('#anggotaAlamat').text('-');
                $('#anggotaInfo').show();

                toastr.success('Anggota ditemukan: ' + customerName);
            } else {
                // Automatically search for the customer
                searchAnggota();
            }
        } else {
            let errorMessage = 'Data QR code tidak valid. ';
            if (!qrData) {
                errorMessage += 'QR data kosong/null.';
            } else if (typeof qrData === 'string' && qrData.trim() === '') {
                errorMessage += 'QR data string kosong.';
            } else if (typeof qrData === 'object' && Object.keys(qrData).length === 0) {
                errorMessage += 'QR data object kosong.';
            } else {
                errorMessage += 'Format tidak dikenali. Data: ' + JSON.stringify(qrData);
            }

            toastr.error(errorMessage);
            $('#scanAnggota').focus();
        }
    }

    // Draft List Functions
    function showDraftList() {
        $('#draftListModal').modal('show');
        loadDraftList();
    }

    function loadDraftList() {
        const $loading = $('#draftLoading');
        const $empty = $('#draftEmpty');
        const $tableBody = $('#draftTableBody');

        $loading.show();
        $empty.hide();
        $tableBody.empty();

        $.ajax({
            url: '<?= base_url('transaksi/jual/get-drafts') ?>',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                $loading.hide();

                if (response.success && response.drafts && response.drafts.length > 0) {
                    response.drafts.forEach(function (draft) {
                        const row = `
                        <tr>
                            <td>${draft.no_nota}</td>
                            <td>${formatDate(draft.created_at)}</td>
                            <td>${draft.customer_name || 'Umum'}</td>
                            <td>Rp ${numberFormat(draft.jml_gtotal)}</td>
                            <td>${draft.outlet_name || '-'}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="loadDraftToForm(${draft.id})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-info" onclick="printDraft(${draft.id})">
                                    <i class="fas fa-print"></i> Print
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteDraft(${draft.id})">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    `;
                        $tableBody.append(row);
                    });
                } else {
                    $empty.show();
                }
            },
            error: function (xhr, status, error) {
                $loading.hide();
                toastr.error('Gagal memuat daftar draft: ' + error);
            }
        });
    }

    function loadDraftToForm(draftId) {
        if (confirm('Apakah Anda yakin ingin memuat draft ini? Data transaksi saat ini akan hilang.')) {
            $.ajax({
                url: '<?= base_url('transaksi/jual/get-draft/') ?>' + draftId,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        const draft = response.draft;

                        // Clear current form
                        clearTransactionForm();

                        // Load draft data
                        if (draft.customer_id) {
                            $('#selectedCustomerId').val(draft.customer_id);
                            $('#selectedCustomerName').val(draft.customer_name);
                            if (draft.customer_type === 'anggota') {
                                $('#customerTypeAnggota').prop('checked', true).trigger('change');
                            } else {
                                $('#customerTypeUmum').prop('checked', true).trigger('change');
                            }
                        }

                        // Load cart items
                        if (draft.items && draft.items.length > 0) {
                            cart = draft.items;
                            currentDraftId = draft.id; // Store draft ID for later processing
                            updateCartDisplay();
                            calculateTotal();
                        }

                        // Load discount and voucher
                        if (draft.discount_amount) {
                            $('#discountAmount').val(draft.discount_amount);
                            $('#discountType').val(draft.discount_type || 'nominal');
                            if (draft.discount_amount > 0) {
                                $('#discountRow').show();
                            }
                        }
                        if (draft.voucher_code) {
                            $('#voucherCode').val(draft.voucher_code);
                            validateVoucher(draft.voucher_code);
                        }

                        // Close modal and show success message
                        $('#draftListModal').modal('hide');
                        toastr.success('Draft berhasil dimuat!');
                    } else {
                        toastr.error(response.message || 'Gagal memuat draft');
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error('Gagal memuat draft: ' + error);
                }
            });
        }
    }

    function deleteDraft(draftId) {
        if (confirm('Apakah Anda yakin ingin menghapus draft ini? Tindakan ini tidak dapat dibatalkan.')) {
            const csrfTokenName = $('input[name^="csrf"]').attr('name');
            const csrfToken = $('input[name^="csrf"]').val();

            $.ajax({
                url: '<?= base_url('transaksi/jual/delete-draft/') ?>' + draftId,
                type: 'POST',
                data: {
                    [csrfTokenName]: csrfToken
                },
                dataType: 'json',
                            success: function (response) {
                if (response.success) {
                    toastr.success('Draft berhasil dihapus!');
                    loadDraftList(); // Reload the list
                } else {
                    toastr.error(response.message || 'Gagal menghapus draft');
                }
            },
                            error: function (xhr, status, error) {
                toastr.error('Gagal menghapus draft: ' + error);
            }
            });
        }
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    /**
     * Print all drafts - DISABLED
     */
    function printAllDrafts() {
        toastr.warning('Print functionality has been disabled. Please use the new unified print system.');
    }
</script>
<?= $this->endSection() ?>