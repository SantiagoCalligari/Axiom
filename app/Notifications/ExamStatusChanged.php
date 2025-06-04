<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Exam;

class ExamStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Exam $exam,
        protected string $status,
        protected string $message
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = $this->status === 'approved' 
            ? 'Tu examen ha sido aprobado'
            : 'Tu examen ha sido rechazado';

        return (new MailMessage)
            ->subject($subject)
            ->line($this->message)
            ->line("Materia: {$this->exam->subject->name}")
            ->line("Título: {$this->exam->title}")
            ->action('Ver examen', url("/exams/{$this->exam->id}"));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'exam_id' => $this->exam->id,
            'subject_id' => $this->exam->subject_id,
            'subject_name' => $this->exam->subject->name,
            'title' => $this->exam->title,
            'status' => $this->status,
            'message' => $this->message,
        ];
    }
}
