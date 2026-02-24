<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CriticalLabResultDetected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $labResult;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(\App\Models\LabResult $labResult)
    {
        $this->labResult = $labResult;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('admin-lab-results');
    }
}
