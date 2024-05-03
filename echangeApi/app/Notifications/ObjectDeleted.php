<?php

namespace App\Notifications;

use App\Models\User;
use App\Support\Interfaces\EventNotifiableContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;

class ObjectDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    private EventNotifiableContract|Model $object;

    private User $causer;

    private string $appName;

    /**
     * Delete a new notification instance.
     *
     * @return void
     */
    public function __construct(EventNotifiableContract $object, User $causer)
    {
        $this->object = $object;
        $this->causer = $causer;
        $this->appName = env('APP_NAME');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $objectType = __('models.'.json_api()->getDefaultResolver()->getResourceType(get_class($this->object)));

        return (new MailMessage)
            ->subject(__(
                'notifications.object_deleted_subject',
                [
                    'appName' => $this->appName,
                    'objectType' => $objectType,
                    'objectName' => $this->object->getObjectName(),
                    'causer' => $this->causer->firstname.' '.$this->causer->lastname,
                ]
            ))
            ->line(__('notifications.notification_title'))
            ->line(__(
                'notifications.object_deleted_description',
                [
                    'appName' => $this->appName,
                    'objectType' => $objectType,
                    'objectName' => $this->object->getObjectName(),
                    'causer' => $this->causer->firstname.' '.$this->causer->lastname,
                ]
            ))
            ->line(__('notifications.notification_before_action'))
            ->action(__('notifications.show'), __('notifications.notifications_url', ['id' => $this->id]))
            ->line(__('notifications.notification_after_action'))
            ->line(__('notifications.notification_footer'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'event' => 'DELETED',
            'object_type' => json_api()->getDefaultResolver()->getResourceType(get_class($this->object)),
            'object_id' => $this->object->id ?? null,
            'object_name' => $this->object->getObjectName(),
            'object_class' => get_class($this->object),
            'object_data' => $this->object->toArray(),
            'causer' => $this->causer->toArray(),
        ];
    }
}
