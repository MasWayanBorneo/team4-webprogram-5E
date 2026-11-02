<?php
/* Pop-up hasil subscribe (dari api/subscribe.php → ?subscribe=...).
   Disisipkan di bawah setiap form newsletter dengan include; style .toast ada di base.css. */
$subscribe_messages = [
    // status => [ikon, judul, keterangan, class tambahan]
    'ok'      => ['★', 'You\'re subscribed!', 'Watch your inbox for news and updates.', ''],
    'exists'  => ['★', 'You\'re already subscribed', 'This email is already on our mailing list.', ''],
    'invalid' => ['!', 'Invalid email', 'Please enter a valid email address.', ' toast--error'],
    'error'   => ['!', 'Something went wrong', 'Please try again in a moment.', ' toast--error'],
];
$subscribe_status = $_GET['subscribe'] ?? '';

if (isset($subscribe_messages[$subscribe_status])):
    [$icon, $title, $text, $modifier] = $subscribe_messages[$subscribe_status];
?>
<div class="toast<?= $modifier ?>" id="subscribe-toast" role="<?= $modifier ? 'alert' : 'status' ?>">
    <span class="toast-icon" aria-hidden="true"><?= $icon ?></span>
    <div class="toast-body">
        <p class="toast-title"><?= $title ?></p>
        <p><?= $text ?></p>
    </div>
    <button type="button" class="toast-close" aria-label="Close">&times;</button>
</div>
<script>
    (() => {
        const toast = document.getElementById('subscribe-toast');
        const hideToast = () => toast.classList.add('is-hidden');

        toast.querySelector('.toast-close').addEventListener('click', hideToast);
        setTimeout(hideToast, 5000);

        // hapus ?subscribe=... dari alamat supaya pop-up tidak muncul lagi saat halaman di-refresh
        history.replaceState(null, '', location.pathname + location.hash);
    })();
</script>
<?php endif; ?>
