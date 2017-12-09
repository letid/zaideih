<?php
/*
echo self::$data["link.privacy"];
print_r(self::$z[90]);
*/
$to      = 'khensolomon@gmail.com';
$subject = 'test email from GCE';
$message = 'hello test email from GCE';
$headers = 'From: developer@zomi.today' . "\r\n" .
    'Reply-To: developer@zomi.today' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);