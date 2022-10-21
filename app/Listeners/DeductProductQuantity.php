<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeductProductQuantity
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OrderCreated $event) // if $event Dealing with diffrents type of Event, dont determine type of Object (here OrderCreated)
    {
        $order = $event->order;
        // UPDATE products SET quantity = 'quantity - 1' so we use DB::raw()
        try {
            foreach ($order->products as $product) {
                $product->decrement('quantity', $product->order_item->quantity); // If we forget [ ->withPivot() ] the relation will return just the foreign key not the quantity and the other attributes

                // Product::where('id', '=', $item->product_id)
                //     ->update([
                //         'quantity' => DB::raw("quantity - {$item->quantity}"),
                //     ]);
            }

        } catch (Throwable $e) {

        }
    }
}
