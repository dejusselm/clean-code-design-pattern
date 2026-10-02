<?php
declare(strict_types=1);

require_once "BookingCalculator.php";
require_once "PostBookingService.php";
require_once "PaymentSupervisor.php";
final class BookingService
{
    public function confirm(Booking $booking, string $paymentMethod = 'stripe'): float
    {
        if (empty($booking->items)) {
            return 0.0;
        }

        $calculator = new BookingCalculator();
        $total = $calculator->calculate($booking);

        $paymentSupervisor = new PaymentSupervisor();
        $paymentSupervisor->supervise($paymentMethod, $total);

        $booking->status = 'confirmed';

        $postBookingService = new PostBookingService();
        $postBookingService->process($booking, $total);

        return $total;

        if (!filter_var($booking->customer->email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Invalid email');
        }

    }
}
