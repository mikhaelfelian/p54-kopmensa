<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Shift Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item active">Shift Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if ($summary['success']): ?>
                <!-- Shift Summary Cards -->
                <?= format_shift_summary($summary) ?>
                
                <!-- Additional Details -->
                <div class="row">
                    <!-- Payment Methods Breakdown -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-credit-card"></i> Metode Pembayaran
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($summary['payment_methods'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Metode</th>
                                                    <th>Jumlah Transaksi</th>
                                                    <th>Total Nominal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($summary['payment_methods'] as $method): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-info">
                                                                <?= ucfirst($method['platform']) ?>
                                                            </span>
                                                        </td>
                                                        <td><?= number_format($method['count']) ?></td>
                                                        <td><?= format_angka($method['total_nominal'], 0) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Tidak ada data pembayaran</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Transactions -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-history"></i> Transaksi Terbaru
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($summary['recent_transactions'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>No Nota</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Waktu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($summary['recent_transactions'] as $transaction): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-secondary">
                                                                <?= $transaction['no_nota'] ?>
                                                            </span>
                                                        </td>
                                                        <td><?= format_angka($transaction['jml_gtotal'], 0) ?></td>
                                                        <td>
                                                            <?php
                                                            $statusClass = 'badge-secondary';
                                                            switch ($transaction['status']) {
                                                                case 'selesai':
                                                                    $statusClass = 'badge-success';
                                                                    break;
                                                                case 'pending':
                                                                    $statusClass = 'badge-warning';
                                                                    break;
                                                                case 'batal':
                                                                    $statusClass = 'badge-danger';
                                                                    break;
                                                            }
                                                            ?>
                                                            <span class="badge <?= $statusClass ?>">
                                                                <?= ucfirst($transaction['status']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?= date('d/m/Y H:i', strtotime($transaction['tgl_masuk'])) ?>
                                                            </small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Tidak ada transaksi</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie"></i> Grafik Transaksi
                                </h3>
                            </div>
                            <div class="card-body">
                                <canvas id="shiftChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Info -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Info:</strong> Data diambil dari shift ID: <?= $summary['shift_id'] ?> | 
                            Terakhir diperbarui: <?= date('d/m/Y H:i:s', strtotime($summary['generated_at'])) ?>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $summary['message'] ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($summary['success']): ?>
    // Create chart
    const ctx = document.getElementById('shiftChart').getContext('2d');
    const shiftChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Total Pendapatan', 'Total Diskon', 'Total PPN'],
            datasets: [{
                data: [
                    <?= $summary['summary']['total_amount'] ?>,
                    <?= $summary['summary']['total_discount'] ?>,
                    <?= $summary['summary']['total_ppn'] ?>
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#17a2b8'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            return label + ': ' + new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            }).format(value);
                        }
                    }
                }
            }
        }
    });
    <?php endif; ?>
    
    // Auto refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});
</script>
<?= $this->endSection() ?>
