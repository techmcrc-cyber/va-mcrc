<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Cancellation</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container { 
            max-width: 75%; 
            margin: 20px auto; 
            background-color: #ffffff;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .content { 
            background-color: #f9f9f9; 
            padding: 30px; 
            border: 1px solid #ddd; 
        }
        .cancellation-details { 
            background-color: white; 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 5px; 
            border-left: 4px solid #dc3545; 
        }
        .remaining-participant { 
            background-color: #d4edda; 
            border: 1px solid #c3e6cb; 
            padding: 10px; 
            margin: 5px 0; 
            border-radius: 3px; 
        }
        .cancelled-participant { 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
            padding: 10px; 
            margin: 5px 0; 
            border-radius: 3px; 
        }
        .highlight { 
            color: #dc3545; 
            font-weight: bold; 
        }
        .success-highlight { 
            color: #28a745; 
            font-weight: bold; 
        }
        .important { 
            background-color: #fff3cd; 
            border: 1px solid #ffeaa7; 
            padding: 10px; 
            border-radius: 5px; 
            margin: 10px 0; 
        }
        @media only screen and (max-width: 600px) {
            .container {
                max-width: 100%;
                margin: 0;
                border-radius: 0;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with table layout for email compatibility -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #b53d5e;">
            <tr>
                <td style="padding: 20px 30px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="120" valign="middle" style="padding-right: 20px;">
                                <img src="https://mountcarmelretreatcentre.org/wp-content/uploads/2022/02/logo_mcrc_new-1-100x118.png" alt="Mount Carmel Retreat Centre Logo" style="max-width: 100px; height: auto; display: block;">
                            </td>
                            <td valign="middle" align="center" style="color: white; font-family: Arial, sans-serif;">
                                <h2 style="margin: 0; font-size: 28px; font-weight: bold; color: white;">Retreat Booking {{ $cancellationType === 'full' ? 'Complete Cancellation' : 'Participant Cancellation' }}</h2>
                                <p style="margin: 5px 0 0 0; font-size: 22px; color: white; font-weight: bold;">Mount Carmel Retreat Centre</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Content Section -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f9f9f9; border: 1px solid #ddd;">
            <tr>
                <td style="padding: 30px; font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
                    <p>Dear {{ $primaryBooking->firstname }} {{ $primaryBooking->lastname }},</p>
            
            @if($cancellationType === 'full')
                <p>Your complete retreat booking has been <strong class="highlight">cancelled</strong>.</p>
            @else
                <p>A participant from your retreat booking has been <strong class="highlight">cancelled</strong>.</p>
            @endif

            <div class="cancellation-details">
                <h3>📋 Booking Details</h3>
                <p><strong>Booking ID:</strong> <span class="highlight">{{ $primaryBooking->booking_id }}</span></p>
                <p><strong>Retreat:</strong> {{ $retreat->title }}</p>
                <p><strong>Dates:</strong> {{ $retreat->start_date->format('M d, Y') }} to {{ $retreat->end_date->format('M d, Y') }}</p>
                <p><strong>Location:</strong> {{ $retreat->location ?? 'Mount Carmel Retreat Centre' }}</p>
                <p><strong>Cancellation Date:</strong> {{ now()->format('M d, Y \\a\\t g:i A') }}</p>
            </div>

            <div class="cancellation-details">
                <h3>👤 Cancelled Participant</h3>
                <div class="cancelled-participant">
                    <p><strong>{{ $cancelledParticipant->firstname }} {{ $cancelledParticipant->lastname }}</strong></p>
                    <p><strong>Serial Number:</strong> {{ $cancelledParticipant->participant_number }}</p>
                    <p><strong>Email:</strong> {{ $cancelledParticipant->email }}</p>
                    <p><strong>WhatsApp:</strong> {{ $cancelledParticipant->whatsapp_number }}</p>
                    <p><strong>Role:</strong> {{ $cancelledParticipant->participant_number === 1 ? 'Primary Participant' : 'Additional Participant' }}</p>
                </div>
            </div>

            @if($cancellationType === 'partial' && count($remainingParticipants) > 0)
                <div class="cancellation-details">
                    <h3>✅ Remaining Active Participants</h3>
                    <p>The following participants remain <span class="success-highlight">confirmed</span> for this retreat:</p>
                    @foreach($remainingParticipants as $participant)
                        <div class="remaining-participant">
                            <p><strong>{{ $participant->firstname }} {{ $participant->lastname }}</strong></p>
                            <p><strong>Serial Number:</strong> {{ $participant->participant_number }}</p>
                            <p><strong>Email:</strong> {{ $participant->email }}</p>
                            <p><strong>WhatsApp:</strong> {{ $participant->whatsapp_number }}</p>
                            <p><strong>Role:</strong> {{ $participant->participant_number === 1 ? 'Primary Participant' : 'Additional Participant' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($cancellationType === 'full')
                <div class="important">
                    <h4>📝 Complete Cancellation Notice</h4>
                    <ul>
                        <li>All participants have been cancelled from this retreat</li>
                        <li>Your booking ID <strong>{{ $primaryBooking->booking_id }}</strong> is now inactive</li>
                        <li>If you paid any fees, please contact us for refund information</li>
                        <li>You are welcome to book future retreats</li>
                    </ul>
                </div>
            @else
                <div class="important">
                    <h4>📝 Partial Cancellation Notice</h4>
                    <ul>
                        <li>Only the specified participant has been cancelled</li>
                        <li>Your booking ID <strong>{{ $primaryBooking->booking_id }}</strong> remains active</li>
                        <li>Remaining participants should still attend as scheduled</li>
                        <li>If applicable, partial refunds will be processed separately</li>
                    </ul>
                </div>
            @endif

            @if($retreat->instructions && $cancellationType === 'partial' && count($remainingParticipants) > 0)
                <div class="cancellation-details">
                    <h4>📋 Retreat Instructions (For Remaining Participants)</h4>
                    <div>{!! $retreat->instructions !!}</div>
                </div>
            @endif

            <div class="important">
                <h4>⚠️ Important Information</h4>
                <ul>
                    <li>Please save this email for your records</li>
                    <li>Contact us if you have any questions about this cancellation</li>
                    <li>Your <strong>Booking ID ({{ $primaryBooking->booking_id }})</strong> and <strong>WhatsApp number</strong> are needed for any inquiries</li>
                    @if($cancellationType === 'partial')
                        <li>Remaining participants should arrive 30 minutes before the retreat starts</li>
                        <li>Bring valid photo IDs for verification</li>
                    @endif
                </ul>
            </div>

            <p>If you have any questions about this cancellation or need assistance, please contact us.</p>

            @if($cancellationType === 'full')
                <p>We're sorry to see you go and hope to welcome you to future retreats.</p>
            @else
                <p>We look forward to welcoming your remaining participants to this spiritual journey!</p>
            @endif

                    <p style="margin-top: 30px; margin-bottom: 0;">
                        God bless,<br>
                        <strong>Mount Carmel Retreat Centre</strong>
                    </p>
                </td>
            </tr>
        </table>

        <!-- Footer Section -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #eee;">
            <tr>
                <td style="padding: 20px; text-align: center; font-family: Arial, sans-serif; font-size: 12px; color: #666;">
                    <p style="margin: 5px 0;"><strong>Mount Carmel Retreat Centre</strong></p>
                    <p style="margin: 5px 0;">
                        Email: <a href="mailto:booking@mountcarmelretreatcentre.org" style="color: #b53d5e; text-decoration: none;">booking@mountcarmelretreatcentre.org</a>
                    </p>
                    <p style="margin: 5px 0;">
                        Retreat Bookings: <a href="tel:+919446113725" style="color: #b53d5e; text-decoration: none;">+91 9446 113 725</a>
                    </p>
                    <p style="margin: 5px 0;">
                        Programme Enquiries: <a href="tel:+918281101101" style="color: #b53d5e; text-decoration: none;">+91 8281 101 101</a>
                    </p>
                    <p style="margin-top: 10px; margin-bottom: 5px; color: #999;">
                        This is an automated message. Please do not reply to this email.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>