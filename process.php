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
$redirecturl = new moodle_url('/local/notificacoes/index.php');

// Verifica se é uma requisição POST válida
$data = data_submitted();
if (!confirm_sesskey() || empty($data) || !isloggedin()) {
    redirect($redirecturl, get_string('invalid_request', 'local_notificacoes'), null, \core\output\notification::NOTIFY_ERROR);
}

try {
    // Validação dos parâmetros
    $categories = optional_param_array('categories', [], PARAM_INT);
    $student_hours = optional_param('student_reminder_hours', 72, PARAM_INT);
    $teacher_hours = optional_param('teacher_alert_hours', 24, PARAM_INT);

    // Validação adicional
    if (empty($categories)) {
        throw new moodle_exception('nocategories', 'local_notificacoes');
    }
    
    if ($student_hours < 1 || $teacher_hours < 1) {
        throw new moodle_exception('invalid_hours', 'local_notificacoes');
    }

    // Salva as configurações
    set_config('course_categories', implode(',', $categories), 'local_notificacoes');
    set_config('student_reminder_hours', $student_hours, 'local_notificacoes');
    set_config('teacher_alert_hours', $teacher_hours, 'local_notificacoes');
    
    // Log de auditoria
    $event = \local_notificacoes\event\settings_updated::create([
        'context' => $context,
        'other' => [
            'categories' => $categories,
            'student_hours' => $student_hours,
            'teacher_hours' => $teacher_hours
        ]
    ]);
    $event->trigger();

    redirect($redirecturl, get_string('settings_saved', 'local_notificacoes'), null, \core\output\notification::NOTIFY_SUCCESS);

} catch (moodle_exception $e) {
    redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
}