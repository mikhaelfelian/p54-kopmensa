<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>

<form action="<?= base_url('gudang/input_stok/store') ?>" method="POST" id="formInputStok">
    <?= csrf_field() ?>
    
    <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Form Input Stok</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tanggal Terima <span class="text-danger">*</span></label>
                                            <input type="datetime-local" name="tgl_terima" class="form-control" 
                                                   value="<?= old('tgl_terima', date('Y-m-d\TH:i')) ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Supplier <span class="text-danger">*</span></label>
                                            <select name="id_supplier" class="form-control rounded-0" required>
                                                <option value="">Pilih Supplier</option>
                                                <?php foreach ($supplierList as $supplier): ?>
                                                    <option value="<?= $supplier->id ?>" <?= old('id_supplier') == $supplier->id ? 'selected' : '' ?>>
                                                        <?= $supplier->nama ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Warehouse / Store <span class="text-danger">*</span></label>
                                            <select name="id_gudang" class="form-control rounded-0" required>
                                                <option value="">Pilih Warehouse / Store</option>
                                                <?php foreach ($gudangList as $gudang): ?>
                                                    <option value="<?= $gudang->id ?>" <?= old('id_gudang') == $gudang->id ? 'selected' : '' ?>>
                                                        <?= $gudang->nama ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan (opsional)"><?= old('keterangan') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Detail Item</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-success btn-sm" id="btnAddItem">
                                        <i class="fas fa-plus"></i> Tambah Item
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tableItems">
                                        <thead>
                                            <tr class="bg-secondary">
                                                <th width="30%">Item</th>
                                                <th width="15%">Satuan</th>
                                                <th width="15%">Jumlah</th>
                                                <th width="30%">Keterangan</th>
                                                <th width="10%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Belum ada item</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Aksi</h3>
                            </div>
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-save"></i> Simpan Input Stok
                                </button>
                                <a href="<?= base_url('gudang/input_stok') ?>" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<!-- Modal Add Item -->
<div class="modal fade" id="modalAddItem" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Item <span class="text-danger">*</span></label>
                    <select id="modalItemId" class="form-control rounded-0">
                        <option value="">Pilih Item</option>
                        <?php foreach ($itemList as $item): ?>
                            <option value="<?= $item->id ?>" data-kode="<?= $item->kode ?>"><?= $item->kode ?> - <?= $item->item ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Satuan <span class="text-danger">*</span></label>
                    <select id="modalSatuanId" class="form-control rounded-0">
                        <option value="">Pilih Satuan</option>
                        <?php foreach ($satuanList as $satuan): ?>
                            <option value="<?= $satuan->id ?>"><?= $satuan->satuanBesar ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah <span class="text-danger">*</span></label>
                    <input type="number" id="modalJumlah" class="form-control rounded-0" step="0.01" min="0.01">
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea id="modalKeterangan" class="form-control rounded-0" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveItem">Tambah</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
// Global variables following cashier.php pattern
let itemIndex = 0;
let items = [];

$(document).ready(function() {
    // Initialize form following cashier.php pattern
    initializeForm();
    
    // Event listeners following cashier.php pattern
    $('#btnAddItem').on('click', function() {
        openAddItemModal();
    });

    $('#btnSaveItem').on('click', function() {
        saveItemFromModal();
    });
    
    // Remove item event listener (delegated like in cashier.php)
    $(document).on('click', '.btnRemoveItem', removeItemFromTable);
    
    // Form validation on submit
    $('#formInputStok').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto clear modal when closed (like cashier.php)
    $('#modalAddItem').on('hidden.bs.modal', function() {
        clearModal();
    });
    
    // Item selection change handler
    $('#modalItemId').on('change', function() {
        const selectedItem = $(this).find('option:selected');
        if (selectedItem.val()) {
            console.log('Item selected:', selectedItem.text());
        }
    });
    
    // Quantity input validation and formatting
    $('#modalJumlah').on('input', function() {
        const value = parseFloat($(this).val()) || 0;
        if (value < 0) {
            $(this).val('');
        }
    });
    
    // Enter key handling like in cashier.php
    $('#modalJumlah').on('keypress', function(e) {
        if (e.which === 13) {
            saveItemFromModal();
        }
    });
    
    // Supplier change handler
    $('select[name="id_supplier"]').on('change', function() {
        const supplierId = $(this).val();
        if (supplierId) {
            console.log('Supplier selected:', supplierId);
        }
    });
    
    // Gudang change handler  
    $('select[name="id_gudang"]').on('change', function() {
        const gudangId = $(this).val();
        if (gudangId) {
            console.log('Gudang selected:', gudangId);
        }
    });
});

// Functions following cashier.php pattern
function initializeForm() {
    console.log('Input Stok form initialized');
    updateItemsDisplay();
}

function openAddItemModal() {
    $('#modalAddItem').modal('show');
    clearModal();
    // Focus first input like in cashier.php
    setTimeout(function() {
        $('#modalItemId').focus();
    }, 500);
}

function saveItemFromModal() {
    const itemId = $('#modalItemId').val();
    const itemText = $('#modalItemId option:selected').text();
    const satuanId = $('#modalSatuanId').val();
    const satuanText = $('#modalSatuanId option:selected').text();
    const jumlah = parseFloat($('#modalJumlah').val());
    const keterangan = $('#modalKeterangan').val();

    // Validation with toastr like cashier.php
    if (!itemId || !satuanId || !jumlah || jumlah <= 0) {
        if (typeof toastr !== 'undefined') {
            toastr.error('Mohon lengkapi semua field yang wajib diisi!');
        } else {
            alert('Mohon lengkapi semua field yang wajib diisi!');
        }
        return;
    }

    // Check for duplicates
    const isDuplicate = items.some(item => 
        item.itemId === itemId && item.satuanId === satuanId
    );
    
    if (isDuplicate) {
        if (typeof toastr !== 'undefined') {
            toastr.error('Item dengan satuan yang sama sudah ada!');
        } else {
            alert('Item dengan satuan yang sama sudah ada!');
        }
        return;
    }

    // Add to items array (like cart in cashier.php)
    const newItem = {
        itemId: itemId,
        itemText: itemText,
        satuanId: satuanId,
        satuanText: satuanText,
        jumlah: jumlah,
        keterangan: keterangan,
        index: itemIndex
    };
    
    items.push(newItem);
    addItemToTable(newItem);
    updateItemsDisplay();
    
    $('#modalAddItem').modal('hide');
    
    if (typeof toastr !== 'undefined') {
        toastr.success('Item berhasil ditambahkan');
    }
    
    itemIndex++;
}

function addItemToTable(item) {
    // Clear empty message if exists
    if ($('#tableItems tbody tr td[colspan="5"]').length > 0) {
        $('#tableItems tbody').empty();
    }

    const row = `
        <tr data-item-id="${item.itemId}" data-satuan-id="${item.satuanId}" data-index="${item.index}">
            <td>
                ${item.itemText}
                <input type="hidden" name="items[${item.index}][id_item]" value="${item.itemId}">
            </td>
            <td>
                ${item.satuanText}
                <input type="hidden" name="items[${item.index}][id_satuan]" value="${item.satuanId}">
            </td>
            <td class="text-right">
                ${numberFormat(item.jumlah)}
                <input type="hidden" name="items[${item.index}][jml]" value="${item.jumlah}">
            </td>
            <td>
                ${item.keterangan || '-'}
                <input type="hidden" name="items[${item.index}][keterangan]" value="${item.keterangan}">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm rounded-0 btnRemoveItem" data-index="${item.index}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#tableItems tbody').append(row);
}

function removeItemFromTable() {
    const index = parseInt($(this).data('index'));
    
    // Remove from items array
    items = items.filter(item => item.index !== index);
    
    // Remove from DOM
    $(this).closest('tr').remove();
    
    // Show empty message if no items
    if (items.length === 0) {
        $('#tableItems tbody').html('<tr><td colspan="5" class="text-center text-muted">Belum ada item</td></tr>');
    }
    
    updateItemsDisplay();
    
    if (typeof toastr !== 'undefined') {
        toastr.success('Item berhasil dihapus');
    }
}

function updateItemsDisplay() {
    const totalItems = items.length;
    console.log('Total items:', totalItems);
    
    // Could add summary display here if needed
    // Example: $('#itemCount').text(totalItems);
}

function clearModal() {
    $('#modalItemId').val('');
    $('#modalSatuanId').val('');
    $('#modalJumlah').val('');
    $('#modalKeterangan').val('');
}

function validateForm() {
    if (items.length === 0) {
        if (typeof toastr !== 'undefined') {
            toastr.error('Minimal harus ada satu item!');
        } else {
            alert('Minimal harus ada satu item!');
        }
        return false;
    }
    
    const supplier = $('select[name="id_supplier"]').val();
    const gudang = $('select[name="id_gudang"]').val();
    const tanggal = $('input[name="tgl_terima"]').val();
    
    if (!supplier) {
        if (typeof toastr !== 'undefined') {
            toastr.error('Supplier harus dipilih!');
        } else {
            alert('Supplier harus dipilih!');
        }
        return false;
    }
    
    if (!gudang) {
        if (typeof toastr !== 'undefined') {
            toastr.error('Gudang harus dipilih!');
        } else {
            alert('Gudang harus dipilih!');
        }
        return false;
    }
    
    if (!tanggal) {
        if (typeof toastr !== 'undefined') {
            toastr.error('Tanggal terima harus diisi!');
        } else {
            alert('Tanggal terima harus diisi!');
        }
        return false;
    }
    
    return true;
}

// Number formatting function like in cashier.php
function numberFormat(number) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(number || 0);
}

// Clear form function (like newTransaction in cashier.php)
function clearForm() {
    items = [];
    itemIndex = 0;
    $('#tableItems tbody').html('<tr><td colspan="5" class="text-center text-muted">Belum ada item</td></tr>');
    $('form')[0].reset();
    updateItemsDisplay();
    
    if (typeof toastr !== 'undefined') {
        toastr.success('Form berhasil dikosongkan');
    }
}
</script>
<?= $this->endSection() ?>
