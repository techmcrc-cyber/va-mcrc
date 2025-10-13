# Queue System Setup for Email Notifications

## Overview
The email notification system uses Laravel's queue system to send emails asynchronously, preventing the application from blocking while emails are being sent.

## Configuration
The queue system is configured to use the **database** driver. This is set in the `.env` file:

```
QUEUE_CONNECTION=database
```

## Database Tables
The following tables are used for queue management:
- `jobs` - Stores queued jobs
- `failed_jobs` - Stores failed jobs for retry
- `job_batches` - Stores batch job information

These tables were created by the migration: `0001_01_01_000002_create_jobs_table.php`

## Running the Queue Worker

### Development
To process queued jobs in development, run:

```bash
php artisan queue:work
```

Or to process jobs with specific options:

```bash
php artisan queue:work --tries=3 --timeout=300
```

### Production
For production environments, it's recommended to use a process monitor like **Supervisor** to keep the queue worker running.

#### Supervisor Configuration Example

Create a file `/etc/supervisor/conf.d/retreat-queue-worker.conf`:

```ini
[program:retreat-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

Then reload Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start retreat-queue-worker:*
```

## Monitoring Queue Jobs

### View Queue Status
```bash
php artisan queue:monitor
```

### View Failed Jobs
```bash
php artisan queue:failed
```

### Retry Failed Jobs
```bash
# Retry all failed jobs
php artisan queue:retry all

# Retry specific job by ID
php artisan queue:retry 1
```

### Clear Failed Jobs
```bash
php artisan queue:flush
```

## How Email Notifications Work

1. Admin composes and submits a notification through the web interface
2. The notification is saved to the database
3. A `SendNotificationToRecipients` job is dispatched to the queue
4. The queue worker picks up the job and processes it
5. Emails are sent to all recipients
6. The notification status is updated to 'sent' or 'failed'

## Troubleshooting

### Queue Worker Not Processing Jobs
- Ensure the queue worker is running: `php artisan queue:work`
- Check the `jobs` table for pending jobs
- Check Laravel logs: `storage/logs/laravel.log`

### Emails Not Being Sent
- Verify mail configuration in `.env`
- Check failed jobs: `php artisan queue:failed`
- Review error logs in `storage/logs/`

### Performance Issues
- Increase the number of queue workers
- Use Redis instead of database for better performance
- Implement job batching for large recipient lists

## Additional Resources
- [Laravel Queue Documentation](https://laravel.com/docs/queues)
- [Laravel Supervisor Configuration](https://laravel.com/docs/queues#supervisor-configuration)
