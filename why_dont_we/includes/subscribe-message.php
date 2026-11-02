<?php
/* Pesan hasil subscribe (dari api/subscribe.php → ?subscribe=...).
   Disisipkan di bawah setiap form newsletter dengan include. */
$subscribe_messages = [
    'ok'      => ['You\'re subscribed! Watch your inbox for updates.', ''],
    'exists'  => ['This email is already subscribed.', ''],
    'invalid' => ['Please enter a valid email address.', ' form-msg--error'],
    'error'   => ['Something went wrong. Please try again.', ' form-msg--error'],
];
$subscribe_status = $_GET['subscribe'] ?? '';

if (isset($subscribe_messages[$subscribe_status])):
    [$text, $modifier] = $subscribe_messages[$subscribe_status];
?>
<p class="form-msg<?= $modifier ?>" role="status"><?= $text ?></p>
<?php endif; ?>
