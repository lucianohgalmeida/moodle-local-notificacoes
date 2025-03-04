<?php
/**
 * Script manual para executar as tarefas do cron do plugin local_notificacoes.
 *
 * @package   local_notificacoes
 * @author    TecheEduconnect.com.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

// Verifica ambiente e parâmetros de segurança
$token = optional_param('token', '', PARAM_ALPHANUM);
$valid_token = get_config('local_notificacoes', 'cron_token');

// Acesso apenas para administradores ou com token válido
if ((!is_siteadmin() && $token !== $valid_token) || !get_config('local_notificacoes', 'enable_manual_cron')) {
    throw new moodle_exception('accessdenied', 'admin');
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/notificacoes/cron.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('cron_execution', 'local_notificacoes'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('cron_execution', 'local_notificacoes'));

// Lista de tarefas para executar
$tasks = [
    'notify_students' => new \local_notificacoes\task\notify_students(),
    'notify_teachers' => new \local_notificacoes\task\notify_teachers()
];

$results = [];
$total_start = microtime(true);

try {
    foreach ($tasks as $key => $task) {
        $task_start = microtime(true);
        
        mtrace("Iniciando tarefa: " . get_class($task));
        $task->execute();
        
        $results[$key] = [
            'status' => 'success',
            'time' => round(microtime(true) - $task_start, 2),
            'memory' => memory_get_peak_usage(true)
        ];
    }
} catch (Throwable $e) {
    $results[$key]['status'] = 'error';
    $results[$key]['error'] = $e->getMessage();
    $results[$key]['trace'] = $e->getTraceAsString();
}

// Gera relatório de execução
$total_time = round(microtime(true) - $total_start, 2);
echo $OUTPUT->render_from_template('local_notificacoes/cron_report', [
    'results' => $results,
    'total_time' => $total_time,
    'execution_date' => userdate(time()),
    'memory_usage' => display_size(memory_get_peak_usage(true)),
    'system_load' => function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 'N/A'
]);

// Log de auditoria
$event = \local_notificacoes\event\manual_cron_executed::create([
    'context' => $context,
    'other' => [
        'results' => $results,
        'execution_time' => $total_time
    ]
]);
$event->trigger();

echo $OUTPUT->footer();