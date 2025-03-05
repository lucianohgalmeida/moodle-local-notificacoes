<?php
/**
 * Instalação do banco de dados para o plugin Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TechEduConnect
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_notificacoes', 
        get_string('pluginname', 'local_notificacoes'),
        'local/notificacoes:manage'
    );

    // ==================== CONFIGURAÇÕES GERAIS ==================== //
    $settings->add(new admin_setting_heading(
        'local_notificacoes/general_heading',
        get_string('generalsettings', 'local_notificacoes'),
        get_string('generalsettings_desc', 'local_notificacoes')
    ));

    // Estado do plugin
    $settings->add(new admin_setting_configcheckbox(
        'local_notificacoes/enableplugin',
        get_string('enableplugin', 'local_notificacoes'),
        get_string('enableplugin_desc', 'local_notificacoes'),
        1
    ));

    // ==================== CONFIGURAÇÕES DE CATEGORIAS ==================== //
    $settings->add(new admin_setting_heading(
        'local_notificacoes/categories_heading',
        get_string('categoriesettings', 'local_notificacoes'),
        get_string('categoriesettings_desc', 'local_notificacoes')
    ));

    // Seletor de categorias
    $categories = core_course_category::make_categories_list();
    $settings->add(new admin_setting_configmultiselect(
        'local_notificacoes/course_categories',
        get_string('coursecategories', 'local_notificacoes'),
        get_string('coursecategories_desc', 'local_notificacoes'),
        [],
        $categories
    ));

    // ==================== CONFIGURAÇÕES DE TEMPO ==================== //
    $settings->add(new admin_setting_heading(
        'local_notificacoes/time_heading',
        get_string('timesettings', 'local_notificacoes'),
        get_string('timesettings_desc', 'local_notificacoes')
    ));

    // Lembrete para alunos
    $settings->add(new admin_setting_configduration(
        'local_notificacoes/student_reminder',
        get_string('studentreminder', 'local_notificacoes'),
        get_string('studentreminder_desc', 'local_notificacoes'),
        72 * 3600, // 72 horas em segundos
        HOURSECS // Unidade de incremento
    ));

    // Alerta para professores
    $settings->add(new admin_setting_configduration(
        'local_notificacoes/teacher_alert',
        get_string('teacheralert', 'local_notificacoes'),
        get_string('teacheralert_desc', 'local_notificacoes'),
        24 * 3600, // 24 horas em segundos
        HOURSECS
    ));

    // ==================== CONFIGURAÇÕES DE NOTIFICAÇÃO ==================== //
    $settings->add(new admin_setting_heading(
        'local_notificacoes/notifications_heading',
        get_string('notificationsettings', 'local_notificacoes'),
        get_string('notificationsettings_desc', 'local_notificacoes')
    ));

    // Configurações de canais
    $settings->add(new admin_setting_configmulticheckbox(
        'local_notificacoes/notification_channels',
        get_string('notificationchannels', 'local_notificacoes'),
        get_string('notificationchannels_desc', 'local_notificacoes'),
        ['email', 'messaging'], // Valores padrão
        [
            'email' => get_string('channel_email', 'local_notificacoes'),
            'messaging' => get_string('channel_messaging', 'local_notificacoes'),
            'sms' => get_string('channel_sms', 'local_notificacoes')
        ]
    ));

    // Remetente de e-mail
    $settings->add(new admin_setting_configtext(
        'local_notificacoes/email_sender',
        get_string('emailsender', 'local_notificacoes'),
        get_string('emailsender_desc', 'local_notificacoes'),
        $CFG->noreplyaddress,
        PARAM_EMAIL
    ));

    // ==================== TEMPLATE DE E-MAIL ==================== //

    // Definir o valor padrão corretamente
    $defaulttemplate = get_string('default_email_template', 'local_notificacoes', 'Prezado(a) {fullname},

Você tem uma nova notificação sobre o curso "{coursename}".

Acesse sua área do aluno para mais detalhes.

Atenciosamente,
Equipe Acadêmica.');

    // Adicionar o editor HTML para o template de e-mail
    $settings->add(new admin_setting_confightmleditor(
        'local_notificacoes/email_template',
        get_string('emailtemplate', 'local_notificacoes'),
        get_string('emailtemplate_desc', 'local_notificacoes'),
        $defaulttemplate
    ));

    $ADMIN->add('localplugins', $settings);
}