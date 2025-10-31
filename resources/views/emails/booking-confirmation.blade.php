<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
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
        .booking-details { 
            background-color: white; 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 5px; 
            border-left: 4px solid #b53d5e; 
        }
        .participant { 
            background-color: white; 
            padding: 10px; 
            margin: 5px 0; 
            border-radius: 3px; 
            border-left: 3px solid #6b7280; 
        }
        .highlight { 
            color: #b53d5e; 
            font-weight: bold; 
        }
        .important { 
            background-color: #fff3cd; 
            border: 1px solid #ffeaa7; 
            padding: 10px; 
            border-radius: 5px; 
            margin: 10px 0; 
        }
        .whatsapp-section { 
            background-color: #e7f7ef; 
            border: 2px solid #25d366; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0; 
            text-align: center; 
        }
        .whatsapp-button { 
            display: inline-block; 
            background-color: #25d366; 
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            margin-top: 10px; 
        }
        .whatsapp-button:hover { 
            background-color: #20ba5a; 
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
                                <h2 style="margin: 0; font-size: 32px; font-weight: bold; color: white;">Retreat Booking Confirmation</h2>
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
                    <p>Dear {{ $booking->firstname }} {{ $booking->lastname }},</p>

                    <p>Thank you for your retreat booking! Your booking has been <strong>confirmed</strong>.</p>

            @php
                $participantsWithFlags = collect($allParticipants)->whereNotNull('flag')->count();
            @endphp

            @if($participantsWithFlags > 0)
                <div class="important">
                    <h4>⚠️ Important Notice</h4>
                    <p><strong>{{ $participantsWithFlags }} participant(s)</strong> in your booking have validation flags that may require administrative review:</p>
                    <ul>
                        @foreach($allParticipants->whereNotNull('flag') as $participant)
                            <li><strong>{{ $participant->firstname }} {{ $participant->lastname }}</strong> (Serial #{{ $participant->participant_number }}):
                                @php
                                    $flags = explode(',', $participant->flag);
                                    $descriptions = [];
                                    foreach($flags as $flag) {
                                        if($flag == 'CRITERIA_FAILED') $descriptions[] = 'Does not meet retreat criteria';
                                        elseif($flag == 'RECURRENT_BOOKING') $descriptions[] = 'Has attended retreat in past year';
                                        else $descriptions[] = $flag;
                                    }
                                @endphp
                                {{ implode(', ', $descriptions) }}
                            </li>
                        @endforeach
                    </ul>
                    <p>Please contact us if you have questions about these flags.</p>
                </div>
            @endif

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

            @if($retreat->description)
                <div class="booking-details">
                    <h3>📖 About This Retreat</h3>
                    <div>{!! $retreat->description !!}</div>
                </div>
            @endif

            <div class="booking-details">
                <h3>👤 Primary Participant</h3>
                <p><strong>Name:</strong> {{ $booking->firstname }} {{ $booking->lastname }}</p>
                <p><strong>Serial Number:</strong> 1 (Primary)</p>
                <p><strong>Email:</strong> {{ $booking->email }}</p>
                <p><strong>WhatsApp:</strong> {{ $booking->whatsapp_number }}</p>
                <p><strong>Age:</strong> {{ $booking->age }} years</p>
                <p><strong>Gender:</strong> {{ ucfirst($booking->gender) }}</p>

                @if($booking->flag)
                    <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 8px; border-radius: 3px; margin-top: 8px;">
                        <p style="margin: 0; font-size: 12px; color: #92400e;">
                            <strong>⚠️ Status Flags:</strong><br>
                            @php
                                $flags = explode(',', $booking->flag);
                                $descriptions = [];
                                foreach($flags as $flag) {
                                    if($flag == 'CRITERIA_FAILED') $descriptions[] = 'Does not meet retreat criteria';
                                    elseif($flag == 'RECURRENT_BOOKING') $descriptions[] = 'Has attended retreat in past year';
                                    else $descriptions[] = $flag;
                                }
                            @endphp
                            {{ implode(', ', $descriptions) }}
                        </p>
                    </div>
                @endif
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

                                @if($participant->flag)
                                    <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 8px; border-radius: 3px; margin-top: 8px;">
                                        <p style="margin: 0; font-size: 12px; color: #92400e;">
                                            <strong>⚠️ Status Flags:</strong><br>
                                            @php
                                                $flags = explode(',', $participant->flag);
                                                $descriptions = [];
                                                foreach($flags as $flag) {
                                                    if($flag == 'CRITERIA_FAILED') $descriptions[] = 'Does not meet retreat criteria';
                                                    elseif($flag == 'RECURRENT_BOOKING') $descriptions[] = 'Has attended retreat in past year';
                                                    else $descriptions[] = $flag;
                                                }
                                            @endphp
                                            {{ implode(', ', $descriptions) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($retreat->instructions)
                <div class="important">
                    <h4>📋 Important Instructions</h4>
                    <div>{!! $retreat->instructions !!}</div>
                </div>
            @endif

            @if($booking->special_remarks)
                <div class="booking-details">
                    <h4>📝 Special Remarks</h4>
                    <p>{{ $booking->special_remarks }}</p>
                </div>
            @endif

            @if($retreat->whatsapp_channel_link)
                <div class="whatsapp-section">
                    <h4 style="color: #25d366; margin-top: 0;">💬 Join Our WhatsApp Community</h4>
                    <p>Stay connected with fellow retreat participants and receive important updates!</p>
                    <a href="{{ $retreat->whatsapp_channel_link }}" class="whatsapp-button" target="_blank">
                        Join WhatsApp Community
                    </a>
                    <p style="font-size: 12px; color: #666; margin-top: 10px;">Click the button above or copy this link: <br>
                    <span style="word-break: break-all;">{{ $retreat->whatsapp_channel_link }}</span></p>
                </div>
            @endif

            <div class="important">
                <h4>⚠️ Important Notes</h4>
                <ul>
                    <li>Please save this email for your records</li>
                    <li>Your <strong>Booking ID ({{ $booking->booking_id }})</strong> and <strong>WhatsApp number</strong> are required for any future inquiries</li>
                    <li>Follow the retreat guidelines and dress code</li>
                </ul>
            </div>

            <p>If you have any questions or need to make changes to your booking, please contact us with your Booking ID.</p>

            <p>We look forward to welcoming you to this spiritual journey!</p>

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
                        Email: <a href="mailto:info@myretreatbooking.com" style="color: #b53d5e; text-decoration: none;">info@myretreatbooking.com</a>
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