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

class settings_updated extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('event_settings_updated', 'local_notificacoes');
    }

    public function get_description() {
        return "User {$this->userid} updated notification settings";
    }
}