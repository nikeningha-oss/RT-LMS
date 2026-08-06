<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Location;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $location;

    public function __construct(Order $order, Location $location)
    {
        $this->order = $order;
        $this->location = $location;
    }

    public function broadcastOn()
    {
        return new Channel('tracking.' . $this->order->id);
    }

    public function broadcastWith()
    {
        return [
            'order_id' => $this->order->id,
            'latitude' => $this->location->latitude,
            'longitude' => $this->location->longitude,
            'speed' => $this->location->speed,
            'heading' => $this->location->heading,
            'recorded_at' => $this->location->recorded_at->toDateTimeString(),
        ];
    }
}