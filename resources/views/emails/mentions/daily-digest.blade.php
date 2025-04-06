<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Daily Bluesky Mentions Digest</title>
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
        .timestamp {
            color: #6c757d;
            font-size: 0.9em;
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
    <h2>Your Daily Bluesky Mentions Digest</h2>
    
    <p>Hello {{ $emailData['user']->name }},</p>
    
    <p>Here are your Bluesky mentions from the last 24 hours:</p>
    
    @foreach($emailData['mentions'] as $mention)
        <div class="mention">
            <p class="author">@{{ $mention->author_handle }}</p>
            <p>{{ $mention->post_text }}</p>
            <p class="timestamp">{{ $mention->post_indexed_at->diffForHumans() }}</p>
            <a href="https://bsky.app/profile/{{ $mention->author_handle }}/post/{{ $mention->post_id }}" class="button">View Post</a>
        </div>
    @endforeach
    
    <p>
        Best regards,<br>
        Your Bluesky Mention Tracker
    </p>
</body>
</html> 