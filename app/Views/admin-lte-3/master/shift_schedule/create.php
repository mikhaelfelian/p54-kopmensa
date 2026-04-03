<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Tambah Jadwal Shift</h3>
            </div>
            <?= form_open(base_url('master/shift-schedule/store'), ['method' => 'post']) ?>
            <?= csrf_field() ?>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Outlet <span class="text-danger">*</span></label>
                    <select name="outlet_id" class="form-control rounded-0" required>
                        <option value="">— Pilih —</option>
                        <?php foreach ($outlets as $o): ?>
                            <option value="<?= (int) $o->id ?>" <?= old('outlet_id') == $o->id ? 'selected' : '' ?>><?= esc($o->nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Hari <span class="text-danger">*</span></label>
                    <select name="day_of_week" class="form-control rounded-0" required>
                        <?php
                        $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                        foreach ($days as $k => $label): ?>
                            <option value="<?= $k ?>" <?= (string) old('day_of_week') === (string) $k ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jam buka</label>
                            <input type="time" name="jam_buka" class="form-control rounded-0" value="<?= esc(old('jam_buka')) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jam tutup</label>
                            <input type="time" name="jam_tutup" class="form-control rounded-0" value="<?= esc(old('jam_tutup')) ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" class="form-control rounded-0" value="<?= esc(old('keterangan')) ?>">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control rounded-0">
                        <option value="1" <?= old('status', '1') === '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= old('status') === '0' ? 'selected' : '' ?>>Nonaktif</option>
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
