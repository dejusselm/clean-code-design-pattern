<?php

declare(strict_types=1);

final class EmailService
{
    public function sendConfirmation(string $email, int $bookingId): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "EMAIL {$email}: booking {$bookingId} confirmed" . PHP_EOL;
        }

    }
}
