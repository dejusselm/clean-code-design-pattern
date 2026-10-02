<?php

declare(strict_types=1);

/**
 * SDK externe fourni par PayFast.
 * CONSIGNE : ne pas modifier cette classe.
 */
interface PayFastGatewayInterface{
    public function charge(float $amount, string $reference) : string;
}
class PayFasteAdapter implements PayFastGatewayInterface{
    public function charge(float $amount, string $reference) : string{
        $amount_cents = (int) round($amount * 100);

        $payload=[
            'reference'=>$reference,
            'amount_cents'=>$amount_cents,
            'currency'=> 'EUR',
        ];
        $result=$this->payFastSdk->executePayment($payload);
        return $result['transaction_id'];
    }
    public function __construct(
        private PayFastSdk $payFastSdk)
    {}
}
class StripeAdapter implements PayFastGatewayInterface{
    public function charge(float $amount, string $reference): string{
        $transactionId = $this->stripeClient->charge($amount);
        return $transactionId;
    }
    public function __construct(
        private StripeClient $stripeClient)
    {}

}
final class PayFastSdk
{
    /**
     * @param array{reference:string,amount_cents:int,currency:string} $payload
     * @return array{success:bool,transaction_id:string}
     */
    public function executePayment(array $payload): array
    {
        if (($payload['amount_cents'] ?? 0) <= 0) {
            return ['success' => false, 'transaction_id' => ''];
        }

        return [
            'success' => true,
            'transaction_id' => 'payfast_' . $payload['reference'],
        ];
    }
}
