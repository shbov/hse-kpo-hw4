<?php

namespace App\Jobs;

use App\Models\Dish;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ServeOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Order $order;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // берем 1 заказ из пулла заказов
        $this->order = Order::where('status', 'created')->first();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Order {$this->order->id} is waiting the chef");
        $this->order->status = 'waiting';
        $this->order->save();

        sleep(1);

        Log::info("Order {$this->order->id} is being proceeded");
        $this->order->status = 'working';
        $this->order->save();
        sleep(1.5);

        // rand() статус, если canceled - возвращаем неиспользуемые блюда на склад
        if (rand(0, 99) < 33) {
            Log::info("Order {$this->order->id} is canceled");
            $this->freeDishes($this->order->dishes);
            $this->order->status = 'canceled';
        } else {
            Log::info("Order {$this->order->id} is finished");
            $this->order->status = 'finished';
        }

        $this->order->save();
    }

    /**
     * @param $dishes
     * @return void
     */
    private function freeDishes($dishes): void
    {
        foreach ($dishes as $record) {
            $pivotData = $record->pivot;
            $dish = Dish::find($record->id);
            if ($dish) {
                $dish->quantity += $pivotData->quantity;
                $dish->save();
            }
        }
    }
}
