<?php
namespace App\Services\Billing\Gateways;use App\Data\Billing\PaymentGatewayResponse;use App\Models\{Invoice,Payment};
class SSLCommerzProvider implements PaymentGatewayInterface{public function createPayment(Invoice $invoice):PaymentGatewayResponse{return new PaymentGatewayResponse(false,'Provider is configured as a placeholder and cannot charge yet.');}public function verifyPayment(Payment $payment):PaymentGatewayResponse{return new PaymentGatewayResponse(false,'Provider verification is not configured yet.');}public function providerName():string{return 'sslcommerz';}}
