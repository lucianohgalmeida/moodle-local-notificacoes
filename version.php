<?php
/**
 * Instalação do banco de dados para o plugin Notificações Automáticas.
 *
 * @package   local_notificacoes
 * @author    TechEduConnect
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_notificacoes'; // Deve ser 'local_notificacoes' e não 'notificacoes'
$plugin->version   = 2023102301;                // Versão atual (AAAAMMDDXX - Ano/Mês/Dia/Sequência)
$plugin->requires  = 2023041800;                // Requer Moodle 4.2 ou superior (versão mínima)
$plugin->maturity  = MATURITY_STABLE;           // Estabilidade: Estável
$plugin->release   = '1.2.0';                   // Versão semântica (Major.Minor.Patch)
$plugin->cron      = 0;                         // Intervalo de execução padrão (0 = usa sistema de tarefas agendadas)
$plugin->supported = [402, 404];                // Compatibilidade com versões do Moodle (4.2 a 4.4)
$plugin->dependencies = [                       // Dependências opcionais
    'mod_forum' => 2023041800                   // Versão mínima do módulo Fórum
];