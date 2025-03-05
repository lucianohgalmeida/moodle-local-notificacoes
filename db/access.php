<?php
/**
 * Instalação do banco de dados para o plugin Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TechEduConnect
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/notificacoes:manage' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW
        ],
        'riskbitmask' => RISK_CONFIG // Adicionado risco de configuração
    ],
    'local/notificacoes:viewlogs' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE, // Contexto mais específico
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        ],
        'riskbitmask' => RISK_PERSONAL // Risco de expor dados pessoais
    ],
];
