<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param Order $order
     * @return void
     */
    public function creating(Order $order): void
    {
        $order->user_id = auth()->user()->id;
    }
}
