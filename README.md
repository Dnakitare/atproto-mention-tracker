# AT Protocol Mention Tracker

A Laravel application for tracking and analyzing mentions on the AT Protocol (Bluesky) platform.

## Features

- Track mentions of your AT Protocol handle
- Analyze sentiment of mentions
- Set up alerts for various conditions:
  - Mention spikes
  - Sentiment spikes
  - Keyword matches
- Receive notifications through multiple channels:
  - Email
  - Slack
  - In-app notifications

## Requirements

- PHP 8.1 or higher
- Laravel 10.x
- MySQL 8.0 or higher
- Composer
- Node.js and npm (for frontend assets)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/atproto-mention-tracker.git
   cd atproto-mention-tracker
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate an application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=atproto_mention_tracker
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. Run database migrations:
   ```bash
   php artisan migrate
   ```

8. Build frontend assets:
   ```bash
   npm run build
   ```

## Configuration

### AT Protocol Configuration

Configure your AT Protocol credentials in the `.env` file:

```
ATPROTO_IDENTIFIER=your.handle.bsky.social
ATPROTO_PASSWORD=your_app_password
```

### Notification Configuration

#### Email Notifications

Configure your email settings in the `.env` file:

```
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=notifications@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### Slack Notifications

1. Create a Slack app in your workspace:
   - Go to https://api.slack.com/apps
   - Click "Create New App"
   - Choose "From scratch"
   - Name your app and select your workspace

2. Enable Incoming Webhooks:
   - In the left sidebar, click on "Incoming Webhooks"
   - Toggle "Activate Incoming Webhooks" to On
   - Click "Add New Webhook to Workspace"
   - Choose the channel where notifications should be posted
   - Copy the Webhook URL

3. Add the webhook URL to your user's notification settings in the application

## Usage

### Setting Up Alerts

1. Log in to the application
2. Navigate to the Alerts section
3. Click "Create Alert"
4. Configure your alert:
   - Name and description
   - Alert type (mention spike, sentiment spike, keyword match)
   - Conditions (threshold, time window, keywords)
   - Notification channels (email, Slack, in-app)
   - Notification frequency (immediate, hourly, daily)

### Viewing Mentions

1. Log in to the application
2. Navigate to the Mentions section
3. View all mentions, filtered by date, sentiment, or keywords

### Analyzing Sentiment

The application automatically analyzes the sentiment of mentions using natural language processing. Sentiment scores range from -1 (very negative) to 1 (very positive).

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

The project follows PSR-12 coding standards. You can check your code style with:

```bash
./vendor/bin/phpcs
```

## Troubleshooting

### Common Issues

1. **Notifications not being sent**
   - Check your email configuration in `.env`
   - Verify Slack webhook URLs are correct
   - Check the Laravel logs for errors

2. **Alerts not triggering**
   - Verify alert conditions are correctly configured
   - Check if the alert is active
   - Ensure the notification frequency settings are correct

3. **Sentiment analysis not working**
   - Verify your AT Protocol credentials
   - Check if the sentiment analysis service is running
   - Review the logs for any API errors

## License

This project is licensed under the MIT License - see the LICENSE file for details.
