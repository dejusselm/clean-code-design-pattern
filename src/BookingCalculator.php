<?php

class BookingCalculator
{
    public function calculate(Booking $booking): float
    {
        $total = 0.0;
        $customerStatus = $booking->customer->type;
        $passType = $booking->passType;

        foreach ($booking->items as $item) {
            if ($item->quantity > 0) {
                $total += $item->ticket->price * $item->quantity;
            }
        }
        if ($customerStatus === 'vip') {
            $total = $this->vipDiscount($total);
        }

        $total = $this->passDiscount($passType, $total);

        if ($total < 0.0) {
            return 0.0;
        }

        return $total;
    }

    private function vipDiscount(float $totalWithoutDiscount): float
    {
        $totalWithDiscount = $totalWithoutDiscount;
        if ($totalWithoutDiscount < 100) {
            return $totalWithDiscount *= 0.95;
        } else if ($totalWithoutDiscount >= 300) {
            return $totalWithDiscount *= 0.85;
        }
        return $totalWithDiscount *= 0.90;
    }

    private function passDiscount(string $passType, float $totalWithoutDiscount): float
    {
        $totalWithDiscount = $totalWithoutDiscount;
        if ($passType === '3days') {
            return $totalWithDiscount -= 20.0;
        }
        return $totalWithDiscount;
    }

    private function isBookingValid(Booking $booking): bool
    {
        if (count($booking->items) > 0) {
            return true;
        }
        return false;
    }
}