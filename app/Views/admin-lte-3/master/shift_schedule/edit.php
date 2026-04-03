<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Ubah Jadwal Shift</h3>
            </div>
            <?= form_open(base_url('master/shift-schedule/update/' . $row->id), ['method' => 'post']) ?>
            <?= csrf_field() ?>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Outlet <span class="text-danger">*</span></label>
                    <select name="outlet_id" class="form-control rounded-0" required>
                        <?php foreach ($outlets as $o): ?>
                            <option value="<?= (int) $o->id ?>" <?= (int) old('outlet_id', $row->outlet_id) === (int) $o->id ? 'selected' : '' ?>><?= esc($o->nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Hari <span class="text-danger">*</span></label>
                    <select name="day_of_week" class="form-control rounded-0" required>
                        <?php
                        $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                        foreach ($days as $k => $label): ?>
                            <option value="<?= $k ?>" <?= (int) old('day_of_week', $row->day_of_week) === $k ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jam buka</label>
                            <input type="time" name="jam_buka" class="form-control rounded-0" value="<?= esc(old('jam_buka', $row->jam_buka ? substr($row->jam_buka, 0, 5) : '')) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jam tutup</label>
                            <input type="time" name="jam_tutup" class="form-control rounded-0" value="<?= esc(old('jam_tutup', $row->jam_tutup ? substr($row->jam_tutup, 0, 5) : '')) ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" class="form-control rounded-0" value="<?= esc(old('keterangan', $row->keterangan ?? '')) ?>">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control rounded-0">
                        <option value="1" <?= old('status', $row->status ?? '1') === '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= old('status', $row->status ?? '1') === '0' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary rounded-0">Simpan</button>
                <a href="<?= base_url('master/shift-schedule') ?>" class="btn btn-secondary rounded-0">Batal</a>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
