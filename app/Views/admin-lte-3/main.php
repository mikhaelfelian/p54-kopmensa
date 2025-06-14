<!-- In the head section -->
<link rel="stylesheet" href="<?= base_url('assets/plugins/toastr/toastr.min.css') ?>">
<link href="<?= base_url('assets/plugins/select2/css/select2.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>" rel="stylesheet">

<!-- Before closing body -->
<script src="<?= base_url('assets/plugins/toastr/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/jquery-number/jquery.number.min.js') ?>"></script>

<!-- Toastr configuration -->
<script>
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "5000"
};
</script> 