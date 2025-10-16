<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $heading }}</title>
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
        .header { 
            background-color: #b53d5e; 
            color: white; 
            padding: 10px 30px; 
            display: flex; 
            justify-content: center;
            align-items: center; 
        }
        .header img { 
            max-width: 100px; 
            height: auto; 
            flex-shrink: 0; 
        }
        .header-text { 
            flex: 1;
            text-align: center;
        }
        .header-text h2 { 
            margin: 0; 
            font-size: 26px;
            font-weight: bold;
        }
        .content { 
            background-color: #f9f9f9; 
            padding: 30px; 
            border: 1px solid #ddd; 
        }
        .content h1 {
            color: #b53d5e;
            margin-top: 0;
            font-size: 20px;
            border-bottom: 2px solid #b53d5e;
            padding-bottom: 10px;
        }
        .content p {
            margin: 15px 0;
        }
        .body-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #b53d5e;
        }
        .footer { 
            background-color: #eee; 
            padding: 20px; 
            text-align: center; 
            font-size: 12px; 
            color: #666;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer a {
            color: #b53d5e;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
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
            .header {
                padding: 10px 15px;
            }
            .header img {
                max-width: 60px;
                margin-right: 10px;
            }
            .header-text h2 {
                font-size: 18px;
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
                                <h2 style="margin: 0; font-size: 32px; font-weight: bold; color: white;">Mount Carmel Retreat Centre</h2>
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
                    <div style="margin-bottom: 20px;">
                        {!! $greeting !!}
                    </div>
                    
                    @if(!empty($heading))
                        <h1 style="color: #b53d5e; margin: 20px 0 10px 0; font-size: 20px; border-bottom: 2px solid #b53d5e; padding-bottom: 10px;">{{ $heading }}</h1>
                    @endif

                    <div style="background-color: white; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #b53d5e;">
                        {!! $body !!}
                    </div>

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
                        Email: <a href="mailto:info@mountcarmelretreat.org" style="color: #b53d5e; text-decoration: none;">info@mountcarmelretreat.org</a> | 
                        Phone: +91-XXXXXXXXXX
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
