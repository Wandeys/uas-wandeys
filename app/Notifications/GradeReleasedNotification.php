<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class GradeReleasedNotification extends Notification
{
    use Queueable;

    protected $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengumuman Nilai Baru Dirilis')
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Nilai untuk mata kuliah {$this->class->course->name} ({$this->class->name}) telah difinalisasi.")
            ->action('Lihat KHS', route('khs.index'))
            ->line('Terima kasih telah menggunakan sistem informasi akademik kami!');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Nilai Baru Dirilis',
            'message' => "Nilai untuk mata kuliah {$this->class->course?->name} ({$this->class->name}) telah difinalisasi.",
            'action_url' => route('khs.index'),
        ];
    }
}
