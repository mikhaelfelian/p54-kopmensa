<?php if (isset($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.<?= $message['type'] ?>('<?= $message['message'] ?>');
        });
    </script>
<?php endif; ?> 