<?php
declare(strict_types=1);

require_once "BookingCalculator.php";
require_once "PostBookingService.php";
require_once "PaymentSupervisor.php";
final class BookingService
{
    public function __construct(
        private PayFastGatewayInterface $paymentGateway,
        private EmailService $emailService
    ) {
    }

    public function confirm(Booking $booking, string $paymentMethod): float
    {
        if (empty($booking->items)) {
            return 0.0;
        }

        $calculator = new BookingCalculator();
        $total = $calculator->calculate($booking);

        $paymentSupervisor = new PaymentSupervisor();
        $paymentSupervisor->supervise($paymentMethod, $total);

        $transactionId = $this->paymentGateway->charge($total, (string) $booking->id);
        echo "PAYMENT {$transactionId}" . PHP_EOL;

        $booking->status = 'confirmed';

        echo "SQL INSERT booking={$booking->id} total={$total} status={$booking->status}" . PHP_EOL;

        $this->emailService->sendConfirmation($booking->customer->email, $booking->id);

        return $total;

        if (!filter_var($booking->customer->email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Invalid email');
        }

    }
}
