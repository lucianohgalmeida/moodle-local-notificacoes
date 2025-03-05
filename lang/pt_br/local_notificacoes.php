<?php
// Arquivo de tradução em Português Brasileiro
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Notificações Automáticas';

// Configurações Gerais
$string['generalsettings'] = 'Configurações Gerais';
$string['generalsettings_desc'] = 'Configurações básicas de operação do plugin';
$string['enableplugin'] = 'Ativar Plugin';
$string['enableplugin_desc'] = 'Habilita/desabilita todo o sistema de notificações';

// Categorias
$string['categoriesettings'] = 'Configurações de Categorias';
$string['categoriesettings_desc'] = 'Selecione as categorias que serão monitoradas';
$string['coursecategories'] = 'Categorias de Cursos';
$string['coursecategories_desc'] = 'Selecione as categorias de cursos para monitoramento';

// Tempo
$string['timesettings'] = 'Configurações de Tempo';
$string['timesettings_desc'] = 'Intervalos para disparo de notificações';
$string['studentreminder'] = 'Lembrete para Alunos';
$string['studentreminder_desc'] = 'Tempo antes do início do curso para enviar lembrete';
$string['teacheralert'] = 'Alerta para Professores';
$string['teacheralert_desc'] = 'Tempo máximo sem resposta nos fóruns';

// Notificações
$string['notificationsettings'] = 'Configurações de Notificação';
$string['notificationsettings_desc'] = 'Preferências de entrega de notificações';
$string['notificationchannels'] = 'Canais de Notificação';
$string['notificationchannels_desc'] = 'Selecione os canais de comunicação a serem utilizados';
$string['channel_email'] = 'E-mail';
$string['channel_messaging'] = 'Mensagens Internas';
$string['channel_sms'] = 'SMS';

// E-mail
$string['emailsender'] = 'Remetente de E-mail';
$string['emailsender_desc'] = 'Endereço de e-mail que aparecerá como remetente';
$string['emailtemplate'] = 'Template de E-mail';
$string['emailtemplate_desc'] = 'Marcadores de posição: {firstname} → Primeiro nome do usuário
{lastname} → Sobrenome do usuário
{coursename} → Nome do curso
{courseurl} → Link para acessar o curso
{date} → Data formatada
{username} → Nome de usuário do Moodle';
$string['default_email_template'] = 'Prezado(a) {fullname},

Você tem uma nova notificação sobre o curso "{coursename}".

Acesse sua área do aluno para mais detalhes.

Atenciosamente,
Equipe Acadêmica.';

// Matricula
$string['messageprovider:matricula_confirmada'] = 'Confirmação de matrícula';


// Notificações para Alunos
$string['subject_enrollment'] = '📚 Matrícula Confirmada no Curso: {$a}';
$string['email_student_enrollment'] = 'Olá {fullname},<br><br>
Você foi matriculado no curso <b>{coursename}</b>.<br><br>
🔗 <b>Acesse o curso:</b> <a href="{courseurl}">{courseurl}</a><br>
👤 <b>Seu login:</b> {username}<br><br>
Bons estudos! 🚀';

$string['subject_reminder'] = '⏳ Seu Curso {$a} Começa em Breve!';
$string['email_student_reminder'] = 'Olá {fullname},<br><br>
O curso <b>{coursename}</b> começará em {startdate}!<br><br>
🔗 <b>Acesse o curso:</b> <a href="{courseurl}">{courseurl}</a><br><br>
Não perca o início! 📆';

// Notificações para Professores
$string['subject_teacher_alert'] = '🕒 Postagem Não Respondida em {$a}';
$string['email_teacher_notification'] = 'Olá {teachername},<br><br>
Há uma postagem não respondida no fórum do curso <b>{coursename}</b>:<br><br>
💬 "{postcontent}"<br><br>
🔗 <a href="{forumurl}">Acessar o fórum</a><br><br>
Por favor, responda dentro de 24 horas. 👍';

// Logs e Estatísticas
$string['total_notifications'] = 'Total de Notificações';
$string['last_week'] = 'Últimos 7 Dias';
$string['last_execution'] = 'Última Execução';
$string['execution_duration'] = 'Duração da Execução';

// Erros
$string['invalid_request'] = 'Requisição Inválida';
$string['nocategories'] = 'Selecione pelo menos uma categoria';
$string['invalid_hours'] = 'Horas devem ser maiores que zero';
$string['error_email_send'] = 'Erro ao enviar para {email}';

// Sucesso
$string['settings_saved'] = 'Configurações salvas com sucesso!';
$string['cron_task_executed'] = 'Tarefa {$a} executada';

// Cron
$string['cron_execution'] = 'Execução Manual do Cron';
$string['cron_report'] = 'Relatório de Execução';
$string['system_load'] = 'Carga do Sistema';
$string['status_success'] = 'Sucesso';
$string['status_error'] = 'Erro';

// Eventos
$string['event_settings_updated'] = 'Configurações Atualizadas';
$string['event_manual_cron_executed'] = 'Cron Manual Executado';