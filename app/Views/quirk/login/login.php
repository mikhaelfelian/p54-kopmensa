<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $Pengaturan->judul_app ?? 'Kopmensa POS' ?></title>

    <link rel="stylesheet" href="<?= base_url('/public/assets/theme/quirk/lib/fontawesome/css/font-awesome.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/public/assets/theme/quirk/css/quirk.css') ?>">
    <!-- Add jQuery Gritter CSS -->
    <link rel="stylesheet" href="<?= base_url('/public/assets/theme/quirk/lib/jquery.gritter/jquery.gritter.css') ?>">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="<?= base_url('/public/assets/plugins/toastr/toastr.min.css') ?>">

    <script src="<?= base_url('/public/assets/theme/quirk/lib/modernizr/modernizr.js') ?>"></script>
    <!-- reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?= model('ReCaptchaModel')->getSiteKey() ?>"></script>
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
    <!-- jQuery -->
    <script src="<?= base_url('/public/assets/theme/quirk/lib/jquery/jquery.js') ?>"></script>
    <!-- Toastr JS -->
    <script src="<?= base_url('/public/assets/plugins/toastr/toastr.min.js') ?>"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?= base_url('/public/assets/theme/quirk/lib/html5shiv/html5shiv.js') ?>"></script>
    <script src="<?= base_url('/public/assets/theme/quirk/lib/respond/respond.src.js') ?>"></script>
    <![endif]-->
</head>

<body class="signwrapper">
    <div class="sign-overlay"></div>
    <div class="signpanel"></div>

    <div class="panel signin">
        <div class="panel-heading">
            <h1><?= $Pengaturan->judul_app ?? 'Kopmensa POS' ?></h1>
            <h4 class="panel-title">Welcome! Please signin.</h4>
        </div>
        <div class="panel-body">
            <?= form_open(base_url('auth/cek_login'), ['id' => 'loginForm']) ?>
            <div class="form-group mb10">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'username',
                        'class' => 'form-control',
                        'placeholder' => 'Enter Username',
                        'required' => true
                    ]) ?>
                </div>
            </div>
            <div class="form-group nomargin">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <?= form_input([
                        'type' => 'password',
                        'name' => 'password',
                        'class' => 'form-control',
                        'placeholder' => 'Enter Password',
                        'required' => true
                    ]) ?>
                </div>
            </div>
            <div><a href="<?= base_url('auth/forgot-password') ?>" class="forgot">Forgot password?</a></div>
            <!-- Hidden input for reCAPTCHA token -->
            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-quirk btn-block" id="submitBtn">
                    <span>Sign In</span>
                    <i class="fa fa-spinner fa-spin d-none"></i>
                </button>
            </div>
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
    </div>
    <!-- panel -->

    <script>
    grecaptcha.ready(function() {
        const form = document.getElementById('loginForm');
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

            // Execute reCAPTCHAapp/Models/ReCaptchaModel.php
            grecaptcha.execute('<?= model('ReCaptchaModel')->getSiteKey() ?>', {action: 'login'})
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

    <?php
    // Show toastr notification from flashdata if available
    if ($flash = session()->getFlashdata('toastr')) {
        echo toast_show($flash['message'], $flash['type'], $flash['title'] ?? '');
    }
    ?>
</body>

</html>