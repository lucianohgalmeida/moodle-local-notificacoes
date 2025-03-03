<?php
/**
 * Interface de gerenciamento do plugin local_notificacoes.
 *
 * @package   local_notificacoes
 * @author    
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/local/notificacoes/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_notificacoes'));
$PAGE->set_heading(get_string('pluginname', 'local_notificacoes'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_notificacoes'));

// Formulário de configuração básica
echo '<form method="post" action="process.php">';
echo '<label>' . get_string('config_category', 'local_notificacoes') . '</label>';
echo '<input type="text" name="categoryid" required>';
echo '<br><label>' . get_string('config_time_students', 'local_notificacoes') . '</label>';
echo '<input type="number" name="time_students" value="72" required> horas';
echo '<br><label>' . get_string('config_time_teachers', 'local_notificacoes') . '</label>';
echo '<input type="number" name="time_teachers" value="24" required> horas';
echo '<br><button type="submit">' . get_string('save_settings', 'local_notificacoes') . '</button>';
echo '</form>';

echo $OUTPUT->footer();
