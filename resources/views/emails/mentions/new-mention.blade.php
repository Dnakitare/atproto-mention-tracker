<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Mention on Bluesky</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .mention {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .author {
            font-weight: bold;
            color: #0066cc;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h2>New Mention on Bluesky</h2>
    
    <p>Hello {{ $emailData['user']->name }},</p>
    
    <p>You have a new mention on Bluesky!</p>
    
    <div class="mention">
        <p class="author">@{{ $emailData['mention']->author_handle }}</p>
        <p>{{ $emailData['mention']->post_text }}</p>
    </div>
    
    <a href="{{ $emailData['postUrl'] }}" class="button">View Post</a>
    
    <p>
        Best regards,<br>
        Your Bluesky Mention Tracker
    </p>
</body>
</html> 