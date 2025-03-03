📢 Plugin Moodle: Notificações Automáticas (local_notificacoes)
📌 Objetivo
Implementar um sistema automatizado de notificações para melhorar a comunicação entre alunos, professores e administradores dentro do Moodle.
✅ Notificações Implementadas
Alunos: Confirmação de matrícula imediata e lembrete antes do início do curso.
Professores: Lembrete para responder alunos em fóruns dentro de 24 horas.
Administração: Configuração personalizada dos templates de notificação diretamente pelo painel do Moodle.

⚙️ Funcionalidades
🔹 1. Notificação Automática ao Aluno quando Matriculado
Utiliza o evento do Moodle user_enrolled para disparo imediato.
Envia notificações via e-mail e mensagens internas do Moodle (message_send()).
Segue o padrão SMTP configurado no Moodle.
Mensagem pode ser personalizada via Admin Settings.
🔹 2. Lembrete ao Aluno 72 Horas Antes do Início do Curso
Baseado na configuração "Data de Início do Curso" (mdl_course.startdate).
Envio automático para alunos matriculados.
Notificação via e-mail e mensagens internas do Moodle.
Mensagem customizável pelo administrador.
🔹 3. Notificação ao Professor sobre Fórum Não Respondido
Monitora postagens em fóruns (mdl_forum_posts).
Se um aluno postar e não houver resposta de um professor/tutor em 24 horas, uma notificação será enviada.
Personalizável via Admin Settings.
Envio via e-mail e mensagens internas.
🔹 4. Configuração via Administração do Site
Interface no Admin Settings para customização dos templates das mensagens.
Definição de categorias do curso que ativarão o sistema.
Personalização de intervalos de tempo (ex: 72h antes, 24h para professores).
Configuração do remetente das notificações seguindo o padrão SMTP do Moodle.
Opção de ativar/desativar notificações específicas.
🔹 5. Logs e Auditoria
Registro detalhado das notificações enviadas.
Nova coluna status na tabela de logs para indicar:
enviado
erro
reenviado
Permite rastrear falhas e sucesso no envio.

🏗️ Estrutura do Plugin
/local/notificacoes
│── db/
│   ├── install.php  # Definição do banco de dados
│   ├── access.php   # Permissões e capacidades do plugin
│
│── classes/
│   ├── task/
│   │   ├── notify_students.php  # Lembretes para alunos
│   │   ├── notify_teachers.php  # Notificações para professores
│   ├── manager.php   # Classe de gerenciamento de notificações
│
│── lang/
│   ├── en/local_notificacoes.php  # Tradução para inglês
│   ├── pt_br/local_notificacoes.php  # Tradução para português
│
│── lib.php  # Funções principais do plugin
│── settings.php  # Configuração via administração do Moodle
│── version.php  # Versão do plugin e dependências
│── cron.php  # Entrada manual para testes de execução do CRON
│── index.php  # Interface de gerenciamento do plugin
│── README.md  # Documentação do plugin

🛢️ Banco de Dados
O plugin utilizará tabelas nativas do Moodle e uma tabela própria para controle de logs de notificações.
🔹 Tabelas Utilizadas
Tabela
Uso
mdl_user_enrolments
Verifica novas matrículas
mdl_course
Obtém a data de início do curso
mdl_forum_posts
Monitora interações de professores e alunos
mdl_user
Obtém detalhes dos usuários

🔹 Nova Tabela (mdl_local_notificacoes_log)
CREATE TABLE {local_notificacoes_log} (
    id BIGINT(10) NOT NULL AUTO_INCREMENT,
    userid BIGINT(10) NOT NULL,
    courseid BIGINT(10) NOT NULL,
    notificationtype VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'enviado',
    timecreated BIGINT(10) NOT NULL,
    PRIMARY KEY (id)
);

🕒 Tarefas Agendadas (CRON)
🔹 1. notify_students.php
Frequência: A cada 6 horas.
Ação: Verifica alunos que precisam do lembrete de 72 horas.
🔹 2. notify_teachers.php
Frequência: A cada 6 horas.
Ação: Verifica se um professor respondeu ao aluno em fóruns.

✉️ Exemplo de Notificações
🔹 1. Notificação ao Aluno (Matrícula)
📩 Assunto: 📚 Você foi matriculado no curso: {NOME_DO_CURSO}
🔹 Olá {NOME_DO_ALUNO}, Você foi matriculado no curso {NOME_DO_CURSO}. 🔗 Acesse o curso: [URL_DO_CURSO] 👤 Seu login: {USERNAME} 🔑 Senha inicial: {SENHA_INICIAL} 🎯 Bons estudos!
🔹 2. Lembrete ao Aluno (72h antes do curso)
📩 Assunto: ⏳ Seu curso começa em breve!
🔹 Olá {NOME_DO_ALUNO}, O curso {NOME_DO_CURSO} começará em 72 horas! 🔗 Acesse o curso: [URL_DO_CURSO] 👤 Seu login: {USERNAME} 📆 Não perca o prazo! Nos vemos no curso! 🚀
🔹 3. Notificação ao Professor (Fórum - 24h sem resposta)
📩 Assunto: 🕒 Responda ao aluno no fórum do curso!
🔹 Olá {NOME_DO_PROFESSOR}, Um aluno postou no fórum do curso {NOME_DO_CURSO} há mais de 24 horas, e ainda não recebeu resposta. 📍 Tópico: {TITULO_DO_TOPICO} 💬 Postagem do aluno: "{TRECHO_DA_POSTAGEM}" 🔗 Acesse o fórum: [URL_DO_TOPICO] 📝 Responder a tempo mantém o engajamento dos alunos!

🚀 Instalação
Faça upload do diretório notificacoes para /local/ no seu Moodle.
Acesse Administração do Site > Notificações e conclua a instalação.
Configure as opções em Administração > Plugins > Notificações Automáticas.
Certifique-se de que o CRON do Moodle está ativo para a execução automática das tarefas.

🎯 Justificativa
✅ Melhora a comunicação entre a instituição e os alunos/professores. ✅ Reduz trabalho manual da equipe de suporte. ✅ Garante que prazos e interações acadêmicas sejam cumpridos. ✅ Centraliza logs e auditoria para rastreamento eficiente das notificações.

📩 Resumo das Notificações Enviadas
📌 Tipo de Notificação
🕒 Quando é Enviada?
📬 Quem Recebe?
Confirmação de Matrícula
No momento da inscrição
Aluno
Lembrete antes do curso
72 horas antes do início do curso
Aluno
Lembrete de Fórum (24h)
Se professor não responder um post em 24h
Professor


