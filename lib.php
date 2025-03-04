<?php
/**
 * Biblioteca principal do plugin de Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TecheEduconnect.com.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Classe com funções auxiliares para o plugin
 */
class local_notificacoes_helper {

    /**
     * Envia notificação para o usuário usando o sistema de mensagens do Moodle
     */
    public static function send_notification($userid, $courseid, $type, $data = []) {
        global $CFG;

        try {
            $manager = new \local_notificacoes\manager();
            $user = \core_user::get_user($userid);
            $course = get_course($courseid);

            // Validação básica
            if ($user->deleted || !$course->visible) {
                throw new moodle_exception('invalid_recipient', 'local_notificacoes');
            }

            // Montagem da mensagem
            $message = $manager::render_notification_template($type, [
                'user' => $user,
                'course' => $course,
                'data' => $data
            ]);

            // Envio multicanal
            self::send_multichannel(
                $user,
                get_string("subject_{$type}", 'local_notificacoes'),
                $message
            );

            return true;

        } catch (Exception $e) {
            $manager::log_failure(
                $userid,
                $courseid,
                $type,
                $e->getMessage()
            );
            return false;
        }
    }

    /**
     * Envio por múltiplos canais (email/mensagem interna)
     */
    private static function send_multichannel($user, $subject, $content) {
        // Configurações do plugin
        $admin_email = get_config('local_notificacoes', 'admin_email') ?: \core_user::get_support_user()->email;
        $enable_sms = get_config('local_notificacoes', 'enable_sms');

        // Envio por email
        email_to_user(
            $user,
            \core_user::get_user_by_email($admin_email),
            $subject,
            strip_tags($content),
            $content
        );

        // Mensagem interna do Moodle
        if (get_config('local_notificacoes', 'enable_messaging')) {
            message_send(
                new \core\message\message(),
                $user,
                $subject,
                $content
            );
        }

        // SMS (se habilitado)
        if ($enable_sms && function_exists('sms_send')) {
            sms_send($user->phone1, shorten_content($content, 160));
        }
    }

    /**
     * Valida se uma notificação já foi enviada
     */
    public static function is_notification_sent($userid, $courseid, $type) {
        return \local_notificacoes\manager::notification_exists(
            $userid,
            $courseid,
            $type
        );
    }

    /**
     * Obtém categorias monitoradas com validação
     */
    public static function get_validated_categories() {
        global $DB;
        
        $categories = explode(',', get_config('local_notificacoes', 'course_categories'));
        if (empty($categories)) return [];

        list($insql, $params) = $DB->get_in_or_equal($categories, SQL_PARAMS_NAMED);
        return $DB->get_fieldset_select(
            'course_categories',
            'id',
            "id $insql AND visible = 1",
            $params
        );
    }

    /**
     * Obtém data de início do curso formatada
     */
    public static function get_formatted_start_date($courseid, $userid = null) {
        $course = get_course($courseid);
        return $course->startdate ? userdate(
            $course->startdate,
            get_string('strftimedatefull', 'langconfig'),
            $userid ? \core_date::get_user_timezone($userid) : null
        ) : get_string('notavailable', 'moodle');
    }

    /**
     * Verifica resposta de professores em discussão
     */
    public static function has_teacher_response($discussionid) {
        global $DB;

        $teacher_roles = $DB->get_records_menu('role', null, '', 'shortname,id');
        $teacher_role_ids = array_filter($teacher_roles, function($k) {
            return in_array($k, ['editingteacher', 'teacher']);
        }, ARRAY_FILTER_USE_KEY);

        return $DB->record_exists_sql(
            "SELECT 1 FROM {forum_posts} fp
             JOIN {role_assignments} ra ON fp.userid = ra.userid
             WHERE fp.discussion = :discussionid
             AND ra.roleid IN (" . implode(',', $teacher_role_ids) . ")",
            ['discussionid' => $discussionid]
        );
    }
}

/**
 * Funções de compatibilidade (para APIs externas)
 */

/**
 * @deprecated
 */
function local_notificacoes_send_email($user, $course, $subject, $message) {
    return local_notificacoes_helper::send_notification(
        $user->id,
        $course->id,
        'legacy_email',
        ['subject' => $subject, 'message' => $message]
    );
}

/**
 * @deprecated
 */
function local_notificacoes_log_notification($userid, $courseid, $notificationtype) {
    \local_notificacoes\manager::log_notification(
        $userid,
        $courseid,
        $notificationtype
    );
}