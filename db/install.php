<?php
/**
 * Instalação do banco de dados para o plugin Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TecheEduconnect.com.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Executa ações ao instalar o plugin.
 */
function xmldb_local_notificacoes_install() {
    global $DB;

    $manager = $DB->get_manager();
    $table = new xmldb_table('local_notificacoes_log');

    // 1. Definição dos Campos
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'ID do usuário');
    $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'ID do curso');
    $table->add_field('notificationtype', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'Tipo de notificação (matrícula/lembrete/forum)');
    $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'enviado', 'Status: enviado|erro|reenviado');
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'Data de criação');
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'Data de modificação');

    // 2. Definição de Chaves
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
    $table->add_key('courseid_fk', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);

    // 3. Índices para Otimização
    $table->add_index('status_idx', XMLDB_INDEX_NOTUNIQUE, ['status']);
    $table->add_index('timecreated_idx', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);
    $table->add_index('user_course_idx', XMLDB_INDEX_NOTUNIQUE, ['userid', 'courseid']);

    // 4. Criação da Tabela (se não existir)
    if (!$manager->table_exists($table)) {
        $manager->create_table($table);
    }

    // 5. Registro de Eventos (Opcional - Recomendado para observers)
    $eventHandler = core_plugin_manager::instance()->get_plugin_info('local_notificacoes');
    $eventHandler->add_event_handler('\core\event\user_enrolment_created', 'local_notificacoes_observer::user_enrolled');
}