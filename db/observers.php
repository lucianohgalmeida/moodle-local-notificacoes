<?php
/**
 * Observadores de eventos para o plugin Notificações Automáticas.
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
        'priority'    => 200, // Prioridade ajustada para otimizar o processamento
    ],
    [
        'eventname'   => '\mod_forum\event\discussion_created',
        'callback'    => 'local_notificacoes_observer::forum_discussion_created',
        'includefile' => '/local/notificacoes/classes/observer.php',
        'priority'    => 250, // Notificação para professores sobre novas discussões no fórum
    ],
    [
        'eventname'   => '\core\event\course_module_completion_updated',
        'callback'    => 'local_notificacoes_observer::module_completion_updated',
        'includefile' => '/local/notificacoes/classes/observer.php',
        'priority'    => 300, // Notificação para alunos sobre conclusão de atividades
    ],
];
