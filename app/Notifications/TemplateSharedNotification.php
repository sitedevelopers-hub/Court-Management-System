<?php

namespace App\Notifications;

use App\Models\WorkTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TemplateSharedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkTemplate $template,
        public $sharer,
        public ?string $note = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("New template shared with you: {$this->template->title}")
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("{$this->sharer->name} shared a work template with you.")
            ->line("**Template:** {$this->template->title}");

        if ($this->note) {
            $message->line("**Note:** {$this->note}");
        }

        return $message
            ->action('Open in Court Management System', url('/work-templates'))
            ->line('You can download it from the Templates page.');
    }

    public function toArray($notifiable): array
    {
        return [
            'template_id' => $this->template->id,
            'title'       => $this->template->title,
            'by'          => $this->sharer->name ?? '',
        ];
    }
}
