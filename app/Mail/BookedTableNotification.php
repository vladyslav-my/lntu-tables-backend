<?php

namespace App\Mail;

use App\Models\BookedTable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookedTableNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bookedTable;

    public function __construct(BookedTable $bookedTable)
    {
        $this->bookedTable = $bookedTable;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booked Table Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booked_table_notification',
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