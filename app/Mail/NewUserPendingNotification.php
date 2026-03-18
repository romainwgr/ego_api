<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserPendingNotification extends Mailable
{
    use SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New membership request – ' . $this->user->first_name . ' ' . $this->user->last_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new_user_pending',
        );
    }
}
