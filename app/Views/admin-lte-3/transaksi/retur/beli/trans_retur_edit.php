<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-25
 * Github : github.com/mikhaelfelian
 * description : Edit Purchase Return Transaction View
 * This file represents the View.
 */
helper('form');
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-edit"></i> Edit Retur Pembelian
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/retur/beli') ?>" class="btn btn-secondary btn-sm rounded-0">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <?= form_open('transaksi/retur/beli/update/' . $retur->id, ['id' => 'form-retur-edit']) ?>
            
            <!-- Transaction Details Section -->
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <!-- Purchase Order Selection -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_beli">No. Pembelian *</label>
                                <select name="id_beli" id="id_beli" class="form-control select2 rounded-0">
                                    <option value="">Pilih Transaksi Pembelian</option>
                                    <?php if (!empty($sql_beli)): ?>
                                        <?php foreach ($sql_beli as $purchase): ?>
                                            <option value="<?= $purchase->id ?>" 
                                                data-supplier="<?= $purchase->id_supplier ?>"
                                                data-no-nota="<?= esc($purchase->no_nota) ?>"
                                                <?= $retur->id_beli == $purchase->id ? 'selected' : '' ?>>
                                                <?= esc($purchase->no_nota) . ' - ' . esc($purchase->supplier_nama ?? 'Unknown Supplier') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Tidak ada transaksi pembelian yang tersedia</option>
                                    <?php endif; ?>
                                </select>
                                <?php if (empty($sql_beli)): ?>
                                    <div class="alert alert-warning mt-2">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Tidak ada transaksi pembelian yang tersedia. Data mungkin sudah dihapus atau terjadi masalah pada database.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Supplier -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_supplier">Supplier *</label>
                                <select name="id_supplier" id="id_supplier" class="form-control select2 rounded-0">
                                    <option value="">Pilih Supplier</option>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier->id ?>" <?= $retur->id_supplier == $supplier->id ? 'selected' : '' ?>>
                                            <?= esc($supplier->nama) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Return Date -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tgl_retur">Tanggal Retur *</label>
                                <?= form_input([
                                    'name' => 'tgl_retur',
                                    'id' => 'tgl_retur',
                                    'type' => 'date',
                                    'class' => 'form-control rounded-0',
                                    'value' => old('tgl_retur', date('Y-m-d', strtotime($retur->tgl_retur))),
                                    'required' => true
                                ]) ?>
                            </div>
                        </div>

                        <!-- Return Note Number -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_nota_retur">No. Nota Retur</label>
                                <?= form_input([
                                    'name' => 'no_nota_retur',
                                    'id' => 'no_nota_retur',
                                    'class' => 'form-control rounded-0',
                                    'value' => old('no_nota_retur', $retur->no_nota_retur),
                                    'placeholder' => 'Auto Generate',
                                    'readonly' => true
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Original Purchase Note -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_nota_asal">No. Nota Asal</label>
                                <?= form_input([
                                    'name' => 'no_nota_asal',
                                    'id' => 'no_nota_asal',
                                    'class' => 'form-control rounded-0',
                                    'value' => old('no_nota_asal', $retur->no_nota_asal),
                                    'placeholder' => 'Nomor nota pembelian asal',
                                    'readonly' => true
                                ]) ?>
                            </div>
                        </div>

                        <!-- User Receiver -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_user_terima">User Penerima</label>
                                <select name="id_user_terima" id="id_user_terima" class="form-control select2 rounded-0">
                                    <option value="">Pilih User</option>
                                    <?php if (isset($users) && is_array($users)): ?>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user->id ?>" <?= $retur->id_user_terima == $user->id ? 'selected' : '' ?>>
                                                <?= esc($user->first_name . ' ' . $user->last_name) ?> (<?= esc($user->username) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- PPN Status -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_ppn">Status PPN</label>
                                <select name="status_ppn" id="status_ppn" class="form-control select2 rounded-0">
                                    <option value="0" <?= ($retur->status_ppn ?? '0') == '0' ? 'selected' : '' ?>>Non PPN</option>
                                    <option value="1" <?= ($retur->status_ppn ?? '0') == '1' ? 'selected' : '' ?>>Dengan PPN</option>
                                    <option value="2" <?= ($retur->status_ppn ?? '0') == '2' ? 'selected' : '' ?>>PPN Ditangguhkan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Return Status -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_retur">Status Retur</label>
                                <select name="status_retur" id="status_retur" class="form-control select2 rounded-0">
                                    <option value="0" <?= ($retur->status_retur ?? '0') == '0' ? 'selected' : '' ?>>Draft</option>
                                    <option value="1" <?= ($retur->status_retur ?? '0') == '1' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Return Reason -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="alasan_retur">Alasan Retur</label>
                                <?= form_textarea([
                                    'name' => 'alasan_retur',
                                    'id' => 'alasan_retur',
                                    'class' => 'form-control rounded-0',
                                    'rows' => 3,
                                    'value' => old('alasan_retur', $retur->alasan_retur),
                                    'placeholder' => 'Masukkan alasan retur barang...'
                                ]) ?>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="catatan">Catatan</label>
                                <?= form_textarea([
                                    'name' => 'catatan',
                                    'id' => 'catatan',
                                    'class' => 'form-control rounded-0',
                                    'rows' => 3,
                                    'value' => old('catatan', $retur->catatan),
                                    'placeholder' => 'Catatan tambahan...'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Display -->
                <div class="col-md-4">
                    <div class="card bg-light rounded-0">
                        <div class="card-header">
                            <h4 class="card-title">Total Retur</h4>
                        </div>
                                                 <div class="card-body">
                             <h2 class="text-center" id="displayTotal"><?= format_angka_rp($retur->jml_total ?? 0) ?></h2>
                         </div>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Items Table -->
            <div class="row">
                <div class="col-12">
                    <h5>Daftar Barang Retur</h5>
                    <div class="table-responsive">
                                                 <table class="table table-bordered rounded-0" id="items-table">
                             <thead>
                                 <tr>
                                     <th width="5%">No</th>
                                     <th width="35%">Item</th>
                                     <th width="10%">Jml</th>
                                     <th width="15%">Harga</th>
                                     <th width="10%">Diskon</th>
                                     <th width="20%">Subtotal</th>
                                     <th width="5%">Aksi</th>
                                 </tr>
                             </thead>
                             <tbody id="items-tbody">
                                 <!-- Items will be populated by JavaScript -->
                             </tbody>
                         </table>
                    </div>
                    
                                         <div class="mt-2">
                         <button type="button" class="btn btn-primary rounded-0" id="add-item">
                             <i class="fas fa-plus"></i> Tambah Baris
                         </button>
                         <button type="button" class="btn btn-danger rounded-0" id="remove-all-items">
                             <i class="fas fa-times"></i> Hapus Semua Baris
                         </button>
                     </div>
                </div>
            </div>

            <hr>

            <!-- Summary -->
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <div class="card rounded-0">
                        <div class="card-body">
                                                         <div class="row mb-2">
                                 <div class="col-6"><strong>Subtotal:</strong></div>
                                 <div class="col-6 text-right">
                                     <span id="display-subtotal"><?= format_angka($retur->jml_subtotal ?? 0, 2) ?></span>
                                 </div>
                             </div>
                             
                             <div class="row mb-2" id="ppn-row" style="display: <?= ($retur->status_ppn ?? '0') == '1' ? 'flex' : 'none' ?>">
                                 <div class="col-6"><strong>PPN (11%):</strong></div>
                                 <div class="col-6 text-right">
                                     <span id="display-ppn"><?= format_angka($retur->jml_ppn ?? 0, 2) ?></span>
                                 </div>
                             </div>
                             
                             <hr>
                             
                             <div class="row">
                                 <div class="col-6"><strong>Total:</strong></div>
                                 <div class="col-6 text-right">
                                     <strong><span id="display-total"><?= format_angka($retur->jml_total ?? 0, 2) ?></span></strong>
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
                    <!-- Additional options can go here -->
                </div>
                <div class="col-md-6 text-right">
                    <a href="<?= base_url('transaksi/retur/beli') ?>" class="btn btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <?= form_submit('submit', 'Update', [
                        'class' => 'btn btn-primary rounded-0'
                    ]) ?>
                </div>
            </div>
        <?= form_close() ?>
    </div>
 </div>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Produk</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control rounded-0" id="productSearch" placeholder="Cari produk...">
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
         theme: 'bootstrap4',
         width: '100%'
     });

    // Debug: Log initial values
    console.log('Initial id_beli value:', $('#id_beli').val());
    console.log('Initial id_supplier value:', $('#id_supplier').val());
     
     // Initialize autonumber for existing price fields
     $('.autonumber').autoNumeric('init', {
         aSep: '.',
         aDec: ',',
         mDec: '0'
     });
    
         let itemsData = [];
     let itemCounter = 0;
     let currentEditingRow = null;

    // Load existing items if editing
    <?php if (isset($retur->items) && !empty($retur->items)): ?>
        <?php foreach ($retur->items as $item): ?>
            addProductRowWithData(
                '<?= esc($item->kode ?? $item->item_kode ?? '') ?>',
                '<?= esc($item->item ?? $item->item_nama ?? '') ?>',
                <?= $item->jml_retur ?? 1 ?>,
                <?= $item->harga ?? 0 ?>,
                <?= $item->id_item ?? 0 ?>
            );
        <?php endforeach; ?>
        calculateTotals();
    <?php else: ?>
        // Add first product row if no existing items
        addProductRow();
    <?php endif; ?>

         // Event listeners
     $('#id_beli').on('change', updatePurchaseFields);
     $('#status_ppn').on('change', calculateTotals);
     $('#add-item').on('click', addProductRow);
     $('#remove-all-items').on('click', removeAllRows);
     
     // Product search
     $('#productSearch').on('input', function() {
         searchProducts($(this).val());
     });

         // Dynamic event listeners
     $(document).on('click', '.remove-item', function() {
         const rowId = $(this).data('row-id');
         removeProductRow(rowId);
     });

     $(document).on('input', '.product-qty, .product-price, .product-discount', function() {
         const rowId = $(this).closest('tr').attr('id').replace('productRow_', '');
         calculateRowTotal(rowId);
     });

     // Product action buttons
     $(document).on('click', '[data-action="search"]', function() {
         const rowId = $(this).data('row-id');
         openProductModal(rowId);
     });

     $(document).on('click', '[data-action="clear"]', function() {
         const rowId = $(this).data('row-id');
         clearProduct(rowId);
     });

     $(document).on('click', '[data-action="select-product"]', function() {
         const productId = $(this).data('product-id');
         const productName = $(this).data('product-name');
         const productCode = $(this).data('product-code');
         const productPrice = $(this).data('product-price');
         const category = $(this).data('category');
         const brand = $(this).data('brand');
         selectProduct(productId, productName, productCode, productPrice, category, brand);
     });

         // Form validation
     $('#form-retur-edit').on('submit', function(e) {
        
        // Validate required fields - Multiple validation approaches for reliability
        const idBeliValue = $('#id_beli').val();
        const idBeliSelected = $('#id_beli option:selected').val();
        const idBeliData = $('#id_beli').select2('data');
        
        // Debug logs (remove these after testing)
        console.log('=== Purchase Transaction Validation ===');
        console.log('id_beli value:', idBeliValue);
        console.log('id_beli selected option value:', idBeliSelected);
        console.log('id_beli select2 data:', idBeliData);
        console.log('id_beli element exists:', $('#id_beli').length);
        
        // More comprehensive validation
        let hasValidPurchase = false;
        
        // Method 1: Check direct value
        if (idBeliValue && idBeliValue !== '' && idBeliValue !== '0' && idBeliValue !== null) {
            hasValidPurchase = true;
        }
        
        // Method 2: Check selected option value
        if (!hasValidPurchase && idBeliSelected && idBeliSelected !== '' && idBeliSelected !== '0' && idBeliSelected !== null) {
            hasValidPurchase = true;
        }
        
        // Method 3: Check select2 data
        if (!hasValidPurchase && idBeliData && idBeliData.length > 0 && idBeliData[0].id && idBeliData[0].id !== '' && idBeliData[0].id !== '0') {
            hasValidPurchase = true;
        }
        
        console.log('Final validation result:', hasValidPurchase);
        
        if (!hasValidPurchase) {
            e.preventDefault();
            toastr.error('Transaksi pembelian harus dipilih');
            $('#id_beli').select2('open');
            return false;
        }
        
        if (!$('#id_supplier').val()) {
            e.preventDefault();
            toastr.error('Supplier harus dipilih');
            return false;
        }
        
        if (!$('#tgl_retur').val()) {
            e.preventDefault();
            toastr.error('Tanggal retur harus diisi');
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
        
        // Collect items data before submission
        let items = [];
        $('#items-tbody tr').each(function() {
            const row = $(this);
            const id_item = row.find('.product-id').val();
            const qty = row.find('.product-qty').val();
            const harga = row.find('.product-price').autoNumeric('get');
            const kode = row.find('.product-code').val();
            const produk = row.find('.product-name').val();
            const satuan = row.find('.product-satuan').val() || 'PCS';
            const discount = row.find('.product-discount').val() || 0;
            
            if (id_item && qty && parseFloat(qty) > 0) {
                items.push({
                    id_item: id_item,
                    qty: parseFloat(qty),
                    harga: parseFloat(harga) || 0,
                    kode: kode || '',
                    produk: produk || '',
                    satuan: satuan,
                    discount: parseFloat(discount) || 0
                });
            }
        });
        
        console.log('Collected items:', items);
        
        if (items.length === 0) {
            e.preventDefault();
            toastr.error('Minimal satu produk dengan quantity valid harus ditambahkan');
            return false;
        }
        
        // Add items data to form as JSON
        if ($('input[name="items"]').length === 0) {
            $(this).append('<input type="hidden" name="items" value="">');
        }
        $('input[name="items"]').val(JSON.stringify(items));
        
        toastr.success('Memproses update retur...');
        return true;
    });

    function updatePurchaseFields() {
        const selectedOption = $('#id_beli option:selected');
        const supplierId = selectedOption.data('supplier');
        const noNota = selectedOption.data('no-nota');
        
        if (supplierId) {
            $('#id_supplier').val(supplierId).trigger('change');
        }
        
        if (noNota) {
            $('#no_nota_asal').val(noNota);
        }
    }

         function addProductRow() {
         itemCounter++;
         const rowHtml = `
             <tr id="productRow_${itemCounter}">
                 <td>${itemCounter}</td>
                 <td>
                     <div class="input-group">
                         <input type="text" class="form-control rounded-0 product-select" placeholder="Pilih produk..." readonly>
                         <div class="input-group-append">
                             <button type="button" class="btn btn-outline-secondary rounded-0" data-action="search" data-row-id="${itemCounter}">
                                 <i class="fas fa-search"></i>
                             </button>
                             <button type="button" class="btn btn-outline-secondary rounded-0" data-action="clear" data-row-id="${itemCounter}">
                                 <i class="fas fa-times"></i>
                             </button>
                         </div>
                     </div>
                     <input type="hidden" name="items[${itemCounter}][id_item]" class="product-id">
                     <input type="hidden" name="items[${itemCounter}][produk]" class="product-name">
                     <input type="hidden" name="items[${itemCounter}][kode]" class="product-code">
                 </td>
                 <td>
                     <input type="number" class="form-control rounded-0 product-qty" name="items[${itemCounter}][qty]" 
                            value="1" min="1" step="1">
                 </td>
                 <td>
                     <input type="text" class="form-control rounded-0 product-price autonumber" name="items[${itemCounter}][harga]" 
                            value="0" readonly>
                 </td>
                 <td>
                     <div class="input-group">
                         <input type="number" class="form-control rounded-0 product-discount" name="items[${itemCounter}][diskon]" 
                                value="0" min="0" max="100" step="0.01">
                         <div class="input-group-append">
                             <span class="input-group-text">%</span>
                         </div>
                     </div>
                     <small class="text-muted discount-amount">Rp 0,00</small>
                 </td>
                 <td>
                     <input type="number" class="form-control rounded-0 product-total" name="items[${itemCounter}][jumlah]" 
                            value="0" readonly>
                 </td>
                 <td>
                     <button type="button" class="btn btn-danger btn-sm rounded-0 remove-item" data-row-id="${itemCounter}">
                         <i class="fas fa-times"></i>
                     </button>
                 </td>
             </tr>
         `;
         
         $('#items-tbody').append(rowHtml);
         
         // Initialize autonumber for new price field
         $(`#productRow_${itemCounter} .autonumber`).autoNumeric('init', {
             aSep: '.',
             aDec: ',',
             mDec: '0'
         });
         
         toastr.success('Baris produk berhasil ditambahkan');
     }

     function addProductRowWithData(productCode, productName, qty, price, productId) {
         itemCounter++;
         const rowHtml = `
             <tr id="productRow_${itemCounter}">
                 <td>${itemCounter}</td>
                 <td>
                     <div class="input-group">
                                                   <input type="text" class="form-control rounded-0 product-select" placeholder="Pilih produk..." value="${productName}" readonly>
                          <div class="input-group-append">
                              <button type="button" class="btn btn-outline-secondary rounded-0" data-action="search" data-row-id="${itemCounter}">
                                  <i class="fas fa-search"></i>
                              </button>
                              <button type="button" class="btn btn-outline-secondary rounded-0" data-action="clear" data-row-id="${itemCounter}">
                                  <i class="fas fa-times"></i>
                              </button>
                          </div>
                     </div>
                     <input type="hidden" name="items[${itemCounter}][id_item]" class="product-id" value="${productId}">
                     <input type="hidden" name="items[${itemCounter}][produk]" class="product-name" value="${productName}">
                     <input type="hidden" name="items[${itemCounter}][kode]" class="product-code" value="${productCode}">
                 </td>
                 <td>
                     <input type="number" class="form-control rounded-0 product-qty" name="items[${itemCounter}][qty]" 
                            value="${qty}" min="1" step="1">
                 </td>
                 <td>
                     <input type="text" class="form-control rounded-0 product-price autonumber" name="items[${itemCounter}][harga]" 
                            value="${price}" readonly>
                 </td>
                 <td>
                     <div class="input-group">
                         <input type="number" class="form-control rounded-0 product-discount" name="items[${itemCounter}][diskon]" 
                                value="0" min="0" max="100" step="0.01">
                         <div class="input-group-append">
                             <span class="input-group-text">%</span>
                         </div>
                     </div>
                     <small class="text-muted discount-amount">Rp 0,00</small>
                 </td>
                 <td>
                     <input type="number" class="form-control rounded-0 product-total" name="items[${itemCounter}][jumlah]" 
                            value="${qty * price}" readonly>
                 </td>
                 <td>
                     <button type="button" class="btn btn-danger btn-sm rounded-0 remove-item" data-row-id="${itemCounter}">
                         <i class="fas fa-times"></i>
                     </button>
                 </td>
             </tr>
         `;
         
         $('#items-tbody').append(rowHtml);
         
         // Initialize autonumber for new price field
         $(`#productRow_${itemCounter} .autonumber`).autoNumeric('init', {
             aSep: '.',
             aDec: ',',
             mDec: '0'
         });
     }

     function removeProductRow(rowId) {
         $(`#productRow_${rowId}`).remove();
         renumberRows();
         calculateTotals();
         toastr.info('Produk berhasil dihapus');
     }

     function removeAllRows() {
         $('#items-tbody').empty();
         itemCounter = 0;
         addProductRow();
         toastr.warning('Semua baris berhasil dihapus');
     }

     function renumberRows() {
         $('#items-tbody tr').each(function(index) {
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
         $(`#productRow_${rowId} .product-price`).autoNumeric('set', '0');
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
                 toastr.error('Gagal memuat data produk');
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
                 toastr.error('Gagal mencari produk');
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
                     <td>Rp ${formatAngka(price, 0)}</td>
                     <td>${stock}</td>
                     <td>
                         <button type="button" class="btn btn-primary btn-sm rounded-0" data-action="select-product" 
                                 data-product-id="${product.id}" data-product-name="${itemName}" data-product-code="${productCode}" 
                                 data-product-price="${price}" data-category="${category}" data-brand="${brand}">
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
             $(`#productRow_${currentEditingRow} .product-price`).autoNumeric('set', productPrice);
             
             calculateRowTotal(currentEditingRow);
             $('#productModal').modal('hide');
             toastr.success('Produk berhasil dipilih');
         }
     }



    function calculateRowTotal(rowId) {
        const qty = parseFloat($(`#productRow_${rowId} .product-qty`).val()) || 0;
        const price = parseFloat($(`#productRow_${rowId} .product-price`).autoNumeric('get')) || 0;
        const discount = parseFloat($(`#productRow_${rowId} .product-discount`).val()) || 0;
        
        const subtotal = qty * price;
        const discountAmount = subtotal * (discount / 100);
        const total = subtotal - discountAmount;
        
        $(`#productRow_${rowId} .product-total`).val(total);
        $(`#productRow_${rowId} .discount-amount`).text(`Rp ${formatAngka(discountAmount, 0)}`);
        
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        
        // Calculate subtotal from products
        $('.product-total').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });

        const statusPpn = $('#status_ppn').val();
        let ppnAmount = 0;
        if (statusPpn === '1') {
            ppnAmount = subtotal * 0.11;
            $('#ppn-row').css('display', 'flex');
        } else {
            $('#ppn-row').hide();
        }

        const total = subtotal + ppnAmount;

        $('#display-subtotal').text(formatAngka(subtotal));
        $('#display-ppn').text(formatAngka(ppnAmount));
        $('#display-total').text(formatAngka(total));
        $('#displayTotal').text(formatAngkaRp(total));
    }



         function formatAngka(number, decimal = 2) {
         // Format similar to your format_angka helper
         if (number === null || number === '' || isNaN(number)) {
             return '0';
         }
         return parseFloat(number).toLocaleString('id-ID', {
             minimumFractionDigits: decimal,
             maximumFractionDigits: decimal
         });
     }

     function formatAngkaRp(number) {
         // Format similar to your format_angka_rp helper
         return 'Rp ' + formatAngka(number, 0);
     }
});
</script>
<?= $this->endSection() ?> 