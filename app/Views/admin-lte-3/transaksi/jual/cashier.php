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

<!-- Hidden CSRF token for AJAX requests -->
<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

<div class="row">
    <!-- Left Column - Product Selection and Cart -->
    <div class="col-md-7">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shopping-cart"></i> Kasir - Transaksi Penjualan
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-info btn-sm me-2" id="refreshSession" title="Refresh Session">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" id="newTransaction">
                        <i class="fas fa-plus"></i> Transaksi Baru
                    </button>
                </div>
            </div>
            <div class="card-body">
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
    <div class="col-md-5">
        <div class="card rounded-0 mb-3">
            <div class="card-header">
                <h4 class="card-title mb-0">Info Transaksi</h4>
            </div>
            <div class="card-body">
                <!-- Product Search -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" id="productSearch"
                                placeholder="Scan barcode atau ketik nama produk...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="searchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-barcode"></i> Barcode scanner aktif - scan langsung ke field ini
                        </small>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control rounded-0" id="warehouse_id">
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
                        <tbody>
                            <tr id="noWarehouseMessage">
                                <td colspan="5" class="text-center text-muted">
                                    <i class="fas fa-info-circle"></i> Silakan pilih outlet terlebih dahulu untuk
                                    melihat produk
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card rounded-0">
            <div class="card-header">
                <h4 class="card-title">Pembayaran</h4>
            </div>
            <div class="card-body">
                <!-- Customer Selection -->
                <div class="form-group customer-type-radio">
                    <label>Jenis Pelanggan</label>
                    <div class="d-flex align-items-center">
                        <div class="form-check mr-3 mb-0">
                            <input class="form-check-input" type="radio" name="customerType" id="customerTypeUmum"
                                value="umum" checked>
                            <label class="form-check-label" for="customerTypeUmum">Umum</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="customerType" id="customerTypeAnggota"
                                value="anggota">
                            <label class="form-check-label" for="customerTypeAnggota">Anggota</label>
                        </div>
                    </div>
                </div>

                <!-- Scan Anggota Field (hidden by default) -->
                <div class="form-group scan-anggota-field" id="scanAnggotaGroup" style="display: none;">
                    <label for="scanAnggota">Scan QR Code Anggota</label>
                    <div class="input-group">
                        <input type="text" class="form-control rounded-0" id="scanAnggota"
                            placeholder="Scan QR code dari mobile atau ketik nomor kartu anggota">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="openQrScanner">
                                <i class="fas fa-camera"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="searchAnggota">
                                <i class="fas fa-qrcode"></i>
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Scan QR code dari aplikasi mobile, atau ketik nomor kartu anggota secara manual
                    </small>

                    <!-- QR Scanner Modal -->
                    <div class="modal fade qr-scanner-modal" id="qrScannerModal" tabindex="-1" role="dialog">
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
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    <button type="button" class="btn btn-warning" id="testQrScanBtn">Test QR
                                        Scan</button>
                                    <button type="button" class="btn btn-primary" id="manualInputBtn">Input
                                        Manual</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="anggotaInfo" class="anggota-info mt-2" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Anggota:</strong> <span id="anggotaNama"></span><br>
                            <small>No. Kartu: <span id="anggotaNoKartu"></span></small>
                        </div>
                    </div>
                </div>

                <!-- Hidden fields for customer data -->
                <input type="hidden" id="selectedCustomerId" name="selectedCustomerId" value="2">
                <input type="hidden" id="selectedCustomerName" name="selectedCustomerName" value="">
                <input type="hidden" id="selectedCustomerType" name="selectedCustomerType" value="umum">

                <script>
                    // Set selectedCustomerId to '2' if Umum, else set to id_user anggota
                    function setCustomerFields(type, anggotaData = null) {
                        if (type === 'umum') {
                            document.getElementById('selectedCustomerId').value = '2';
                            document.getElementById('selectedCustomerType').value = 'umum';
                            document.getElementById('selectedCustomerName').value = '';
                        } else if (type === 'anggota' && anggotaData) {
                            // anggotaData should contain at least id_user and nama
                            document.getElementById('selectedCustomerId').value = anggotaData.id_user;
                            document.getElementById('selectedCustomerType').value = 'anggota';
                            document.getElementById('selectedCustomerName').value = anggotaData.nama || '';
                        }
                    }

                    // On page load, set default to Umum
                    document.addEventListener('DOMContentLoaded', function () {
                        setCustomerFields('umum');
                    });

                    // Listen for radio change
                    document.querySelectorAll('input[name="customerType"]').forEach(function (radio) {
                        radio.addEventListener('change', function () {
                            if (this.value === 'umum') {
                                document.getElementById('scanAnggotaGroup').style.display = 'none';
                                setCustomerFields('umum');
                            } else {
                                document.getElementById('scanAnggotaGroup').style.display = '';
                                // Wait for anggota scan/input to set fields
                            }
                        });
                    });
                </script>

                <!-- Customer Status Display -->
                <div class="form-group customer-status-display" id="customerStatusDisplay" style="display: none;">
                    <div class="alert alert-info">
                        <strong>Status Pelanggan:</strong>
                        <span id="customerTypeDisplay">Umum</span>
                        <div id="customerInfoDisplay" class="mt-1" style="display: none;">
                            <small>
                                <strong>Nama:</strong> <span id="displayCustomerName"></span><br>
                                <strong>No. Kartu:</strong> <span id="displayCustomerCard"></span>
                            </small>
                        </div>
                    </div>
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

                    <div class="row mb-2">
                        <div class="col-6">DPP:</div>
                        <div class="col-6 text-right">
                            <span id="dppDisplay">Rp 0</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6">PPN (<?= $Pengaturan->ppn ?>%):</div>
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
                    console.log('Session refreshed successfully');
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
        addPaymentMethod(); // Add first payment method by default

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

        $('#productSearch').on('input', function () {
            const currentTime = Date.now();
            const inputValue = $(this).val();
            inputCount++;

            // If input is very fast (typical of barcode scanner), treat it as a scan
            if (currentTime - lastInputTime < 100 && inputValue.length > 5) {
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
                        } else {
                            console.log('Duplicate barcode scan prevented:', inputValue);
                        }
                    }
                }, 300); // Wait 300ms after last input to confirm it's a complete scan
            }

            lastInputTime = currentTime;

            // Also handle normal search input (but only if not a barcode scan)
            if (inputCount === 1 || currentTime - lastInputTime > 500) {
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
        });

        $('#productSearch').on('blur', function () {
            inputCount = 0;
        });

        // Reset duplicate prevention when field is cleared
        $('#productSearch').on('input', function () {
            if ($(this).val() === '') {
                lastScannedBarcode = '';
                lastScanTime = 0;
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

        // Payment method event listeners
        $('#addPaymentMethod').on('click', addPaymentMethod);
        $(document).on('click', '.remove-payment', removePaymentMethod);
        $(document).on('input', '.payment-amount', calculatePaymentTotals);
        $(document).on('change', '.payment-platform', calculatePaymentTotals);
        $(document).on('change', '.payment-type', autoFillPaymentAmount);
        $(document).on('click', '.denomination-tag', incrementDenomination);
        $(document).on('contextmenu', '.denomination-tag', resetDenomination);

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

        // Open QR Scanner button click
        $('#openQrScanner').on('click', function () {
            openQrScanner();
        });

        // Manual input button in QR scanner modal
        $('#manualInputBtn').on('click', function () {
            $('#qrScannerModal').modal('hide');
            $('#scanAnggota').focus();
        });

        // QR Scanner modal events
        $('#qrScannerModal').on('shown.bs.modal', function () {
            startQrScanner();
        });

        $('#qrScannerModal').on('hidden.bs.modal', function () {
            stopQrScanner();
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

            // If it's cash payment (type 1), use the amount field directly
            if (paymentType === '1') {
                let val = $(this).val();
                if (typeof val === 'string') {
                    val = val.replace(/\./g, '').replace(',', '.');
                }
                const amount = parseFloat(val) || 0;
                totalPaid += amount;
            } else {
                // For non-cash payments, use the amount field directly
                let val = $(this).val();
                if (typeof val === 'string') {
                    val = val.replace(/\./g, '').replace(',', '.');
                }
                const amount = parseFloat(val) || 0;
                totalPaid += amount;
            }
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
        const warehouseId = $('#warehouse_id').val();

        if (!warehouseId) {
            $('#productListTable tbody').html(`
            <tr id="noWarehouseMessage">
                <td colspan="5" class="text-center text-muted">
                    <i class="fas fa-info-circle"></i> Silakan pilih outlet terlebih dahulu untuk melihat produk
                </td>
            </tr>
        `);
            return;
        }

        $.ajax({
            url: '<?= base_url('transaksi/jual/search-items') ?>',
            type: 'POST',
            data: {
                warehouse_id: warehouseId
            },
            success: function (response) {
                if (response.items) {
                    displayProducts(response.items);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading products:', error);
                $('#productListTable tbody').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error memuat produk: ${error}
                    </td>
                </tr>
            `);
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

        $.ajax({
            url: '<?= base_url('transaksi/jual/search-items') ?>',
            type: 'POST',
            data: {
                search: query,
                warehouse_id: warehouseId
            },
            success: function (response) {
                if (response.items) {
                    displayProducts(response.items);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error searching products:', error);
                $('#productListTable tbody').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error mencari produk: ${error}
                    </td>
                </tr>
            `);
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

                    }

                    // Update cart display and totals
                    updateCartDisplay();
                    calculateTotal();

                    // Clear search field and focus for next scan
                    $('#productSearch').val('').focus();

                } else {
                    // Product not found
                    toastr.error(`Produk dengan barcode/kode ${barcode} tidak ditemukan`);
                    $('#productSearch').focus();
                }
            },
            error: function (xhr, status, error) {
                console.error('Error finding product by barcode:', error);
                toastr.error('Gagal mencari produk: ' + error);
                $('#productSearch').focus();
            },
            complete: function () {
                // Re-enable search field
                $('#productSearch').prop('disabled', false);
            }
        });
    }

    function displayProducts(products) {
        let html = '';

        if (products && products.length > 0) {
            products.forEach(function (product) {
                const itemName = product.item || product.nama || product.produk || '-';
                const category = product.kategori || '-';
                const brand = product.merk || '-';
                const price = product.harga_jual || product.harga || 0;
                const stock = product.stok || 0;

                html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center justify-content-between">
                            <button 
                                type="button" 
                                class="btn btn-block btn-outline-primary product-list-btn mb-2 text-left d-flex align-items-center justify-content-between shadow-sm"
                                style="border-radius: 8px; min-height: 70px; transition: box-shadow 0.2s;"
                                onclick="checkVariant(${product.id}, '${itemName.replace(/'/g, "\\'")}', '${product.kode}', ${price})"
                                title="Pilih produk ${itemName}"
                            >
                                <div class="d-flex flex-column flex-grow-1" style="min-width:0;">
                                    <span class="badge badge-primary mb-1 px-2 py-1" style="font-size:0.75rem; border-radius: 5px; width: fit-content;">
                                        <i class="fas fa-barcode" style="font-size:0.85em;"></i> ${product.kode || '-'}
                                    </span>
                                    <span class="font-weight-bold text-dark text-truncate" style="font-size:0.95rem; max-width: 260px;">
                                        ${itemName}
                                    </span>
                                    <span class="text-muted" style="font-size:0.92rem;">
                                        ${category} - ${brand}
                                    </span>
                                </div>
                                <div class="text-right ml-3 d-flex flex-column align-items-end" style="min-width:90px;">
                                    <span class="text-success font-weight-bold" style="font-size:1.1rem;">
                                        Rp ${numberFormat(price)}
                                    </span>
                                    <span class="text-muted" style="font-size:0.92rem;">
                                        Stok: <span class="font-weight-bold">${stock}</span> ${product.satuan || 'PCS'}
                                    </span>
                                </div>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            });
        } else {
            html = `
            <tr>
                <td colspan="5" class="text-center text-muted">
                    <i class="fas fa-search"></i> Tidak ada produk ditemukan
                </td>
            </tr>
        `;
        }

        $('#productListTable tbody').html(html);
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
        $('#productSearch').val('').focus();
    }

    function updateCartDisplay() {
        let html = '';
        cart.forEach(function (item, index) {
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

        console.log('completeTransaction called - isDraft:', isDraft, 'currentDraftId:', currentDraftId);

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
        console.log('Sending transaction data:', transactionData);
        console.log('Current draft ID:', currentDraftId);
        console.log('Is draft:', isDraft);

        $.ajax({
            url: '<?= base_url('transaksi/jual/process-transaction') ?>',
            type: 'POST',
            data: transactionData,
            success: function (response) {
                if (response.success) {
                    if (isDraft) {
                        // Draft transaction saved successfully
                        toastr.success('Draft transaksi berhasil disimpan!');

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
                    }
                } else {
                    toastr.error(response.message || 'Gagal memproses transaksi');
                }
            },
            error: function (xhr, status, error) {
                console.error('Transaction error:', error);
                toastr.error('Terjadi kesalahan saat memproses transaksi');
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

        // Focus on product search for next transaction
        setTimeout(function () {
            $('#productSearch').focus();
        }, 500);
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
     * Generate receipt HTML content
     */
    function generateReceiptHTML(transactionData) {
        const { no_nota, customer_name, customer_type, items, subtotal, discount, voucher, ppn, total, payment_methods, date, outlet } = transactionData;

        let itemsHTML = '';
        items.forEach(item => {
            itemsHTML += `
            <div class="item">
                <div>${item.name}</div>
                <div>${item.quantity} x ${formatCurrency(item.price)} = ${formatCurrency(item.total)}</div>
            </div>
        `;
        });

        let paymentHTML = '';
        if (payment_methods && payment_methods.length > 0) {
            paymentHTML += '<div style="margin-bottom: 5px;"><strong>METODE PEMBAYARAN:</strong></div>';
            payment_methods.forEach(pm => {
                let methodName = 'Unknown';
                let methodIcon = '💳';

                if (pm.type === '1') {
                    methodName = 'TUNAI';
                    methodIcon = '💵';
                } else if (pm.type === '2') {
                    methodName = 'NON TUNAI';
                    methodIcon = '💳';
                } else if (pm.type === '3') {
                    methodName = 'PIUTANG';
                    methodIcon = '📝';
                }

                paymentHTML += `
                <div class="payment-method-item">
                    <span>${methodIcon} ${methodName}</span>
                    <span style="font-weight: bold;">${formatCurrency(pm.amount)}</span>
                </div>
            `;
            });

            // Add total payment
            const totalPayment = payment_methods.reduce((sum, pm) => sum + parseFloat(pm.amount), 0);
            paymentHTML += `
            <div class="payment-total">
                TOTAL PEMBAYARAN: ${formatCurrency(totalPayment)}
            </div>
        `;

            // Add change if applicable
            if (totalPayment > total) {
                const change = totalPayment - total;
                paymentHTML += `
                <div class="payment-change">
                    KEMBALIAN: ${formatCurrency(change)}
                </div>
            `;
            }
        }

        return `
        <div class="receipt">
            <div class="header">
                <h3>KOPMENSA</h3>
                <div>${outlet}</div>
                <div>${date}</div>
                <div>No: ${no_nota}</div>
            </div>
            
            <div class="divider"></div>
            
            <div class="customer">
                <div>Customer: ${customer_name}</div>
                <div>Type: ${customer_type}</div>
            </div>
            
            ${payment_methods && payment_methods.length > 0 ? `
                <div class="divider"></div>
                <div style="text-align: center; font-weight: bold; color: #007bff; margin: 5px 0;">
                    💳 METODE PEMBAYARAN: ${payment_methods.map(pm => {
            if (pm.type === '1') return 'TUNAI';
            if (pm.type === '2') return 'NON TUNAI';
            if (pm.type === '3') return 'PIUTANG';
            return 'UNKNOWN';
        }).join(' + ')}
                </div>
            ` : ''}
            
            <div class="divider"></div>
            
            <div class="items">
                ${itemsHTML}
            </div>
            
            <div class="divider"></div>
            
            <div class="summary">
                <div>Subtotal: ${formatCurrency(subtotal)}</div>
                ${discount > 0 ? `<div>Diskon: ${discount}%</div>` : ''}
                ${voucher ? `<div>Voucher: ${voucher}</div>` : ''}
                <div>PPN (${ppn}%): ${formatCurrency(subtotal * ppn / 100)}</div>
                <div class="total">TOTAL: ${formatCurrency(total)}</div>
            </div>
            
            ${paymentHTML ? `
                <div class="divider"></div>
                <div class="payment">
                    ${paymentHTML}
                </div>
            ` : ''}
            

            
            <div class="divider"></div>
            
            <div class="footer">
                <div>Terima kasih atas kunjungan Anda</div>
                <div>Barang yang sudah dibeli tidak dapat dikembalikan</div>
                <div>Powered by Kopmensa System</div>
            </div>
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

    // Denomination click functionality for uang pas
    $(document).ready(function () {
        $('.denomination-tag').on('click', function () {
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

            // Show success message
            toastr.success(`Ditambahkan: Rp ${numberFormat(denomination)}`);
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

            console.log('Uang Pas clicked. Grand total:', grandTotal);

            if (grandTotal > 0) {
                // Find the current payment method row (first one or active one)
                const currentPaymentRow = $('.payment-method-row').first();
                const amountField = currentPaymentRow.find('.payment-amount');

                console.log('Payment row found:', currentPaymentRow.length > 0);
                console.log('Amount field found:', amountField.length > 0);

                if (amountField.length > 0) {
                    // Set the amount to grand total
                    amountField.val(grandTotal);
                    // Trigger change to recalculate totals
                    amountField.trigger('change');
                    toastr.success(`Uang pas: Rp ${numberFormat(grandTotal)}`);
                    console.log('Amount set successfully to:', grandTotal);
                } else {
                    toastr.error('Field jumlah pembayaran tidak ditemukan');
                    console.error('Amount field not found');
                }
            } else {
                toastr.warning('Grand total belum dihitung. Silakan tambahkan produk terlebih dahulu.');
                console.warn('Grand total is 0 or not calculated');
            }
        });

        // Load available printers
        loadPrinters();
    });

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
                    console.log('QR Code parsed, found id_pelanggan:', customerId);
                } else if (qrData.id) {
                    customerId = qrData.id;
                    console.log('QR Code parsed, found id:', customerId);
                } else {
                    // If no id found, try to use the original input
                    customerId = kartuNumber;
                    console.log('QR Code parsed but no id found, using original input:', customerId);
                }
            } catch (e) {
                console.log('Failed to parse QR data as JSON, treating as plain text');
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
            data: { kartu: customerId },
            success: function (response) {
                if (response.success && response.data) {
                    const anggota = response.data;

                    // Store customer data
                    $('#selectedCustomerId').val(anggota.id);
                    $('#selectedCustomerName').val(anggota.nama);

                    // Show anggota info
                    $('#displayCustomerName').text(anggota.nama);
                    $('#displayCustomerCard').text(anggota.nomor_kartu || customerId);
                    $('#customerInfoDisplay').show();

                    // Clear scan input
                    $('#scanAnggota').val('');

                    toastr.success('Anggota ditemukan: ' + anggota.nama);

                    // Log successful scan
                    console.log('Successfully found anggota:', anggota);
                } else {
                    toastr.error('Anggota tidak ditemukan');
                    $('#customerInfoDisplay').hide();
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
                } else {
                    toastr.error('Gagal mencari anggota: ' + error);
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

    // QR Scanner Functions
    let qrScanner = null;
    let qrStream = null;

    // NOTE: This is a basic QR scanner implementation
    // To make it fully functional, you need to integrate with a QR code library
    // Recommended libraries:
    // 1. jsQR: https://github.com/cozmo/jsQR (Pure JavaScript)
    // 2. ZXing: https://github.com/zxing-js/library (More comprehensive)
    // 3. QuaggaJS: https://github.com/serratus/quaggajs (Barcode/QR scanner)

    function openQrScanner() {
        $('#qrScannerModal').modal('show');

        // Show helpful message
        setTimeout(() => {
            const status = document.getElementById('qrScannerStatus');
            if (status) {
                status.innerHTML = '<p class="text-info"><i class="fas fa-info-circle"></i> Klik tombol kamera untuk memulai scanning</p>';
            }
        }, 500);
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
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        })
            .then(function (stream) {
                qrStream = stream;
                video.srcObject = stream;
                video.play();

                status.innerHTML = '<p class="text-success"><i class="fas fa-camera"></i> Kamera aktif. Arahkan ke QR code</p>';

                // Start QR code detection
                startQrDetection(video);
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
        const context = canvas.getContext('2d');

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
        // This function processes video frames to detect QR codes

        // Check if jsQR library is loaded
        if (typeof jsQR === 'undefined') {
            console.error('jsQR library not loaded. Please include the jsQR script.');
            return;
        }

        try {
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);

            if (code) {
                console.log('QR Code detected:', code.data);

                // Try to parse the QR data
                let qrData;
                try {
                    qrData = JSON.parse(code.data);
                } catch (e) {
                    // If not JSON, treat as plain text
                    qrData = { id_pelanggan: code.data };
                }

                // Handle the QR scan result
                handleQrScanResult(qrData);
            }
        } catch (error) {
            console.error('QR detection error:', error);
        }
    }

    function stopQrScanner() {
        if (qrScanner) {
            qrScanner = false;
        }

        if (qrStream) {
            qrStream.getTracks().forEach(track => track.stop());
            qrStream = null;
        }

        const video = document.getElementById('qrVideo');
        if (video.srcObject) {
            video.srcObject = null;
        }

        const status = document.getElementById('qrScannerStatus');
        status.innerHTML = '<p class="text-muted">Kamera dinonaktifkan</p>';
    }

    // Function to handle QR code scan result (called by QR library)
    function handleQrScanResult(qrData) {
        console.log('QR Code detected:', qrData);

        // Close the scanner modal
        $('#qrScannerModal').modal('hide');

        // Set the scanned data in the input field
        $('#scanAnggota').val(JSON.stringify(qrData));

        // Automatically search for the customer
        searchAnggota();

        // Show success message
        toastr.success('QR Code berhasil di-scan!');
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

        console.log('Loading drafts...');

        $.ajax({
            url: '<?= base_url('transaksi/jual/get-drafts') ?>',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log('Draft response:', response);
                $loading.hide();

                if (response.success && response.drafts && response.drafts.length > 0) {
                    console.log('Found', response.drafts.length, 'drafts');
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
                    console.log('No drafts found or empty response');
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
                            console.log('Draft loaded - ID:', currentDraftId, 'Items:', draft.items.length);
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
            console.log('CSRF Token Name:', csrfTokenName);
            console.log('CSRF Token:', csrfToken);
            console.log('Draft ID to delete:', draftId);

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