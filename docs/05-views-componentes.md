# Views e Components reutilizáveis

## Estrutura de pasta

`src/view/<modulo-em-kebab-case>/` espelha o nome do controller (ex.: controller
`BranchController` → pasta `src/view/branch/`). Arquivos comuns dentro de cada
pasta de módulo:

| Arquivo | Papel |
|---|---|
| `index.php` | listagem (filtros + tabela + paginação) |
| `add.php` | formulário de criação |
| `edit.php` | formulário de edição |
| `menu.php` | abas de navegação entre sub-telas de edição (`navTabs`) |
| `modals.php` | modais específicos da tela, quando não cabem inline |
| `print*.php` | view usada para gerar PDF via dompdf (sem header/footer do painel) |

Views são **PHP puro misturado com HTML** (sem Blade/Twig). Elas rodam dentro do
escopo do método do controller que fez o `require` — variáveis locais do
controller (`$this->route`, `$contentHeader`, `$table`, etc.) já estão
disponíveis.

## Layout

`src/view/_templates/header.php` e `footer.php` envolvem toda página de painel
(estrutura AdminLTE 2: sidebar, topbar, content-wrapper). São incluídos
manualmente em cada método de controller que renderiza página (não há um layout
automático) — **sempre** os dois `require`s (header antes, footer depois) ao
redor do `require` da view específica.

## Components (`src/components/`)

Classes PHP simples que **recebem um objeto de configuração no construtor e
imprimem HTML diretamente** (sem retorno — o `echo`/HTML acontece dentro do
`render()` privado chamado pelo próprio construtor). Uso típico dentro de uma
view:

```php
<?php new ContentHeaderComponent4214($contentHeader) ?>
...
<?php new TableComponent5432($table->thead, $table->data, $table->config); ?>
...
<?php new PaginationComponent1245($pagination); ?>
```

**Atenção**: vários components existem em **duas versões** (uma sem sufixo, uma
com sufixo numérico) com formatos de dados diferentes entre si. Isso não é um
capricho de nomenclatura — são implementações distintas e **não intercambiáveis**.
Antes de usar qualquer component, ver `docs/11-duplicidades-legado.md` para saber
qual versão é a esperada no contexto, e `grep -r "new NomeDoComponent" src/view`
para confirmar o formato de dado que as telas vizinhas já usam.

### `TableComponent5432` (versão em uso predominante — 28 usos)

Espera:
```php
$table = (object)[
    'config' => (object)['responsive' => true, 'condensed' => true, 'bordered' => true, 'striped' => true],
    'thead'  => [
        (object)['class' => 'text-center', 'text' => 'Cód.', 'column' => (object)['type' => 'text', 'link' => 'id']],
        (object)['class' => 'text-center', 'text' => 'Status', 'column' => (object)['type' => 'label', 'link' => 'status']],
        (object)['class' => 'text-center', 'text' => 'Ações', 'column' => (object)['type' => 'button', 'link' => 'action']],
    ],
    'data' => $response->data, // cada item precisa ter as propriedades referenciadas em 'link'
];
new TableComponent5432($table->thead, $table->data, $table->config);
```
`column.type` suportados: `text` (default), `label` (badge colorido — item precisa
ter `{value, color}`), `button` (item precisa ser um array de objetos de botão),
`image`. Para incluir ações (editar/ativar/desativar), montar `item->action` como
array de objetos `{icon, href|attr.sendTo, title, size, color, class}` — ver
`BranchController::index()` como referência completa de como montar `$table` e os
botões de ação (inclusive o padrão de modal de confirmar desativar/ativar, com
classe `.btn-disable-item`/`.btn-enable-item` capturada em JS genérico).

### `ContentHeaderComponent4214` / `PaginationComponent1245`

Recebem, respectivamente, `$contentHeader` (`{route, title, caption, buttons?}`)
e `$pagination` (retorno de `Pagination::pages()`, `src/libs/Pagination.php`).
Ver assinatura exata lendo o arquivo antes de montar o objeto — não adivinhar os
campos.

O `PaginationComponent1245` monta os links de página a partir de
`$_SERVER['REQUEST_URI']` (mantém os filtros da tela) e **já escapa** a URL com
`htmlspecialchars` — não passe URL escapada para ele nem escape de novo.
Valores de filtro devolvidos em campos da view (`value="..."`) e `href`
montados a partir da query precisam de
`htmlspecialchars($x, ENT_QUOTES, 'UTF-8')` na própria view/controller.

### Outros components disponíveis (usar antes de criar HTML solto)

`BadgeComponent`, `BoxInfoComponent`, `BranchLogoComponent`, `ButtonComponent`,
`ContentInfoBoxComponent`, `FilterFormCardComponent`, `ImageComponent`,
`InfoBoxComponent`, `InputComponent`, `ListingCardComponent`,
`MainSidebarComponent`, `MenusComponent`, `MessagesDropdownComponent`,
`NavItemComponent`, `NavSidebarComponent`, `NavTabsComponent`,
`NotificationsDropdownComponent`, `ProgressComponent`, `PropertyFilterComponent`,
`SidebarComponent`, `SidebarSearchComponent`, `UserDropdownComponent`.

Todos seguem o mesmo padrão: `new NomeComponent($objetoDeConfig)`, imprime HTML.
Antes de montar um `<input>`/`<button>`/`<table>` "na mão" numa view nova, checar
se um desses components já resolve — e se resolve parcialmente, preferir
estendê-lo/parametrizá-lo a duplicar a lógica de renderização em outro lugar
(alinhar com o usuário se a extensão for não trivial).

## Formulário padrão (sem component, HTML direto)

Boa parte dos formulários (`add.php`/`edit.php`) ainda é HTML/Bootstrap 3
(AdminLTE 2) escrito diretamente, **sem** passar por `InputComponent` — ver
`src/view/branch/add.php` como referência de estrutura: `box box-primary` →
`box-header` → `form` com `enctype="multipart/form-data"` quando há upload →
`box-body` com grid `row`/`col-md-*` → `box-footer` com botão "Voltar" (link) à
esquerda e botão de submit à direita (`pull-right`). Seguir essa estrutura visual
para manter consistência, mesmo quando não usar `InputComponent`.

## Escape de dados na view (XSS — A3/M3, 2026-10-02)

Todo texto vindo do banco ou do usuário sai escapado. Qual função usar:
- **Texto simples** (nome, descrição, comentário, observação, inclusive dentro
  de `<textarea>` e de atributos `value=""`/`title=""`):
  `htmlspecialchars($x ?? '', ENT_QUOTES, 'UTF-8')`.
- **Campo de editor de texto rico** (`textarea.box-ckeditor`: observações do
  veículo, observação de transferência): `Util::richText($x)` (HTMLPurifier —
  mantém a formatação, remove script/eventos/`javascript:`).
- **Campo que mistura texto digitado com HTML gravado pelo sistema** (timeline
  de cheque; descrição de lançamento/parcela gerada por cheque, exibida nos
  relatórios financeiros; título de notificação): `Util::escapeSystemHtml($x)`
  — escapa tudo e restaura só `<strong>` e os links internos
  `<a href='{URL}rota' target='_blank'>` que o sistema grava. Se um código novo
  gravar outro tipo de HTML nesses campos, ele aparece como texto — prefira não
  gravar HTML em campo de texto.
- **JS**: dado de AJAX vai para o DOM com `.text()`, não `.html()`. Se precisar
  montar HTML em template string, escape o valor antes.

## Antes de criar uma view/component novo

1. Achar a tela mais parecida já existente (mesmo tipo de CRUD, mesma
   complexidade) e copiar a estrutura, não começar do zero.
2. Confirmar com `grep` qual versão de cada component (`docs/11-duplicidades-legado.md`)
   as telas do mesmo módulo já usam, e manter consistência dentro do módulo.
3. Só criar um component novo se nenhum dos existentes cobrir o caso — e, indo
   por esse caminho, seguir o mesmo padrão de construtor recebendo um objeto de
   config e um `render()` privado.
