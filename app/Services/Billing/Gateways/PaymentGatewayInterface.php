<?php
namespace App\Services\Billing\Gateways;use App\Data\Billing\PaymentGatewayResponse;use App\Models\{Invoice,Payment};
interface PaymentGatewayInterface{public function createPayment(Invoice $invoice):PaymentGatewayResponse;public function verifyPayment(Payment $payment):PaymentGatewayResponse;public function providerName():string;}
