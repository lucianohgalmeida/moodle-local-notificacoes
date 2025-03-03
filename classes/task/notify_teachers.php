<?php
/**
 * Tarefa agendada para notificar professores sobre postagens não respondidas.
 *
 * @package   local_notificacoes
 * @author    
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_notificacoes\task;

defined('MOODLE_INTERNAL') || die();

class notify_teachers extends \core\task\scheduled_task {

    /**
     * Nome da tarefa agendada.
     */
    public function get_name() {
        return get_string('task_notify_teachers', 'local_notificacoes');
    }

    /**
     * Executa a tarefa de notificação para professores.
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/local/notificacoes/classes/manager.php');

        mtrace("🔔 Executando tarefa agendada: Notificação de professores");

        // Verifica e envia notificações para professores
        \local_notificacoes\manager::notify_teachers_about_unanswered_posts();

        mtrace("✅ Notificações de professores processadas!");
    }
}
