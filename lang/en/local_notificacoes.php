<?php
/**
 * Language file for English (en).
 *
 * @package   local_notificacoes
 * @author    
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin name
$string['pluginname'] = 'Automatic Notifications';

// Plugin settings
$string['settings_header'] = 'Notification Settings';
$string['settings_description'] = 'Configure the monitored course categories and notification sending intervals.';
$string['enable_notifications'] = 'Enable notifications';
$string['enable_notifications_desc'] = 'Enable or disable all notifications sent by the plugin.';
$string['course_categories'] = 'Course categories';
$string['course_categories_desc'] = 'Enter the category IDs separated by commas for monitoring.';
$string['admin_email'] = 'Administrator Email';
$string['admin_email_desc'] = 'Email used as the sender of notifications.';

// Student notifications
$string['subject_student_enrollment'] = '📚 Enrollment Confirmed for the course: {$a->coursename}';
$string['email_student_enrollment'] = 'Hello {$a->fullname},

You have been enrolled in the course <b>{$a->coursename}</b>.

🔗 <b>Access the course:</b> <a href="{$a->siteurl}">{$a->siteurl}</a>  
👤 <b>Your login:</b> {$a->username}  

Happy learning! 🚀';

// Student reminder notifications
$string['subject_student_reminder'] = '⏳ Your course {$a->coursename} starts soon!';
$string['email_student_reminder'] = 'Hello {$a->fullname},

The course <b>{$a->coursename}</b> will start soon!

🔗 <b>Access the course:</b> <a href="{$a->siteurl}">{$a->siteurl}</a>  
📆 <b>Start date:</b> {$a->coursestartdate}  

Don't miss this learning opportunity! 🎯';

// Teacher notifications for unanswered posts
$string['subject_teacher_notification'] = '🕒 Respond to the student in the forum for the course {$a->coursename}';
$string['email_teacher_notification'] = 'Hello {$a->teachername},

A student posted in the forum of the course <b>{$a->coursename}</b> more than 24 hours ago and has not yet received a response.

📍 <b>Forum:</b> <a href="{$a->siteurl}">Access the topic</a>  

Active teacher participation enhances student learning. We count on you! 👍';

// Log messages
$string['log_enrollment_sent'] = 'Enrollment notification sent to {$a->userid} in course {$a->courseid}.';
$string['log_reminder_sent'] = 'Reminder sent to {$a->userid} in course {$a->courseid}.';
$string['log_teacher_notified'] = 'Pending interaction notification sent to teacher {$a->userid} in course {$a->courseid}.';

// Error messages
$string['error_no_categories'] = 'No category has been set up for notifications.';
$string['error_no_enrollments'] = 'No new enrollments found for notification sending.';
$string['error_no_teachers'] = 'No teacher found for unanswered post notifications.';
$string['error_no_reminders'] = 'No reminders needed at this time.';
$string['error_email_send'] = 'Error sending notification to {$a->email}.';

// Success messages
$string['success_notification_sent'] = 'Notification sent successfully!';
$string['success_configuration_saved'] = 'Settings saved successfully!';
