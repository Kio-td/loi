<?php
function sendmail($sub, $mail, $user, $body) {
require '/var/www/loi/vendor/autoload.php';
$email = new \SendGrid\Mail\Mail();
$email->setFrom("Noreply@loi.nayami.party", "Legend of Ikaros");
$email->setSubject($sub);
$email->addTo($mail, $user);
$email->addContent("text/plain", $body);
$sendgrid = new \SendGrid("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");
try {
    $response = $sendgrid->send($email);
} catch (Exception $e) {
    echo 'We were unable to send your email. Please, notify Kio.';
}
}
?>
