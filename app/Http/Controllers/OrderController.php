<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }


    public function buy($productId)
    {
        try {

            $product = Product::findOrFail($productId);
            
            $user = Auth::user();
            
            $order = Order::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'status' => Order::STATUS_PENDING
            ]);
            
            $webhookUrl = route('payment.webhook');
            
            $paymentData = $this->paymentService->getPaymentUrl(
                $product->price,
                $webhookUrl
            );
            
            $order->payment_url = $paymentData['pay_url'];
            $order->order_id_payment_system = $paymentData['order_id'];
            $order->save();
            
            return response()->json([
                'pay_url' => $paymentData['pay_url']
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'data' => OrderResource::collection($orders)
        ]);
    }
}
