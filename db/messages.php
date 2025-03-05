<?php
/**
 * Instalação do banco de dados para o plugin Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TechEduConnect
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function local_notificacoes_get_message_providers() {
    return [
        'matricula_confirmada' => [
            'capability'  => 'moodle/site:sendmessage',
            'defaults'    => [
                'popup' => MESSAGE_PERMITTED,
                'email' => MESSAGE_PERMITTED,
            ],
            'configurable' => true,
        ],
    ];
}
