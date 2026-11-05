# Instalação e banco de dados (Valuehost / DirectAdmin)

Requisitos: PHP 8.1+ com PDO, pdo_mysql e mbstring; MySQL/MariaDB; Apache com mod_rewrite.

## Configurar localmente

1. Crie um banco MySQL e um usuário no DirectAdmin (em hospedagens compartilhadas, o prefixo do usuário pode ser obrigatório).
2. No diretório do projeto, crie sua configuração local:
   ```bash
   cp config.example.php config.php
   ```
3. Edite `config.php` e informe `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASS`. Não envie esse arquivo ao GitHub.
4. No terminal do diretório do projeto, rode:
   ```bash
   php scripts/install.php
   ```
   O comando conecta ao banco e aplica `database/schema.sql`. A execução inicial é idempotente por usar `CREATE TABLE IF NOT EXISTS`. Ele **não cria o banco**, apenas a tabela dentro de um banco existente.
5. Para testar o site localmente, rode `php -S localhost:8000 index.php`. Atenção: o servidor embutido do PHP não interpreta as regras de reescrita do Apache; algumas rotas podem precisar ser testadas no Apache.

## Publicação

- Em `public_html` ficam os arquivos públicos; o projeto atual protege `config.php`, `.env`, `app/`, `database/`, `docs/` e `scripts/` por regras Apache no `.htaccess`.
- **Recomendado para produção:** mover `config.php`, `app/`, `database/` e `scripts/` para fora de `public_html` e ajustar os caminhos no código, em vez de depender apenas do `.htaccess`.
- Confira que `AllowOverride` e `mod_rewrite` estão ativos; HTTPS é obrigatório para produção.
- Valide que acessar `/config.php`, `/database/schema.sql` e `/scripts/install.php` pela web retorna 403.
- Mantenha backup do banco; valide dados oficiais e política LGPD antes de publicar.
- Não existe painel de administração nesta versão.

## Compatibilidade

O arquivo `.env` ainda é aceito para compatibilidade com instalações existentes; valores em `config.php`, quando presente, têm precedência.

## Páginas

Início (`/`), Quem somos (`/quem-somos`), Planos (`/planos`), Contato (`/contato`) e Privacidade (`/politica-de-privacidade`). Sem páginas de unidades ou parceiros.

## Painel administrativo

Após `git pull origin main`, execute `php scripts/install.php` novamente. O comando é idempotente, cria as tabelas adicionais (`admin_users`, `site_plans`, `site_settings`) e cadastra os três planos iniciais apenas se ainda não existirem.

Crie o primeiro administrador em um **terminal interativo**, sem digitar a senha como argumento:
```bash
php scripts/create-admin.php
```
Depois acesse `https://seu-dominio/admin`.

Em `config.php`, configure também `APP_KEY` com um segredo aleatório de 32 bytes codificados em base64. Para gerar:
```bash
php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```
Guarde esta chave com backup seguro: a senha SMTP é criptografada com AES-256-GCM, e trocar a chave impede sua leitura futura.

- **Usuários:** administradores gerenciam usuários, planos e configurações; editores gerenciam somente planos. Senhas são armazenadas como hashes.
- **Planos:** CRUD com nome, slug, benefícios (uma linha por benefício), ordem, visibilidade, destaque e texto do preço; a área pública usa os planos ativos do banco.
- **Identidade visual:** nome, subtítulo, cores e **caminho de imagem local** para logotipo. Coloque o arquivo no diretório `assets/` previamente (não há upload de arquivos nesta etapa).
- **Contato:** informações públicas aplicadas ao cabeçalho, rodapé e página de contato.
- **SMTP:** endereço, porta, usuário, remetente, criptografia e senha cifrada. Esta fase **apenas armazena a configuração**; o disparo de e-mails e testes SMTP ainda não foram implementados.

Recomendações: use HTTPS, credenciais fortes, backups regulares, restrinja o acesso a `/admin` e considere uma proteção adicional contra força bruta no servidor (a tela implementa limitação de tentativas por sessão).


## Bootstrap e catálogo (etapa atual)

- A página pública de planos não usa mais hero e começa diretamente pelo catálogo.
- O painel em `/admin/planos` permite cadastrar, editar, excluir, ativar/desativar e destacar planos. Planos marcados como ativos aparecem na home e na página `/planos`; dados são lidos da tabela `site_plans`.
- O Bootstrap 5.3.8 e Bootstrap Icons 1.13.2 foram incluídos nos layouts público e administrativo via CDN do jsDelivr. **A nova tela de planos do painel já usa componentes Bootstrap**; as outras telas continuam com CSS legado durante uma migração gradual.
- Para ambientes sem acesso à internet, é necessário hospedar os arquivos do Bootstrap e seus ícones localmente.
- Não é necessária migração adicional no MySQL para esta atualização se a tabela `site_plans` já existe.


## Gerenciamento de planos — nova navegação

- Listagem em `/admin/planos`; cadastro em `/admin/planos/novo`; edição em `/admin/planos/editar?id=ID`.
- No catálogo, arraste as linhas pelo ícone ou foque o ícone e use ↑/↓. A nova ordem é enviada por POST autenticado com CSRF e gravada em transação MySQL. Em caso de erro, o navegador recupera a ordem previamente salva.
- Não é preciso informar `sort_order` no formulário. Novos planos entram no final automaticamente.
- Ações de editar, publicar/ocultar e excluir usam apenas ícones Bootstrap com rótulos acessíveis.
- Sem alterações de schema: tabela `site_plans` existente.


## Identidade visual / uploads persistentes

- Em **Administração → Configurações**, envie o logotipo e o favicon em PNG ou SVG (até 2 MB; SVG até 512 KB).
- O servidor armazena os arquivos em `assets/uploads/branding/`. A pasta está no `.gitignore`, portanto **não será atualizada nem removida pelo git pull**, desde que os arquivos sejam mantidos no servidor e não sejam usados comandos de limpeza como `git clean -fdx`.
- Crie a pasta e certifique-se de que o usuário do PHP tem permissão para gravar nela (`mkdir -p assets/uploads/branding`). O sistema também tenta criá-la automaticamente.
- Não versionar nem fazer `git add -f` dos arquivos enviados. Fazer backups regulares de `assets/uploads/` e do banco MySQL; o banco guarda os caminhos das imagens.
- PNG tem assinatura e dimensões verificadas. SVG aceita somente elementos e atributos gráficos de uma lista restrita; SVGs complexos com scripts, imagens externas ou outros recursos não são aceitos por segurança. Extensão PHP DOM é necessária para SVG.
- No momento não há edição avançada, conversão ou redimensionamento automático das imagens. O favicon padrão permanece disponível caso o personalizado seja removido.
- O **slug dos planos** é gerado no servidor a partir do nome (com prévia automática no formulário). Se dois nomes produzirem o mesmo slug, o banco impedirá a duplicação e será preciso usar outro nome.


## Slides da página inicial

Depois de atualizar o projeto, execute `php scripts/install.php` para criar a tabela `home_banners`.
O módulo está em `/admin/banners`. São permitidos até **4 banners** cadastrados, apresentados na ordem de inclusão.
Cada banner requer uma imagem desktop **1600 × 600 px** e uma mobile **1000 × 600 px**, em PNG, JPG ou WebP (até 6 MB cada). O link é opcional e aceita URLs HTTP(S) ou rotas locais como `/planos`.
As imagens são salvas em `assets/uploads/banners/` e **não são versionadas**. Mantenha backup dessa pasta e do banco de dados.
O carousel muda automaticamente a cada 5,5 segundos, pausa durante interação e respeita a preferência por movimento reduzido.
Sem banners cadastrados, a home mantém o hero anterior. Quando houver banner cadastrado, o carrossel substitui o hero.
Verifique `upload_max_filesize`, `post_max_size`, a extensão `fileinfo` e permissões de gravação no PHP do DirectAdmin.


## Teste SMTP e Auditoria

Depois de `git pull origin main`, rode `php scripts/install.php` para criar `admin_audit_logs`.
Em **Configurações → Servidor SMTP**, salve host, porta, usuário, remetente, criptografia e senha antes de testar. O teste envia um e-mail de verdade ao endereço selecionado e não modifica as configurações. Aceita portas SMTP TLS/STARTTLS e SSL implícito; exige certificado válido e utiliza a senha criptografada pela APP_KEY.
A resposta de sucesso significa que o servidor SMTP aceitou a mensagem, **não** garante entrega na caixa postal. O teste tem intervalo mínimo de 30 segundos por sessão; o host deve permitir conexões SMTP de saída.
Em **Administração → Auditoria**, apenas administradores visualizam os últimos eventos, com filtro e paginação. São registrados acessos, alterações de cadastros, banners, configurações e testes SMTP. O histórico começa após a instalação da tabela e não inclui conteúdos de mensagens, senhas ou tokens. Para retenção, estabeleça política de expurgo e backup coerente com a LGPD.
