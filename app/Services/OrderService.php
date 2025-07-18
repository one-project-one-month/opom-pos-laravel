<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public static function generateOrderNumber()
    {
        $date = date("Ymd");

        $latestOrder = Order::where('order_number', 'like', $date . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($latestOrder) {
            // Extract the sequence number from the latest order
            $lastSequence = (int) substr($latestOrder->order_number, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        // Format: YYYYMMDD + 4-digit sequence (e.g., 202501150001)
        $orderNumber = $date . str_pad($newSequence, 4, '0', STR_PAD_LEFT) + 0;

        return $orderNumber;
    }
}
