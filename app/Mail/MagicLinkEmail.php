<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class MagicLinkEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $AppName;
    /**
     * Create a new message instance.
     */
    public function __construct(private $name, private $magicLink)
    {
        $this->name = $name;
        $this->magicLink = $magicLink;
        $this->AppName = Config::get('app.name');

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{{$this->AppName}] Junte-se ao maior bot com fluxo automatizado do mercado.",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.magic',
            with: [
                'appName' => $this->AppName,
                'name' => $this->name,
                'link' => $this->magicLink,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
