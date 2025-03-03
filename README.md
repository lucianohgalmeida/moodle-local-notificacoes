<?php
/**
 * Script manual para executar as tarefas do cron do plugin local_notificacoes.
 *
 * @package   local_notificacoes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

$context = context_system::instance();
$redirecturl = new moodle_url('/local/notificacoes/index.php');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('cron_execution', 'local_notificacoes'));

try {
    // Executa manualmente as tarefas do CRON do plugin.
    $tasks = [
        new \local_notificacoes\task\notify_students(),
        new \local_notificacoes\task\notify_teachers()
    ];

    foreach ($tasks as $task) {
        $task->execute();
        echo html_writer::div(get_string('cron_task_executed', 'local_notificacoes', get_class($task)), 'alert alert-success');
    }
} catch (Exception $e) {
    echo html_writer::div(get_string('cron_error', 'local_notificacoes', $e->getMessage()), 'alert alert-danger');
}

echo $OUTPUT->footer();
