<?php
/**
 * Observador de eventos para Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TecheEduconnect.com.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class local_notificacoes_observer {

    /**
     * Captura evento de matrícula e registra no log do plugin.
     * 
     * @param \core\event\user_enrolment_created $event
     */
    public static function user_enrolled(\core\event\user_enrolment_created $event) {
        global $DB;

        mtrace("📩 [Notificações Automáticas] Evento de matrícula capturado.");

        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        if (empty($userid) || empty($courseid)) {
            mtrace("❌ [Notificações Automáticas] Erro: ID do usuário ou do curso inválido.");
            return;
        }

        // Verifica se o usuário já tem uma notificação registrada para evitar duplicação
        if (self::is_notification_already_logged($userid, $courseid)) {
            mtrace("⚠️ [Notificações Automáticas] Notificação já registrada para userid: $userid e courseid: $courseid.");
            return;
        }

        // Registra no log do plugin
        if (!self::register_notification_log($userid, $courseid)) {
            mtrace("❌ [Notificações Automáticas] Falha ao registrar a notificação no banco.");
            return;
        }

        // Envia a notificação para o usuário
        self::send_notification($userid, $courseid);
    }

    /**
     * Verifica se a notificação já foi registrada para evitar duplicação.
     * 
     * @param int $userid
     * @param int $courseid
     * @return bool
     */
    private static function is_notification_already_logged($userid, $courseid) {
        global $DB;
        return $DB->record_exists('local_notificacoes_log', ['userid' => $userid, 'courseid' => $courseid]);
    }

    /**
     * Registra a notificação no banco de dados.
     * 
     * @param int $userid
     * @param int $courseid
     * @return bool
     */
    private static function register_notification_log($userid, $courseid) {
        global $DB;

        $record = new stdClass();
        $record->userid = $userid;
        $record->courseid = $courseid;
        $record->notificationtype = 'matricula';
        $record->status = 'pendente';
        $record->timecreated = time();
        $record->timemodified = time();

        try {
            $DB->insert_record('local_notificacoes_log', $record);
            mtrace("✅ [Notificações Automáticas] Registro salvo no banco para userid: $userid, courseid: $courseid.");
            return true;
        } catch (Exception $e) {
            mtrace("❌ [Notificações Automáticas] Erro ao salvar no banco: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia notificação ao usuário matriculado.
     * 
     * @param int $userid
     * @param int $courseid
     */
    private static function send_notification($userid, $courseid) {
        global $DB;

        mtrace("📩 [Notificações Automáticas] Preparando envio de mensagem para userid: $userid.");

        $user = $DB->get_record('user', ['id' => $userid], 'id, firstname, email');
        if (!$user) {
            mtrace("❌ [Notificações Automáticas] Erro: Usuário não encontrado.");
            return;
        }

        $message = new \core\message\message();
        $message->component = 'local_notificacoes';
        $message->name = 'matricula_confirmada';
        $message->userfrom = get_admin();
        $message->userto = $user;
        $message->subject = "Confirmação de matrícula";
        $message->fullmessage = "Olá {$user->firstname}, sua matrícula foi confirmada!";
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = "<p>Olá {$user->firstname}, sua matrícula foi confirmada!</p>";
        $message->smallmessage = "Confirmação de matrícula";
        $message->notification = 1;
      
        try {
            $result = message_send($message);
            $status = $result ? 'enviado' : 'erro';

            // Atualiza status no banco
            self::update_notification_status($userid, $courseid, $status);
        } catch (Exception $e) {
            mtrace("❌ [Notificações Automáticas] Erro ao enviar mensagem: " . $e->getMessage());
        }
    }

    /**
     * Atualiza o status da notificação no banco de dados.
     * 
     * @param int $userid
     * @param int $courseid
     * @param string $status
     */
    private static function update_notification_status($userid, $courseid, $status) {
        global $DB;

        try {
            $DB->execute("UPDATE {local_notificacoes_log} SET status = ?, timemodified = ? WHERE userid = ? AND courseid = ?", 
                [$status, time(), $userid, $courseid]);

            mtrace("✅ [Notificações Automáticas] Status atualizado para '$status' para userid: $userid, courseid: $courseid.");
        } catch (Exception $e) {
            mtrace("❌ [Notificações Automáticas] Erro ao atualizar status no banco: " . $e->getMessage());
        }
    }
}