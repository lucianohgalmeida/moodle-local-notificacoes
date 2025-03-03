<?php
/**
 * Main library for the plugin local_notificacoes.
 *
 * @package   local_notificacoes
 * @author    
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Sends a notification email to the user.
 *
 * @param object $user User object
 * @param object $course Course object
 * @param string $subject Email subject
 * @param string $message Email body
 * @return bool True if email sent successfully, false otherwise
 */
function local_notificacoes_send_email($user, $course, $subject, $message) {
    global $CFG;

    $emailuser = new stdClass();
    $emailuser->id = $user->id;
    $emailuser->email = $user->email;
    $emailuser->firstname = $user->firstname;
    $emailuser->lastname = $user->lastname;

    $emailfrom = get_admin();
    return email_to_user($emailuser, $emailfrom, $subject, strip_tags($message), $message);
}

/**
 * Logs notification activity in the database.
 *
 * @param int $userid User ID
 * @param int $courseid Course ID
 * @param string $notificationtype Type of notification
 */
function local_notificacoes_log_notification($userid, $courseid, $notificationtype) {
    global $DB;

    $record = new stdClass();
    $record->userid = $userid;
    $record->courseid = $courseid;
    $record->notificationtype = $notificationtype;
    $record->timecreated = time();

    $DB->insert_record('local_notificacoes_log', $record);
}

/**
 * Retrieves the list of monitored course categories.
 *
 * @return array List of category IDs
 */
function local_notificacoes_get_monitored_categories() {
    $categories = get_config('local_notificacoes', 'course_categories');
    return !empty($categories) ? explode(',', $categories) : [];
}

/**
 * Checks if a notification has already been sent.
 *
 * @param int $userid User ID
 * @param int $courseid Course ID
 * @param string $notificationtype Type of notification
 * @return bool True if notification exists, false otherwise
 */
function local_notificacoes_notification_exists($userid, $courseid, $notificationtype) {
    global $DB;
    return $DB->record_exists('local_notificacoes_log', [
        'userid' => $userid,
        'courseid' => $courseid,
        'notificationtype' => $notificationtype
    ]);
}

/**
 * Fetches enrolled users in a course.
 *
 * @param int $courseid Course ID
 * @return array List of enrolled users
 */
function local_notificacoes_get_enrolled_users($courseid) {
    global $DB;
    $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email
            FROM {user_enrolments} ue
            JOIN {user} u ON ue.userid = u.id
            JOIN {enrol} e ON ue.enrolid = e.id
            WHERE e.courseid = ?";
    return $DB->get_records_sql($sql, [$courseid]);
}

/**
 * Fetches the enrollment date of a user in a course.
 *
 * @param int $userid User ID
 * @param int $courseid Course ID
 * @return string Enrollment date in d/m/Y format
 */
function local_notificacoes_get_enrollment_date($userid, $courseid) {
    global $DB;
    $sql = "SELECT ue.timecreated
            FROM {user_enrolments} ue
            JOIN {enrol} e ON ue.enrolid = e.id
            WHERE ue.userid = ? AND e.courseid = ?";
    $record = $DB->get_record_sql($sql, [$userid, $courseid]);

    return !empty($record->timecreated) ? date('d/m/Y', $record->timecreated) : 'N/A';
}

/**
 * Fetches the course start date.
 *
 * @param int $courseid Course ID
 * @return string Course start date in d/m/Y format
 */
function local_notificacoes_get_course_start_date($courseid) {
    global $DB;
    $startdate = $DB->get_field('course', 'startdate', ['id' => $courseid]);
    return !empty($startdate) ? date('d/m/Y', $startdate) : 'N/A';
}

/**
 * Checks if a teacher has responded to a student's post in a forum.
 *
 * @param int $discussionid Forum discussion ID
 * @return bool True if a teacher has responded, false otherwise
 */
function local_notificacoes_teacher_responded($discussionid) {
    global $DB;

    $sql = "SELECT p.id
            FROM {forum_posts} p
            JOIN {user} u ON p.userid = u.id
            JOIN {role_assignments} ra ON ra.userid = u.id
            JOIN {context} ctx ON ra.contextid = ctx.id
            JOIN {course} c ON ctx.instanceid = c.id
            WHERE p.discussion = ? AND ra.roleid IN (3, 4)"; // 3 = Editing Teacher, 4 = Non-editing Teacher

    return $DB->record_exists_sql($sql, [$discussionid]);
}
