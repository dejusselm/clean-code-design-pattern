<?php

declare(strict_types=1);

final class BookingService
{
    public function __construct(
        private PayFastGatewayInterface $paymentGateway,
        private EmailService $emailService
    ) {}

     public function confirm(Booking $booking): float
    {
        if (count($booking->items) === 0) {
            throw new RuntimeException('Empty booking');
        }

        if (!filter_var($booking->customer->email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Invalid email');
        }
        
        $total = 0.0;

        foreach ($booking->items as $item) {
            if ($item->quantity <= 0) {
                throw new RuntimeException('Invalid quantity');
            }

            $total += $item->ticket->price * $item->quantity;
        }

        // Ancienne règle VIP : remise fixe de 10 %.
        if ($booking->customer->type === 'vip') {
            $total *= 0.90;
        }

        // Ancienne règle Pass 3 jours : remise fixe de 10 euros.
        if ($booking->passType === '3days') {
            $total -= 10.0;
        }

        // 3. Appel de l'adaptateur (Stripe ou PayFast selon ce qui a été injecté)
        $transactionId = $this->paymentGateway->charge($total, (string) $booking->id);
        echo "PAYMENT {$transactionId}" . PHP_EOL;

        $booking->status = 'confirmed';

        echo "SQL INSERT booking={$booking->id} total={$total} status={$booking->status}" . PHP_EOL;

        // 4. Utilisation du service d'email injecté
        $this->emailService->sendConfirmation($booking->customer->email, $booking->id);

        return $total;
    }
}
