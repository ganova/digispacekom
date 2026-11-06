<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BookingReceived extends Mailable
{
    public function __construct(public Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        $b = $this->booking;
        $who = $b->name ?: ($b->device ?: $b->location);
        $subject = ($b->type === 'penawaran' ? 'Permintaan penawaran baru' : 'Booking teknisi baru').($who ? ' - '.$who : '');

        return new Envelope(subject: trim(preg_replace('/\s+/', ' ', $subject)));
    }

    public function content(): Content
    {
        return new Content(text: 'emails.booking');
    }
}
