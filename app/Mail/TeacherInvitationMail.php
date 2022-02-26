<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('邀请加入')->view('emails.invitation', [
            'url' => config('app.url') . "/api/teacher/accept?teacher_id={$this->data['teacher_id']}&school_id={$this->data['school_id']}&secret={$this->data['secret']}",
            'school' => $this->data['school'],
        ]);
    }
}
