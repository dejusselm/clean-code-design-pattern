<?php
declare(strict_types=1);

require_once "BookingCalculator.php";
require_once "PostBookingService.php";
require_once "PaymentSupervisor.php";
final class BookingService
{
    public function confirm(Booking $booking, string $paymentMethod): float
    {
        if (empty($booking->items)) {
            return 0.0;
        }

        $calculator = new BookingCalculator();
        $total = $calculator->calculate($booking);

        $booking->status = 'confirmed';

        $paymentSupervisor = new PaymentSupervisor();
        $paymentSupervisor->supervise($booking, $paymentMethod, $total);

        return $total;
    }
}
