<?php
/**
 * Processa as configurações do plugin local_notificacoes.
 *
 * @package   local_notificacoes
 * @author    TecheEduconnect.com.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

$context = context_system::instance();
$redirecturl = new moodle_url('/local/notificacoes/index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryid = required_param('categoryid', PARAM_INT);
    $time_students = required_param('time_students', PARAM_INT);
    $time_teachers = required_param('time_teachers', PARAM_INT);

    // Salva as configurações no banco de dados
    set_config('categoryid', $categoryid, 'local_notificacoes');
    set_config('time_students', $time_students, 'local_notificacoes');
    set_config('time_teachers', $time_teachers, 'local_notificacoes');

    redirect($redirecturl, get_string('config_saved', 'local_notificacoes'), null, \core\output\notification::NOTIFY_SUCCESS);
} else {
    redirect($redirecturl, get_string('invalid_request', 'local_notificacoes'), null, \core\output\notification::NOTIFY_ERROR);
}
