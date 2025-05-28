<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.payment.url', 'http://localhost:8081');
    }

    public function getPaymentUrl($price, $webhookUrl)
    {
        try {
            $response = Http::post($this->apiUrl . '/payments', [
                'price' => $price,
                'webhook_url' => $webhookUrl
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['pay_url']) && isset($data['order_id'])) {
                    return [
                        'pay_url' => $data['pay_url'],
                        'order_id' => $data['order_id']
                    ];
                }
                
                throw new Exception('Invalid response format: ' . json_encode($data));
            }
            
            throw new Exception('Payment service error: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Payment service error: ' . $e->getMessage(), [
                'price' => $price,
                'webhook_url' => $webhookUrl,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}