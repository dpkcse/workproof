<?php
namespace App\Data\Billing;
class PaymentGatewayResponse{public function __construct(public bool $successful,public string $message='',public ?string $transactionReference=null,public array $metadata=[]){}}
