<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #8b4165; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .footer { background-color: #eee; padding: 15px; text-align: center; border-radius: 0 0 5px 5px; font-size: 12px; }
        .booking-details { background-color: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #8b4165; }
        .participant { background-color: white; padding: 10px; margin: 5px 0; border-radius: 3px; border-left: 3px solid #6b7280; }
        .highlight { color: #8b4165; font-weight: bold; }
        .important { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🙏 Retreat Booking Confirmation</h2>
            <p>Mount Carmel Retreat Centre</p>
        </div>

        <div class="content">
            <p>Dear {{ $booking->firstname }} {{ $booking->lastname }},</p>
            
            <p>Thank you for your retreat booking! Your booking has been <strong>confirmed</strong>.</p>

            <div class="booking-details">
                <h3>📋 Booking Details</h3>
                <p><strong>Booking ID:</strong> <span class="highlight">{{ $booking->booking_id }}</span></p>
                <p><strong>Retreat:</strong> {{ $retreat->title }}</p>
                <p><strong>Dates:</strong> {{ $retreat->start_date->format('M d, Y') }} to {{ $retreat->end_date->format('M d, Y') }}</p>
                <p><strong>Timings:</strong> {{ $retreat->timings }}</p>
                <p><strong>Location:</strong> {{ $retreat->location ?? 'Mount Carmel Retreat Centre' }}</p>
                @if($retreat->address)
                <p><strong>Address:</strong> {{ $retreat->address }}, {{ $retreat->city }}, {{ $retreat->state }}</p>
                @endif
            </div>

            <div class="booking-details">
                <h3>👤 Primary Participant</h3>
                <p><strong>Name:</strong> {{ $booking->firstname }} {{ $booking->lastname }}</p>
                <p><strong>Serial Number:</strong> 1 (Primary)</p>
                <p><strong>Email:</strong> {{ $booking->email }}</p>
                <p><strong>WhatsApp:</strong> {{ $booking->whatsapp_number }}</p>
                <p><strong>Age:</strong> {{ $booking->age }} years</p>
                <p><strong>Gender:</strong> {{ ucfirst($booking->gender) }}</p>
            </div>

            @if(count($allParticipants) > 1)
                <div class="booking-details">
                    <h3>👥 Additional Participants</h3>
                    @foreach($allParticipants as $participant)
                        @if($participant->participant_number > 1)
                            <div class="participant">
                                <p><strong>{{ $participant->firstname }} {{ $participant->lastname }}</strong></p>
                                <p><strong>Serial Number:</strong> {{ $participant->participant_number }}</p>
                                <p><strong>Email:</strong> {{ $participant->email }}</p>
                                <p><strong>WhatsApp:</strong> {{ $participant->whatsapp_number }}</p>
                                <p><strong>Age:</strong> {{ $participant->age }} years</p>
                                <p><strong>Gender:</strong> {{ ucfirst($participant->gender) }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($retreat->instructions)
                <div class="important">
                    <h4>📋 Important Instructions</h4>
                    <p>{{ $retreat->instructions }}</p>
                </div>
            @endif

            @if($booking->special_remarks)
                <div class="booking-details">
                    <h4>📝 Special Remarks</h4>
                    <p>{{ $booking->special_remarks }}</p>
                </div>
            @endif

            <div class="important">
                <h4>⚠️ Important Notes</h4>
                <ul>
                    <li>Please save this email for your records</li>
                    <li>Your <strong>Booking ID ({{ $booking->booking_id }})</strong> and <strong>WhatsApp number</strong> are required for any future inquiries</li>
                    <li>Arrive at least 30 minutes before the retreat starts</li>
                    <li>Bring a valid photo ID for verification</li>
                    <li>Follow the retreat guidelines and dress code</li>
                </ul>
            </div>

            <p>If you have any questions or need to make changes to your booking, please contact us with your Booking ID.</p>

            <p>We look forward to welcoming you to this spiritual journey!</p>

            <p>God bless,<br>
            <strong>Mount Carmel Retreat Centre</strong></p>
        </div>

        <div class="footer">
            <p>Mount Carmel Retreat Centre | Email: info@mountcarmelretreat.org | Phone: +91-XXXXXXXXXX</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>