<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use SerializesModels;

    public $user;

    /**
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Subject
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome on EGC !',
        );
    }

    /**
     * Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
        );
    }
}