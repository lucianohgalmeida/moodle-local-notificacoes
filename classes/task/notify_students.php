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

    const MAX_EXECUTION_TIME = 3300; // 55 minutos em segundos
    const PROGRESS_INTERVAL = 100;   // Intervalo de log de progresso

    /**
     * Retorna o nome traduzido da tarefa
     */
    public function get_name() {
        return get_string('task_notify_students', 'local_notificacoes');
    }

    /**
     * Executa o processo principal de notificações
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/local/notificacoes/classes/manager.php');
        
        $manager = new \local_notificacoes\manager();
        $start_time = time();
        $timeout_check = fn() => (time() - $start_time) < self::MAX_EXECUTION_TIME;

        try {
            $this->start_processing_banner();
            
            // Processamento principal
            $enrollment_result = $this->process_enrollments($manager, $timeout_check);
            $reminder_result = $this->process_reminders($manager, $timeout_check);
            
            // Relatório final
            $this->show_summary($enrollment_result, $reminder_result);
            
        } catch (\Throwable $e) {
            $this->handle_critical_error($manager, $e);
        }
        
        $this->show_completion_banner();
    }

    // ==================== PROCESSAMENTO PRINCIPAL ==================== //

    /**
     * Processa notificações de matrícula
     */
    protected function process_enrollments($manager, $timeout_check) {
        $this->show_section_header('📩 Iniciando processamento de matrículas');
        
        $result = (object)[
            'processed' => 0,
            'success' => 0,
            'failures' => 0,
            'timeout' => false
        ];

        $rs = $manager->get_pending_enrollments();
        
        foreach ($rs as $i => $record) {
            if (!$timeout_check()) {
                $result->timeout = true;
                break;
            }

            $this->handle_enrollment_record($manager, $record, $result, $i);
            $this->show_progress($i);
        }
        
        $rs->close();
        return $result;
    }

    /**
     * Processa lembretes de 72h
     */
    protected function process_reminders($manager, $timeout_check) {
        $this->show_section_header('⏳ Iniciando lembretes de 72h');
        
        return $manager->process_reminders(
            $timeout_check,
            fn($sent, $total) => $this->show_reminder_progress($sent, $total)
        );
    }

    // ==================== MANIPULAÇÃO DE REGISTROS ==================== //

    /**
     * Processa um registro individual de matrícula
     */
    private function handle_enrollment_record($manager, $record, $result, $index) {
        try {
            if ($manager->send_enrollment_notification($record->userid, $record->courseid)) {
                $result->success++;
            }
            $result->processed++;
        } catch (\Exception $e) {
            $result->failures++;
            $manager->log_failure(
                $record->userid,
                $record->courseid,
                \local_notificacoes\manager::NOTIFICATION_ENROLLMENT,
                $e
            );
        }
    }

    // ==================== INTERFACE DE LOGS ==================== //

    /**
     * Exibe resumo consolidado
     */
    private function show_summary($enrollments, $reminders) {
        mtrace("\n📋 RESUMO FINAL");
        mtrace(str_repeat('─', 50));
        
        // Matrículas
        mtrace("Matrículas processadas:");
        mtrace("  ├─ Total: " . $enrollments->processed);
        mtrace("  ├─ Sucessos: " . $enrollments->success);
        mtrace("  ├─ Falhas: " . $enrollments->failures);
        if ($enrollments->timeout) mtrace("  ⚠️ Timeout parcial");
        
        // Lembretes
        mtrace("\nLembretes de 72h:");
        mtrace("  ├─ Enviados: " . $reminders->sent);
        mtrace("  ├─ Falhas: " . $reminders->failed);
        if ($reminders->timeout) mtrace("  ⚠️ Timeout parcial");
    }

    /**
     * Exibe progresso das matrículas
     */
    private function show_progress($current) {
        if ($current % self::PROGRESS_INTERVAL === 0) {
            mtrace("  ├─ Processados: " . ($current + 1));
        }
    }

    /**
     * Exibe progresso dos lembretes
     */
    private function show_reminder_progress($sent, $total) {
        mtrace(sprintf(
            "  ├─ Progresso: %d/%d (%.1f%%)",
            $sent,
            $total,
            ($total > 0 ? ($sent/$total)*100 : 0)
        );
    }

    // ==================== UTILITÁRIOS VISUAIS ==================== //

    private function start_processing_banner() {
        mtrace("\n🚀 " . strtoupper(get_string('processing_start', 'local_notificacoes')));
        mtrace(str_repeat('═', 60));
    }

    private function show_completion_banner() {
        mtrace("\n" . str_repeat('═', 60));
        mtrace("✅ " . strtoupper(get_string('processing_complete', 'local_notificacoes')));
    }

    private function show_section_header($title) {
        mtrace("\n" . str_repeat('─', 60));
        mtrace($title);
        mtrace(str_repeat('─', 60));
    }

    // ==================== GERENCIAMENTO DE ERROS ==================== //

    private function handle_critical_error($manager, $e) {
        mtrace("\n🔥 " . str_repeat('=', 20) . " ERRO CRÍTICO " . str_repeat('=', 20));
        mtrace("Mensagem: " . $e->getMessage());
        mtrace("Arquivo: " . $e->getFile());
        mtrace("Linha: " . $e->getLine());
        mtrace(str_repeat('═', 60));
        
        $manager->log_critical_error(
            'notify_students',
            $e->getMessage() . PHP_EOL . $e->getTraceAsString()
        );
    }
}