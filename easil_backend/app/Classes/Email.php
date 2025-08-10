<?php
class Email {
    public static function send(string $to, string $subject, string $body): bool {
        // Placeholder: integrate with SMTP or mail() later here
        // return mail($to, $subject, $body);
        return false; // not configured
    }
}