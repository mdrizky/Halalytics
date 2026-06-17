<?php

namespace App\Events;

use App\Models\NutritionConsultationMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNutritionMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public NutritionConsultationMessage $message)
    {
        // No heavy loading here to keep ShouldBroadcastNow fast
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('consultation.' . $this->message->consultation_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'consultation_id' => $this->message->consultation_id,
            'sender_id' => $this->message->sender_user_id,
            'sender_role' => $this->message->sender_role,
            'message' => $this->message->body,
            'metadata' => $this->message->metadata,
            'read_at' => $this->message->read_at ? $this->message->read_at->toISOString() : null,
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.message';
    }
}
