<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?> | <?= $Pengaturan->judul_app ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url($Pengaturan->favicon) ?>">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/dist/css/adminlte.min.css') ?>">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/toastr/toastr.min.css') ?>">

    <!-- Add reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?= config('Recaptcha')->siteKey ?>"></script>
    <style>
        /* Style for loading indicator */
        .loading {
            position: relative;
            pointer-events: none;
        }
        .loading:after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8) url('<?= base_url('public/assets/img/loading.gif') ?>') center no-repeat;
            z-index: 2;
        }
        /* Style for reCAPTCHA badge */
        .grecaptcha-badge {
            bottom: 60px !important;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-success">
            <div class="card-header text-center">
                <img src="<?= !empty($Pengaturan->logo_header) ? base_url($Pengaturan->logo_header) : base_url('public/assets/theme/admin-lte-3/dist/img/AdminLTELogo.png') ?>" alt="Logo" class="img-fluid"
                    style="width: 209px; height: 94px; background-color: #fff;">
            </div>
            <div class="card-body">
                <p class="login-box-msg">Silahkan Login</p>
                
                <!-- Error Display -->
                <?php if (isset($error) && !empty($error)): ?>
                    <div class="alert alert-danger alert-sm mb-3">
                        <strong>Error:</strong> <?= esc($error) ?>
                    </div>
                <?php endif; ?>

                <?= form_open('auth/cek_login_kasir', ['id' => 'login_form']) ?>
                <div class="input-group mb-3">
                    <?= form_input([
                        'type'          => 'text',
                        'name'          => 'user', 
                        'class'         => 'form-control rounded-0',
                        'placeholder'   => 'Pengguna ...',
                        'required'      => true
                    ]) ?>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <?= form_input([
                        'type'          => 'password',
                        'name'          => 'pass', 
                        'class'         => 'form-control rounded-0',
                        'placeholder'   => 'Kata Sandi ...',
                        'required'      => true
                    ]) ?>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Outlet Selection Dropdown -->
                <div class="input-group mb-3">
                    <select name="outlet" class="form-control rounded-0" required>
                        <option value="">Pilih Outlet ...</option>
                        <?php if (!empty($outlets)): ?>
                            <?php foreach ($outlets as $outlet): ?>
                                <option value="<?= $outlet->id ?>"><?= esc($outlet->nama) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>-- Tidak ada outlet aktif --</option>
                        <?php endif; ?>
                    </select>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-store"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <?= form_checkbox([
                                'id'    => 'remember',
                                'name'  => 'ingat',
                                'value' => '1'
                            ]) ?>
                            <label for="remember">
                                Ingat Saya
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block rounded-0" id="submitBtn">
                            <span>Masuk</span>
                            <i class="fas fa-spinner fa-spin d-none"></i>
                        </button>
                    </div>
                </div>

                <!-- Hidden reCAPTCHA token field -->
                <input type="hidden" name="recaptcha_response" id="recaptchaResponse">

                <?= form_close() ?>

                <!-- reCAPTCHA info -->
                <div class="text-center mt-3">
                    <small class="text-muted">
                        This site is protected by reCAPTCHA and the Google
                        <a href="https://policies.google.com/privacy">Privacy Policy</a> and
                        <a href="https://policies.google.com/terms">Terms of Service</a> apply.
                    </small>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery/jquery.min.js') ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/dist/js/adminlte.min.js') ?>"></script>
    <!-- Toastr -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/toastr/toastr.min.js') ?>"></script>

    <?php if (session()->has('toastr')): ?>
        <?php 
        $toastr = session('toastr');
        echo toast_show($toastr['message'], $toastr['type'], 'Login');
        ?>
    <?php endif ?>

    <!-- Add reCAPTCHA script -->
    <script>
    grecaptcha.ready(function() {
        const form = document.getElementById('login_form');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = submitBtn.querySelector('span');
        const btnLoader = submitBtn.querySelector('i');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            submitBtn.disabled = true;
            btnText.classList.add('d-none');
            btnLoader.classList.remove('d-none');
            form.classList.add('loading');

            // Execute reCAPTCHA
            grecaptcha.execute('<?= config('Recaptcha')->siteKey ?>', {action: 'login'})
                .then(function(token) {
                    // Add token to form
                    document.getElementById('recaptchaResponse').value = token;
                    // Submit form
                    form.submit();
                })
                .catch(function(error) {
                    // Handle error
                    console.error('reCAPTCHA error:', error);
                    toastr.error('Error verifying reCAPTCHA. Please try again.');
                    
                    // Reset loading state
                    submitBtn.disabled = false;
                    btnText.classList.remove('d-none');
                    btnLoader.classList.add('d-none');
                    form.classList.remove('loading');
                });
        });
    });
    </script>
</body>
</html>