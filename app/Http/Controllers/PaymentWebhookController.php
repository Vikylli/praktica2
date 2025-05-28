<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class PaymentWebhookController extends Controller
{
      public function handlePaymentWebhook(Request $request)
    {
        $orderId = $request->input('order_id');
        $status = $request->input('status');
        
        Log::info('Payment webhook received', [
            'order_id' => $orderId,
            'status' => $status
        ]);
        
        $order = Order::where('order_id_payment_system', $orderId)->first();
        
        if (!$order) {
            Log::warning('Order not found for payment webhook', [
                'order_id' => $orderId
            ]);
            
            return response()->noContent();
        }
        
        if ($status === 'success') {
            $order->status = Order::STATUS_SUCCESS;
        } else if ($status === 'failed') {
            $order->status = Order::STATUS_FAILED;
        }
        
        $order->save();
        
        return response()->noContent();
    }
}
