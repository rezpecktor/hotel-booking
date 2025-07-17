<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    /**
     * Create a new message instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // KITA HAPUS PEMANGGILAN KE VIEW, DAN GANTI DENGAN KONTEN HTML LANGSUNG

        $guestName = $this->reservation->guest_name;
        $roomNumber = $this->reservation->rooms->first()->room_number;
        $fromDate = \Carbon\Carbon::parse($this->reservation->from_date)->format('d F Y');
        $toDate = \Carbon\Carbon::parse($this->reservation->to_date)->format('d F Y');
        $totalPerson = $this->reservation->total_person;
        // Format harga menjadi Rupiah di sini
        $totalPrice = 'Rp' . number_format($this->reservation->total_price, 0, ',', '.');

        return new Content(
            htmlString: "
                <h1>Booking Confirmation</h1>
                <p>Dear {$guestName},</p>
                <p>We are delighted to confirm your booking with us. Below are the details of your reservation:</p>
                <p><strong>Guest Name:</strong> {$guestName}</p>
                <p><strong>Room Number:</strong> {$roomNumber}</p>
                <p><strong>From Date:</strong> {$fromDate}</p>
                <p><strong>To Date:</strong> {$toDate}</p>
                <p><strong>Total Guests:</strong> {$totalPerson}</p>
                <p><strong>Total Price:</strong> {$totalPrice}</p>
                <p>Thank you for choosing to stay with us!</p>
            "
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
