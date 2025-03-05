<?php
/**
 * Instalação do banco de dados para o plugin Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TechEduConnect
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// English language file
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Auto Notifications';

// General Settings
$string['generalsettings'] = 'General Settings';
$string['generalsettings_desc'] = 'Basic plugin operation settings';
$string['enableplugin'] = 'Enable Plugin';
$string['enableplugin_desc'] = 'Enable/disable the entire notification system';

// Categories
$string['categoriesettings'] = 'Category Settings';
$string['categoriesettings_desc'] = 'Select categories to monitor';
$string['coursecategories'] = 'Course Categories';
$string['coursecategories_desc'] = 'Select course categories for monitoring';

// Time
$string['timesettings'] = 'Time Settings';
$string['timesettings_desc'] = 'Notification trigger intervals';
$string['studentreminder'] = 'Student Reminder';
$string['studentreminder_desc'] = 'Time before course start to send reminder';
$string['teacheralert'] = 'Teacher Alert';
$string['teacheralert_desc'] = 'Maximum time without forum responses';

// Notifications
$string['notificationsettings'] = 'Notification Settings';
$string['notificationsettings_desc'] = 'Notification delivery preferences';
$string['notificationchannels'] = 'Notification Channels';
$string['notificationchannels_desc'] = 'Select communication channels to use';
$string['channel_email'] = 'Email';
$string['channel_messaging'] = 'Internal Messaging';
$string['channel_sms'] = 'SMS';

// Email
$string['emailsender'] = 'Email Sender';
$string['emailsender_desc'] = 'Email address that will appear as sender';
$string['emailtemplate'] = 'Email Template';
$string['emailtemplate_desc'] = 'Use placeholders: {user}, {course}, {date}';
$string['default_email_template'] = 'Dear {fullname},

You have a new notification regarding the course "{coursename}".

Access your student area for more details.

Best regards,
Academic Team.';

//Confination
$string['messageprovider:matricula_confirmada'] = 'Confirmação de matrícula';

// Student Notifications
$string['subject_enrollment'] = '📚 Enrollment Confirmed in Course: {$a}';
$string['email_student_enrollment'] = 'Hello {fullname},<br><br>
You have been enrolled in the course <b>{coursename}</b>.<br><br>
🔗 <b>Access the course:</b> <a href="{courseurl}">{courseurl}</a><br>
👤 <b>Your login:</b> {username}<br><br>
Happy learning! 🚀';

$string['subject_reminder'] = '⏳ Your Course {$a} Starts Soon!';
$string['email_student_reminder'] = 'Hello {fullname},<br><br>
The course <b>{coursename}</b> will start on {startdate}!<br><br>
🔗 <b>Access the course:</b> <a href="{courseurl}">{courseurl}</a><br><br>
Don\'t miss the start! 📆';

// Teacher Notifications
$string['subject_teacher_alert'] = '🕒 Unanswered Post in {$a}';
$string['email_teacher_notification'] = 'Hello {teachername},<br><br>
There is an unanswered post in the forum of course <b>{coursename}</b>:<br><br>
💬 "{postcontent}"<br><br>
🔗 <a href="{forumurl}">Access the forum</a><br><br>
Please respond within 24 hours. 👍';

// Logs & Statistics
$string['total_notifications'] = 'Total Notifications';
$string['last_week'] = 'Last 7 Days';
$string['last_execution'] = 'Last Execution';
$string['execution_duration'] = 'Execution Duration';

// Errors
$string['invalid_request'] = 'Invalid Request';
$string['nocategories'] = 'Select at least one category';
$string['invalid_hours'] = 'Hours must be greater than zero';
$string['error_email_send'] = 'Error sending to {email}';

// Success
$string['settings_saved'] = 'Settings saved successfully!';
$string['cron_task_executed'] = 'Task {$a} executed';

// Cron
$string['cron_execution'] = 'Manual Cron Execution';
$string['cron_report'] = 'Execution Report';
$string['system_load'] = 'System Load';
$string['status_success'] = 'Success';
$string['status_error'] = 'Error';

// Events
$string['event_settings_updated'] = 'Settings Updated';
$string['event_manual_cron_executed'] = 'Manual Cron Executed';