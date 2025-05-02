<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;

class MailModel extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope()
    {
        return new Envelope(
            from: new Address('76d6c04a2cc637@mailtrap.io', 'Glinthub Team'),
            subject: 'Password Recovery - Glinthub',
        );
    }

    public function content()
    {
        return new Content(
            view: 'mails.recovery',
        );
    }
}