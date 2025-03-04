<?php
/**
 * Classe responsável por gerenciar as notificações do plugin.
 *
 * @package   local_notificacoes
 * @author    TecheEduconnect.com.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_notificacoes;

defined('MOODLE_INTERNAL') || die();

class manager {
    const NOTIFICATION_ENROLLMENT = 'enrollment';
    const NOTIFICATION_REMINDER = 'reminder';
    const NOTIFICATION_TEACHER_ALERT = 'teacher_alert';
    
    // Configurações
    const CFG_ENABLE_EMAIL = 'enable_email';
    const CFG_ENABLE_SMS = 'enable_sms';
    const CFG_ENABLE_MESSAGING = 'enable_messaging';
    
    /** @var \moodle_database */
    protected $db;
    
    /** @var \core_renderer */
    protected $renderer;

    public function __construct() {
        global $DB, $PAGE;
        $this->db = $DB;
        $this->renderer = $PAGE->get_renderer('local_notificacoes');
    }

    // ==================== CORE FUNCTIONALITY ==================== //

    /**
     * Processa todas as notificações de matrícula
     */
    public function process_enrollments(): int {
        $categories = $this->get_valid_categories();
        if (empty($categories)) {
            throw new \moodle_exception('nocategories', 'local_notificacoes');
        }

        $rs = $this->get_pending_enrollments($categories);
        $count = $this->process_enrollment_records($rs);
        $rs->close();

        return $count;
    }

    /**
     * Processa lembretes de 72h com controle de tempo
     */
    public function process_reminders(\closure $continue_callback): \stdClass {
        $result = (object)[
            'sent' => 0,
            'failed' => 0,
            'timeout' => false
        ];

        $rs = $this->db->get_recordset_sql("
            SELECT ue.userid, c.id AS courseid
            FROM {user_enrolments} ue
            JOIN {enrol} e ON ue.enrolid = e.id
            JOIN {course} c ON e.courseid = c.id
            WHERE c.startdate BETWEEN ? AND ?
            AND NOT EXISTS (
                SELECT 1 FROM {local_notificacoes_log} ln 
                WHERE ln.userid = ue.userid 
                AND ln.courseid = c.id 
                AND ln.notificationtype = ?
            )", [time(), time() + 72 * HOURSECS, self::NOTIFICATION_REMINDER]);

        foreach ($rs as $record) {
            if (!$continue_callback()) {
                $result->timeout = true;
                break;
            }

            try {
                $this->send_reminder_notification($record->userid, $record->courseid);
                $result->sent++;
            } catch (\Exception $e) {
                $result->failed++;
                $this->log_failure(...);
            }
        }

        $rs->close();
        return $result;
    }

    // ==================== NOTIFICATION METHODS ==================== //

    /**
     * Envia notificação de matrícula
     */
    public function send_enrollment_notification(int $userid, int $courseid): bool {
        $user = $this->get_valid_user($userid);
        $course = $this->get_valid_course($courseid);

        $message = $this->render_notification(
            self::NOTIFICATION_ENROLLMENT,
            $this->get_enrollment_data($user, $course)
        );

        $this->send_multichannel_notification($user, $message);
        $this->log_success($userid, $courseid, self::NOTIFICATION_ENROLLMENT);
        
        return true;
    }

    /**
     * Envia alerta para professores
     */
    public function send_teacher_alert(\stdClass $teacher, \stdClass $post): bool {
        $message = $this->render_notification(
            self::NOTIFICATION_TEACHER_ALERT,
            $this->get_teacher_alert_data($teacher, $post)
        );

        $this->send_multichannel_notification($teacher, $message);
        return true;
    }

    // ==================== SUPPORT METHODS ==================== //

    /**
     * Processa registros de matrícula
     */
    protected function process_enrollment_records(\moodle_recordset $rs): int {
        $count = 0;
        foreach ($rs as $record) {
            try {
                if ($this->send_enrollment_notification($record->userid, $record->courseid)) {
                    $count++;
                }
            } catch (\Exception $e) {
                $this->log_failure(...);
            }
        }
        return $count;
    }

    /**
     * Obtém matrículas pendentes
     */
    protected function get_pending_enrollments(array $categories): \moodle_recordset {
        list($insql, $params) = $this->db->get_in_or_equal($categories, SQL_PARAMS_NAMED, 'cat');
        $params['type'] = self::NOTIFICATION_ENROLLMENT;

        return $this->db->get_recordset_sql("
            SELECT ue.userid, e.courseid
            FROM {user_enrolments} ue
            JOIN {enrol} e ON ue.enrolid = e.id
            WHERE e.courseid IN (
                SELECT id FROM {course}
                WHERE category $insql
                AND visible = 1
            )
            AND NOT EXISTS (
                SELECT 1 FROM {local_notificacoes_log} log
                WHERE log.userid = ue.userid
                AND log.courseid = e.courseid
                AND log.notificationtype = :type
            )", $params);
    }

    // ==================== NOTIFICATION ENGINE ==================== //

    /**
     * Envio multicanal integrado
     */
    protected function send_multichannel_notification(\stdClass $user, \stdClass $message): void {
        $sender = $this->get_sender();
        
        if ($this->is_email_enabled()) {
            $this->send_email($user, $sender, $message);
        }
        
        if ($this->is_messaging_enabled()) {
            $this->send_internal_message($user, $message);
        }
        
        if ($this->is_sms_enabled() && !empty($user->phone1)) {
            $this->send_sms($user->phone1, $message->sms);
        }
    }

    /**
     * Renderiza notificação a partir de template
     */
    protected function render_notification(string $type, array $data): \stdClass {
        return (object)[
            'subject' => get_string("subject_{$type}", 'local_notificacoes', $data),
            'html' => $this->renderer->render_from_template("local_notificacoes/{$type}_html", $data),
            'text' => get_string("text_{$type}", 'local_notificacoes', $data),
            'sms' => get_string("sms_{$type}", 'local_notificacoes', $data)
        ];
    }

    // ==================== VALIDATION METHODS ==================== //

    protected function get_valid_user(int $userid): \stdClass {
        $user = \core_user::get_user($userid, '*', MUST_EXIST);
        if ($user->deleted || !$user->confirmed) {
            throw new \moodle_exception('invaliduser', 'local_notificacoes');
        }
        return $user;
    }

    protected function get_valid_course(int $courseid): \stdClass {
        $course = get_course($courseid);
        if (!$course->visible) {
            throw new \moodle_exception('coursehidden', 'local_notificacoes');
        }
        return $course;
    }

    // ==================== LOGGING METHODS ==================== //

    protected function log_success(int $userid, int $courseid, string $type): void {
        $this->db->insert_record('local_notificacoes_log', [
            'userid' => $userid,
            'courseid' => $courseid,
            'notificationtype' => $type,
            'status' => 'sent',
            'timecreated' => time(),
            'timemodified' => time()
        ]);
    }

    protected function log_failure(int $userid, int $courseid, string $type, \Throwable $e): void {
        $this->db->insert_record('local_notificacoes_log', [
            'userid' => $userid,
            'courseid' => $courseid,
            'notificationtype' => $type,
            'status' => 'error',
            'error' => $e->getMessage(),
            'timecreated' => time(),
            'timemodified' => time()
        ]);
    }

    // ==================== UTILITY METHODS ==================== //

    protected function get_valid_categories(): array {
        $categories = get_config('local_notificacoes', 'course_categories');
        return $categories ? array_filter(explode(',', $categories), 'ctype_digit') : [];
    }

    protected function is_email_enabled(): bool {
        return (bool)get_config('local_notificacoes', self::CFG_ENABLE_EMAIL);
    }

    protected function is_sms_enabled(): bool {
        return (bool)get_config('local_notificacoes', self::CFG_ENABLE_SMS);
    }

    protected function is_messaging_enabled(): bool {
        return (bool)get_config('local_notificacoes', self::CFG_ENABLE_MESSAGING);
    }

    protected function get_sender(): \stdClass {
        return \core_user::get_support_user();
    }
}