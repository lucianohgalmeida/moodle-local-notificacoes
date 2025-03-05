<?php
/**
 * Instalação do banco de dados para o plugin Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TechEduConnect
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/lib/classes/task/manager.php');

use core\task\manager;
use core\output\notification;
use local_notificacoes\task\notify_students;
use local_notificacoes\task\notify_teachers;

// Verifica permissões e token
$token = optional_param('token', '', PARAM_ALPHANUM);
$valid_token = get_config('local_notificacoes', 'cron_token');

if ((!is_siteadmin() && $token !== $valid_token) || !get_config('local_notificacoes', 'enable_manual_cron')) {
    throw new moodle_exception('accessdenied', 'admin');
}

// Configuração da página
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/notificacoes/cron.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('cron_execution', 'local_notificacoes'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('cron_execution', 'local_notificacoes'));

// Criando tarefas ad hoc
$tasks = [
    new notify_students(),
    new notify_teachers()
];

$results = [];
$total_start = microtime(true);

try {
    foreach ($tasks as $task) {
        $task_name = get_class($task);
        mtrace("Enfileirando tarefa: {$task_name}");

        manager::queue_adhoc_task($task);

        $results[] = [
            'task' => $task_name,
            'status' => 'queued',
            'time' => round(microtime(true) - $total_start, 2),
        ];
    }

    echo $OUTPUT->notification(get_string('tasks_queued', 'local_notificacoes'), notification::NOTIFY_SUCCESS);
} catch (Throwable $e) {
    error_log("Erro ao enfileirar tarefas: " . $e->getMessage());

    $results[] = [
        'task' => $task_name ?? 'N/A',
        'status' => 'error',
        'error' => $e->getMessage()
    ];
    
    echo $OUTPUT->notification(get_string('tasks_error', 'local_notificacoes') . ': ' . $e->getMessage(), notification::NOTIFY_ERROR);
}

// Registro do evento no Moodle
$event = \local_notificacoes\event\manual_cron_executed::create([
    'context' => $context,
    'other' => [
        'results' => json_encode($results),
        'execution_time' => round(microtime(true) - $total_start, 2)
    ]
]);
$event->trigger();

echo $OUTPUT->footer();
