/**
 * Purchase Multi-Supplier Helper
 * Kopmensa POS - v1.0
 * 
 * This script enables multi-supplier selection for purchase orders.
 * When an item is selected, it dynamically loads all available suppliers
 * for that item from the tbl_m_item_supplier mapping table.
 * 
 * Dependencies:
 * - jQuery
 * - Select2
 * 
 * Usage in PO form view:
 * <script src="<?= base_url('assets/js/purchase-multi-supplier.js') ?>"></script>
 */

(function ($) {
    'use strict';

    /**
     * Initialize multi-supplier functionality
     * 
     * @param {Object} options Configuration options
     * @param {string} options.itemSelector - CSS selector for item dropdown
     * @param {string} options.supplierSelector - CSS selector for supplier dropdown
     * @param {string} options.apiUrl - Base URL for API endpoint (default: /transaksi/beli/get-suppliers-by-item/)
     */
    window.PurchaseMultiSupplier = {
        
        init: function(options) {
            var settings = $.extend({
                itemSelector: '#item',
                supplierSelector: '#supplier',
                apiUrl: window.baseUrl + '/transaksi/beli/get-suppliers-by-item/',
                debug: false
            }, options);

            var $itemSelect = $(settings.itemSelector);
            var $supplierSelect = $(settings.supplierSelector);

            // Initialize supplier Select2 for multi-select
            $supplierSelect.select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Pilih Supplier...',
                allowClear: true,
                multiple: true,
                data: []
            });

            // Handle item change event
            $itemSelect.on('change', function() {
                var itemId = $(this).val();
                
                if (settings.debug) {
                    console.log('[PurchaseMultiSupplier] Item changed:', itemId);
                }

                if (!itemId) {
                    // Clear supplier dropdown if no item selected
                    $supplierSelect.empty().trigger('change');
                    $supplierSelect.prop('disabled', true);
                    return;
                }

                // Load suppliers for selected item
                PurchaseMultiSupplier.loadSuppliers(itemId, settings, $supplierSelect);
            });
        },

        /**
         * Load suppliers for a specific item via AJAX
         * 
         * @param {number} itemId - Item ID
         * @param {Object} settings - Configuration settings
         * @param {jQuery} $supplierSelect - Supplier dropdown element
         */
        loadSuppliers: function(itemId, settings, $supplierSelect) {
            // Show loading state
            $supplierSelect.prop('disabled', true);
            $supplierSelect.empty().append('<option>Loading...</option>').trigger('change');

            $.ajax({
                url: settings.apiUrl + itemId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (settings.debug) {
                        console.log('[PurchaseMultiSupplier] Response:', response);
                    }

                    if (response.status === 'success' && response.data && response.data.length > 0) {
                        // Clear dropdown
                        $supplierSelect.empty();

                        // Populate supplier options
                        var supplierData = response.data.map(function(supplier) {
                            return {
                                id: supplier.id,
                                text: supplier.text,
                                kode: supplier.kode,
                                nama: supplier.nama,
                                alamat: supplier.alamat,
                                no_tlp: supplier.no_tlp,
                                harga_beli: supplier.harga_beli,
                                prioritas: supplier.prioritas
                            };
                        });

                        // Initialize Select2 with new data
                        $supplierSelect.select2({
                            theme: 'bootstrap4',
                            width: '100%',
                            placeholder: 'Pilih Supplier (bisa pilih lebih dari 1)...',
                            allowClear: true,
                            multiple: true,
                            data: supplierData,
                            templateResult: PurchaseMultiSupplier.formatSupplierOption,
                            templateSelection: PurchaseMultiSupplier.formatSupplierSelection
                        });

                        $supplierSelect.prop('disabled', false);

                        // Auto-select first supplier (highest priority)
                        if (supplierData.length > 0) {
                            $supplierSelect.val([supplierData[0].id]).trigger('change');
                        }

                        // Trigger custom event
                        $supplierSelect.trigger('suppliers:loaded', [response.data]);

                    } else {
                        // No suppliers found
                        $supplierSelect.empty();
                        $supplierSelect.select2({
                            theme: 'bootstrap4',
                            width: '100%',
                            placeholder: 'Tidak ada supplier untuk item ini',
                            data: []
                        });
                        $supplierSelect.prop('disabled', true);

                        if (settings.debug) {
                            console.warn('[PurchaseMultiSupplier] No suppliers found for item:', itemId);
                        }

                        // Show warning
                        if (typeof toastr !== 'undefined') {
                            toastr.warning('Item ini belum memiliki supplier. Silakan tambahkan mapping supplier terlebih dahulu.');
                        } else {
                            alert('Item ini belum memiliki supplier. Silakan tambahkan mapping supplier terlebih dahulu.');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('[PurchaseMultiSupplier] AJAX error:', error);
                    
                    $supplierSelect.empty();
                    $supplierSelect.select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: 'Error loading suppliers',
                        data: []
                    });
                    $supplierSelect.prop('disabled', true);

                    if (typeof toastr !== 'undefined') {
                        toastr.error('Gagal memuat data supplier: ' + error);
                    } else {
                        alert('Gagal memuat data supplier: ' + error);
                    }
                }
            });
        },

        /**
         * Format supplier option in dropdown (with details)
         * 
         * @param {Object} supplier - Supplier data
         * @returns {jQuery|string} Formatted HTML
         */
        formatSupplierOption: function(supplier) {
            if (!supplier.id) {
                return supplier.text;
            }

            var $option = $(
                '<div class="supplier-option">' +
                    '<div class="supplier-name"><strong>' + supplier.text + '</strong></div>' +
                    '<div class="supplier-details" style="font-size: 0.85em; color: #6c757d;">' +
                        (supplier.alamat ? '<div>Alamat: ' + supplier.alamat + '</div>' : '') +
                        (supplier.no_tlp ? '<div>Telp: ' + supplier.no_tlp + '</div>' : '') +
                        (supplier.harga_beli ? '<div>Harga: Rp ' + parseFloat(supplier.harga_beli).toLocaleString('id-ID') + '</div>' : '') +
                    '</div>' +
                '</div>'
            );

            return $option;
        },

        /**
         * Format selected supplier (compact display)
         * 
         * @param {Object} supplier - Supplier data
         * @returns {string} Formatted text
         */
        formatSupplierSelection: function(supplier) {
            return supplier.text || supplier.nama;
        },

        /**
         * Get selected supplier IDs
         * 
         * @param {string} supplierSelector - CSS selector for supplier dropdown
         * @returns {Array} Array of supplier IDs
         */
        getSelectedSuppliers: function(supplierSelector) {
            var $supplierSelect = $(supplierSelector || '#supplier');
            return $supplierSelect.val() || [];
        },

        /**
         * Get selected supplier data (with full details)
         * 
         * @param {string} supplierSelector - CSS selector for supplier dropdown
         * @returns {Array} Array of supplier objects
         */
        getSelectedSuppliersData: function(supplierSelector) {
            var $supplierSelect = $(supplierSelector || '#supplier');
            var selectedIds = $supplierSelect.val() || [];
            var suppliersData = [];

            selectedIds.forEach(function(id) {
                var option = $supplierSelect.find('option[value="' + id + '"]');
                if (option.length) {
                    suppliersData.push({
                        id: id,
                        text: option.text(),
                        data: option.data()
                    });
                }
            });

            return suppliersData;
        }
    };

})(jQuery);

/**
 * AUTO-INITIALIZATION
 * Automatically initialize if elements exist on page load
 */
$(document).ready(function() {
    if ($('#item').length && $('#supplier').length) {
        // Auto-init with default settings if both selectors exist
        // Can be overridden by manual init with custom settings
        if (typeof window.baseUrl === 'undefined') {
            window.baseUrl = $('base').attr('href') || '';
        }
        
        console.log('[PurchaseMultiSupplier] Auto-initializing...');
        PurchaseMultiSupplier.init();
    }
});

