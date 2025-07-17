<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        /* CSS sederhana untuk email */
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        h1 { color: #5e35b1; }
        .details { margin-top: 20px; }
        .details p { margin: 5px 0; }
        .footer { margin-top: 30px; font-size: 0.9em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Booking Confirmation</h1>
        <p>Dear {{ $reservation->guest_name }},</p>
        <p>We are delighted to confirm your booking with us. Below are the details of your reservation:</p>

        <div class="details">
            <p><strong>Guest Name:</strong> {{ $reservation->guest_name }}</p>
            {{-- Menampilkan nomor kamar pertama yang dipesan --}}
            <p><strong>Room Number:</strong> {{ $reservation->rooms->first()->room_number }}</p>
            <p><strong>From Date:</strong> {{ \Carbon\Carbon::parse($reservation->from_date)->format('d F Y') }}</p>
            <p><strong>To Date:</strong> {{ \Carbon\Carbon::parse($reservation->to_date)->format('d F Y') }}</p>
            <p><strong>Total Guests:</strong> {{ $reservation->total_person }}</p>
            <p><strong>Total Price:</strong>
                {{-- Format harga menjadi Rupiah (IDR) --}}
                Rp{{ number_format($reservation->total_price, 0, ',', '.') }}
            </p>
        </div>

        <p class="footer">
            Thank you for choosing to stay with us! If you have any special requests or need assistance during your stay, please don't hesitate to contact our front desk.
            <br><br>
            Safe travels, and we look forward to welcoming you soon.
            <br>
            Warm regards,
            <br>
            The Hotel Team
        </p>
    </div>
</body>
</html>
