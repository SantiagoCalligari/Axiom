<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Exam;

class NewExamUploaded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Exam $exam
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
        return (new MailMessage)
            ->subject('Nuevo examen pendiente de aprobación')
            ->line('Se ha subido un nuevo examen que requiere tu aprobación.')
            ->line("Materia: {$this->exam->subject->name}")
            ->line("Título: {$this->exam->title}")
            ->line("Subido por: {$this->exam->uploader->name}")
            ->action('Ver examen', url("/admin/exams/{$this->exam->id}"))
            ->line('Por favor, revisa el examen y decide si aprobarlo o rechazarlo.');
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
            'uploader_id' => $this->exam->user_id,
            'uploader_name' => $this->exam->uploader->name,
            'title' => $this->exam->title,
            'message' => "Nuevo examen subido por {$this->exam->uploader->name} en {$this->exam->subject->name}",
        ];
    }
}
