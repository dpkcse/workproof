<?php
namespace App\Services\Billing\Gateways;use App\Data\Billing\PaymentGatewayResponse;use App\Models\{Invoice,Payment};
class ManualPaymentProvider implements PaymentGatewayInterface{public function createPayment(Invoice $invoice):PaymentGatewayResponse{return new PaymentGatewayResponse(true,'Manual payment request recorded.');}public function verifyPayment(Payment $payment):PaymentGatewayResponse{return new PaymentGatewayResponse($payment->status==='successful','Manual verification required.', $payment->transaction_reference);}public function providerName():string{return 'manual';}}
