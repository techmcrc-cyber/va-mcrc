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
            align-items: center; 
            justify-content: flex-start; 
        }
        .header img { 
            max-width: 100px; 
            height: auto; 
            margin-right: 20px; 
            flex-shrink: 0; 
        }
        .header-text { 
            flex: 1; 
            text-align: center; 
        }
        .header-text h2 { 
            margin: 0 0 5px 0; 
            font-size: 24px;
        }
        .header-text p { 
            margin: 0; 
            font-size: 14px;
            opacity: 0.9;
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
        <div class="header">
            <img src="https://mountcarmelretreatcentre.org/wp-content/uploads/2022/02/logo_mcrc_new-1-100x118.png" alt="Mount Carmel Retreat Centre Logo">
            <div class="header-text">
                <p>Mount Carmel Retreat Centre</p>
            </div>
        </div>

        <div class="content">
            {!! $greeting !!}
            
            <h1>{{ $heading }}</h1>

            <div class="body-content">
                {!! $body !!}
            </div>

            <p style="margin-top: 30px;">
                God bless,<br>
                <strong>Mount Carmel Retreat Centre</strong>
            </p>
        </div>

        <div class="footer">
            <p><strong>Mount Carmel Retreat Centre</strong></p>
            <p>
                Email: <a href="mailto:info@mountcarmelretreat.org">info@mountcarmelretreat.org</a> | 
                Phone: +91-XXXXXXXXXX
            </p>
            <p style="margin-top: 10px; color: #999;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
