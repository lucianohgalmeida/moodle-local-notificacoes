<?php
/**
 * Instalação do banco de dados para o plugin Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TechEduConnect
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\core\event\user_enrolment_created',
        'callback'    => 'local_notificacoes_observer::user_enrolled',
        'includefile' => '/local/notificacoes/classes/observer.php',
        'internal'    => false,
        'priority'    => 9999,
    ],
];
