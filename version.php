<?php
/**
 * Declaração da versão do plugin local_notificacoes.
 *
 * @package   local_notificacoes
 * @author    
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_notificacoes'; // Nome do componente do plugin.
$plugin->version   = 2025022000; // Versão do plugin (AAAAMMDDXX).
$plugin->requires  = 2022112803.07; // Requer o Moodle 4.1 ou superior.
$plugin->maturity  = MATURITY_STABLE; // Maturidade do plugin.
$plugin->release   = '1.0.0'; // Versão legível do plugin.
$plugin->cron      = 0; // Tarefas são executadas via sistema de tarefas agendadas.
