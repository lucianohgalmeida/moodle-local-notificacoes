<?php
/**
 * Configurações do plugin local_notificacoes.
 *
 * @package   local_notificacoes
 * @author    
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_notificacoes', get_string('pluginname', 'local_notificacoes'));

    // Seção de Configurações Gerais.
    $settings->add(new admin_setting_heading(
        'local_notificacoes/generalsettings',
        get_string('generalsettings', 'local_notificacoes'),
        ''
    ));

    // Configuração para selecionar categorias monitoradas.
    $settings->add(new admin_setting_configtext(
        'local_notificacoes/course_categories',
        get_string('coursecategories', 'local_notificacoes'),
        get_string('coursecategories_desc', 'local_notificacoes'),
        '',
        PARAM_TEXT
    ));

    // Configuração para definir o intervalo de lembrete para alunos antes do início do curso (padrão: 72h).
    $settings->add(new admin_setting_configtext(
        'local_notificacoes/student_reminder_hours',
        get_string('studentreminderhours', 'local_notificacoes'),
        get_string('studentreminderhours_desc', 'local_notificacoes'),
        72,
        PARAM_INT
    ));

    // Configuração para definir o tempo limite para professores responderem posts (padrão: 24h).
    $settings->add(new admin_setting_configtext(
        'local_notificacoes/teacher_response_hours',
        get_string('teacherresponsehours', 'local_notificacoes'),
        get_string('teacherresponsehours_desc', 'local_notificacoes'),
        24,
        PARAM_INT
    ));

    // Configuração para ativar/desativar notificações para alunos.
    $settings->add(new admin_setting_configcheckbox(
        'local_notificacoes/enable_student_notifications',
        get_string('enablestudentnotifications', 'local_notificacoes'),
        '',
        1
    ));

    // Configuração para ativar/desativar notificações para professores.
    $settings->add(new admin_setting_configcheckbox(
        'local_notificacoes/enable_teacher_notifications',
        get_string('enableteachernotifications', 'local_notificacoes'),
        '',
        1
    ));

    // Configuração do remetente do e-mail.
    $settings->add(new admin_setting_configtext(
        'local_notificacoes/email_sender',
        get_string('emailsender', 'local_notificacoes'),
        get_string('emailsender_desc', 'local_notificacoes'),
        'admin@seudominio.com',
        PARAM_EMAIL
    ));

    $ADMIN->add('localplugins', $settings);
}
