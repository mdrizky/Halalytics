<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HaramProductDetected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $nutritionScan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(\App\Models\NutritionScan $nutritionScan)
    {
        $this->nutritionScan = $nutritionScan;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('admin-nutrition-alerts');
    }
}
