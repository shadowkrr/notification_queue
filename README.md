# Notification Queue System

A PHP-based alert and notification system that monitors various services and sends notifications to Discord webhooks when issues are detected.

## Features

- **HTTP Monitoring**: Detects when HTTP requests return non-200 status codes with timeout protection
- **Network Monitoring**: Monitors network connectivity using ping with input validation
- **JSON Validation**: Validates JSON files and alerts on malformed data or missing files
- **Discord Integration**: Sends rich embedded notifications to Discord channels
- **Queue Management**: Prevents duplicate alerts and manages notification queues with error recovery
- **Cross-Platform Support**: Works on Linux, macOS, and Windows
- **Security Enhanced**: Input validation, command injection prevention, and proper error handling

## Setup

### 1. Configure Discord Webhook

Edit `discord.php` and set your Discord webhook URL:

```php
define("ALERT_WEBHOOK", "https://discordapp.com/api/webhooks/YOUR_WEBHOOK_ID/YOUR_WEBHOOK_TOKEN");
```

### 2. Configure Monitoring Targets

#### HTTP Monitoring (get.php)
Edit the `$checkUrls` array to add your endpoints:

```php
$checkUrls = [
    'api_server' => 'https://your-api.com/health',
    'website' => 'https://your-website.com',
    'service' => 'https://service.example.com/status'
];
```

#### Network Monitoring (ping.php)
Edit the `$monitorHosts` array to add your hosts:

```php
$monitorHosts = [
    'local_server' => '127.0.0.1',
    'google_dns' => '8.8.8.8',
    'main_server' => '192.168.1.100',
    'website' => 'example.com'
];
```

#### JSON File Monitoring (json.php)
Edit the `$checkFiles` array to add your JSON files:

```php
$checkFiles = [
    'config' => 'config.json',
    'data' => 'important_data.json'
];
```

### 3. Set up CRON Jobs

Add these cron jobs to automate monitoring:

```bash
# HTTP and JSON checks every minute
* * * * * cd /path/to/notification_queue; /usr/bin/php get.php >> /path/to/notification_queue/monitor.log 2>&1
* * * * * cd /path/to/notification_queue; /usr/bin/php json.php >> /path/to/notification_queue/monitor.log 2>&1

# Network checks every minute
* * * * * cd /path/to/notification_queue; /usr/bin/php ping.php >> /path/to/notification_queue/monitor.log 2>&1

# Send notifications every 10 minutes
*/10 * * * * cd /path/to/notification_queue; /usr/bin/php push.php >> /path/to/notification_queue/push.log 2>&1
```

## File Structure

### Core Files

- **`queue.php`** - Core library for managing alert queues with enhanced error handling
- **`discord.php`** - Discord webhook integration and message formatting
- **`queues.json`** - JSON file storing pending alerts (auto-created if missing)
- **`push.php`** - Processes and sends queued notifications to Discord with comprehensive logging

### Monitoring Scripts

- **`get.php`** - Monitors HTTP endpoints with timeout and proper error handling
- **`ping.php`** - Performs network connectivity checks with input validation and multi-platform support
- **`json.php`** - Validates JSON files with existence checks and proper error reporting

## Recent Improvements

### Security Enhancements
- **Input Validation**: All user inputs are now validated to prevent injection attacks
- **Command Safety**: Shell commands use `escapeshellarg()` for safe execution
- **Timeout Protection**: HTTP requests include 30-second timeout to prevent hanging

### Error Handling
- **Comprehensive Logging**: All errors are logged with detailed messages
- **Graceful Degradation**: System continues operating even when individual components fail
- **JSON Corruption Recovery**: Automatically recovers from corrupted queue files

### Reliability Improvements
- **Duplicate Prevention**: Enhanced duplicate detection in alert queue
- **File Safety**: Automatic creation of missing configuration files
- **Cross-Platform**: Improved compatibility across different operating systems

## Usage

### Adding Custom Monitors

Include the queue library in your monitoring scripts:

```php
<?php
require_once 'queue.php';

// Add an alert to the queue
$alert = [
    'title' => 'Service Alert',
    'text' => 'Description of the issue',
    'create' => date('c')  // ISO 8601 format
];

if (addAlertQueue($alert)) {
    echo "Alert queued successfully\n";
} else {
    echo "Failed to queue alert\n";
}
?>
```

### Manual Testing

Run monitoring scripts manually to test:

```bash
# Test HTTP monitoring
php get.php

# Test network monitoring
php ping.php

# Test JSON validation
php json.php

# Send queued notifications
php push.php
```

### Monitoring Logs

Check log files for system status:

```bash
# Monitor general activity
tail -f monitor.log

# Monitor notification sending
tail -f push.log

# Check PHP error logs
tail -f /var/log/php_errors.log
```

## Requirements

- **PHP 7.0 or higher**
- **cURL extension enabled**
- **File write permissions** for `queues.json` and log files
- **Valid Discord webhook URL**
- **Network access** for HTTP monitoring and Discord notifications

## Troubleshooting

### Common Issues

1. **Permission Denied**: Ensure web server has write access to the directory
2. **Discord Not Receiving**: Verify webhook URL and network connectivity
3. **CRON Not Running**: Check cron service status and log files
4. **JSON Errors**: Check `queues.json` file format and permissions

### Debug Mode

Enable error reporting in PHP files for debugging:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Security Best Practices

- **File Permissions**: Set appropriate permissions on `queues.json` (644 or 600)
- **Webhook Security**: Keep Discord webhook URLs private and secure
- **Log Monitoring**: Regularly check log files for security incidents
- **Input Validation**: Never disable the built-in input validation
- **Access Control**: Restrict access to monitoring scripts and configuration files