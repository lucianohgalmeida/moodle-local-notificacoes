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

require_login();
require_capability('local/notificacoes:manage', context_system::instance());

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/notificacoes/index.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_notificacoes'));
$PAGE->set_heading(get_string('settings_heading', 'local_notificacoes'));

// Carrega dependências
require_once($CFG->dirroot.'/local/notificacoes/classes/forms/settings_form.php');

$form = new \local_notificacoes\forms\settings_form();

// Processamento do formulário
if ($data = $form->get_data()) {
    require_sesskey();
    
    // Salva configurações
    set_config('course_categories', implode(',', $data->categories), 'local_notificacoes');
    set_config('student_reminder_hours', $data->student_reminder_hours, 'local_notificacoes');
    set_config('teacher_alert_hours', $data->teacher_alert_hours, 'local_notificacoes');
    
    \core\notification::success(get_string('settings_saved', 'local_notificacoes'));
}

echo $OUTPUT->header();

// Exibe alerta se o plugin estiver desabilitado
if (!get_config('local_notificacoes', 'enableplugin')) {
    echo $OUTPUT->notification(get_string('plugin_disabled', 'local_notificacoes'), 'warning');
}

// Renderiza template com informações do sistema
echo $OUTPUT->render_from_template('local_notificacoes/settings_page', [
    'form' => $form->render(),
    'stats' => $this->get_plugin_stats(),
    'sesskey' => sesskey(),
    'last_execution' => $this->get_last_execution_info(),
    'notification_types' => $this->get_notification_types_info()
]);

echo $OUTPUT->footer();

/**
 * Obtém estatísticas de uso do plugin
 */
function get_plugin_stats() {
    global $DB;
    
    return [
        'total_notifications' => $DB->count_records('local_notificacoes_log'),
        'last_week' => $DB->count_records_select(
            'local_notificacoes_log',
            'timecreated > :time',
            ['time' => time() - (7 * DAYSECS)]
        )
    ];
}

/**
 * Obtém informações da última execução
 */
function get_last_execution_info() {
    $last = get_config('local_notificacoes', 'last_cron_run');
    return [
        'timestamp' => $last ? userdate($last) : get_string('never', 'local_notificacoes'),
        'duration' => format_time(get_config('local_notificacoes', 'last_cron_duration'))
    ];
}

/**
 * Obtém tipos de notificação configuráveis
 */
function get_notification_types_info() {
    return [
        [
            'name' => get_string('enrollment_notifications', 'local_notificacoes'),
            'count' => get_config('local_notificacoes', 'total_enrollment_notifications'),
            'last_sent' => userdate(get_config('local_notificacoes', 'last_enrollment_sent'))
        ],
        [
            'name' => get_string('reminder_notifications', 'local_notificacoes'),
            'count' => get_config('local_notificacoes', 'total_reminder_notifications'),
            'last_sent' => userdate(get_config('local_notificacoes', 'last_reminder_sent'))
        ]
    ];
}