<?php
/**
 * Shared Print Receipt View
 * This view can be used by both index.php and cashier.php
 * 
 * Expected variables:
 * - $transactionData: Object containing transaction information
 * - $printType: 'pdf' or 'printer'
 * - $showButtons: boolean to show/hide print buttons
 */
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struk - <?= $transactionData->no_nota ?? 'DRAFT' ?></title>
    <style>
        @media print {
            body { margin: 0; padding: 10px; }
            .no-print { display: none; }
            .receipt { 
                font-family: 'Courier New', monospace; 
                font-size: <?= $printType === 'printer' ? '10px' : '12px' ?>; 
                line-height: <?= $printType === 'printer' ? '1.2' : '1.4' ?>;
                max-width: <?= $printType === 'printer' ? '200px' : '300px' ?>; 
                margin: 0 auto; 
                padding: <?= $printType === 'printer' ? '5px' : '10px' ?>;
            }
        }
        
        .receipt { 
            font-family: 'Courier New', monospace; 
            font-size: <?= $printType === 'printer' ? '10px' : '12px' ?>; 
            line-height: <?= $printType === 'printer' ? '1.2' : '1.4' ?>;
            max-width: <?= $printType === 'printer' ? '200px' : '300px' ?>; 
            margin: 0 auto; 
            padding: <?= $printType === 'printer' ? '5px' : '10px' ?>;
        }
        
        .header { text-align: center; margin-bottom: <?= $printType === 'printer' ? '10px' : '15px' ?>; }
        .divider { border-top: 1px dashed #000; margin: <?= $printType === 'printer' ? '8px 0' : '10px 0' ?>; }
        .item { margin: <?= $printType === 'printer' ? '3px 0' : '5px 0' ?>; }
        .total { font-weight: bold; margin: <?= $printType === 'printer' ? '8px 0' : '10px 0' ?>; }
        
        .payment { 
            margin: <?= $printType === 'printer' ? '8px 0' : '10px 0' ?>; 
            padding: <?= $printType === 'printer' ? '5px' : '8px' ?>; 
            background: #f8f9fa; 
            border-radius: <?= $printType === 'printer' ? '3px' : '5px' ?>; 
            border: 1px solid #dee2e6;
        }
        
        .payment-method-item {
            margin: <?= $printType === 'printer' ? '2px 0' : '3px 0' ?>; 
            padding: <?= $printType === 'printer' ? '1px 0' : '2px 0' ?>;
            display: flex;
            justify-content: space-between;
        }
        
        .payment-total {
            border-top: 1px dashed #000; 
            margin: <?= $printType === 'printer' ? '3px 0' : '5px 0' ?>; 
            padding-top: <?= $printType === 'printer' ? '3px' : '5px' ?>;
            font-weight: bold;
        }
        
        .payment-change {
            color: #28a745; 
            font-weight: bold; 
            margin-top: <?= $printType === 'printer' ? '3px' : '5px' ?>;
        }
        
        .footer { 
            text-align: center; 
            margin-top: <?= $printType === 'printer' ? '10px' : '15px' ?>; 
            font-size: <?= $printType === 'printer' ? '8px' : '10px' ?>; 
        }
        
        .btn { 
            background: <?= $printType === 'printer' ? '#28a745' : '#007bff' ?>; 
            color: white; 
            padding: <?= $printType === 'printer' ? '8px 16px' : '10px 20px' ?>; 
            border: none; 
            border-radius: <?= $printType === 'printer' ? '4px' : '5px' ?>; 
            cursor: pointer; 
            margin: <?= $printType === 'printer' ? '3px' : '5px' ?>;
            font-size: <?= $printType === 'printer' ? '12px' : '14px' ?>;
        }
        
        .btn:hover { 
            background: <?= $printType === 'printer' ? '#218838' : '#0056b3' ?>; 
        }
        
        .customer { margin: <?= $printType === 'printer' ? '5px 0' : '8px 0' ?>; }
        .items { margin: <?= $printType === 'printer' ? '5px 0' : '8px 0' ?>; }
        .summary { margin: <?= $printType === 'printer' ? '5px 0' : '8px 0' ?>; }
        
        .payment-method-header {
            text-align: center; 
            font-weight: bold; 
            color: #007bff; 
            margin: <?= $printType === 'printer' ? '3px 0' : '5px 0' ?>;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h3>KOPMENSA</h3>
            <div><?= $transactionData->outlet ?? 'OUTLET' ?></div>
            <div><?= $transactionData->date ?? date('d/m/Y H:i') ?></div>
            <div>No: <?= $transactionData->no_nota ?? 'DRAFT' ?></div>
        </div>
        
        <div class="divider"></div>
        
        <div class="customer">
            <div>Customer: <?= $transactionData->customer_name ?? 'UMUM' ?></div>
            <div>Type: <?= $transactionData->customer_type ?? 'UMUM' ?></div>
        </div>
        
        <?php if (!empty($transactionData->payment_methods)): ?>
            <div class="divider"></div>
            <div class="payment-method-header">
                💳 METODE PEMBAYARAN: 
                <?php
                $methodTypes = [];
                foreach ($transactionData->payment_methods as $pm) {
                    if ($pm->type === '1') $methodTypes[] = 'TUNAI';
                    elseif ($pm->type === '2') $methodTypes[] = 'NON TUNAI';
                    elseif ($pm->type === '3') $methodTypes[] = 'PIUTANG';
                    else $methodTypes[] = 'TUNAI';
                }
                echo implode(' + ', $methodTypes);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="divider"></div>
        
        <div class="items">
            <?php if (!empty($transactionData->items)): ?>
                <?php foreach ($transactionData->items as $item): ?>
                    <div class="item">
                        <div><?= esc($item->name ?? $item->produk ?? 'Unknown Product') ?></div>
                        <div><?= ($item->quantity ?? $item->jml ?? 1) ?> x Rp <?= number_format($item->price ?? $item->harga ?? 0, 0, ',', '.') ?> = Rp <?= number_format($item->total ?? $item->subtotal ?? 0, 0, ',', '.') ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="item">No items available</div>
            <?php endif; ?>
        </div>
        
        <div class="divider"></div>
        
        <div class="summary">
            <div>Subtotal: Rp <?= number_format($transactionData->subtotal ?? $transactionData->jml_subtotal ?? 0, 0, ',', '.') ?></div>
            <?php if (($transactionData->discount ?? 0) > 0): ?>
                <div>Diskon: <?= $transactionData->discount ?>%</div>
            <?php endif; ?>
            <?php if (!empty($transactionData->voucher)): ?>
                <div>Voucher: <?= esc($transactionData->voucher) ?></div>
            <?php endif; ?>
            <div>PPN (<?= $transactionData->ppn ?? 11 ?>%): Rp <?= number_format(($transactionData->subtotal ?? $transactionData->jml_subtotal ?? 0) * (($transactionData->ppn ?? 11) / 100), 0, ',', '.') ?></div>
            <div class="total">TOTAL: Rp <?= number_format($transactionData->total ?? $transactionData->jml_gtotal ?? 0, 0, ',', '.') ?></div>
        </div>
        
        <?php if (!empty($transactionData->payment_methods)): ?>
            <div class="divider"></div>
            <div class="payment">
                <div style="margin-bottom: 5px;"><strong>METODE PEMBAYARAN:</strong></div>
                <?php 
                $totalPayment = 0;
                foreach ($transactionData->payment_methods as $pm): 
                    $methodName = 'Tunai';
                    $methodIcon = '💳';
                    
                    if ($pm->type === '1') {
                        $methodName = 'TUNAI';
                        $methodIcon = '💵';
                    } elseif ($pm->type === '2') {
                        $methodName = 'NON TUNAI';
                        $methodIcon = '💳';
                    } elseif ($pm->type === '3') {
                        $methodName = 'PIUTANG';
                        $methodIcon = '📝';
                    }
                    
                    $amount = $pm->amount ?? $pm->nominal ?? 0;
                    $totalPayment += $amount;
                ?>
                    <div class="payment-method-item">
                        <span><?= $methodIcon ?> <?= $methodName ?></span>
                        <span style="font-weight: bold;">Rp <?= number_format($amount, 0, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="payment-total">
                    TOTAL PEMBAYARAN: Rp <?= number_format($totalPayment, 0, ',', '.') ?>
                </div>
                
                <?php 
                $grandTotal = $transactionData->total ?? $transactionData->jml_gtotal ?? 0;
                if ($totalPayment > $grandTotal): 
                    $change = $totalPayment - $grandTotal;
                ?>
                    <div class="payment-change">
                        KEMBALIAN: Rp <?= number_format($change, 0, ',', '.') ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="divider"></div>
        
        <div class="footer">
            <div>Terima kasih atas kunjungan Anda</div>
            <div>Barang yang sudah dibeli tidak dapat dikembalikan</div>
            <div>Powered by Kopmensa System</div>
        </div>
    </div>
    
    <?php if ($showButtons): ?>
        <div class="no-print" style="text-align: center; margin-top: <?= $printType === 'printer' ? '15px' : '20px' ?>;">
            <button class="btn" onclick="window.print()">
                <?= $printType === 'printer' ? 'Print to Dot Matrix' : 'Print PDF' ?>
            </button>
            <button class="btn" onclick="window.close()">Close</button>
        </div>
    <?php endif; ?>
</body>
</html> 