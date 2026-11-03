# Publicação na Valuehost / DirectAdmin

## Requisitos
- PHP 8.1 ou superior, com extensões PDO, pdo_mysql e mbstring;
- Apache com mod_rewrite habilitado;
- MySQL ou MariaDB;
- HTTPS ativo.

## Preparação
1. Crie no DirectAdmin um banco MySQL e um usuário com permissões apenas sobre este banco.
2. Importe `database/schema.sql` no phpMyAdmin.
3. Copie `.env.example` como `.env` e preencha as variáveis `DB_*` e os dados oficiais `APP_*`. **Nunca envie o .env ao GitHub.**
4. Coloque os arquivos do projeto no `public_html` do domínio. A pasta `app` e o `.env` são bloqueados pelo `.htaccess`. Verifique com HTTPS que `/.env`, `/database/schema.sql` e `/app/bootstrap.php` retornam acesso negado. **Preferência para produção:** manter `.env`, `app` e `database` fora do diretório público e adaptar os caminhos.
5. Confirme que `AllowOverride` está habilitado para o `.htaccess` e que URLs como `/planos` e `/contato` carregam.
6. Teste formulário, validações, gravação no banco e página 404.

## Cuidados antes de divulgar
- Validar identidade visual, marca, telefone, e-mail, CNPJ e endereço;
- Revisar juridicamente política de privacidade e estabelecer retenção dos contatos;
- Substituir as descrições provisórias dos planos por preços e coberturas oficiais;
- Configurar um processo interno seguro para consultar e responder mensagens no banco (nenhum painel administrativo foi publicado);
- Adotar backup automático e proteção anti-spam adicional conforme o volume.
- Se for usar subdiretório em vez da raiz do domínio, adaptar rotas e caminhos absolutos.

## Rotas
- `/` Início
- `/quem-somos` Quem somos
- `/planos` Planos
- `/contato` Contato
- `/politica-de-privacidade` Privacidade

A estrutura foi inspirada nas páginas institucionais do site de referência, sem copiar sua marca, dados, preços ou conteúdo contratual. Não há páginas de unidades ou parceiros.
