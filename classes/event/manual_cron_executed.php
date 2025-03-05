<?php
/**
 * Instalação do banco de dados para o plugin Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TechEduConnect
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_notificacoes\event;

defined('MOODLE_INTERNAL') || die();

class manual_cron_executed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('event_manual_cron_executed', 'local_notificacoes');
    }

    public function get_description() {
        return "Manual cron execution performed by user {$this->userid}";
    }
}