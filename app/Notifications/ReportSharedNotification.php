<?php

namespace App\Notifications;

use App\Models\GeneratedReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportSharedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public GeneratedReport $report,
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
            ->subject("New report shared with you: {$this->report->title}")
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("{$this->sharer->name} shared a report with you.")
            ->line("**Report:** {$this->report->title}")
            ->line("**Period:** {$this->report->start_date} to {$this->report->end_date}");

        if ($this->note) {
            $message->line("**Note:** {$this->note}");
        }

        return $message
            ->action('Open in Court Management System', url('/reports'))
            ->line('You can download it from the Reports page.');
    }

    public function toArray($notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'title'     => $this->report->title,
            'by'        => $this->sharer->name ?? '',
        ];
    }
}
