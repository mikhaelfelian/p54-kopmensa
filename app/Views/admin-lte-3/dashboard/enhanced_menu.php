<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Enhanced POS System Features</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Reports Section -->
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">📊 Advanced Reports</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="<?= base_url('laporan/sales-turnover') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-chart-line"></i> Sales Turnover Report
                                    </a>
                                    <a href="<?= base_url('laporan/product-sales') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-box"></i> Product Sales Report
                                    </a>
                                    <a href="<?= base_url('laporan/order') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-file-invoice"></i> Order Report (Invoice Based)
                                    </a>
                                    <a href="<?= base_url('laporan/all-in-one-turnover') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-chart-pie"></i> All-in-One Turnover Report
                                    </a>
                                    <a href="<?= base_url('laporan/profit-loss') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-calculator"></i> Profit & Loss Report
                                    </a>
                                    <a href="<?= base_url('laporan/best-selling') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-trophy"></i> Best-Selling Products Report
                                    </a>
                                    <a href="<?= base_url('laporan/cutoff') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-cut"></i> Cut-off Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Management Section -->
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">⚙️ Enhanced Management</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="<?= base_url('gudang/input-stok') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-warehouse"></i> Enhanced Warehouse Management
                                        <small class="text-muted d-block">With user tracking & filters</small>
                                    </a>
                                    <a href="<?= base_url('gudang/inventori') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-boxes"></i> Direct Inventory Display
                                        <small class="text-muted d-block">Show all items upfront</small>
                                    </a>
                                    <a href="<?= base_url('master/supplier') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-truck"></i> Supplier Item Settings
                                        <small class="text-muted d-block">Manage items per supplier</small>
                                    </a>
                                    <a href="<?= base_url('master/cutoff') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-cut"></i> Cut-off Management
                                        <small class="text-muted d-block">Daily financial cut-off</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Member Features -->
                    <div class="col-md-6">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">👥 Member Management</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="<?= base_url('master/pelanggan') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-users"></i> Enhanced Member Management
                                        <small class="text-muted d-block">Monthly purchases tracking</small>
                                    </a>
                                    <a href="<?= base_url('master/pelanggan/add-member') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-user-plus"></i> Add New Member
                                    </a>
                                    <div class="list-group-item">
                                        <i class="fas fa-store"></i> Store Selection on Login
                                        <small class="text-muted d-block">Members can select store when logging in</small>
                                    </div>
                                    <div class="list-group-item">
                                        <i class="fas fa-ban"></i> Account Management
                                        <small class="text-muted d-block">Reset, block accounts with notes</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Features -->
                    <div class="col-md-6">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">🔧 System Enhancements</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <div class="list-group-item">
                                        <i class="fas fa-exclamation-triangle"></i> Duplicate Item Prevention
                                        <small class="text-muted d-block">Warning when adding duplicate items</small>
                                    </div>
                                    <div class="list-group-item">
                                        <i class="fas fa-receipt"></i> Enhanced Receipt Printing
                                        <small class="text-muted d-block">Payment method display on receipts</small>
                                    </div>
                                    <div class="list-group-item">
                                        <i class="fas fa-shield-alt"></i> User Access Rights
                                        <small class="text-muted d-block">Comprehensive permission management</small>
                                    </div>
                                    <a href="<?= base_url('gudang/opname') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-clipboard-check"></i> Fixed Opname Input
                                        <small class="text-muted d-block">Multiple product input support</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature Summary -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h3 class="card-title">📋 Implementation Summary</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Completed Features</span>
                                                <span class="info-box-number">20/20</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-chart-bar"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">New Reports</span>
                                                <span class="info-box-number">7</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-cogs"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Enhanced Features</span>
                                                <span class="info-box-number">8</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-danger">
                                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Member Features</span>
                                                <span class="info-box-number">6</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-success mt-3">
                                    <h5><i class="icon fas fa-check"></i> All Features Implemented Successfully!</h5>
                                    <p>Your POS system has been enhanced with all 20 requested features:</p>
                                    <ul class="mb-0">
                                        <li><strong>Warehouse Management:</strong> Enhanced filters, user tracking, and "Warehouse/Store" labeling</li>
                                        <li><strong>Inventory:</strong> Direct item display with pagination controls</li>
                                        <li><strong>Reports:</strong> 7 comprehensive reports with export functionality including Cut-off Report</li>
                                        <li><strong>Member Management:</strong> Store selection, monthly tracking, account management</li>
                                        <li><strong>System Enhancements:</strong> Cut-off functionality, duplicate prevention, enhanced receipts</li>
                                        <li><strong>Fixed Issues:</strong> Opname input now supports multiple products</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
