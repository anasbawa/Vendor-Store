<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendOrderCreatedNotification
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
    public function handle(OrderCreated $event)
    {
        $order = $event->order;
        $user = User::where('store_id', $order->store_id)->first();
        $user->notify(new OrderCreatedNotification($order)); // User Must Be Notifiable
        // $user->notifyNow(new OrderCreatedNotification($order)); Used to give this notification the first priority if it is in a queue

        // $users = User::where('store_id', $order->store_id)->get();
        // Notification::send($users, new OrderCreatedNotification($order)); // If I want send notifiacation to multi users
        /*
        $users = User::where('store_id', $order->store_id)->get(); // If I want send notifiacation to multi users
        foreach ($users as $user) {
            $user->notify(new OrderCreatedNotification($order));
        } */


    }
}
