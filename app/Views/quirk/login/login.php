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

    <script src="<?= base_url('/public/assets/theme/quirk/lib/modernizr/modernizr.js') ?>"></script>
    <!-- reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?= model('ReCaptchaModel')->getSiteKey() ?>"></script>
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
                            'placeholder' => 'Enter Username'
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
                            'placeholder' => 'Enter Password'
                        ]) ?>
                    </div>
                </div>
                <div><a href="<?= base_url('auth/forgot-password') ?>" class="forgot">Forgot password?</a></div>
                <!-- Hidden input for reCAPTCHA token -->
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-quirk btn-block">Sign In</button>
                </div>
            <?= form_close() ?>
            
            <hr class="invisible">
            <div class="form-group">
                <a href="<?= base_url('auth/register') ?>" class="btn btn-default btn-quirk btn-stroke btn-stroke-thin btn-block btn-sign">
                    Not a member? Sign up now!
                </a>
            </div>
        </div>
    </div><!-- panel -->

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        grecaptcha.ready(function() {
            grecaptcha.execute('<?= model('ReCaptchaModel')->getSiteKey() ?>', {action: 'login'})
            .then(function(token) {
                document.getElementById('recaptcha_token').value = token;
                document.getElementById('loginForm').submit();
            });
        });
    });
    </script>
</body>
</html>