# Ambiente, configuração e instalação

## Requisitos

- PHP `>= 7.4` — é o piso real gerado em `vendor/composer/platform_check.php`,
  vindo do `composer.lock` (o mais restritivo entre os pacotes travados é
  `phenx/php-svg-lib`, que pede `^7.4 || ^8.0`; o próprio `README.md` do
  projeto também diz "PHP 7"). Não há sintaxe exclusiva de PHP 8 em `src/`
  (sem `?->`, `enum`, `readonly`, `match`) — confirmado por busca em
  2026-09-25. O ambiente local costuma rodar PHP 8.2 (XAMPP), mas isso é só
  o que está instalado na máquina do dev, não uma exigência do código.
- MySQL/MariaDB.
- Composer.

## Instalação local (resumo do `README.md`)

1. `composer install` (ou `composer i`) na raiz — instala dependências
   declaradas em `composer.json` (autoload PSR-4 `RR\ → src/`).
2. Copiar `src/config/config.example.php` para `src/config/config.php` e ajustar
   as constantes de banco (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
   **`src/config/config.php` não deve ser versionado** (contém credenciais) —
   confirmar que está no `.gitignore` antes de qualquer commit que o inclua.
3. **Não usar `db/realize_repasse.sql`** para criar o banco — esse dump está
   obsoleto (schema antigo, de quando o sistema era só imobiliário; ver alerta
   em `docs/07-banco-de-dados.md`). Confirmado com o usuário em 2026-09-03: o
   banco de qualquer ambiente novo (produção incluída) deve ser criado a
   partir da **estrutura do banco de desenvolvimento local atual** (export/dump
   gerado a partir dele, não do arquivo antigo), com as migrations de
   `db/migrations/` já aplicadas.
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

`smottt/wideimage` (só usado pelo `FileUploader::uploadImg*`, sem chamador; a cópia
local `src/libs/wideImage` foi removida no N12 e o pacote sai no pós-lançamento),
`phpmailer/phpmailer` (e-mail), `dompdf/dompdf` (PDF do cartão digital; traz
`dompdf/php-svg-lib`/`php-font-lib`), `firebase/php-jwt` (recuperação de
senha), `symfony/cache` (cache de menu, `FilesystemAdapter`), `imagine/imagine`
(processamento de imagem; fixado na release 1.5.4 com `^1.5.4@stable`). Dev-only:
`almasaeed2010/adminlte` (tema AdminLTE 2).

O `composer.json` tem `"minimum-stability": "dev"`: sem uma flag de estabilidade
no pacote, o composer pode instalar um branch de desenvolvimento em vez de uma
release. Para fixar um pacote em release estável sem mudar isso, usar
`"pacote": "^X.Y@stable"` (como no Imagine).

O dompdf vem do Composer (`dompdf/dompdf` ^3.1.6, N18 em 2026-10-02); a antiga cópia
vendorizada `src/libs/dompdf/` foi removida. Em deploy, rodar `composer install`.
O cache de fontes dele fica em `storage/dompdf-fonts/` (fora de `public/`, conteúdo
ignorado pelo git) e precisa ser gravável pelo usuário do Apache — no XAMPP do Mac,
`daemon`: `chmod 777 storage/dompdf-fonts` (e os arquivos que já estiverem nela).

## Sem testes automatizados / CI

Não há suíte de testes, linter configurado ou pipeline de CI neste projeto no
momento. Validação de mudança é manual (rodar localmente, checar a tela no
navegador). Ao implementar algo, testar o fluxo manualmente sempre que possível
antes de reportar como concluído — não presumir que "não quebrou" sem checar.
