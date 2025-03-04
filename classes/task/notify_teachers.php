<?php
/**
 * Tarefa agendada para notificar professores sobre postagens não respondidas.
 *
 * @package   local_notificacoes
 * @author    TecheEduconnect.com.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_notificacoes\task;

defined('MOODLE_INTERNAL') || die();

class notify_teachers extends \core\task\scheduled_task {

    /**
     * Nome da tarefa agendada (traduzível).
     */
    public function get_name() {
        return get_string('task_notify_teachers', 'local_notificacoes');
    }

    /**
     * Executa a tarefa com tratamento completo de erros e controle de performance.
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/local/notificacoes/classes/manager.php');
        $manager = new \local_notificacoes\manager();

        mtrace("🚀 Iniciando processo de notificações para professores...");

        try {
            // 1. Verifica se o recurso está habilitado
            if (!get_config('local_notificacoes', 'enable_forum_notif')) {
                mtrace("⏸️ Notificações de fórum desabilitadas nas configurações");
                return;
            }

            // 2. Controle de tempo de execução
            $starttime = time();
            $maxduration = 55 * 60; // 55 minutos
            
            // 3. Executa o processo principal
            $result = $manager->notify_teachers_about_unanswered_posts(
                function() use ($starttime, $maxduration) {
                    return (time() - $starttime) < $maxduration;
                }
            );

            // 4. Logging detalhado
            mtrace("📊 Estatísticas:");
            mtrace("   👨🏫 Cursos processados: " . $result->courses_processed);
            mtrace("   📩 Notificações enviadas: " . $result->notifications_sent);
            mtrace("   ⚠️ Falhas: " . $result->failures);
            
            if ($result->timeout) {
                mtrace("⏰ Aviso: Processo interrompido por limite de tempo");
            }

        } catch (\Throwable $e) {
            mtrace("🔥 Erro crítico: " . $e->getMessage());
            $manager->log_critical_error('notify_teachers', $e->getMessage());
        }

        mtrace("✅ Processo concluído!");
    }
}