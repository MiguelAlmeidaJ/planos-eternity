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
