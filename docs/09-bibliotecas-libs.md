# Bibliotecas utilitárias (`src/libs/`)

Todas namespace `RR\libs`, a maioria com métodos estáticos (chamadas tipo
`Util::maskCnpj($valor)`, sem precisar instanciar). **Antes de escrever uma
função utilitária nova (máscara, formatação, data, upload...), procurar aqui
primeiro** — é o ponto nº1 de duplicidade evitável no projeto.

## `Util.php` — utilitário genérico mais usado do projeto

Máscaras/formatação: `maskCpf`, `maskCnpj`, `maskCpfCnpj`, `maskRg`, `maskCep`,
`maskTelefone`, `maskAgency`, `maskAccount`, `maskMoney`, `maskMoneyInt`,
`maskInt`, `maskCurrency($valor, $currencyId)`, `mask($valor, $mascaraGenerica)`.
Inversas/limpeza: `unmaskMoney`, `removeNumberFormatting`,
`removeNonNumericForFloat`, `removeNonNumericCharacters`. Texto:
`titleCase`, `removeAccentuation`, `removeAccentsTransformLowercase`, `slugify`,
`br2nl`, `zeroFill`. Array: `findArrayObjectElement($array, $chave, $valor)`,
`findIntersectionInMatrix`, `compareArray`, `repositionArray`. Outros:
`converte()` (número por extenso, ex. valor em reais escrito), `matheval`
(avalia expressão matemática em string), `coalesce`, `likePHP` (simula `LIKE`
SQL em PHP), `debug($var, $titulo, $die)` (var_dump formatado, usar em vez de
`var_dump`/`print_r` cru ao debugar), `gererateToken($tamanho)`,
`getIp()`, `resizeImageWithCanvas(...)`.

**Sempre usar `Util::maskX`/`Util::removeNonNumericCharacters` para
formatar/limpar CPF, CNPJ, telefone, CEP, dinheiro** em vez de escrever regex
novo — é o padrão usado em todo o projeto (ver `BranchController` usando
`Util::maskCnpj`/`Util::removeNonNumericCharacters`/`Util::unmaskMoney`/
`Util::maskMoney`).

## Data (`Date.php`)

`date`, `day`, `date_full`, `date_hour`, `date_hour_full`, `month_full`,
`month_abreviation`, `hour`, `year_month`, `year`, `month`,
`rangeOfDays($ini, $fim)`, `createArrayDates($ini, $fim)`,
`add_working_days($data, $dias)`, `safeGenNextDueDate($isoDate, $diaAlvo)`.
Usar para toda formatação de data em pt-BR em vez de `date()`/`DateTime` cru.

## Upload/arquivo/imagem

- `FileUploader.php` — API mais nova e genérica: `uploadImg`, `uploadImgSingle`,
  `uploadFiles`, `getFileExtension`. Aceita array de `$sizes` (redimensionamento
  múltiplo).
  **Tipo de arquivo (C4 etapa 2)**: whitelists `ALLOWED_ATTACHMENT` (pdf, jpg/jpeg,
  png, xml, docx, xlsx), `ALLOWED_IMAGE` (jpg/jpeg, png) e `ALLOWED_PNG`.
  `typeError($_FILES['campo'], FileUploader::ALLOWED_...)` devolve a mensagem para
  o Toast (mesmo uso de `sizeLimitError`, logo depois dele, antes de qualquer
  gravação); `allowedExtension($nome, $tmp, $whitelist)` devolve a extensão
  **normalizada** (`jpeg` → `jpg`) a gravar, ou `null`. Confere extensão do nome
  **e** MIME real (`finfo`), abre a imagem (`getimagesize`) e recusa
  `php*`/`phtml`/`phar`/`html`/`svg`/`js`/executáveis em qualquer parte do nome.
  `uploadFiles($files, $paths, FileUploader::ALLOWED_ATTACHMENT)` já usa isso.
  **Nunca** tirar a extensão do nome enviado (`substr(..., -4)`/`pathinfo`).
- `UploadFiles.php` — API mais antiga (`upload($files, $path, $table, $arrayInsert)`,
  além de um método claramente de teste/lixo `WOWOW()` — não usar `WOWOW`, é
  resquício de debug).
- `FuncaoImagem.php` — `wideImagePhoto(...)`, wrapper direto sobre a lib WideImage
  vendorizada (`src/libs/wideImage/`). É o método efetivamente usado hoje pelos
  controllers de upload de logo/imagem (ver `BranchController::handleSubmitImages`).
- `Resizer.php` — tem **4 funções de resize concorrentes** (`resize`, `resize1`,
  `resize3`, `resize4`) — ver `docs/11-duplicidades-legado.md` antes de usar
  qualquer uma; não criar uma 5ª variante.
- `FuncaoArray.php::reArrayFiles($_FILES)` — reorganiza `$_FILES` de um input
  `name="foo[]"` (array de arquivos) para um array por índice em vez de por
  propriedade. Usar sempre que for tratar upload múltiplo de arquivos.
- `DeleteFile.php::deleteFile($ids, $table, $paths)` — exclusão de arquivo(s)
  físico(s) + registro(s) vinculados, genérico por tabela.
- `foto.class.php` / `ImageThumb.class.php` — classes antigas de manipulação de
  imagem (estilo pré-namespace); confirmar se ainda estão em uso antes de
  estender (podem já ter sido substituídas por WideImage/`FileUploader`).

## PDF

- `src/libs/dompdf/` — build vendorizado do dompdf (não é gerenciado pelo
  Composer do projeto raiz, é uma cópia dentro do repo). Usado pelas views
  `print*.php` de cada módulo para gerar contrato/recibo/relatório em PDF.
  Não vendorizar uma segunda lib de PDF — todo PDF novo deve seguir o padrão das
  views `print*.php` existentes (ex.: `src/view/sales/printContract.php`,
  `src/view/property/print.php`).

## E-mail

- `Email.php` — monta o objeto de e-mail (`__construct($assunto, $mensagem, $destinatarios, $cc, $bcc)`).
- `MoreMailer.php::enviarEmail($email)` — efetivamente envia via PHPMailer.
  Sempre usar essa dupla (`new Email(...)` + `MoreMailer::enviarEmail(...)`) para
  qualquer e-mail novo, não instanciar `PHPMailer` direto num controller.

## JWT / tokens

`JWTWrapper.php` (`encode`/`decode`) — usado hoje só no fluxo de recuperação de
senha (tabela `tokens`). Reaproveitar para qualquer necessidade nova de
token assinado com expiração.

## Negócio / cálculo

- `CommissionArrangement.php` — calcula comissão/rateio de venda (valor,
  percentual, impostos, por posição/hierarquia de usuário na filial). Usado em
  `BranchController::paymentArrangement`. **Toda lógica de cálculo de comissão
  deve passar por essa classe** — não recalcular percentual/rateio na mão em um
  controller novo; se o cálculo precisar de uma variação não coberta, estender
  a classe (alinhar com o usuário a regra de negócio antes).
- `RecursiveCostCenter.php` — árvore recursiva de centro de custo: montagem
  (`recursiveTree`, `recursiveGetChildren`), clonagem (`recursiveClone`), e várias
  formas de renderizar a árvore para UI (`recursiveTreeView*`, `recursiveOption*`,
  `recursiveTreeArray`, `recursiveTreeString`) — cada `recursive*View*` gera um
  HTML/formato ligeiramente diferente; conferir qual a tela já usa (`grep`) antes
  de escolher uma variante para uma tela nova.

## Outros

- `BoxAlert.php` — alerta inline na página (`successAlert`/`errorAlert`/
  `warningAlert`/`defaultItemAlerts`), diferente do `Toast` (que é flash
  pós-redirect). Usar `BoxAlert` quando o alerta deve aparecer na própria
  página sem redirect.
- `TableDefault.php` — **mais uma implementação concorrente de tabela** (além dos
  `TableComponent*` em `src/components/`) — ver `docs/11-duplicidades-legado.md`
  antes de usar.
- `Helper.php::debugPDO($sql, $parametros)` — imprime a query com os parâmetros
  interpolados, só para debug manual; nunca deixar chamada ativa em código
  commitado.
- `Normalize.php::normalize($filename)` — normaliza nome de arquivo (remove
  acento/espaço) antes de salvar em disco.
- `Qrcode.php` — gerador de QR code vendorizado (biblioteca completa, não mexer
  a menos que seja bug da própria lib).
- `Pagination.php` — `getPage()` (lê `$_GET['page']`), `pages($totalItems, $rows)`
  (monta os dados para `PaginationComponent1245`), `listItemsOnPage(...)`.
  Sempre usar esta classe para paginação de listagem — é o que
  `BranchController::index()` usa.
