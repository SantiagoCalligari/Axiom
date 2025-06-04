<?php

namespace App\Observers;

use App\Models\Exam;
use App\Notifications\NewExamUploaded;
use App\Notifications\ExamStatusChanged;

class ExamObserver
{
    /**
     * Handle the Exam "created" event.
     */
    public function created(Exam $exam): void
    {
        // Cargar las relaciones necesarias
        $exam->load(['subject.administrators', 'subject.career.administrators', 'subject.career.university.administrators']);

        // Notificar a los administradores de la materia
        if ($exam->subject && $exam->subject->administrators) {
            foreach ($exam->subject->administrators as $admin) {
                if ($admin) {
                    $admin->notify(new NewExamUploaded($exam));
                }
            }
        }
    }

    /**
     * Handle the Exam "updated" event.
     */
    public function updated(Exam $exam): void
    {
        // Si el examen fue aprobado o rechazado, notificar al subidor
        if ($exam->isDirty('approval_status')) {
            $status = $exam->approval_status;
            $message = $status === 'approved' 
                ? 'Tu examen ha sido aprobado y ahora está visible para todos los usuarios.'
                : "Tu examen ha sido rechazado. Razón: {$exam->rejection_reason}";

            // Cargar la relación con el subidor
            $exam->load('uploader');
            if ($exam->uploader) {
                $exam->uploader->notify(new ExamStatusChanged($exam, $status, $message));
            }
        }
    }

    /**
     * Handle the Exam "deleted" event.
     */
    public function deleted(Exam $exam): void
    {
        //
    }

    /**
     * Handle the Exam "restored" event.
     */
    public function restored(Exam $exam): void
    {
        //
    }

    /**
     * Handle the Exam "force deleted" event.
     */
    public function forceDeleted(Exam $exam): void
    {
        //
    }
}
