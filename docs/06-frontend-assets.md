# Assets de front-end

## Versionamento por "pasta de versão"

CSS, JS e plugins de terceiros ficam versionados por uma pasta `v_01/` dentro de
`public/css/`, `public/js/` e `public/plugins/`. As constantes `CSSVERSION`,
`JSVERSION`, `PLUGINSVERSION` (definidas em `public/index.php`, hoje todas
`v_01`) controlam qual pasta é referenciada — trocar o valor da constante seria
a forma de "publicar" uma nova versão de assets sem sobrescrever a antiga.
**Não criar uma nova convenção de versionamento** — se for necessário versionar
algo novo, seguir esse mesmo esquema de pasta `v_XX`.

Além disso, `FrontController::renderScript()` faz cache-busting automático por
`filesize()` do arquivo (`?filever=<tamanho em bytes>`) quando o arquivo existe
localmente, e `renderStyle()` usa um `?v=<timestamp atual>` (sempre muda, cuidado:
isso significa que **CSS nunca é cacheado pelo navegador** entre requests —
comportamento existente, não é bug a "corrigir" sem alinhar antes).

## Onde registrar um asset novo

- Assets **globais** (usados em toda página do painel): registrar em
  `src/controller/project/FrontController.php`, no construtor, via
  `$this->addStyle(...)`/`$this->addScript(...)`.
- Assets **específicos de um módulo/tela**: registrar dentro do método do
  controller daquele módulo, também via `$this->addScript(...)`/`addStyle(...)`,
  **antes** do `require` da view. Ver `BranchController::addItem()` chamando
  `$this->addScript(URL . "js/" . JSVERSION . "/state.js")` e
  `.../branch/branch.js`.

## Estrutura de `public/js/v_01/`

- Scripts genéricos/compartilhados soltos na raiz: `script.js`, `mask.js`,
  `modals.js`, `toast.js`, `toast-config.js`, `notification.js`, `application.js`,
  `state.js`, `autocep.js`, `cnpj.js`, `arrangementAnimation.js`, etc.
- Scripts **por módulo**, em subpasta com o mesmo nome (kebab-case) do `route`/
  `dir` do controller: `branch/`, `sales/`, `property/`, `vehicles/`,
  `purchase-requests/`, `sale-requests/`, `bill-receive/`,
  `bills-to-pay-installment/`, `attendance/`, `calendar/`, `cost-center/`,
  `recursive-cost-center/`, `lead/`, `lead-config/`, `customer/`, `settings/`,
  `report-attendance/`, `check-control/`, `construction/`, `integration/`,
  `config-integration/`, `order/`.

Ao criar JS novo para uma tela, seguir esse padrão: se é específico de 1 módulo,
criar/editar dentro da subpasta do módulo; se é reaproveitável entre módulos,
avaliar se cabe num dos scripts genéricos da raiz antes de duplicar lógica (ex.:
máscara de campo → `mask.js`; toast → `toast.js`/`toast-config.js`; modal de
confirmação de ativar/desativar → `modals.js`, que já escuta `.btn-disable-item`/
`.btn-enable-item`, ver `docs/05-views-componentes.md`).

## Estrutura de `public/css/v_01/`

Mesma lógica: `styles.css`/`spacing.css`/`template.css` são globais (registrados
no `FrontController`); pastas por módulo (`attendance/`, `calendar/`,
`lead-config/`, `cost-center/`, `property/`, `presentations/`, `layout-site/`,
`construction/`, `customer/`, `record-bills-to-pay/`) para CSS específico de tela.
Antes de escrever uma regra CSS nova, procurar se já existe classe utilitária
equivalente em `spacing.css`/`styles.css` (ou no próprio Bootstrap 3/AdminLTE 2
já carregados) — não duplicar utilitário de espaçamento/cor já coberto.

## Plugins de terceiros (`public/plugins/v_01/`)

Já disponíveis e carregados globalmente pelo `FrontController`: jQuery,
jQuery UI, Bootstrap 3, Bootstrap Colorpicker, Bootstrap Datepicker, Select2,
Font Awesome, AdminLTE 2 (skin blue), SweetAlert2, fonts-google, fancybox
(`jquery/fancybox.min.css`), Moment.js, inputmask + maskmoney.

Também presentes na pasta de plugins mas **não** carregados globalmente (incluir
sob demanda, por tela, se for usar): `chart.js`, `fullcalendar`,
`bootstrap-slider`, `bootstrap-daterangepicker`, `bootstrap-timepicker`,
`ckeditor`, `colorpicker`, além de `toastr` (fora da pasta `v_01`, na raiz de
`plugins/`).

**Antes de adicionar uma lib de terceiros nova via CDN/npm**, verificar se algum
desses plugins já cobre a necessidade (calendário → `fullcalendar`; gráfico →
`chart.js`; editor rich text → `ckeditor`; range de datas →
`bootstrap-daterangepicker`). Este projeto não usa bundler (Webpack/Vite) — tudo
é `<script src="...">`/`<link>` direto, então adicionar lib nova = vendorizar os
arquivos dentro de `public/plugins/v_01/<nome>/` e registrar via
`addScript`/`addStyle`, seguindo o padrão dos plugins existentes.
