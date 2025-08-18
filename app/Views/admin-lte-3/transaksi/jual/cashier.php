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
                <div id="loadMoreContainer" class="text-center mt-3" style="display: none;">
                    <button type="button" class="btn btn-primary btn-lg" id="loadMoreProducts">
                        <i class="fas fa-arrow-down"></i> Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Order Management -->
    <div class="col-lg-5">
        <div class="card rounded-0">
            <div class="card-header bg-light">
                <?php if (session('kasir_outlet_name')): ?>
                    <h4 class="mb-0 font-weight-normal text-secondary">
                        <i class="fas fa-cash-register"></i> Kasir Penjualan, <b><?= session('kasir_outlet_name') ?></b>
                    </h4>
                <?php else: ?>
                    <h4 class="mb-0 font-weight-normal text-secondary">
                        <i class="fas fa-cash-register"></i> Kasir Penjualan
                    </h4>
                <?php endif; ?>
            </div>
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
                        <!-- Scan Anggota Field (hidden by default) -->
                        <div class="form-group scan-anggota-field mb-3" id="scanAnggotaGroup" style="display: none;">
                            <label for="scanAnggota">Scan QR Code Anggota</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" id="scanAnggota"
                                    placeholder="Scan QR code atau ketik nomor kartu">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="openQrScanner">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="searchAnggota">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Scan QR code atau ketik nomor kartu anggota
                            </small>

                            <!-- QR Scanner Modal -->
                            <div class="modal fade qr-scanner-modal rounded-0" id="qrScannerModal" tabindex="-1"
                                role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Scan QR Code Anggota</h5>
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

                                                <div id="anggotaInfo" class="anggota-info mt-2" style="display: none;">
                        <div class="alert alert-info alert-sm">
                            <div class="row">
                                <div class="col-12">
                                    <strong>Informasi Anggota:</strong>
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
                                    <small class="text-muted">small text here</small>
                                </div>
                            </div>
                        </div>
                        <div class="cart-summary">
                            <div class="summary-row">
                                <span class="summary-label">DPP:</span>
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
                <button type="button" class="btn btn-info" onclick="printReceipt('pdf')">
                    <i class="fas fa-file-pdf"></i> Print PDF
                </button>
                <button type="button" class="btn btn-success" onclick="printReceipt('printer')">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
                <button type="button" class="btn btn-primary" id="printReceipt">
                    <i class="fas fa-print"></i> Cetak Struk
                </button>
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

<!-- Print Options Modal -->
<div class="modal fade" id="printOptionsModal" tabindex="-1" role="dialog" aria-labelledby="printOptionsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printOptionsModalLabel">Pilih Metode Cetak</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                <h6>Cetak ke PDF</h6>
                                <p class="text-muted">Simpan sebagai file PDF atau cetak via browser</p>
                                <button type="button" class="btn btn-danger btn-block"
                                    onclick="printReceipt('pdf', window.currentPrintData)">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-print fa-3x text-success mb-3"></i>
                                <h6>Cetak ke Printer</h6>
                                <p class="text-muted">Cetak langsung ke dot matrix printer</p>
                                <button type="button" class="btn btn-success btn-block"
                                    onclick="printReceipt('printer', window.currentPrintData)">
                                    <i class="fas fa-print"></i> Printer
                                </button>
                            </div>
                        </div>
                    </div>
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
                            <?= form_input([
                                'type' => 'number',
                                'class' => 'form-control form-control-sm rounded-0',
                                'id' => 'discountPercent',
                                'placeholder' => '%',
                                'step' => '0.01'
                            ]); ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6">Voucher:</div>
                        <div class="col-6">
                            <?= form_input([
                                'type' => 'text',
                                'class' => 'form-control form-control-sm rounded-0',
                                'id' => 'voucherCode',
                                'placeholder' => 'Kode voucher'
                            ]); ?>
                            <small class="text-muted" id="voucherInfo"></small>
                            <input type="hidden" id="voucherDiscount" name="voucherDiscount" value="0">
                            <input type="hidden" id="voucherId" name="voucherId" value="">
                            <input type="hidden" id="voucherType" name="voucherType" value="">
                            <input type="hidden" id="voucherDiscountAmount" name="voucherDiscountAmount" value="0">
                        </div>
                    </div>

                    <div class="row mb-2" id="voucherDiscountRow" style="display: none;">
                        <div class="col-6">Potongan Voucher:</div>
                        <div class="col-6 text-right">
                            <span id="voucherDiscountDisplay">Rp 0</span>
                        </div>
                    </div>
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

                <!-- Payment Summary -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="mb-3">Ringkasan Pembayaran</h6>
                    <div class="row mb-2">
                        <div class="col-6">Total Bayar:</div>
                        <div class="col-6 text-right">
                            <span id="grandTotalPayment">Rp 0</span>
                        </div>
                    </div>
                    <div class="row mb-2" id="remainingPayment" style="display: none;">
                        <div class="col-6">Kurang:</div>
                        <div class="col-6 text-right text-danger">
                            <span id="remainingAmount">Rp 0</span>
                        </div>
                    </div>
                    <div class="row mb-2" id="changePayment" style="display: none;">
                        <div class="col-6">Kembalian:</div>
                        <div class="col-6 text-right text-success">
                            <span id="changeAmount">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Action Buttons Row -->
                <div class="row w-100 mb-3">
                    <div class="col-12 mb-2">
                        <button type="button" class="btn btn-info btn-block rounded-0" id="showDraftList">
                            <i class="fas fa-list"></i> Daftar Draft
                        </button>
                    </div>
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

    .denomination-inputs {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
    }

    .denomination-tag {
        background: white;
        color: #28a745;
        padding: 20px 15px;
        border: 2px solid #28a745;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        min-height: 80px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        font-size: 16px;
    }

    .denomination-tag:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        background: #f8fff9;
    }

    .denomination-tag:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .denomination-label {
        font-size: 16px;
        font-weight: bold;
        color: #28a745;
    }

    .denomination-tag.clicked {
        transform: scale(1.05);
        background: #28a745;
        color: white;
    }

    .denomination-tag.clicked .denomination-label {
        color: white;
    }

    .denomination-tag.reset {
        transform: scale(0.95);
        background: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .denomination-tag.reset .denomination-label {
        color: white;
    }

    .denomination-tag.active {
        transform: scale(1.05);
        background: #007bff;
        border-color: #007bff;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .denomination-tag.active .denomination-label {
        color: white;
    }

    /* Uang Pas button styling */
    #uangPas {
        transition: all 0.3s ease;
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

    .denomination-inputs {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
    }

    .reference-input {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
    }

    .denomination-tag:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        background: #f8fff9;
    }

    .denomination-tag:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .denomination-label {
        font-size: 16px;
        font-weight: bold;
        color: #28a745;
    }

    .denomination-tag.clicked {
        transform: scale(1.05);
        background: #28a745;
        color: white;
    }

    .denomination-tag.clicked .denomination-label {
        color: white;
    }

    .denomination-tag.reset {
        transform: scale(0.95);
        background: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .denomination-tag.reset .denomination-label {
        color: white;
    }

    .denomination-tag.active {
        transform: scale(1.05);
        background: #007bff;
        border-color: #007bff;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .denomination-tag.active .denomination-label {
        color: white;
    }

    /* Uang Pas button styling */
    #uangPas {
        transition: all 0.3s ease;
    }

    #uangPas:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    #uangPas:active {
        transform: translateY(0);
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

    #paymentMethodsModal .denomination-tag {
        background: white;
        color: #28a745;
        padding: 15px 10px;
        border: 2px solid #28a745;
        border-radius: 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        min-height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        font-size: 14px;
    }

    #paymentMethodsModal .denomination-tag:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        background: #f8fff9;
    }

    #paymentMethodsModal .denomination-tag.clicked {
        transform: scale(1.05);
        background: #28a745;
        color: white;
    }

    #paymentMethodsModal .denomination-tag.reset {
        transform: scale(0.95);
        background: #dc3545;
        border-color: #dc3545;
        color: white;
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
                    // Other errors - log but don't redirect
                    console.warn('Session check failed:', error);
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

        $('#discountPercent').on('input', calculateTotal);
        $('#voucherCode').on('blur', function () {
            validateVoucher($(this).val());
        });

        // Payment method event listeners - use namespaced events to prevent duplicates
        $('#addPaymentMethod').off('click.payment').on('click.payment', addPaymentMethod);
        $(document).off('click.payment').on('click.payment', '.remove-payment', removePaymentMethod);
        $(document).off('input.payment').on('input.payment', '.payment-amount', calculatePaymentTotals);
        $(document).off('change.payment').on('change.payment', '.payment-platform', calculatePaymentTotals);
        $(document).off('change.payment').on('change.payment', '.payment-type', autoFillPaymentAmount);
        $(document).off('click.payment').on('click.payment', '.denomination-tag', incrementDenomination);
        $(document).off('contextmenu.payment').on('contextmenu.payment', '.denomination-tag', resetDenomination);

        // Clear any existing event handlers to prevent duplicates
        $(document).off('click.denomination');
        $(document).off('click.uangPas');
        $(document).off('click.clearAmount');

        // Denomination click functionality for uang pas - use event delegation to prevent duplicates
        $(document).off('click.denomination').on('click.denomination', '.denomination-tag', function () {
            const denomination = parseInt($(this).data('denomination'));
            // Find the payment amount field in the same row
            const paymentRow = $(this).closest('.payment-method-row');
            const amountField = paymentRow.find('.payment-amount');
            const currentAmount = parseFloat(amountField.val()) || 0;
            const newAmount = currentAmount + denomination;

            amountField.val(newAmount);

            // Trigger change event to recalculate totals
            amountField.trigger('change');

            // Add visual feedback
            $(this).addClass('active');
            setTimeout(() => {
                $(this).removeClass('active');
            }, 200);

            // Show success message - only show once
            if (!$(this).hasClass('message-shown')) {
                toastr.success(`Ditambahkan: Rp ${numberFormat(denomination)}`);
                $(this).addClass('message-shown');
                setTimeout(() => {
                    $(this).removeClass('message-shown');
                }, 1000);
            }
        });

        // Clear amount button functionality
        $('#clearAmount').on('click', function () {
            // Find the payment amount field in the same row
            const paymentRow = $(this).closest('.payment-method-row');
            const amountField = paymentRow.find('.payment-amount');

            amountField.val('');
            amountField.trigger('change');
            toastr.info('Jumlah uang diterima berhasil dihapus');
        });

        // Uang Pas button functionality - sets amount received equal to grand total
        $('#uangPas').on('click', function () {
            const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/[^\d]/g, '')) || 0;

            if (grandTotal > 0) {
                // Find the current payment method row (first one or active one)
                const currentPaymentRow = $('.payment-method-row').first();
                const amountField = currentPaymentRow.find('.payment-amount');

                if (amountField.length > 0) {
                    // Set the amount to grand total
                    amountField.val(grandTotal);
                    // Trigger change to recalculate totals
                    amountField.trigger('change');
                    toastr.success(`Uang pas: Rp ${numberFormat(grandTotal)}`);
                } else {
                    toastr.error('Field jumlah pembayaran tidak ditemukan');
                }
            } else {
                toastr.warning('Grand total belum dihitung. Silakan tambahkan produk terlebih dahulu.');
            }
        });

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
        $('#printReceipt').on('click', showPrinterModal);
        $('#showDraftList').on('click', showDraftList);

        // Payment Methods Modal
        $('#openPaymentModal').on('click', function () {
            $('#paymentMethodsModal').modal('show');
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
                    console.error('Test search error:', error);
                    console.error('Response:', xhr.responseText);
                    toastr.error('Test search failed: ' + error);
                }
            });
        });

    });

    // Payment Methods Functions
    function addPaymentMethod() {
        paymentCounter++;
        const platforms = <?= json_encode($platforms ?? []) ?>;



        let platformOptions = '<option value="">Pilih Platform</option>';
        if (platforms && platforms.length > 0) {
            platforms.forEach(platform => {
                platformOptions += `<option value="${platform.id}">${platform.platform}</option>`;
            });
        }

        const paymentHtml = `
        <div class="payment-method-row border rounded p-2 mb-2 rounded-0" data-payment-id="${paymentCounter}">
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
            <div class="row mt-2 denomination-inputs" style="display: none;">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="mb-0">Pecahan Uang:</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-success me-2" id="uangPas">
                                <i class="fas fa-money-bill-wave"></i> Uang Pas
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAmount">
                                <i class="fas fa-eraser"></i> Hapus
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="denomination-tag" data-denomination="15000">
                                <span class="denomination-label">15.000</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="denomination-tag" data-denomination="20000">
                                <span class="denomination-label">20.000</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="denomination-tag" data-denomination="30000">
                                <span class="denomination-label">30.000</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="denomination-tag" data-denomination="50000">
                                <span class="denomination-label">50.000</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="denomination-tag" data-denomination="75000">
                                <span class="denomination-label">75.000</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="denomination-tag" data-denomination="100000">
                                <span class="denomination-label">100.000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-2 reference-input" style="display: none;">
                <div class="col-12">
                    <input type="text" class="form-control form-control-sm rounded-0" 
                           name="payments[${paymentCounter}][reference]" placeholder="No. Referensi (opsional)">
                </div>
            </div>
        </div>
    `;

        $('#paymentMethods').append(paymentHtml);

        // Initialize the first payment method as cash by default
        if (paymentCounter === 1) {
            const firstPaymentRow = $('.payment-method-row').first();
            firstPaymentRow.find('.payment-type').val('1').trigger('change');
        }

        calculatePaymentTotals();
    }

    function removePaymentMethod() {
        const paymentId = $(this).data('payment-id');
        $(`.payment-method-row[data-payment-id="${paymentId}"]`).remove();
        calculatePaymentTotals();
    }

    function autoFillPaymentAmount() {
        const selectedValue = $(this).val();
        const paymentRow = $(this).closest('.payment-method-row');
        const amountField = paymentRow.find('.payment-amount');
        const denominationInputs = paymentRow.find('.denomination-inputs');
        const referenceInput = paymentRow.find('.reference-input');

        // Hide all input sections first
        denominationInputs.hide();
        referenceInput.hide();

        // Clear denomination selections when switching methods
        paymentRow.find('.denomination-tag').removeClass('clicked');

        // If option 1 (Tunai) is selected, show denomination inputs
        if (selectedValue === '1') {
            denominationInputs.show();
            amountField.val(''); // Clear amount field for manual input
        }
        // If option 2, 3, or 4 is selected, auto-fill with total bill amount and show reference input
        else if (selectedValue === '2' || selectedValue === '3' || selectedValue === '4') {
            // Get the grand total and remove formatting
            const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/\./g, '').replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
            amountField.val(grandTotal);
            referenceInput.show();

            // Trigger calculation update
            calculatePaymentTotals();
        }
    }

    function incrementDenomination() {
        const denomination = parseInt($(this).data('denomination')) || 0;
        const paymentRow = $(this).closest('.payment-method-row');
        const amountField = paymentRow.find('.payment-amount');

        // Set the amount to the clicked denomination value
        amountField.val(denomination);

        // Remove clicked class from all denomination tags
        paymentRow.find('.denomination-tag').removeClass('clicked');

        // Add clicked class to the selected denomination
        $(this).addClass('clicked');

        // Trigger payment calculation
        calculatePaymentTotals();
    }

    function resetDenomination(e) {
        e.preventDefault(); // Prevent context menu
        const paymentRow = $(this).closest('.payment-method-row');
        const amountField = paymentRow.find('.payment-amount');

        // Reset amount field to 0
        amountField.val('');

        // Remove clicked class from all denomination tags
        paymentRow.find('.denomination-tag').removeClass('clicked');

        // Add visual feedback
        $(this).addClass('reset');
        setTimeout(() => $(this).removeClass('reset'), 200);

        // Trigger payment calculation
        calculatePaymentTotals();
    }

    function calculatePaymentTotals() {
        let totalPaid = 0;
        // Remove dots (thousand separators) before parsing grand total
        const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/\./g, '').replace(/[^\d,-]/g, '').replace(',', '.')) || 0;



        $('.payment-amount').each(function () {
            const paymentRow = $(this).closest('.payment-method-row');
            const paymentType = paymentRow.find('.payment-type').val();
            const amount = parseFloat($(this).val()) || 0;

            totalPaid += amount;
        });

        // Update displays with formatted currency (showing dots as thousand separator)
        if ($('#grandTotalPayment').length) {
            $('#grandTotalPayment').text(formatCurrency(grandTotal));
        }
        if ($('#totalPaidAmount').length) {
            $('#totalPaidAmount').text(formatCurrency(totalPaid));
        }

        const remaining = grandTotal - totalPaid;

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

    function removePaymentMethod() {
        const paymentId = $(this).data('payment-id');
        $(`.payment-method-row[data-payment-id="${paymentId}"]`).remove();
        calculatePaymentTotals();
    }

    function autoFillPaymentAmount() {
        const selectedValue = $(this).val();
        const paymentRow = $(this).closest('.payment-method-row');
        const amountField = paymentRow.find('.payment-amount');
        const denominationInputs = paymentRow.find('.denomination-inputs');
        const referenceInput = paymentRow.find('.reference-input');

        // Hide all input sections first
        denominationInputs.hide();
        referenceInput.hide();

        // Clear denomination selections when switching methods
        paymentRow.find('.denomination-tag').removeClass('clicked');

        // If option 1 (Tunai) is selected, show denomination inputs
        if (selectedValue === '1') {
            denominationInputs.show();
            amountField.val(''); // Clear amount field for manual input
        }
        // If option 2, 3, or 4 is selected, auto-fill with total bill amount and show reference input
        else if (selectedValue === '2' || selectedValue === '3' || selectedValue === '4') {
            // Get the grand total and remove formatting
            const grandTotal = parseFloat($('#grandTotalDisplay').text().replace(/\./g, '').replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
            amountField.val(grandTotal);
            referenceInput.show();

            // Trigger calculation update
            calculatePaymentTotals();
        }
    }

    function incrementDenomination() {
        const denomination = parseInt($(this).data('denomination')) || 0;
        const paymentRow = $(this).closest('.payment-method-row');
        const amountField = paymentRow.find('.payment-amount');

        // Set the amount to the clicked denomination value
        amountField.val(denomination);

        // Remove clicked class from all denomination tags
        paymentRow.find('.denomination-tag').removeClass('clicked');

        // Add clicked class to the selected denomination
        $(this).addClass('clicked');

        // Trigger payment calculation
        calculatePaymentTotals();
    }

    function resetDenomination(e) {
        e.preventDefault(); // Prevent context menu
        const paymentRow = $(this).closest('.payment-method-row');
        const amountField = paymentRow.find('.payment-amount');

        // Reset amount field to 0
        amountField.val('');

        // Remove clicked class from all denomination tags
        paymentRow.find('.denomination-tag').removeClass('clicked');

        // Add visual feedback
        $(this).addClass('reset');
        setTimeout(() => $(this).removeClass('reset'), 200);

        // Trigger payment calculation
        calculatePaymentTotals();
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
                console.error('Error loading products:', error);
                console.error('Response:', xhr.responseText);
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
                console.error('Error searching products:', error);
                console.error('Response:', xhr.responseText);
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

                    // Check if product already in cart by ID
                    const existingItemIndex = cart.findIndex(item => item.id === product.id);

                    if (existingItemIndex !== -1) {
                        // Product already exists - increment quantity
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
                            total: product.harga_jual || 0
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
                console.error('Error finding product by barcode:', error);
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
        const defaultImage = '<?= base_url('public/assets/theme/admin-lte-3/dist/img/default.png') ?>';
        const base_url = '<?= base_url() ?>';

        if (products && products.length > 0) {
            products.forEach(function (product) {
                const itemName = product.item || product.nama || product.produk || '-';
                const category = product.kategori || '-';
                const brand = product.merk || '-';
                const price = product.harga_jual || product.harga || 0;
                const stock = product.stok || 0;
                // Use foto if exists, otherwise use default
                let foto = product.foto || product.gambar || product.image || '';
                // If foto is empty, use default
                let imageSrc = foto && foto.trim() !== '' ? foto : defaultImage;

                html += `
                <div class="col-md-3 col-sm-4 col-6">
                    <div class="product-grid-item rounded-0" onclick="checkVariant(${product.id}, '${itemName.replace(/'/g, "\\'")}', '${product.kode}', ${price})">
                        <div class="product-image" style="width:150px; height:150px; display:flex; align-items:center; justify-content:center; margin:auto;">
                            <img src="${base_url}${imageSrc}" alt="${itemName}" class="product-thumbnail"
                                 style="width:100%; max-width:150px; height:auto; aspect-ratio:1/1; object-fit:cover;"
                                 onerror="this.onerror=null;this.src='${defaultImage}'">
                                </div>
                        <div class="product-info">
                            <div class="product-category">${category} - ${brand}</div>
                            <div class="product-name">${itemName}</div>
                            <div class="product-price">Rp ${numberFormat(price)}</div>
                            <small class="text-muted">Stok: ${stock} ${product.satuan || 'PCS'}</small>
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
                    console.error('Error loading products by category:', error);
                    console.error('Response:', xhr.responseText);
                    console.error('Status:', status);
                    console.error('XHR:', xhr);
                    console.error('HTTP Status:', xhr.status);
                    console.error('Response Headers:', xhr.getAllResponseHeaders());

                    // Try to parse response as JSON for more details
                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.error('Parsed error response:', response);
                    } catch (e) {
                        console.error('Could not parse response as JSON');
                    }

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
                        const image = product.gambar || product.image || '<?= base_url('public/assets/theme/admin-lte-3/dist/img/default.png') ?>';

                        return `
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="product-grid-item rounded-0" onclick="checkVariant(${product.id}, '${itemName.replace(/'/g, "\\'")}', '${product.kode}', ${price})">
                                <div class="product-image" style="width:150px; height:150px; display:flex; align-items:center; justify-content:center; margin:auto;">
                                    <img src="${image}" alt="${itemName}" class="product-thumbnail" 
                                         style="max-width:150px; max-height:150px; width:auto; height:auto; object-fit:cover;"
                                         onerror="this.src='<?= base_url('public/assets/theme/admin-lte-3/dist/img/default.png') ?>'">
                                </div>
                                <div class="product-info">
                                    <div class="product-category">${category} - ${brand}</div>
                                    <div class="product-name">${itemName}</div>
                                    <div class="product-price">Rp ${numberFormat(price)}</div>
                                    <small class="text-muted">Stok: ${stock} ${product.satuan || 'PCS'}</small>
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
                console.error('Error loading more products:', error);
                toastr.error('Gagal memuat produk tambahan');
            },
            complete: function () {
                // Reset button state
                $('#loadMoreProducts').prop('disabled', false).html('<i class="fas fa-arrow-down"></i> Lanjutkan');
            }
        });
    }

    // Function to check for variants and handle add to cart
    function checkVariant(productId, productName, productCode, price) {
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
                    addToCart(productId, productName, productCode, price);
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
                    console.warn('Failed to get variants, adding product directly:', error);
                    addToCart(productId, productName, productCode, price);
                }
            }
        });
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
                    <span class="cart-item-name">${item.name}</span>
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
        cart.forEach(function (item) {
            subtotal += item.total;
        });

        $('#subtotalDisplay').text(`Rp ${numberFormat(subtotal)}`);

        // Calculate discount
        const discountPercent = parseFloat($('#discountPercent').val()) || 0;
        const discountAmount = subtotal * (discountPercent / 100);
        const afterDiscount = subtotal - discountAmount;

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

    function validateVoucher(voucherCode) {
        if (!voucherCode) {
            $('#voucherInfo').text('').removeClass('text-success text-danger');
            $('#voucherDiscount').val(0);
            $('#voucherId').val('');
            $('#voucherType').val('');
            $('#voucherDiscountAmount').val(0);
            return;
        }

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
                        displayText = `Voucher valid: ${response.discount}%`;
                    } else if (response.jenis_voucher === 'nominal') {
                        displayText = `Voucher valid: Rp ${numberFormat(response.discount_amount)}`;
                    }

                    $('#voucherInfo').text(displayText).removeClass('text-danger').addClass('text-success');
                    $('#voucherDiscount').val(response.discount);
                    $('#voucherId').val(response.voucher_id);
                    $('#voucherType').val(response.jenis_voucher);
                    $('#voucherDiscountAmount').val(response.discount_amount);
                    calculateTotal();
                } else {
                    $('#voucherInfo').text(response.message || 'Voucher tidak valid').removeClass('text-success').addClass('text-danger');
                    $('#voucherDiscount').val(0);
                    $('#voucherId').val('');
                    $('#voucherType').val('');
                    $('#voucherDiscountAmount').val(0);
                    calculateTotal();
                }
            },
            error: function () {
                $('#voucherInfo').text('Error validasi voucher').removeClass('text-success').addClass('text-danger');
                $('#voucherDiscount').val(0);
                $('#voucherId').val('');
                $('#voucherType').val('');
                $('#voucherDiscountAmount').val(0);
                calculateTotal();
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

        // Initialize payment variables (needed for both draft and completed transactions)
        let paymentMethods = [];
        let totalPaymentAmount = 0;

        // If it's a draft, skip payment validation
        if (!isDraft) {

            // Validate payment methods
            let hasValidPayment = false;

            $('.payment-method-row').each(function () {
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
                        reference: reference || ''
                    });
                    totalPaymentAmount += amount;
                }
            });

            if (!hasValidPayment) {
                toastr.error('Minimal harus ada satu metode pembayaran dengan jumlah > 0');
                return;
            }

            if (totalPaymentAmount < grandTotal) {
                toastr.error(`Jumlah bayar (${formatCurrency(totalPaymentAmount)}) kurang dari total (${formatCurrency(grandTotal)})`);
                return;
            }
        } // End of draft check

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

        const transactionData = {
            cart: cleanCart,
            customer_id: $('#selectedCustomerId').val() || null,
            customer_type: $('#selectedCustomerType').val(),
            customer_name: $('#selectedCustomerName').val() || null,
            warehouse_id: $('#warehouse_id').val() || null,
            discount_percent: parseFloat($('#discountPercent').val()) || 0,
            voucher_code: $('#voucherCode').val() || null,
            voucher_discount: parseFloat($('#voucherDiscount').val()) || 0,
            voucher_id: $('#voucherId').val() || null,
            voucher_type: $('#voucherType').val() || null,
            voucher_discount_amount: parseFloat($('#voucherDiscountAmount').val()) || 0,
            payment_methods: isDraft ? [] : paymentMethods,
            total_amount_received: isDraft ? 0 : totalPaymentAmount,
            grand_total: grandTotal,
            is_draft: isDraft,
            draft_id: currentDraftId // Include draft ID if converting from draft
        };

        // Show loading state
        const buttonId = isDraft ? '#saveAsDraft' : '#completeTransaction';
        const buttonText = isDraft ? 'Menyimpan Draft...' : 'Memproses...';
        $(buttonId).prop('disabled', true).html(`<i class="fas fa-spinner fa-spin"></i> ${buttonText}`);



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

                            // Close payment methods modal
                            $('#paymentMethodsModal').modal('hide');

                            // Redirect to mobile QR scanner page
                            window.open('<?= base_url('transaksi/jual/qr-scanner') ?>/' + response.transaction_id, '_blank');
                            toastr.success('Transaksi Piutang berhasil! Arahkan ke halaman scan QR.');

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
                    }
                } else {
                    toastr.error(response.message || 'Gagal memproses transaksi');
                }
            },
            error: function (xhr, status, error) {
                console.error('Transaction error:', error);
                console.error('XHR Status:', xhr.status);
                console.error('Response Text:', xhr.responseText);
                console.error('Status:', status);

                // Try to parse response for more details
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.error('Parsed error response:', response);
                    if (response.message) {
                        toastr.error('Error: ' + response.message);
                    } else {
                        toastr.error('Terjadi kesalahan saat memproses transaksi');
                    }
                } catch (e) {
                    console.error('Could not parse response as JSON');
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

        $('#discountPercent').val('');
        $('#voucherCode').val('');
        $('#voucherInfo').text('');
        $('#voucherDiscountRow').hide();
        $('#paymentMethod').val('');
        // Clear payment amounts in all payment method rows
        $('.payment-amount').val('');
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
        $('#discountPercent').val('');
        $('#voucherCode').val('');
        $('#voucherInfo').text('').removeClass('text-success text-danger');
        $('#voucherDiscount').val('0');
        $('#voucherId').val('');
        $('#voucherType').val('');
        $('#voucherDiscountAmount').val(0);
        $('#voucherDiscountRow').hide();

        // Reset payment methods
        $('#paymentMethods').empty();
        paymentMethods = [];
        paymentCounter = 0;
        addPaymentMethod(); // Add first payment method by default

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
            currentDraftId = null; // Clear current draft ID
            newTransaction();
        }
    }

    /**
     * Print function that supports both PDF and dot matrix printers
     * @param {string} type - 'pdf' for browser PDF, 'printer' for dot matrix
     * @param {object} transactionData - Transaction data to print
     */
    function printReceipt(type = 'pdf', transactionData = null) {
        // If no transaction data provided, use current transaction
        if (!transactionData) {
            transactionData = {
                no_nota: $('#noNotaDisplay').text() || 'DRAFT',
                customer_name: $('#selectedCustomerName').val() || 'Umum',
                customer_type: $('#selectedCustomerType').val() || 'umum',
                items: cart,
                subtotal: parseFloat($('#subtotalDisplay').text().replace(/[^\d]/g, '')) || 0,
                discount: parseFloat($('#discountPercent').val()) || 0,
                voucher: $('#voucherCode').val() || '',
                ppn: PPN_PERCENTAGE,
                total: parseFloat($('#grandTotalDisplay').text().replace(/[^\d]/g, '')) || 0,
                payment_methods: paymentMethods,
                date: new Date().toLocaleString('id-ID'),
                outlet: $('#warehouse_id option:selected').text() || 'Outlet'
            };
        }

        if (type === 'pdf') {
            printToPDF(transactionData);
        } else {
            printToPrinter(transactionData);
        }
    }

    /**
     * Print to PDF using browser's print functionality
     */
    function printToPDF(transactionData) {
        // Create URL with query parameters
        const url = '<?= base_url('transaksi/jual/print-receipt-view') ?>';
        const params = new URLSearchParams();

        // Add transaction data
        params.append('transactionData', JSON.stringify(transactionData));
        params.append('printType', 'pdf');
        params.append('showButtons', 'true');

        // Open in new window
        const printWindow = window.open(url + '?' + params.toString(), '_blank', 'width=800,height=600');

        if (!printWindow) {
            toastr.error('Pop-up blocked. Please allow pop-ups for this site.');
        }
    }

    /**
     * Print to dot matrix printer using HTML
     */
    function printToPrinter(transactionData) {
        // Create URL with query parameters
        const url = '<?= base_url('transaksi/jual/print-receipt-view') ?>';
        const params = new URLSearchParams();

        // Add transaction data
        params.append('transactionData', JSON.stringify(transactionData));
        params.append('printType', 'printer');
        params.append('showButtons', 'true');

        // Open in new window
        const printWindow = window.open(url + '?' + params.toString(), '_blank', 'width=400,height=600');

        if (!printWindow) {
            toastr.error('Pop-up blocked. Please allow pop-ups for this site.');
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

        // Payment block
        let paymentHTML = '';
        let totalPayment = 0;
        let change = 0;
        if (payment_methods && payment_methods.length > 0) {
            paymentHTML += `<div style="font-family:monospace;">Tender</div>`;
            payment_methods.forEach(pm => {
                let methodName = '';
                if (pm.type === '1') methodName = 'Cash';
                else if (pm.type === '2') methodName = 'Non Tunai';
                else if (pm.type === '3') methodName = 'Piutang';
                else methodName = 'Other';
                paymentHTML += `<div style="font-family:monospace;">
                    ${padRight(methodName, 8)}${padLeft(numberFormat(pm.amount), 24)}
                </div>`;
                totalPayment += parseFloat(pm.amount);
            });
            change = totalPayment - total;
        }

        // Compose HTML
        return `
<div style="font-family:monospace; font-size:13px; max-width:300px; margin:auto;">
${padRight('Date', 12)}: ${dateStr || '-'}<br>
${padRight('Order Number', 12)}: ${no_nota || '-'}<br>
${padRight('Sales Type', 12)}: ${sales_type}<br>
${padRight('User', 12)}: ${user || cashier || '-'}<br>
${padRight('Cashier', 12)}: ${cashier || user || '-'}<br>
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
                        discount: draft.discount_percent || 0,
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
            error: function () {
                console.warn('Failed to load printers');
            }
        });
    }

    function showPrinterModal() {
        $('#printerModal').modal('show');
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
            error: function () {
                toastr.error('Gagal melakukan test printer');
            },
            complete: function () {
                // Reset button state
                $btn.prop('disabled', false);
                $icon.removeClass('fa-spinner fa-spin').addClass('fa-plug');
            }
        });
    }

    function printReceiptWithPrinter() {
        const selectedPrinter = $('#printerSelect').val();
        const transactionId = getCurrentTransactionId(); // You'll need to implement this

        if (!transactionId) {
            toastr.error('Tidak ada transaksi yang aktif');
            return;
        }

        $.ajax({
            url: '<?= base_url('transaksi/jual/print-receipt') ?>/' + transactionId,
            type: 'POST',
            data: {
                printer_id: selectedPrinter
            },
            success: function (response) {
                if (response.success) {
                    toastr.success('Struk berhasil dicetak');
                    $('#printerModal').modal('hide');
                } else {
                    toastr.error('Gagal mencetak struk: ' + response.message);
                }
            },
            error: function () {
                toastr.error('Gagal mencetak struk');
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
            url: '<?= base_url('api/anggota/search') ?>',
            type: 'GET',
            dataType: 'json',
            data: { kartu: customerId },
            success: function (response) {
                if (response && response.success && response.data) {
                    const anggota = response.data;

                    // Store customer data
                    $('#selectedCustomerId').val(anggota.id);
                    $('#selectedCustomerName').val(anggota.nama);

                    // Show anggota info in the display section
                    $('#displayCustomerName').text(anggota.nama);
                    $('#displayCustomerCard').text(anggota.nomor_kartu || customerId);
                    $('#customerInfoDisplay').show();

                    // Show detailed anggota info below
                    $('#anggotaNama').text(anggota.nama || '-');
                    $('#anggotaKode').text(anggota.nomor_kartu || customerId || '-');
                    $('#anggotaAlamat').text(anggota.alamat || '-');
                    $('#anggotaInfo').show();

                    // Clear scan input
                    $('#scanAnggota').val('');

                    // Show success message only once
                    toastr.success('Anggota ditemukan: ' + anggota.nama);
                } else {
                    toastr.error('Anggota tidak ditemukan');
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
        $('#confirmPrint').on('click', printReceiptWithPrinter);
    });

    // Load available printers
    loadPrinters();

    // Manual anggota search function
    function searchAnggota() {
        let kartuNumber = $('#scanAnggota').val().trim();

        if (!kartuNumber) {
            toastr.warning('Masukkan nomor kartu anggota');
            return;
        }

        // Show loading state
        $('#searchAnggota').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '<?= base_url('api/anggota/search') ?>',
            type: 'GET',
            dataType: 'json',
            data: { kartu: kartuNumber },
            success: function (response) {
                if (response && response.success && response.data) {
                    const anggota = response.data;

                    // Store customer data
                    $('#selectedCustomerId').val(anggota.id);
                    $('#selectedCustomerName').val(anggota.nama);

                    // Show anggota info in the display section
                    $('#displayCustomerName').text(anggota.nama);
                    $('#displayCustomerCard').text(anggota.nomor_kartu || kartuNumber);
                    $('#customerInfoDisplay').show();

                    // Show detailed anggota info below
                    $('#anggotaNama').text(anggota.nama || '-');
                    $('#anggotaKode').text(anggota.nomor_kartu || kartuNumber || '-');
                    $('#anggotaAlamat').text(anggota.alamat || '-');
                    $('#anggotaInfo').show();

                    // Clear scan input
                    $('#scanAnggota').val('');

                    // Show success message only once
                    toastr.success('Anggota ditemukan: ' + anggota.nama);
                } else {
                    toastr.error('Anggota tidak ditemukan');
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
                                console.error('Video play error:', err);
                                if (err.name === 'AbortError') {
                                    status.innerHTML = '<p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Video diinterupsi, mencoba lagi...</p>';
                                    // Retry after a short delay
                                    setTimeout(() => {
                                        if (qrScanner && video.srcObject) {
                                            video.play().then(() => {
                                                status.innerHTML = '<p class="text-success"><i class="fas fa-camera"></i> Kamera aktif. Arahkan ke QR code</p>';
                                                startQrDetection(video);
                                            }).catch(retryErr => {
                                                console.error('Retry failed:', retryErr);
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
                console.error('Camera error:', err);
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
            console.error('jsQR library not loaded. Please include the jsQR script.');
            return;
        }

        try {
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);

            if (code && code.data) {
                console.log('QR Code detected!');
                console.log('Code object:', code);
                console.log('Code data:', code.data);
                console.log('Code data length:', code.data.length);

                // Stop scanning to prevent multiple detections
                stopQrScanner();

                // Handle the QR scan result directly with the raw data
                // Let handleQrScanResult handle all the parsing logic
                handleQrScanResult(code.data);
            } else if (code) {
                console.log('Code object found but no data:', code);
                console.log('Code object keys:', Object.keys(code));
            } else {
                console.log('No QR code detected in this frame');
            }
        } catch (error) {
            console.error('QR detection error:', error);
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
                console.error('Error stopping camera tracks:', error);
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
                console.error('Error clearing video source:', error);
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


    // Test function to debug QR handling
    function testQrHandling() {
        console.log('=== Testing QR Handling ===');
        
        // Test 1: Empty data
        console.log('Test 1: Empty data');
        handleQrScanResult('');
        
        // Test 2: Null data
        console.log('Test 2: Null data');
        handleQrScanResult(null);
        
        // Test 3: Plain text
        console.log('Test 3: Plain text');
        handleQrScanResult('12345');
        
        // Test 4: JSON object
        console.log('Test 4: JSON object');
        handleQrScanResult({id_pelanggan: '67890', nama: 'Test Customer'});
        
        console.log('=== End Testing ===');
    }

    // Function to handle QR code scan result (called by QR library)
    function handleQrScanResult(qrData) {
        
        // Debug: Log what we received
        console.log('QR Data received:', qrData);
        console.log('QR Data type:', typeof qrData);
        console.log('QR Data length:', qrData ? qrData.length : 'N/A');

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
                    console.log('Parsed JSON string:', parsedData);
                    
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
                    console.log('Failed to parse as JSON, treating as plain text');
                    customerId = trimmedData;
                }
            } else {
                // Regular plain text
                customerId = trimmedData;
            }
            
            console.log('String QR processed, customerId:', customerId);
        } else if (qrData && typeof qrData === 'object') {
            // JSON/object QR code
            console.log('Object QR detected, keys:', Object.keys(qrData));
            
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

        console.log('Extracted customerId:', customerId);
        console.log('Extracted customerName:', customerName);

        if (customerId && customerId.toString().trim() !== '') {
            console.log('Valid customerId found:', customerId);
            
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
            console.error('Invalid QR data - customerId is empty or null');
            console.error('customerId value:', customerId);
            console.error('customerId type:', typeof customerId);
            console.error('customerId length:', customerId ? customerId.length : 'N/A');
            
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
                console.error('Draft loading error:', xhr, status, error);
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
                        if (draft.discount_percent) {
                            $('#discountPercent').val(draft.discount_percent);
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
                    console.log('Delete response:', response);
                    if (response.success) {
                        toastr.success('Draft berhasil dihapus!');
                        loadDraftList(); // Reload the list
                    } else {
                        toastr.error(response.message || 'Gagal menghapus draft');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Delete error:', xhr, status, error);
                    console.error('Response text:', xhr.responseText);
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
     * Print all drafts
     */
    function printAllDrafts() {
        $.ajax({
            url: '<?= base_url('transaksi/jual/get-drafts') ?>',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success && response.drafts && response.drafts.length > 0) {
                    // Use AJAX to get formatted HTML for each draft
                    const draftPromises = response.drafts.map(draft => {
                        const draftData = {
                            no_nota: draft.no_nota,
                            customer_name: 'Draft',
                            customer_type: 'draft',
                            items: [], // We don't have item details in the list
                            subtotal: draft.jml_gtotal * (100 / (100 + PPN_PERCENTAGE)),
                            discount: 0,
                            voucher: '',
                            ppn: PPN_PERCENTAGE,
                            total: draft.jml_gtotal,
                            payment_methods: [],
                            date: new Date(draft.created_at).toLocaleString('id-ID'),
                            outlet: draft.outlet_name || 'Draft'
                        };

                        return $.ajax({
                            url: '<?= base_url('transaksi/jual/print-receipt-view') ?>',
                            type: 'POST',
                            data: {
                                transactionData: JSON.stringify(draftData),
                                printType: 'pdf',
                                showButtons: false
                            }
                        });
                    });

                    // Wait for all drafts to be processed
                    Promise.all(draftPromises).then(draftResponses => {
                        const allDraftsHTML = draftResponses.map(response =>
                            `<div style="page-break-after: always; margin-bottom: 20px;">${response}</div>`
                        ).join('');

                        // Create print window for all drafts
                        const printWindow = window.open('', '_blank', 'width=800,height=600');
                        printWindow.document.write(`
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>All Drafts - Print</title>
                            <style>
                                @media print {
                                    body { margin: 0; padding: 10px; }
                                    .no-print { display: none; }
                                }
                                .draft-item { margin-bottom: 20px; }
                                .btn { 
                                    background: #007bff; 
                                    color: white; 
                                    padding: 10px 20px; 
                                    border: none; 
                                    border-radius: 5px; 
                                    cursor: pointer; 
                                    margin: 5px;
                                }
                                .btn:hover { background: #0056b3; }
                            </style>
                        </head>
                        <body>
                            <div class="no-print" style="text-align: center; margin-bottom: 20px;">
                                <h3>Print All Drafts</h3>
                                <button class="btn" onclick="window.print()">Print All</button>
                                <button class="btn" onclick="window.close()">Close</button>
                            </div>
                            ${allDraftsHTML}
                        </body>
                        </html>
                    `);

                        printWindow.document.close();
                    }).catch(error => {
                        console.error('Error processing drafts:', error);
                        toastr.error('Gagal memproses draft untuk print');
                    });
                } else {
                    toastr.warning('Tidak ada draft untuk dicetak');
                }
            },
            error: function (xhr, status, error) {
                toastr.error('Gagal memuat daftar draft: ' + error);
            }
        });
    }
</script>
<?= $this->endSection() ?>