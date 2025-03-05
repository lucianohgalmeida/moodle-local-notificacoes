<?php
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
