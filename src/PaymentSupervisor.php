<?php
declare(strict_types=1);

class PaymentSupervisor
{
    public function supervise(Booking $booking, string $paymentMethod, float $total): void
    {
        $startTime = hrtime(true);

        if ($total > 0.0) {
            $transactionId = $this->paymentRedirection($paymentMethod, $total, (string) $booking->id);

            if ($transactionId !== '') {
                echo "PAYMENT {$transactionId} is successful" . PHP_EOL;
            } else {
                echo "PAYMENT FAILED: Unsupported payment method '{$paymentMethod}'" . PHP_EOL;
            }
        }

        $paymentDurationInMs = (hrtime(true) - $startTime) / 1e6;

        echo "Payment duration : " . number_format($paymentDurationInMs, 2) . " ms " . PHP_EOL;
    }

    public function paymentRedirection(string $paymentMethod, float $amount, string $bookingId = ''): string
    {
        if ($paymentMethod === 'stripe') {
            $stripeClient = new StripeClient();
            $stripeAdapter = new StripeAdapter($stripeClient);
            return $stripeAdapter->charge($amount, $bookingId);
        }

        if ($paymentMethod === 'payfast') {
            $payFastClient = new PayFastSdk();
            $payFastAdapter = new PayFastAdapter($payFastClient);
            return $payFastAdapter->charge($amount, $bookingId);
        }

        return '';
    }
}