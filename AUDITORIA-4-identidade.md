# Auditoria 4 — Identidade de marca residual (cliente anterior / fornecedor)

**Data**: 2026-09-29. **Tipo**: auditoria somente leitura — nenhum arquivo de
código ou registro de banco foi alterado durante a investigação.

## Contexto

O sistema atual é da revenda de veículos **"Repasse Sandré"**. O software é
white-label de um fornecedor chamado **"Ydeal Tecnologia"**
(`ydealtecnologia.com.br`/`ydeal.net.br`), usado antes por um cliente
diferente do ramo imobiliário chamado **"Realize Repasse"** — nome que, por
coincidência de sigla, ainda está entranhado na arquitetura (`namespace RR\`,
`$_SESSION['RR']`, nome do repositório). Três auditorias irmãs já mapearam
resíduo funcional desse histórico (`AUDITORIA-1-dominio.md`,
`AUDITORIA-2-tecnica.md`, `AUDITORIA-3-seguranca.md`); este documento é
focado especificamente em **identidade visível** — o que um cliente,
fornecedor ou parceiro externo veria hoje que não é "Repasse Sandré".

**Metodologia**: 3 agentes em paralelo (texto/contato hardcoded; logos/
cores/favicon; templates de e-mail/PDF), cada um consultando o banco real
`veiculos_repasse` (só `SELECT`/`SHOW`) além do código-fonte. Achados mais
importantes verificados pessoalmente por leitura direta antes deste
documento.

---

## 1. Achados ATIVOS — visíveis hoje sem precisar de nenhuma ação especial

### 1.1 — CRÍTICO — Servidor de e-mail (SMTP) ainda é do fornecedor antigo

Único fluxo de e-mail do sistema (`LoginController::sendRecoverPasswordMail`
→ `MoreMailer.php` → PHPMailer, config lida de `configuracao_email`).
**Confirmado no banco real**:
```
id=1  nome='Website'  smtp='mail.ydeal.net.br'  porta=587  email=''
```
`MoreMailer` monta `$mail->Host = 'mail.ydeal.net.br'` e
`$mail->FromName = 'Website'` — nem o host nem o nome de remetente são da
Repasse Sandré. O campo `email` está vazio, então hoje o envio de
recuperação de senha provavelmente já falha silenciosamente (PHPMailer
rejeita remetente vazio) — mas **não existe nenhuma tela de administração**
para corrigir isso (`SettingsSite::getSettingEmailById()` nunca é chamado
por nenhum controller); só é editável via SQL/migration manual.

**Severidade: CRÍTICO** — é o único canal de e-mail do sistema, e está
configurado com a infraestrutura do fornecedor antigo, sem tela para
corrigir.

### 1.2 — ALTO — Telefone do fornecedor hardcoded em todo o painel

`554832636688` (WhatsApp) aparece fixo em dois pontos centrais do código,
não em configuração de filial (a tabela `branch` não tem nem coluna de
telefone):

- **`src/view/_templates/header.php:56`** — link "Suporte Técnico" no
  cabeçalho de **toda tela autenticada** do sistema:
  ```php
  <a href="https://api.whatsapp.com/send?phone=554832636688" target="_blank">
  ```
- **`src/core/Model.php:18`** (propriedade herdada por todo Model) —
  mensagem de erro genérica financeira, usada em pelo menos 6 Models
  (`Sales.php`, `Branch.php`, `ArrangementPaymentChargesInvoiceReceiveInstallment.php`,
  `SummarySale.php`, `SalesChargePaymentAgreement.php`):
  ```php
  protected $message_admins = "Ops! ... Entre em contato os <a ... href=\"https://api.whatsapp.com/send?phone=554832636688\">administradores</a>";
  ```
No dump legado (`db/realize_repasse.sql`), o único cadastro com esse número
é **"Wyllyam Neves Rozenq" / "Ydeal Tecnologia Ltda"** — forte indício de
que é contato do fornecedor, não da revenda de veículos atual.

**Severidade: ALTO** — visível em toda página do painel e em qualquer erro
financeiro que qualquer usuário encontre.

### 1.3 — ALTO — E-mail da filial "Repasse Sandré" é do domínio do fornecedor

**Confirmado no banco real**:
```
branch.id=1  name='Repasse Sandré'  email='suporte@ydealtecnologia.com.br'  cnpj='00000000000000'
```
Visível para qualquer usuário que abra **Filiais → Editar → Repasse
Sandré** — o campo Email vem pré-preenchido com o domínio do fornecedor
(`src/view/branch/edit.php:27`). O CNPJ também é um placeholder inválido
(`00000000000000`), não o CNPJ real da empresa. **Este mesmo valor está
fixado na migration de seed de produção**
(`db/migrations/2026_09_25_1000_seed_inicial_producao.sql:5964-5971`) — o
próprio autor da migration já deixou um comentário sinalizando o problema
e pedindo ajuste manual pós-deploy. Se a migration rodar sem esse ajuste, a
instalação de produção nasce com o e-mail do fornecedor cadastrado na
filial.

**Severidade: ALTO** — dado de identidade legal/contato da empresa errado,
visível na tela principal de cadastro da filial e reproduzido no seed de
produção.

### 1.4 — MÉDIO — "Suporte Ydeal" como condição hardcoded no código

`src/controller/project/UsersController.php:148,257` — o nome do fornecedor
comparado como string em lógica de produção (decide se mostra o botão "Ver
como este usuário" e se uma sessão está em modo "voltar"). Relacionado ao
achado de escalação de privilégio já catalogado em `AUDITORIA-3-seguranca.md`
(achado 5.1) — aqui pela ótica de que o nome do fornecedor está cravado como
regra de negócio, não só um bug de permissão.

### 1.5 — MÉDIO — Link morto para site institucional do cliente anterior, entregue a terceiros no Cartão Digital

`src/view/cardPDF/index.php:208-210` — o PDF do Cartão Digital (documento
gerado e entregue a clientes/parceiros de um vendedor) inclui um ícone de
link clicável que usa `configuracao.url_global`:
```php
<?php if (!empty($config->url_global)) { ?>
    <a class="icone-corpo" href="<?= $config->url_global ?>" target="_blank">
```
**Valor real no banco**: `'http://localhost/website/'` — um link `localhost`
morto, resíduo do site institucional do cliente imobiliário anterior,
embutido em todo cartão digital em PDF gerado hoje.

**Severidade: MÉDIO** — não identifica a marca antiga por nome, mas é um
link quebrado e sem sentido entregue a terceiros num documento oficial.

### 1.6 — BAIXO — Conta de usuário "Suporte Ydeal" ativa

`users.id=1`, `name='Suporte Ydeal'`, `email='suporte@ydeal.net.br'` — já
catalogada como achado de segurança (conta Superadmin ativa) em memória do
projeto; aqui registrado pela ótica de identidade: o nome "Suporte Ydeal"
aparece em qualquer tela que mostre "criado por"/"editado por" ou no
cabeçalho de quem estiver logado com essa conta.

---

## 2. Achados de BANCO — armazenados mas não renderizados hoje (órfãos)

Dado real, mas **confirmado por grep que nenhum código atual lê essas
colunas** — não vazam para tela nenhuma hoje, mas continuam armazenando
identidade de dois clientes/fornecedores anteriores em texto pleno,
inclusive em migrations que talvez ainda não rodaram em produção.

| Tabela.coluna | Valor real | Observação |
|---|---|---|
| `configuracao.nome` | `'Realize Repasse'` | nome do cliente imobiliário anterior |
| `configuracao.rodape` | `'© Realize Repasse - Todos os direitos reservados - Desenvolvido por Ydeal Tecnologia'` | menciona os dois nomes anteriores |
| `configuracao.frase` | `'SEU FUTURO ESTÁ EM NOSSOS PLANOS!1'` | slogan imobiliário (com bug de "1" sobrando) |
| `configuracao.endereco` | `'Rua Manoel Luis dos Santos'` | endereço físico do cliente anterior |
| `configuracao.url_sistema` | `'https://sistema.realizerepasses.com.br/'` | domínio do cliente anterior |
| `texto` (6 linhas: empresa/horário/depoimento/contato/termos/equipe) | texto institucional completo, começa "A Ydeal Construtora E Incorporadora começou há 10 anos..." | texto "Sobre Nós" completo da construtora cliente anterior |

O rodapé **realmente renderizado** em toda tela (`src/view/_templates/footer.php:9`)
usa `system_config.footer` — coluna **diferente**, de outra tabela, valor
real `'Repasse Sandré'` (limpo). A confusão entre `system_config` (limpo,
em uso) e `configuracao`/`texto` (sujo, órfão) é o padrão de duplicidade já
documentado em `docs/11-duplicidades-legado.md`.

**Recomendação**: como `configuracao` também guarda o achado 1.5 (ativo),
uma eventual migration de limpeza pode tratar os dois de uma vez — mas isso
é decisão de correção, fora do escopo desta auditoria.

---

## 3. Contratos/recibos do domínio imobiliário — gap de validação (não é branding hardcoded, mas pode vazar o mesmo texto)

`standard_contract`: 8 dos 9 registros são templates imobiliários
(`PROPOSTA COMPRA E VENDA PADRÃO`, `CONTRATO DE COMPROMISSO DE COMPRA E
VENDA DE IMÓVEL`, citando `{%loteamento_produto%}`, CRECI etc.), já
desativados (`status=0`) pela migration `2026_09_21_1010_desativar_standard_contract_imovel.sql`
(já aplicada). Só `id=17` ("Recibo Simples", genérico e limpo) aparece no
dropdown de emissão.

**Gap residual confirmado**: `BillReceiveInstallmentController::printReceipt()`
(linha 962) e o equivalente de Contas a Pagar (linha 699) buscam o
`standard_contract` só por `id` (`Model::getItemById`, `WHERE id = :id`
puro) — **sem validar `type_contract` nem `status` no servidor**. O filtro
que impede escolher um template imobiliário é só o `<select>` do
front-end; um `POST` com `id_standard_contract` manipulado (5, 10, 11, 12,
14, 15 ou 16) ainda renderiza o contrato imobiliário completo dentro da
tela de impressão de recibo. Cenário raro (exige adulterar o POST
manualmente), mas tecnicamente possível hoje.

**Severidade: BAIXO** — requer manipulação deliberada de request, não
acontece navegando normalmente.

---

## 4. Identidade visual (logos, cores, favicon) — CONFIRMADO JÁ REBRANDIZADO

Boa notícia: a limpeza visual já foi feita corretamente nos 3 assets
visíveis por padrão hoje:

| Asset | Conteúdo real (imagem lida) | Onde aparece |
|---|---|---|
| `public/img/settings/logo_favicon-2.png` | Monograma "RS" azul-marinho | favicon do navegador |
| `public/img/settings/logo_login-2.png` | "RS · Repasses Sandré / REPASSES DE VEÍCULOS" | tela de login |
| `public/img/settings/logo_menu-3.png` + `public/img/branch/1/logo_menu-1.png` | "RS Repasses Sandré", horizontal | header do painel |

**Nenhum logo do fornecedor "Ydeal Tecnologia" nem do cliente imobiliário
anterior sobrevive em `public/img/`** — confirmado por leitura direta de
todas as 15 imagens do diretório e por busca de nome de arquivo
(`*ydeal*`, `*imobil*`, `*imovel*`, `*property*`) em todo o repositório
(zero resultado fora de documentação/migration esperada).

**Cores**: não há paleta de marca customizada residual — o sistema usa o
skin azul-padrão de fábrica do AdminLTE 2 (`skin-blue.min.css`, arquivo de
biblioteca não modificado), sem override de tema.

### Achado incidental (mismatch de domínio, não é marca antiga)

`public/img/customer/default/default.png` — ícone padrão de avatar de
Cliente/Fornecedor/Vendedor é um **operário de construção civil com
capacete**, usado como fallback sempre que o cadastro não tem foto própria
(`CustomerController.php:205`). Não identifica o cliente/fornecedor
anterior por nome, mas é um resíduo temático de construção civil/imóveis
num sistema de revenda de veículos — vale trocar por um ícone neutro.

---

## 5. Títulos de página, meta tags e composer.json

- `<title>` (todo o painel + login) usa `system_config.title` = **`'Repasse Sandré'`** — limpo.
- Não existem meta tags `description`/`keywords`/Open Graph em nenhuma view
  da aplicação (as únicas ocorrências do projeto são dentro de exemplos da
  biblioteca CKEditor vendorizada, sem relação com o app).
- **`composer.json:2-3`** — `"name": "ydeal/realize_repasse"`,
  `"description": "Realize Repasse"`. Não visível a usuário final, mas é a
  primeira coisa que um desenvolvedor vê ao abrir o projeto — carrega os
  dois nomes anteriores (fornecedor como vendor namespace, cliente
  imobiliário na descrição), mesmo o produto já se chamando "Repasse
  Sandré" em toda a interface.

### Achado estrutural (fora do pedido literal, registrado por transparência)

`namespace RR\` aparece em 219 arquivos; `$_SESSION['RR']` aparece 476
vezes — "RR" quase certamente vem de "**R**ealize **R**epasse" (cliente
imobiliário anterior). Está entranhado em toda a arquitetura (autoload
PSR-4, chave de sessão) — não é um grep-replace trivial e envolve risco real
de regressão num projeto deste tamanho. Citado apenas como registro; não é
recomendado mexer sem alinhar escopo e risco com o usuário (regra nº2/nº4
do CLAUDE.md — não introduzir mudança estrutural grande no achismo). O nome
do arquivo `db/realize_repasse.sql` carrega o mesmo nome (já sinalizado
como dump não confiável por `AUDITORIA-1-dominio.md`).

---

## 6. Telefones e CNPJ — resumo

- **Telefone hardcoded em código**: só o já citado em 1.2
  (`554832636688`). Demais telefones em views vêm de variável de banco.
- **CNPJ hardcoded em código-fonte**: nenhum. O único CNPJ "errado" é dado
  de banco (`branch.id=1`, placeholder inválido `00000000000000`, achado
  1.3). O CNPJ real do fornecedor (`19471199000164`, "Ydeal Tecnologia
  Ltda") só existe no dump morto `db/realize_repasse.sql`, não no banco
  real nem em migration.

---

## Tabela-resumo final

| # | Achado | Onde | Classificação |
|---|---|---|---|
| 1 | SMTP do fornecedor (`mail.ydeal.net.br`), sem tela pra corrigir | `configuracao_email` + `MoreMailer.php` | **CRÍTICO**, ativo |
| 2 | Telefone do fornecedor hardcoded em todo o painel + erros financeiros | `header.php:56`, `Model.php:18` | ALTO, ativo |
| 3 | E-mail da filial "Repasse Sandré" é do domínio do fornecedor, replicado no seed de produção | `branch.email`, `branch/edit.php:27`, migration de seed | ALTO, ativo |
| 4 | "Suporte Ydeal" hardcoded como regra de negócio | `UsersController.php:148,257` | MÉDIO, ativo |
| 5 | Link `localhost` morto do site antigo, embutido em todo Cartão Digital PDF | `cardPDF/index.php:208-210` + `configuracao.url_global` | MÉDIO, ativo |
| 6 | Conta "Suporte Ydeal" ativa (Superadmin) | `users.id=1` | BAIXO, ativo (já catalogado como risco de segurança) |
| 7 | Nome/rodapé/slogan/endereço do cliente imobiliário anterior | `configuracao.*` | BAIXO, órfão (não renderizado) |
| 8 | Texto institucional completo da construtora anterior | tabela `texto` | BAIXO, órfão |
| 9 | Templates de contrato imobiliário acessíveis via POST manipulado | `standard_contract` + `printReceipt()` sem validação server-side | BAIXO, requer manipulação |
| 10 | Ícone padrão de avatar é operário de construção civil | `public/img/customer/default/default.png` | BAIXO, mismatch temático |
| 11 | `composer.json` com vendor "ydeal" e descrição "Realize Repasse" | `composer.json:2-3` | BAIXO, não visível a usuário final |
| 12 | Namespace `RR\`/`$_SESSION['RR']` (sigla do cliente anterior) entranhado na arquitetura | 219 arquivos / 476 usos | informativo, não recomendado mexer sem alinhar escopo |
| — | Logos, favicon, cores | `public/img/settings/*`, CSS | **confirmado limpo**, já rebrandizado |
| — | Títulos de página, meta tags | `system_config.title` | **confirmado limpo** |
| — | Cartão digital (texto "Imobiliária" hardcoded) | `cardPDF/index.php` | **confirmado limpo** (já removido em auditoria anterior) |

## Conclusão

A parte **visual** (logo, favicon, cores, título de página) já foi corretamente
rebrandizada para "Repasse Sandré" — não há resíduo de imagem/marca do
cliente imobiliário anterior nem do fornecedor "Ydeal Tecnologia". O
problema real está em **dados de contato e configuração**: o servidor de
e-mail (achado 1), o telefone de suporte (achado 2) e o e-mail da filial
(achado 3) ainda apontam para o fornecedor antigo, todos **ativos e visíveis
hoje**, sem tela de administração para o e-mail de SMTP. Recomenda-se, antes
de qualquer coisa, alinhar com o usuário quais são os dados de contato reais
da Repasse Sandré hoje (regra do CLAUDE.md — não presumir dado de negócio) e
então gerar as migrations correspondentes para corrigir `configuracao_email`
e `branch.email`, e trocar o telefone hardcoded em `header.php`/`Model.php`
por uma fonte configurável (ou pelo dado real, se for informação estática
mesmo).

Nenhuma correção foi aplicada nesta auditoria — só o relatório, como pedido
("SOMENTE LEITURA").
