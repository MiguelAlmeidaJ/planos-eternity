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
