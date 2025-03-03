<?php
/**
 * Arquivo de tradução para o idioma Português do Brasil (pt_br).
 *
 * @package   local_notificacoes
 * @author    TecheEduconnect.com.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Nome do plugin
$string['pluginname'] = 'Notificações Automáticas';

// Configurações do plugin
$string['settings_header'] = 'Configurações das Notificações';
$string['settings_description'] = 'Configure as categorias de cursos monitoradas e os intervalos de envio de notificações.';
$string['enable_notifications'] = 'Ativar notificações';
$string['enable_notifications_desc'] = 'Ativar ou desativar todas as notificações enviadas pelo plugin.';
$string['course_categories'] = 'Categorias de cursos';
$string['course_categories_desc'] = 'Informe as IDs das categorias separadas por vírgula para monitoramento.';
$string['admin_email'] = 'E-mail do Administrador';
$string['admin_email_desc'] = 'E-mail usado como remetente das notificações.';

// Notificações para alunos
$string['subject_student_enrollment'] = '📚 Matrícula Confirmada no curso: {$a->coursename}';
$string['email_student_enrollment'] = 'Olá {$a->fullname},

Você foi matriculado no curso <b>{$a->coursename}</b>.

🔗 <b>Acesse o curso:</b> <a href="{$a->siteurl}">{$a->siteurl}</a>  
👤 <b>Seu login:</b> {$a->username}  

Bons estudos! 🚀';

// Notificações de lembrete aos alunos
$string['subject_student_reminder'] = '⏳ Seu curso {$a->coursename} começa em breve!';
$string['email_student_reminder'] = 'Olá {$a->fullname},

O curso <b>{$a->coursename}</b> começará em breve!

🔗 <b>Acesse o curso:</b> <a href="{$a->siteurl}">{$a->siteurl}</a>  
📆 <b>Data de início:</b> {$a->coursestartdate}  

Não perca essa oportunidade de aprendizado! 🎯';

// Notificações para professores sobre postagens não respondidas
$string['subject_teacher_notification'] = '🕒 Responda ao aluno no fórum do curso {$a->coursename}';
$string['email_teacher_notification'] = 'Olá {$a->teachername},

Um aluno fez uma postagem no fórum do curso <b>{$a->coursename}</b> há mais de 24 horas e ainda não recebeu uma resposta.

📍 <b>Fórum:</b> <a href="{$a->siteurl}">Acesse o tópico</a>  

A participação ativa dos professores melhora o aprendizado dos alunos. Contamos com você! 👍';

// Mensagens de log
$string['log_enrollment_sent'] = 'Notificação de matrícula enviada para {$a->userid} no curso {$a->courseid}.';
$string['log_reminder_sent'] = 'Lembrete enviado para {$a->userid} no curso {$a->courseid}.';
$string['log_teacher_notified'] = 'Notificação de interação pendente enviada para o professor {$a->userid} no curso {$a->courseid}.';

// Mensagens de erro
$string['error_no_categories'] = 'Nenhuma categoria foi configurada para notificações.';
$string['error_no_enrollments'] = 'Nenhuma matrícula nova encontrada para envio de notificações.';
$string['error_no_teachers'] = 'Nenhum professor encontrado para notificação de postagens não respondidas.';
$string['error_no_reminders'] = 'Nenhum lembrete necessário no momento.';
$string['error_email_send'] = 'Erro ao enviar notificação para {$a->email}.';

// Mensagens de sucesso
$string['success_notification_sent'] = 'Notificação enviada com sucesso!';
$string['success_configuration_saved'] = 'Configurações salvas com sucesso!';
