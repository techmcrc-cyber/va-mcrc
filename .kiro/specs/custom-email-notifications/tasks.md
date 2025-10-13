# Implementation Plan

- [x] 1. Create database migration and model



  - Create migration for notifications table with all required columns (id, need, retreat_id, heading, subject, body, additional_users_emails, total_recipients, status, sent_at, created_by, timestamps)
  - Create Notification model with fillable attributes, casts, and relationships (retreat, creator)
  - Add query scopes (byNeed, byStatus, recent) and helper methods (getRecipientsArray, markAsQueued, markAsSent, markAsFailed)
  - Run migration to create table


  - _Requirements: 6.1, 6.2_

- [ ] 2. Create notification service layer
  - Create NotificationService class in app/Services directory
  - Implement createNotification method to handle notification creation logic
  - Implement parseEmails method to parse and validate comma-separated email addresses
  - Implement getRetreatParticipantEmails method to fetch active booking emails for a retreat


  - Implement calculateTotalRecipients method to count total recipients
  - Implement getRecipients method to return all email addresses for a notification
  - _Requirements: 3.5, 4.3, 4.4, 5.1_

- [ ] 3. Create form request validation
  - Create StoreNotificationRequest class with validation rules
  - Add validation for need (required, in:retreat,custom)


  - Add validation for retreat_id (required_if:need,retreat, exists:retreats,id)
  - Add validation for heading, subject, body (required, string)
  - Add custom validation for additional_users_emails (comma-separated email format)
  - Add custom validation to ensure at least one recipient exists
  - _Requirements: 2.6, 3.6, 4.4, 4.5, 4.6_



- [ ] 4. Create notification controller
  - Create NotificationController in app/Http/Controllers/Admin directory
  - Implement index method to display paginated notifications list
  - Implement create method to show composition form with active retreats
  - Implement store method to validate input, create notification via service, and redirect
  - Add authorization checks using Laravel's authorization system


  - _Requirements: 1.2, 2.1, 2.2, 5.1_

- [ ] 5. Create Laravel notification class
  - Create CustomEmailNotification class in app/Notifications directory
  - Implement ShouldQueue interface for asynchronous delivery
  - Add constructor to accept heading, subject, and body parameters
  - Implement via method to return ['mail'] channel


  - Implement toMail method to compose email using custom template
  - _Requirements: 5.2, 5.8, 7.4, 7.5_

- [ ] 6. Create queue job for notification dispatch
  - Create SendNotificationToRecipients job class
  - Implement handle method to send notification to each recipient
  - Update notification status to 'processing' when job starts
  - Update notification status to 'sent' when all emails are sent successfully


  - Implement failed method to mark notification as 'failed' on error
  - Add retry logic and timeout configuration
  - _Requirements: 5.3, 5.4, 5.5, 5.6, 5.7_

- [ ] 7. Create notification index view
  - Create index.blade.php in resources/views/admin/notifications directory
  - Extend admin layout and set page title
  - Add "Compose Mail" button at top of page
  - Create DataTable with columns: ID, DateTime, Need, Retreat ID, Recipients Count, Additional Emails, Status
  - Add status badges with color coding (pending=warning, sent=success, failed=danger)
  - Implement pagination and search functionality


  - Add link to retreat details when retreat_id is present
  - _Requirements: 1.3, 1.4, 1.5, 2.1, 6.2, 6.3, 6.4, 6.5, 6.6_

- [ ] 8. Create notification compose form view
  - Create create.blade.php in resources/views/admin/notifications directory
  - Add need type selector (radio buttons or dropdown for retreat/custom)
  - Add active retreats dropdown (shown conditionally when need=retreat)


  - Add additional_users_emails textarea with placeholder for comma-separated emails
  - Add heading input field
  - Add subject input field
  - Add body textarea with CKEditor integration for rich text
  - Add form validation error display
  - Add JavaScript for dynamic form field visibility based on need type


  - Add submit button with loading state
  - _Requirements: 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 4.1, 4.2, 7.1, 7.2, 7.3_

- [ ] 9. Create email template view
  - Create custom-notification.blade.php in resources/views/emails directory
  - Use consistent structure with existing booking-confirmation.blade.php


  - Add dynamic heading display
  - Add dynamic body content with preserved formatting
  - Add footer with retreat center branding
  - Ensure responsive design for mobile devices
  - _Requirements: 7.4, 7.5, 7.6_




- [ ] 10. Add sidebar menu item
  - Update resources/views/admin/layouts/sidebar.blade.php
  - Add "Notification" menu item with icon (fa-bell or fa-envelope)
  - Add route to admin.notifications.index
  - Add active state highlighting when on notifications pages
  - Add permission check (@can('view-notifications'))
  - _Requirements: 1.1, 1.2_

- [ ] 11. Create routes
  - Add notification routes in routes/web.php under admin middleware group
  - Add route for index: GET /admin/notifications
  - Add route for create: GET /admin/notifications/create
  - Add route for store: POST /admin/notifications
  - Add route names (admin.notifications.index, admin.notifications.create, admin.notifications.store)
  - _Requirements: 1.2, 2.1, 2.2_

- [ ] 12. Configure queue system
  - Verify queue configuration in config/queue.php (default to database driver)
  - Create queue migration if not exists: php artisan queue:table
  - Run migration to create jobs, failed_jobs, and job_batches tables
  - Update .env with QUEUE_CONNECTION=database
  - Document queue worker command: php artisan queue:work
  - _Requirements: 5.3, 8.1, 8.2, 8.3, 8.4, 8.5, 8.6_

- [ ] 13. Integrate notification dispatch in controller
  - In NotificationController store method, call NotificationService to create notification
  - Dispatch SendNotificationToRecipients job with notification and recipients
  - Update notification status to 'queued' after dispatching
  - Add success flash message
  - Redirect to notifications index page
  - _Requirements: 5.1, 5.2, 5.3, 5.5_

- [ ]* 14. Create permissions and authorization
  - Create 'view-notifications' permission in database
  - Create 'create-notifications' permission in database
  - Add permission checks in NotificationController methods
  - Update sidebar menu to show only for authorized users
  - _Requirements: Security considerations from design_

- [ ]* 15. Add comprehensive error handling
  - Add try-catch blocks in NotificationService methods
  - Add try-catch blocks in SendNotificationToRecipients job
  - Log errors to Laravel log with context
  - Display user-friendly error messages in UI
  - Handle email sending failures gracefully
  - _Requirements: Error handling from design_

- [ ]* 16. Write unit tests for NotificationService
  - Test parseEmails method with valid and invalid email strings
  - Test calculateTotalRecipients with different scenarios
  - Test getRetreatParticipantEmails with active and inactive bookings
  - Test createNotification method
  - _Requirements: Testing strategy from design_

- [ ]* 17. Write feature tests for notification management
  - Test index page displays notifications correctly
  - Test compose form displays with active retreats
  - Test notification creation with retreat type
  - Test notification creation with custom type
  - Test validation errors are displayed
  - Test authorization checks work correctly
  - _Requirements: Testing strategy from design_

- [ ]* 18. Write feature tests for email sending
  - Test emails are queued when notification is created
  - Test emails are sent to correct recipients
  - Test email content matches notification data
  - Test notification status updates after sending
  - Test failed email handling
  - _Requirements: Testing strategy from design_
