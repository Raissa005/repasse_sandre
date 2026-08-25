# Ambiente, configuração e instalação

## Requisitos

- PHP `>= 8.1` (ver `platform_check.php`, gerado pelo Composer).
- MySQL/MariaDB.
- Composer.

## Instalação local (resumo do `README.md`)

1. `composer install` (ou `composer i`) na raiz — instala dependências
   declaradas em `composer.json` (autoload PSR-4 `RR\ → src/`).
2. Copiar `src/config/config.example.php` para `src/config/config.php` e ajustar
   as constantes de banco (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
   **`src/config/config.php` não deve ser versionado** (contém credenciais) —
   confirmar que está no `.gitignore` antes de qualquer commit que o inclua.
3. Importar `db/realize_repasse.sql` no banco (schema base — mas ver o alerta em
   `docs/07-banco-de-dados.md` sobre módulos não cobertos por esse dump, ex.
   veículos).
4. Apontar o vhost para `public/` (ou deixar o `.htaccess` da raiz redirecionar).

## Constantes principais (`src/config/config.php`)

| Constante | Papel |
|---|---|
| `ENVIRONMENT` | `development` mostra erros PHP e usa `PDO::ERRMODE_WARNING`; qualquer outro valor assume produção (`ERRMODE_EXCEPTION`, sem exibir erro). **Nunca deixar `development` ativo em produção.** |
| `REAL_ESTATE` | flag que indica o perfil de negócio ativo da instalação (ver `docs/01-visao-geral.md`) |
| `TOKEN` | usado para derivar `$this->token` em `Controller`/`Model`/`Ajax` (`preg_replace('/[0-9\W\s]/', '', TOKEN)`) — não é o segredo do JWT (ver `JWTWrapper.php` para isso) |
| `URL`, `URL_DOMAIN`, `URL_SUB_FOLDER`, `URL_PUBLIC_FOLDER`, `URL_PROTOCOL` | auto-detectadas a partir de `$_SERVER`; base de toda URL gerada no sistema (`URL . 'rota'`) |
| `DB_TYPE`/`DB_HOST`/`DB_NAME`/`DB_USER`/`DB_PASS`/`DB_CHARSET` | conexão PDO (ver `Model::openDatabaseConnection`) |

## Reescrita de URL

- `public/.htaccess` — reescreve tudo para `index.php?url=$1` (ver
  `docs/02-arquitetura-fluxo.md` para o parse dessa URL).
- `.htaccess` da raiz — só existe como fallback, redireciona para `/public` caso
  o vhost aponte para a raiz do projeto em vez de `public/`.

## Dependências Composer relevantes

`smottt/wideimage` (redimensionamento de imagem, vendorizado também via cópia em
`src/libs/wideImage`), `phpmailer/phpmailer` (e-mail), `phenx/php-font-lib` +
`phenx/php-svg-lib` (suporte do dompdf), `firebase/php-jwt` (recuperação de
senha), `symfony/cache` (cache de menu, `FilesystemAdapter`), `imagine/imagine`
(processamento de imagem). Dev-only: `almasaeed2010/adminlte` (tema AdminLTE 2).

`src/libs/dompdf/` é uma **cópia vendorizada** do dompdf dentro do próprio
`src/libs` (não gerenciada pelo Composer raiz — tem seu próprio `composer.json`
interno, mas não é isso que é carregado). Não atualizar via `composer require` —
se precisar atualizar essa lib, é uma troca manual dos arquivos, e vale alinhar
com o usuário antes por ser uma dependência grande e vendorizada.

## Sem testes automatizados / CI

Não há suíte de testes, linter configurado ou pipeline de CI neste projeto no
momento. Validação de mudança é manual (rodar localmente, checar a tela no
navegador). Ao implementar algo, testar o fluxo manualmente sempre que possível
antes de reportar como concluído — não presumir que "não quebrou" sem checar.
