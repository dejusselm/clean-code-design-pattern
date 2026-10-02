<?php

class PaymentSupervisor
{
    public function supervise(string $paymentMethod, float $total): void
    {
        $startTime = hrtime(true);

        if ($paymentMethod === 'stripe' && $total > 0) {
            $stripe = new StripeClient();
            $transactionId = $stripe->charge($total);
            echo "PAYMENT {$transactionId} is successful" . PHP_EOL;
        }

        $paymentDurationInMs = ((hrtime(true)) - $startTime) / 1e6;

        echo "Payment duration : " . $paymentDurationInMs . " ms " . PHP_EOL;
    }
}