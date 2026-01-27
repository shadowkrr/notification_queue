# Notification Queue System

A PHP-based alert and notification system that monitors various services and sends notifications to Discord and Slack when issues are detected.

## Features

- **HTTP Monitoring**: Detects when HTTP requests return non-200 status codes with timeout protection
- **Network Monitoring**: Monitors network connectivity using ping with input validation
- **JSON Validation**: Validates JSON files and alerts on malformed data or missing files
- **Discord Integration**: Sends rich embedded notifications to Discord channels
- **Slack Integration**: Sends notifications to Slack channels via Incoming Webhooks
- **Queue Management**: Prevents duplicate alerts and manages notification queues with error recovery
- **Cross-Platform Support**: Works on Linux, macOS, and Windows
- **Security Enhanced**: Input validation, command injection prevention, and proper error handling

## Directory Structure

```
notification_queue/
├── bin/                    # Executable scripts
│   ├── push.php            # Send queued notifications
│   ├── get.php             # HTTP endpoint monitoring
│   ├── ping.php            # Network connectivity monitoring
│   └── json.php            # JSON file validation
├── src/                    # Library files
│   ├── env.php             # Environment variable loader
│   ├── queue.php           # Alert queue management
│   ├── discord.php         # Discord webhook integration
│   ├── slack.php           # Slack webhook integration
│   └── scraper/            # Custom scrapers (git ignored)
├── data/                   # Generated data (git ignored)
│   └── queues.json         # Pending alerts
├── examples/               # Sample scripts
│   ├── custom_monitor.php  # Custom monitoring example
│   ├── send_alert.php      # CLI alert sender
│   └── test_notification.php # Webhook tester
├── .env                    # Environment variables (git ignored)
├── .env.example            # Environment template
├── composer.json           # Dependencies
└── README.md
```

## Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Webhooks

```bash
cp .env.example .env
```

Edit `.env` with your webhook URLs:

```
DISCORD_WEBHOOK=https://discordapp.com/api/webhooks/YOUR_ID/YOUR_TOKEN
SLACK_WEBHOOK=https://hooks.slack.com/services/YOUR_WORKSPACE/YOUR_CHANNEL/YOUR_TOKEN
```

#### Getting Webhook URLs

**Discord:**
1. Server Settings > Integrations > Webhooks
2. Create Webhook and copy URL

**Slack:**
1. Go to [Slack API](https://api.slack.com/apps)
2. Create a new app or select an existing one
3. Enable "Incoming Webhooks"
4. Add a new webhook to your workspace

### 3. Configure Monitoring Targets

#### HTTP Monitoring (bin/get.php)
```php
$checkUrls = [
    'api_server' => 'https://your-api.com/health',
    'website' => 'https://your-website.com',
];
```

#### Network Monitoring (bin/ping.php)
```php
$monitorHosts = [
    'local_server' => '127.0.0.1',
    'google_dns' => '8.8.8.8',
];
```

#### JSON File Monitoring (bin/json.php)
```php
$checkFiles = [
    'config' => 'config.json',
];
```

### 4. Set up CRON Jobs

```bash
# Set environment variables at the top of crontab (edit with: crontab -e)
DISCORD_WEBHOOK=https://discordapp.com/api/webhooks/YOUR_ID/YOUR_TOKEN
SLACK_WEBHOOK=https://hooks.slack.com/services/YOUR_WORKSPACE/YOUR_CHANNEL/YOUR_TOKEN

# Monitoring checks every minute
* * * * * /usr/bin/php /path/to/notification_queue/bin/get.php >> /var/log/monitor.log 2>&1
* * * * * /usr/bin/php /path/to/notification_queue/bin/ping.php >> /var/log/monitor.log 2>&1
* * * * * /usr/bin/php /path/to/notification_queue/bin/json.php >> /var/log/monitor.log 2>&1

# Send notifications every 10 minutes
*/10 * * * * /usr/bin/php /path/to/notification_queue/bin/push.php >> /var/log/push.log 2>&1
```

## Usage

### Manual Testing

```bash
# Test webhook configuration
php examples/test_notification.php slack

# Send a test alert
php examples/send_alert.php "Test Alert" "This is a test message"

# Run monitoring scripts
php bin/get.php
php bin/ping.php
php bin/push.php
```

### Adding Custom Monitors

```php
<?php
require_once __DIR__ . '/src/queue.php';

$alert = [
    'title' => 'Service Alert',
    'text' => 'Description of the issue',
    'channel' => 'slack',  // 'slack' or 'discord'
    'create' => date('c')
];

addAlertQueue($alert);
```

### Alert Fields

| Field | Required | Description |
|-------|----------|-------------|
| `title` | Yes | Alert title |
| `text` | Yes | Alert message |
| `channel` | No | Notification target: `slack` (default) or `discord` |
| `create` | No | Timestamp (ISO 8601 format, auto-generated if omitted) |
| `color` | No | Hex color code (e.g., `ff0000`) |
| `link` | No | URL to include in the notification |

### Creating Custom Scrapers

Create your scraper in `src/scraper/` (this directory is git ignored):

```php
<?php
// src/scraper/my_scraper.php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../env.php';
require_once __DIR__ . '/../queue.php';

// Your scraping logic here
```

## Requirements

- PHP 7.4 or higher
- cURL extension enabled
- Composer (for scraper dependencies)

## Troubleshooting

1. **Notifications Not Sending**: Verify environment variables are set (`echo $SLACK_WEBHOOK`)
2. **Permission Denied**: Ensure write access to `data/` directory
3. **CRON Not Running**: Check cron service and environment variables in crontab

### Debug Mode

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Security Best Practices

- Store webhook URLs in `.env`, never commit to git
- Set appropriate permissions on `.env` (600)
- Ensure `data/` and `src/scraper/` are in `.gitignore`
