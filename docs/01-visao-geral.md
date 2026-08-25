# Visão geral

## O que é

Sistema web interno (painel administrativo, "ERP/CRM") chamado **Realize Repasse**,
desenvolvido sob medida (empresa "ydeal") em PHP puro sem framework de mercado
(sem Laravel/Symfony completo — só usa algumas libs pontuais via Composer). Roda
em `PHP >= 8.1` (ver `platform_check.php`) e usa o tema **AdminLTE 2** para a UI.

O composer.json identifica o pacote como `ydeal/realize_repasse`.

## Os dois domínios de negócio

O sistema atende **dois ramos de negócio diferentes dentro do mesmo painel**,
aparentemente ligados por multi-tenant/whitelabel:

1. **Imobiliária**: imóveis (`property*`), construções (`constructions`),
   contratos de venda, comissões, clientes, site institucional do imóvel.
2. **Revenda de veículos ("repasse")**: compra e venda de carros usados
   (`vehicles*`, `purchase_requests`, `sale_requests`, `record_of_purchased/sold
   _vehicles`), com controle de custo, histórico do veículo, comissão.

A constante `REAL_ESTATE` em `src/config/config.php` sinaliza qual perfil está
ativo para a instalação atual. **Não presumir** qual dos dois modos está ativo
numa tarefa sem checar essa flag ou perguntar — muita lógica de view/controller
usa esse flag ou o tipo de filial (`branch.type`) para decidir o que mostrar.

Além disso, o painel administra um **site institucional** acoplado (banners,
depoimentos, textos, imagens, redes sociais, popup, meta tags) — ver
`SettingsSiteController` e as tabelas `banner`, `depoimento`, `texto`, etc.

## Papéis / hierarquia de usuário

Existe um sistema de **perfis com nível de acesso numérico** (`users_profiles.access`)
usado extensivamente para autorização (`Secure::access_*` — ver
`docs/08-autenticacao-permissoes.md`). Quanto **menor** o número, **maior** o
privilégio (ex.: super admin tem acesso mais baixo/privilegiado). Também existe
o conceito de **filial (`branch`)** — cada usuário pertence a uma ou mais filiais,
e o "Super ADM" é representado pela filial de `id = 0`.

## Onde estão as coisas (mapa rápido)

| Camada | Pasta |
|---|---|
| Front controller único | `public/index.php` |
| Núcleo do framework caseiro | `src/core/` |
| Controllers de painel (HTML) | `src/controller/project/` |
| Controllers AJAX (JSON) | `src/controller/ajax/` |
| Models (1 por tabela, aprox.) | `src/model/` |
| Views (PHP puro) | `src/view/<modulo>/` |
| Components de UI reutilizáveis | `src/components/` |
| Bibliotecas utilitárias | `src/libs/` |
| Assets públicos (css/js/plugins/img) | `public/` |
| Dump/estrutura do banco | `db/realize_repasse.sql` |
| Migrations manuais (novo, ver doc 07) | `db/migrations/` |

Para o fluxo técnico completo de uma requisição, ver
[`02-arquitetura-fluxo.md`](02-arquitetura-fluxo.md).
