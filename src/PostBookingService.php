<?php

class PostBookingService
{
    public function process(Booking $booking, float $total): void
    {
        $phoneNumber = $booking->customer->phone;

        $emailService = new EmailService();
        $emailService->sendConfirmation($booking->customer->email, $booking->id);

        $loyaltyService = new LoyaltyService();
        $loyaltyService->addPoints($booking->customer->id, 100);

        echo "SQL INSERT booking={$booking->id} total={$total} status={$booking->status}" . PHP_EOL;

        if ($phoneNumber) {
            $smsService = new SmsClient();
            $smsService->send($phoneNumber, "Your booking (booking n°{$booking->id}, price : {$total}) was confirmed.");
        }
    }
}