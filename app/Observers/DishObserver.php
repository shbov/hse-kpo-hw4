<?php

namespace App\Observers;

use App\Models\Dish;
use Illuminate\Support\Facades\Log;

class DishObserver
{
    /**
     * Handle the Dish "creating" event.
     */
    public function creating(Dish $dish): void
    {
        $this->updateAvailable($dish);
    }

    /**
     * @param Dish $dish
     * @return void
     */
    private function updateAvailable(Dish $dish): void
    {
        $dish->is_available = $dish->quantity > 0;
    }

    /**
     * Handle the Dish "updating" event.
     */
    public function updating(Dish $dish): void
    {
        $this->updateAvailable($dish);
    }
}
