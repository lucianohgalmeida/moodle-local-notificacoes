<?php
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
