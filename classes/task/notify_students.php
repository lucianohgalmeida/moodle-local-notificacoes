<?php
/**
 * Tarefa agendada para enviar notificações a alunos matriculados em cursos de extensão.
 *
 * @package   local_notificacoes
 * @author    TecheEduconnect.com.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_notificacoes\task;

defined('MOODLE_INTERNAL') || die();

class notify_students extends \core\task\scheduled_task {

    /**
     * Nome da tarefa agendada.
     */
    public function get_name() {
        return get_string('task_notify_students', 'local_notificacoes');
    }

    /**
     * Executa a tarefa de notificação para alunos.
     */
    public function execute() {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/local/notificacoes/classes/manager.php');

        mtrace("🔔 Executando tarefa agendada: Notificação de alunos");

        // Verifica novas matrículas e envia notificações
        $students = $DB->get_records_sql("
            SELECT ue.userid, e.courseid
            FROM {user_enrolments} ue
            JOIN {enrol} e ON ue.enrolid = e.id
            LEFT JOIN {local_notificacoes_log} ln ON ue.userid = ln.userid AND e.courseid = ln.courseid AND ln.notificationtype = 'enrollment'
            WHERE ln.id IS NULL
        ");

        foreach ($students as $student) {
            \local_notificacoes\manager::notify_student_enrollment($student->userid, $student->courseid);
        }

        // Envia lembretes de 72h antes do início do curso
        \local_notificacoes\manager::notify_students_before_start();

        mtrace("✅ Notificações de alunos processadas!");
    }
}
