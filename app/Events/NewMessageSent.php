<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        $this->message->loadMissing('sender');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('consultation.' . $this->message->consultation_id),
            new PrivateChannel('private-consultation.' . $this->message->consultation_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'consultation_id' => $this->message->consultation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->full_name ?? $this->message->sender->username,
            'message' => $this->message->message,
            'attachment_path' => $this->message->attachment_path,
            'is_read' => $this->message->is_read,
            'created_at' => optional($this->message->created_at)->toISOString(),
        ];
    }
}
