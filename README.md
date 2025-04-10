# AT Protocol Mention Tracker

A Laravel application for tracking and analyzing mentions on the AT Protocol (Bluesky) platform.

## Features

- Track mentions of your AT Protocol handle
- Manage tracked keywords with active/inactive status
- Analyze sentiment of mentions
- Set up alerts for various conditions:
  - Mention spikes
  - Sentiment spikes
  - Keyword matches
- Receive notifications through multiple channels:
  - Email
  - Slack
  - In-app notifications
- Structured logging for better debugging and monitoring
- Redis caching for improved performance

## Requirements

- PHP 8.1 or higher
- Laravel 10.x
- MySQL 8.0 or higher
- Redis
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

7. Configure Redis in the `.env` file:
   ```
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

8. Run database migrations:
   ```bash
   php artisan migrate
   ```

9. Build frontend assets:
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

### Managing Keywords

1. Log in to the application
2. Navigate to the Keywords section
3. Add new keywords:
   - Enter the keyword text
   - Select the type (keyword, username, or hashtag)
   - Set the active status
4. Edit existing keywords:
   - Modify the keyword text
   - Change the type
   - Toggle active status
5. Delete keywords when no longer needed

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

### Logging

The application uses structured logging with the following features:
- JSON-formatted log entries
- Request ID tracking
- User context in logs
- Environment information
- Memory usage tracking
- Web request details

Log files are stored in `storage/logs/`:
- `laravel.log`: Standard application logs
- `structured.log`: JSON-formatted structured logs

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

4. **Redis connection issues**
   - Verify Redis is running
   - Check Redis configuration in `.env`
   - Ensure the Redis PHP extension is installed

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Deployment

### Prerequisites
- Docker and Docker Compose
- Git
- Composer (for local development)

### Environment Setup
1. Copy the environment file:
```bash
cp .env.example .env
```

2. Update the following environment variables in `.env`:
```
APP_ENV=production
APP_DEBUG=false
DB_DATABASE=atproto_mention_tracker
DB_USERNAME=postgres
DB_PASSWORD=your_secure_password
REDIS_HOST=redis
QUEUE_CONNECTION=redis
```

### Docker Deployment
1. Build and start the containers:
```bash
docker-compose up -d --build
```

2. Run migrations:
```bash
docker-compose exec app php artisan migrate --force
```

3. Clear cache and optimize:
```bash
docker-compose exec app php artisan optimize
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
```

4. Set up the scheduler for background jobs:
```bash
docker-compose exec app php artisan schedule:work
```

### Health Checks
The application includes health check endpoints:
- `/health`: Basic application health check
- `/health/detailed`: Detailed health check including database and Redis connectivity

### Monitoring
- Application logs: `docker-compose logs -f app`
- Database logs: `docker-compose logs -f db`
- Redis logs: `docker-compose logs -f redis`
- Nginx logs: `docker-compose logs -f nginx`

### Backup and Maintenance
1. Database backup:
```bash
docker-compose exec db pg_dump -U postgres atproto_mention_tracker > backup.sql
```

2. Restore database:
```bash
docker-compose exec -T db psql -U postgres atproto_mention_tracker < backup.sql
```
