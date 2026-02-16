<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DynamicMail extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $data;
    public $htmlContent;

    /**
     * Create a new message instance.
     */
    public function __construct($templateKey, $data)
    {
        $this->template = DB::table('email_templates')->where('key', $templateKey)->where('is_active', true)->first();
        $this->data = $data;

        if ($this->template) {
            $this->htmlContent = $this->template->email_content;
            $this->subject = $this->template->subject;

            foreach ($this->data as $key => $value) {
                $this->htmlContent = str_replace("##{$key}##", $value, $this->htmlContent);
                $this->subject = str_replace("##{$key}##", $value, $this->subject);
            }
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject ?? 'Universal Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->htmlContent ?? '',
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
