<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/TestRunner.php';

$tests = new TestRunner();

function createBooking(
    string $customerType = 'standard',
    string $passType = 'day',
    float $price = 50.0,
    int $quantity = 1,
    ?string $phone = '0600000000'
): Booking {
    $customer = new Customer(1, 'test@example.com', $phone, $customerType);
    $ticket = new Ticket('TEST', 'Ticket test', $price);
    $booking = new Booking(1, $customer, $passType);
    $booking->addItem(new BookingItem($ticket, $quantity));
    return $booking;
}

ob_start();
$service = new BookingService();

$standard = createBooking('standard', 'day', 50.0, 2);
$standardTotal = $service->confirm($standard, 'stripe');
$tests->near(100.0, $standardTotal, 'standard customer keeps initial total');
$tests->same('confirmed', $standard->status, 'booking becomes confirmed');

$vipUnderHundred = createBooking('vip', 'day', 45.0, 2);
$vipUnderHundredTotal = $service->confirm($vipUnderHundred, 'stripe');
$tests->same(85.5, $vipUnderHundredTotal, 'legacy VIP rule gives 5 percent discount');

$vipOverHundred = createBooking('vip', 'day', 60.0, 2);
$vipOverHundredTotal = $service->confirm($vipOverHundred, 'stripe');
$tests->near(108, $vipOverHundredTotal, 'legacy VIP rule gives 10 percent discount');

$threeDays = createBooking('standard', '3days', 60.0, 2);
$threeDaysTotal = $service->confirm($threeDays, 'stripe');
$tests->near(100.0, $threeDaysTotal, 'legacy three day pass discount is 10 euros');

$noTicket = createBooking('standard', 'day', 50.0, 0);
$noTicketTotal = $service->confirm($noTicket, 'stripe');
$tests->same(0.0, $noTicketTotal, 'booking is not possible');

$free = createBooking('standard', 'day', 0.0, 1);
$freeTotal = $service->confirm($free, 'stripe');
$tests->same(0.0, $freeTotal, 'free ticket is 0.0€');

$cheapThreeDays = createBooking('standard', '3days', 8.0, 1);
$cheapThreeDaysTotal = $service->confirm($cheapThreeDays, 'stripe');
$tests->same(0.0, $cheapThreeDaysTotal, 'total is 0.0 with three days pass');


ob_end_clean();
$tests->summary();

