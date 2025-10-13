# Requirements Document

## Introduction

This feature introduces a comprehensive custom email notification system for the retreat management application. The system allows administrators to send targeted email notifications to retreat participants and custom email lists. It provides a dedicated interface for composing, managing, and tracking email notifications with support for both retreat-specific and custom email campaigns. The system leverages Laravel's built-in notification framework and implements a job queue system for efficient email delivery.

## Requirements

### Requirement 1: Notification Management Interface

**User Story:** As an administrator, I want to access a dedicated notification management section from the sidebar menu, so that I can easily manage and track all email notifications sent through the system.

#### Acceptance Criteria

1. WHEN the administrator views the admin sidebar THEN the system SHALL display a "Notification" menu item
2. WHEN the administrator clicks on the "Notification" menu item THEN the system SHALL navigate to the notification index page
3. WHEN the notification index page loads THEN the system SHALL display a table with all sent notifications
4. WHEN displaying the notifications table THEN the system SHALL include columns for: id, datetime, need (retreat/custom), retreat_id (if need is retreat), no_of_total_notification_sending_users, additional_users_emails (comma separated), status, and timestamp
5. WHEN the notifications table is empty THEN the system SHALL display an appropriate empty state message

### Requirement 2: Notification Composition Interface

**User Story:** As an administrator, I want to compose new email notifications through a user-friendly form, so that I can send targeted communications to retreat participants or custom email lists.

#### Acceptance Criteria

1. WHEN the administrator is on the notification index page THEN the system SHALL display a "Compose Mail" button at the top of the page
2. WHEN the administrator clicks the "Compose Mail" button THEN the system SHALL navigate to a notification composition form
3. WHEN the composition form loads THEN the system SHALL display a "need" field with options: "retreat" and "custom"
4. WHEN the composition form loads THEN the system SHALL display input fields for: heading, subject, and body
5. WHEN the administrator enters text in the body field THEN the system SHALL support rich text formatting or plain text input
6. IF the form is submitted with missing required fields THEN the system SHALL display validation error messages

### Requirement 3: Retreat-Specific Notification Configuration

**User Story:** As an administrator, I want to select a specific retreat when composing notifications, so that I can send emails to all participants of that retreat along with additional custom recipients.

#### Acceptance Criteria

1. WHEN the administrator selects "retreat" as the need type THEN the system SHALL display a dropdown list of active retreats
2. WHEN displaying the active retreats dropdown THEN the system SHALL only include retreats with is_active status set to true
3. WHEN the administrator selects "retreat" as the need type THEN the system SHALL display an "additional_users_emails" input field
4. WHEN the administrator enters emails in the "additional_users_emails" field THEN the system SHALL accept comma-separated email addresses
5. WHEN a retreat is selected THEN the system SHALL automatically calculate the total number of notification recipients (retreat participants + additional emails)
6. IF the administrator submits the form without selecting a retreat THEN the system SHALL display a validation error message

### Requirement 4: Custom Email List Configuration

**User Story:** As an administrator, I want to send notifications to a custom list of email addresses, so that I can communicate with recipients who may not be associated with a specific retreat.

#### Acceptance Criteria

1. WHEN the administrator selects "custom" as the need type THEN the system SHALL hide the retreat selection dropdown
2. WHEN the administrator selects "custom" as the need type THEN the system SHALL display only the "additional_users_emails" input field
3. WHEN the administrator enters emails in the "additional_users_emails" field THEN the system SHALL accept comma-separated email addresses
4. WHEN the administrator enters emails THEN the system SHALL validate that each email address follows proper email format
5. IF the administrator submits the form with invalid email addresses THEN the system SHALL display validation error messages indicating which emails are invalid
6. IF the administrator submits the form without any email addresses THEN the system SHALL display a validation error message

### Requirement 5: Email Notification Delivery System

**User Story:** As an administrator, I want email notifications to be sent reliably using Laravel's notification system with queue support, so that email delivery doesn't block the application and can handle large recipient lists efficiently.

#### Acceptance Criteria

1. WHEN the administrator submits a valid notification form THEN the system SHALL create a notification record in the database
2. WHEN a notification record is created THEN the system SHALL use Laravel's notification framework to send emails
3. WHEN sending emails THEN the system SHALL dispatch email jobs to a queue for asynchronous processing
4. WHEN processing queued email jobs THEN the system SHALL send individual emails to each recipient
5. WHEN all emails for a notification are queued THEN the system SHALL update the notification status to "queued" or "processing"
6. WHEN all emails for a notification are successfully sent THEN the system SHALL update the notification status to "sent"
7. IF any email fails to send THEN the system SHALL update the notification status to "failed" or "partially_sent"
8. WHEN emails are sent THEN the system SHALL use the same email template structure as existing booking confirmation emails

### Requirement 6: Notification Tracking and Status Management

**User Story:** As an administrator, I want to view the status and details of all sent notifications, so that I can track email delivery and troubleshoot any issues.

#### Acceptance Criteria

1. WHEN a notification is created THEN the system SHALL store: id, datetime, need type, retreat_id (if applicable), total recipient count, additional_users_emails, status, and timestamps
2. WHEN displaying the notifications table THEN the system SHALL show the most recent notifications first
3. WHEN displaying notification status THEN the system SHALL use clear status indicators (e.g., "pending", "queued", "processing", "sent", "failed", "partially_sent")
4. WHEN the administrator views the notifications table THEN the system SHALL display the total number of recipients for each notification
5. WHEN the administrator views a notification with additional emails THEN the system SHALL display the comma-separated list of additional email addresses
6. WHEN the administrator views a retreat notification THEN the system SHALL display the associated retreat_id with a link to the retreat details

### Requirement 7: Email Content and Formatting

**User Story:** As an administrator, I want to create well-formatted email notifications with custom headings, subjects, and body content, so that recipients receive professional and clear communications.

#### Acceptance Criteria

1. WHEN composing a notification THEN the system SHALL allow the administrator to enter a custom heading
2. WHEN composing a notification THEN the system SHALL allow the administrator to enter a custom subject line
3. WHEN composing a notification THEN the system SHALL allow the administrator to enter custom body content
4. WHEN the email is sent THEN the system SHALL use the custom heading, subject, and body in the email template
5. WHEN the email is sent THEN the system SHALL maintain consistent branding and formatting with existing booking confirmation emails
6. WHEN the email body contains line breaks THEN the system SHALL preserve formatting in the sent email

### Requirement 8: Queue Configuration and Processing

**User Story:** As a system administrator, I want the email notification system to use Laravel's queue system properly configured, so that email delivery is reliable and doesn't impact application performance.

#### Acceptance Criteria

1. WHEN the notification system is deployed THEN the system SHALL have queue configuration set up in the Laravel application
2. WHEN emails are dispatched THEN the system SHALL use the configured queue connection (database, redis, etc.)
3. WHEN the queue worker is running THEN the system SHALL process email jobs in the order they were queued
4. IF a queue job fails THEN the system SHALL retry according to the configured retry policy
5. WHEN queue jobs are processed THEN the system SHALL log any errors for troubleshooting
6. WHEN the queue worker is not running THEN the system SHALL queue jobs for later processing without blocking the application
