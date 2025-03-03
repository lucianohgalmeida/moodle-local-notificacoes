<?php
/**
 * Classe responsável por gerenciar as notificações do plugin.
 *
 * @package   local_notificacoes
 * @author    
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_notificacoes;

defined('MOODLE_INTERNAL') || die();

class manager {

    /**
     * Notifica os alunos recém-matriculados em um curso.
     */
    public static function notify_new_enrollments() {
        global $DB, $CFG;

        // Obtém categorias configuradas no plugin.
        $categories = get_config('local_notificacoes', 'course_categories');
        if (empty($categories)) {
            mtrace("⚠️ Nenhuma categoria configurada para notificações.");
            return;
        }

        $categories = explode(',', $categories);

        // Busca os alunos inscritos recentemente.
        $sql = "SELECT ue.id, ue.userid, ue.timecreated, c.id AS courseid, c.fullname AS coursename, u.username, u.email, u.firstname, u.lastname
                FROM {user_enrolments} ue
                JOIN {enrol} e ON ue.enrolid = e.id
                JOIN {course} c ON e.courseid = c.id
                JOIN {user} u ON ue.userid = u.id
                WHERE c.category IN (" . implode(',', array_map('intval', $categories)) . ")
                AND ue.timecreated > :lastcheck";

        $lastcheck = time() - (5 * 60); // Últimos 5 minutos.
        $new_enrollments = $DB->get_records_sql($sql, ['lastcheck' => $lastcheck]);

        foreach ($new_enrollments as $enrollment) {
            // Monta a mensagem
            $message = get_string('email_student_enrollment', 'local_notificacoes', [
                'fullname' => "{$enrollment->firstname} {$enrollment->lastname}",
                'coursename' => $enrollment->coursename,
                'username' => $enrollment->username,
                'siteurl' => $CFG->wwwroot
            ]);

            // Envia e-mail
            email_to_user(
                (object) ['email' => $enrollment->email, 'firstname' => $enrollment->firstname, 'lastname' => $enrollment->lastname],
                (object) ['email' => get_config('local_notificacoes', 'admin_email')],
                get_string('subject_student_enrollment', 'local_notificacoes'),
                strip_tags($message),
                $message
            );

            // Registra log
            $DB->insert_record('local_notificacoes_log', [
                'userid' => $enrollment->userid,
                'courseid' => $enrollment->courseid,
                'notificationtype' => 'enrollment',
                'timecreated' => time()
            ]);
        }
    }

    /**
     * Notifica alunos sobre cursos que começam em 72 horas.
     */
    public static function notify_students_about_upcoming_courses() {
        global $DB, $CFG;

        $timewindow = time() + (72 * 3600);

        $sql = "SELECT ue.userid, c.id AS courseid, c.fullname AS coursename, u.email, u.firstname, u.lastname
                FROM {user_enrolments} ue
                JOIN {enrol} e ON ue.enrolid = e.id
                JOIN {course} c ON e.courseid = c.id
                JOIN {user} u ON ue.userid = u.id
                WHERE c.startdate BETWEEN :now AND :timewindow";

        $students = $DB->get_records_sql($sql, ['now' => time(), 'timewindow' => $timewindow]);

        foreach ($students as $student) {
            $message = get_string('email_student_reminder', 'local_notificacoes', [
                'fullname' => "{$student->firstname} {$student->lastname}",
                'coursename' => $student->coursename,
                'siteurl' => $CFG->wwwroot
            ]);

            email_to_user(
                (object) ['email' => $student->email, 'firstname' => $student->firstname, 'lastname' => $student->lastname],
                (object) ['email' => get_config('local_notificacoes', 'admin_email')],
                get_string('subject_student_reminder', 'local_notificacoes'),
                strip_tags($message),
                $message
            );

            $DB->insert_record('local_notificacoes_log', [
                'userid' => $student->userid,
                'courseid' => $student->courseid,
                'notificationtype' => 'reminder',
                'timecreated' => time()
            ]);
        }
    }

    /**
     * Notifica professores sobre postagens não respondidas no fórum.
     */
    public static function notify_teachers_about_unanswered_posts() {
        global $DB, $CFG;

        $time_limit = time() - (24 * 3600);

        $sql = "SELECT fp.id AS postid, fp.userid AS studentid, fp.discussion, d.course, c.fullname AS coursename, u.email, u.firstname, u.lastname
                FROM {forum_posts} fp
                JOIN {forum_discussions} d ON fp.discussion = d.id
                JOIN {course} c ON d.course = c.id
                JOIN {user} u ON fp.userid = u.id
                WHERE fp.created < :timelimit
                AND NOT EXISTS (
                    SELECT 1 FROM {forum_posts} r WHERE r.parent = fp.id AND r.userid IN 
                    (SELECT ra.userid FROM {role_assignments} ra 
                    JOIN {context} ctx ON ra.contextid = ctx.id WHERE ctx.instanceid = c.id AND ra.roleid = 3)
                )";

        $unanswered_posts = $DB->get_records_sql($sql, ['timelimit' => $time_limit]);

        foreach ($unanswered_posts as $post) {
            $teacher_sql = "SELECT u.id, u.email, u.firstname, u.lastname
                            FROM {user} u
                            JOIN {role_assignments} ra ON u.id = ra.userid
                            JOIN {context} ctx ON ra.contextid = ctx.id
                            WHERE ctx.instanceid = :courseid AND ra.roleid = 3";

            $teachers = $DB->get_records_sql($teacher_sql, ['courseid' => $post->course]);

            foreach ($teachers as $teacher) {
                $message = get_string('email_teacher_notification', 'local_notificacoes', [
                    'teachername' => "{$teacher->firstname} {$teacher->lastname}",
                    'coursename' => $post->coursename,
                    'siteurl' => $CFG->wwwroot
                ]);

                email_to_user(
                    (object) ['email' => $teacher->email, 'firstname' => $teacher->firstname, 'lastname' => $teacher->lastname],
                    (object) ['email' => get_config('local_notificacoes', 'admin_email')],
                    get_string('subject_teacher_notification', 'local_notificacoes'),
                    strip_tags($message),
                    $message
                );

                $DB->insert_record('local_notificacoes_log', [
                    'userid' => $teacher->id,
                    'courseid' => $post->course,
                    'notificationtype' => 'teacher_reminder',
                    'timecreated' => time()
                ]);
            }
        }
    }
}
