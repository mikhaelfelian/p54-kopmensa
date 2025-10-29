<!-- Unified Print Options Modal -->
<div class="modal fade" id="printOptionsModal" tabindex="-1" role="dialog" aria-labelledby="printOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printOptionsModalLabel">
                    <i class="fas fa-print"></i> Print Options
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <p class="mb-3">Choose print format:</p>
                    
                    <!-- Receipt Button -->
                    <div class="mb-2">
                        <button type="button" class="btn btn-success btn-block" onclick="printReceipt()">
                            <i class="fas fa-receipt"></i> Receipt (Thermal)
                        </button>
                        <small class="text-muted">For POS thermal printer</small>
                    </div>
                    
                    <!-- Invoice Button -->
                    <div class="mb-2">
                        <button type="button" class="btn btn-primary btn-block" onclick="printInvoice()">
                            <i class="fas fa-file-invoice"></i> Invoice (PDF)
                        </button>
                        <small class="text-muted">Formal A4 invoice</small>
                    </div>
                    
                    <!-- Report Button -->
                    <div class="mb-2">
                        <button type="button" class="btn btn-info btn-block" onclick="printReport()">
                            <i class="fas fa-file-alt"></i> Report (PDF)
                        </button>
                        <small class="text-muted">Detailed A4 report</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for print functions
var currentTransactionType = '';
var currentTransactionId = '';

/**
 * Show print options modal
 * @param {string} type - Transaction type (jual, beli, po)
 * @param {number} id - Transaction ID
 */
function showPrintOptions(type, id) {
    currentTransactionType = type;
    currentTransactionId = id;
    $('#printOptionsModal').modal('show');
}

/**
 * Print receipt (thermal/dot matrix)
 */
function printReceipt() {
    if (!currentTransactionType || !currentTransactionId) {
        toastr.error('Transaction data not available');
        return;
    }
    
    const url = `<?= base_url('print/receipt') ?>/${currentTransactionType}/${currentTransactionId}`;
    
    // Open in new window for thermal printer
    const printWindow = window.open(url, '_blank', 'width=400,height=600');
    
    if (printWindow) {
        printWindow.focus();
        // Auto-print after a short delay
        setTimeout(() => {
            printWindow.print();
        }, 500);
    } else {
        toastr.error('Please allow popups for printing');
    }
    
    $('#printOptionsModal').modal('hide');
}

/**
 * Print invoice (A4 PDF)
 */
function printInvoice() {
    if (!currentTransactionType || !currentTransactionId) {
        toastr.error('Transaction data not available');
        return;
    }
    
    const url = `<?= base_url('print/invoice') ?>/${currentTransactionType}/${currentTransactionId}`;
    
    // Download PDF
    window.open(url, '_blank');
    
    $('#printOptionsModal').modal('hide');
}

/**
 * Print report (A4 PDF)
 */
function printReport() {
    if (!currentTransactionType || !currentTransactionId) {
        toastr.error('Transaction data not available');
        return;
    }
    
    const url = `<?= base_url('print/report') ?>/${currentTransactionType}/${currentTransactionId}`;
    
    // Download PDF
    window.open(url, '_blank');
    
    $('#printOptionsModal').modal('hide');
}
</script>
