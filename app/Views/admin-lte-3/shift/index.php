<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- Active Shift Alert -->
        <?php if (!empty($activeShift)): ?>
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-2">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Anda Memiliki Shift Aktif!</strong>
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Kode Shift</small>
                                <strong class="text-dark"><?= esc($activeShift['shift_code']) ?></strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Outlet</small>
                                <strong class="text-dark"><?= esc($activeShift['outlet_name'] ?? 'N/A') ?></strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Waktu Buka</small>
                                <strong class="text-dark"><?= tgl_indo7($activeShift['start_at']) ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="<?= base_url('transaksi/shift/continue/' . $activeShift['id']) ?>" 
                           class="btn btn-success btn-lg rounded-0 mb-2" style="min-width: 150px;">
                            <i class="fas fa-play-circle"></i> Lanjutkan Shift
                        </a>
                        <br>
                        <a href="<?= base_url('transaksi/shift/close/' . $activeShift['id']) ?>" 
                           class="btn btn-warning btn-lg rounded-0" style="min-width: 150px;">
                            <i class="fas fa-stop-circle"></i> Tutup Shift
                        </a>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Main Card -->
        <div class="card card-outline card-primary rounded-0">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-clock text-primary"></i> 
                            <strong>Manajemen Shift</strong>
                        </h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="<?= base_url('master/shift-schedule') ?>" class="btn btn-outline-secondary btn-sm rounded-0" title="Master jadwal jam operasional per outlet">
                            <i class="fas fa-calendar-alt"></i> Pengaturan Jadwal
                        </a>
                        <a href="<?= base_url('transaksi/shift/open') ?>" class="btn btn-primary btn-sm rounded-0">
                            <i class="fas fa-plus-circle"></i> Buka Shift Baru
                        </a>
                        <button type="button" class="btn btn-info btn-sm rounded-0" onclick="location.reload()" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->

            <div class="card-body border-bottom py-3">
                <?= form_open('', ['method' => 'get', 'class' => 'form-horizontal', 'autocomplete' => 'off']) ?>
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label class="small text-muted mb-0">Tanggal mulai</label>
                        <input type="date" name="start_date" class="form-control form-control-sm rounded-0" value="<?= esc($filter_start_date ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-0">Tanggal akhir</label>
                        <input type="date" name="end_date" class="form-control form-control-sm rounded-0" value="<?= esc($filter_end_date ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-0">Status</label>
                        <select name="status" class="form-control form-control-sm rounded-0">
                            <option value="">Semua</option>
                            <option value="open" <?= (($filter_status ?? '') === 'open') ? 'selected' : '' ?>>Aktif</option>
                            <option value="closed" <?= (($filter_status ?? '') === 'closed') ? 'selected' : '' ?>>Ditutup</option>
                            <option value="approved" <?= (($filter_status ?? '') === 'approved') ? 'selected' : '' ?>>Disetujui</option>
                            <option value="void" <?= (($filter_status ?? '') === 'void') ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-0">Outlet</label>
                        <select name="outlet_id" class="form-control form-control-sm rounded-0">
                            <option value="">Semua outlet</option>
                            <?php foreach ($outlets ?? [] as $o): ?>
                                <option value="<?= (int) $o->id ?>" <?= (string)($filter_outlet_id ?? '') === (string) $o->id ? 'selected' : '' ?>><?= esc($o->nama) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-0">Kata kunci</label>
                        <input type="text" name="keyword" class="form-control form-control-sm rounded-0" placeholder="Kode shift / kasir" value="<?= esc($filter_keyword ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-0 d-block">Per halaman</label>
                        <select name="per_page" class="form-control form-control-sm rounded-0">
                            <?php $pp = (int) ($per_page ?? 25); ?>
                            <option value="25" <?= $pp === 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= $pp === 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $pp === 100 ? 'selected' : '' ?>>100</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-0 d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-sm rounded-0"><i class="fas fa-search"></i> Filter</button>
                        <a href="<?= base_url('transaksi/shift') ?>" class="btn btn-secondary btn-sm rounded-0">Reset</a>
                    </div>
                </div>
                <?= form_close() ?>
            </div>

            <!-- Summary Statistics -->
            <?php
            $totalFiltered = (int) ($total_shifts ?? count($shifts));
            $openShifts = 0;
            $closedShifts = 0;
            $approvedShifts = 0;
            $totalSales = 0;
            
            foreach ($shifts as $s) {
                switch ($s['status']) {
                    case 'open':
                        $openShifts++;
                        break;
                    case 'closed':
                        $closedShifts++;
                        break;
                    case 'approved':
                        $approvedShifts++;
                        break;
                }
                $totalSales += (float)($s['sales_cash_total'] ?? 0);
            }
            ?>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= number_format($totalFiltered) ?></h3>
                                <p>Total Shift <small class="text-white-50">(sesuai filter)</small></p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-list"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($openShifts) ?></h3>
                                <p>Shift Aktif <small class="text-white-50">(halaman ini)</small></p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-play-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($closedShifts) ?></h3>
                                <p>Shift Ditutup <small class="text-white-50">(halaman ini)</small></p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-stop-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?= number_format($totalSales, 0, ',', '.') ?></h3>
                                <p>Total Penjualan <small class="text-white-50">(kas, halaman ini)</small></p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped text-nowrap" id="shiftTable">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%" class="text-center">
                                <i class="fas fa-hashtag"></i>
                            </th>
                            <th width="10%">
                                <i class="fas fa-store"></i> Outlet
                            </th>
                            <th width="10%">
                                <i class="fas fa-calendar-alt"></i> Waktu Mulai
                            </th>
                            <th width="10%">
                                <i class="fas fa-calendar-check"></i> Waktu Selesai
                            </th>
                            <th width="8%" class="text-center">
                                <i class="fas fa-info-circle"></i> Status
                            </th>
                            <th width="10%" class="text-right">
                                <i class="fas fa-wallet"></i> Modal
                            </th>
                            <th width="5%" class="text-center">
                                <i class="fas fa-cog"></i> Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($shifts)) : ?>
                            <?php 
                            $no = (($current_page ?? 1) - 1) * ($per_page ?? 25);
                            foreach ($shifts as $shift) : 
                                $no++;
                            ?>
                                <tr>
                                    <td class="text-center text-muted">
                                        <strong><?= $no ?></strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-store text-muted"></i>
                                        <small><?= esc($shift['outlet_name'] ?? 'Outlet ID: ' . $shift['outlet_id']) ?></small><br/>
                                        <?= esc($shift['shift_code']) ?>
                                        <?php 
                                        $userName = trim(($shift['user_open_name'] ?? '') . ' ' . ($shift['user_open_lastname'] ?? ''));
                                        if (empty($userName) || $userName === 'Unknown') {
                                            echo br().'<small><em><span class="text-muted"><i class="fas fa-user-slash"></i> User ID: ' . ($shift['user_open_id'] ?? 'N/A') . '</span></em></small>';
                                        } else {
                                            echo br().'<small><em><i class="fas fa-user text-primary"></i> ' . esc($userName) . '</em></small>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="far fa-clock text-info"></i>
                                            <?= tgl_indo2($shift['start_at']) ?><br>
                                            <span class="text-muted"><?= date('H:i:s', strtotime($shift['start_at'])) ?></span>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($shift['end_at']): ?>
                                            <small>
                                                <i class="far fa-clock text-success"></i>
                                                <?= tgl_indo2($shift['end_at']) ?><br>
                                                <span class="text-muted"><?= date('H:i:s', strtotime($shift['end_at'])) ?></span>
                                            </small>
                                        <?php else: ?>
                                            <span class="badge badge-light">
                                                <i class="fas fa-minus"></i> Belum Selesai
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $config = statusShift($shift['status'] ?? null);
                                        ?>
                                        <span class="badge <?= $config['class'] ?> badge-lg">
                                            <i class="fas <?= $config['icon'] ?>"></i> <?= $config['text'] ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <?php 
                                        $pettyTotal = $shift['petty_in_total'] - $shift['petty_out_total'];
                                        $pettyClass = $pettyTotal >= 0 ? 'text-success' : 'text-danger';
                                        $pettyIcon = $pettyTotal >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                                        ?>
                                        <span class="<?= $pettyClass ?>">
                                            <i class="fas <?= $pettyIcon ?>"></i> <?= format_angka($pettyTotal) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group-vertical btn-group-sm">
                                            <?php if ($shift['status'] === 'open') : ?>
                                                <?php if ($shift['user_open_id'] == session('user_id')) : ?>
                                                    <a href="<?= base_url('transaksi/shift/continue/' . $shift['id']) ?>" 
                                                       class="btn btn-success btn-xs rounded-0" 
                                                       title="Lanjutkan Shift"
                                                       data-toggle="tooltip">
                                                        <i class="fas fa-play"></i>
                                                    </a>
                                                    <a href="<?= base_url('transaksi/shift/close/' . $shift['id']) ?>" 
                                                       class="btn btn-warning btn-xs rounded-0" 
                                                       title="Tutup Shift"
                                                       data-toggle="tooltip">
                                                        <i class="fas fa-stop"></i>
                                                    </a>
                                                <?php else : ?>
                                                    <button class="btn btn-secondary btn-xs rounded-0" 
                                                            disabled 
                                                            title="Hanya user yang membuka shift yang dapat menutup shift ini"
                                                            data-toggle="tooltip">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php elseif ($shift['status'] === 'closed') : ?>
                                                <a href="<?= base_url('transaksi/shift/print/' . $shift['id']) ?>" 
                                                   class="btn btn-info btn-xs rounded-0" 
                                                   title="Cetak Laporan"
                                                   target="_blank"
                                                   data-toggle="tooltip">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="<?= base_url('transaksi/shift/approve/' . $shift['id']) ?>" 
                                                   class="btn btn-success btn-xs rounded-0" 
                                                   title="Setujui Shift"
                                                   onclick="return confirm('Apakah Anda yakin ingin menyetujui shift ini?')"
                                                   data-toggle="tooltip">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php elseif ($shift['status'] === 'approved') : ?>
                                                <a href="<?= base_url('transaksi/shift/print/' . $shift['id']) ?>" 
                                                   class="btn btn-info btn-xs rounded-0" 
                                                   title="Cetak Laporan"
                                                   target="_blank"
                                                   data-toggle="tooltip">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="<?= base_url('transaksi/shift/view/' . $shift['id']) ?>" 
                                                   class="btn btn-primary btn-xs rounded-0" 
                                                   title="Lihat Detail"
                                                   data-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">Tidak ada data shift</p>
                                        <small>Klik "Buka Shift Baru" untuk memulai shift pertama</small>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (! empty($pager) && isset($total_shifts) && $total_shifts > 0): ?>
                <div class="card-footer clearfix">
                    <div class="float-left text-muted small pt-2">
                        Menampilkan <?= (($current_page ?? 1) - 1) * ($per_page ?? 25) + 1 ?>–<?= min(($current_page ?? 1) * ($per_page ?? 25), (int) $total_shifts) ?>
                        dari <?= (int) $total_shifts ?> shift
                    </div>
                    <div class="float-right">
                        <?= $pager->links('shift_hist', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif; ?>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .small-box {
        border-radius: 0.25rem;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        display: block;
        margin-bottom: 20px;
        position: relative;
    }
    
    .small-box > .inner {
        padding: 10px;
    }
    
    .small-box > .small-box-footer {
        background-color: rgba(0,0,0,.1);
        color: rgba(255,255,255,.8);
        display: block;
        padding: 3px 0;
        position: relative;
        text-align: center;
        text-decoration: none;
        z-index: 10;
    }
    
    .small-box .icon {
        color: rgba(0,0,0,.15);
        z-index: 0;
    }
    
    .small-box .icon > i {
        font-size: 70px;
        position: absolute;
        right: 15px;
        top: 15px;
        transition: -webkit-transform .3s linear;
        transition: transform .3s linear;
        transition: transform .3s linear,-webkit-transform .3s linear;
    }
    
    .badge-lg {
        padding: 0.5em 0.75em;
        font-size: 0.9em;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,.075);
        cursor: pointer;
    }
    
    .thead-light th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 2px;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
    
    .alert-warning {
        border-left: 4px solid #ffc107;
    }
    
    @media (max-width: 768px) {
        .small-box .inner h3 {
            font-size: 1.5rem;
        }
        
        .table-responsive {
            font-size: 0.85rem;
        }
        
        .btn-group-vertical {
            flex-direction: row;
        }
        
        .btn-group-vertical .btn {
            margin-right: 2px;
            margin-bottom: 0;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize DataTable if available
    if ($.fn.DataTable) {
        $('#shiftTable').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[0, "desc"]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        });
    }
    
    // Row click to view details
    $('#shiftTable tbody tr').on('click', function(e) {
        // Don't trigger if clicking on buttons or links
        if ($(e.target).closest('a, button').length === 0) {
            const shiftId = $(this).find('a[href*="/view/"]').attr('href');
            if (shiftId) {
                window.location.href = shiftId;
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
