# Identidade visual Eternity — novembro de 2026

Fonte: Manual_da_Marca_Eternity_Atualizado.pdf, fornecido para este projeto (6 páginas).

## Direção
Confiança, cuidado e continuidade; visual sóbrio, simples e reconhecível. O nome institucional no manual é **Eternity Plano Familiar**.

## Cores
- Azul Eternity — #293451 (principal)
- Azul profundo — #17243E (contraste)
- Branco — #FFFFFF
- Cinza claro — #F4F6F9 (superfícies)
- Dourado suave — #C2A77D (acentos opcionais, não como cor dominante de botão)

## Tipografia
**Lato Bold/Heavy** em títulos e chamadas; **Lato Regular** para textos. A tipografia e geometria do logotipo são arte fixa: não reconstruir seu lettering em HTML quando houver imagem oficial.

## Implementação
- Arquivo `assets/css/eternity-brand.css` aplica a direção ao site público, carregado após o legado.
- `assets/css/eternity-admin-brand.css` aplica ao painel.
- O logotipo cadastrado pelo administrador é exibido sozinho (não duplica o nome à direita). No rodapé escuro ele recebe um suporte branco para manter a versão azul-marinho legível.
- O favicon continua vindo do upload configurado no admin.
- Os uploads persistem em `assets/uploads/branding/` e não são versionados no Git.
- As cores salvas anteriormente em `site_settings` continuam editáveis; o manual define os novos valores recomendados. Evite aplicar filtros ou efeitos sobre o próprio símbolo.
- As fotografias existentes foram preservadas nesta atualização; o manual não fornece uma imagem específica para banner.
