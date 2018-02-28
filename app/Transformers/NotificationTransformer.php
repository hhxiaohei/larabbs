<?php

namespace App\Transformers;

use App\Models\Image;
use Illuminate\Notifications\DatabaseNotification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    public function transform (DatabaseNotification $notification)
    {
        return [
            'id'              => $notification->id,
            'type'            => $notification->type,
            'notifiable_id'   => $notification->notifiable_id,
            'notifiable_type' => $notification->notifiable_type,
            'data'            => $notification->data,
            'read_at'         => (string)$notification->read_at ?: null,
            'created_at'      => $notification->created_at->toDateTimeString(),
            'updated_at'      => $notification->updated_at->toDateTimeString(),
        ];
    }
}
