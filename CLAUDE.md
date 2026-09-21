# Realize Repasse — Índice

Este arquivo é **apenas um índice**. Ele não contém as explicações em si — cada tópico
aponta para um arquivo em `docs/` com o conteúdo detalhado. Leia o(s) arquivo(s)
relevante(s) para a tarefa atual antes de implementar qualquer coisa; não tente
adivinhar o padrão a partir daqui.

## Regras de trabalho (sempre válidas, não deferidas)

1. **Banco de dados**: nunca alterar o banco diretamente, nunca mesmo, por
   nenhum meio — nem SQL direto (`ALTER`/`CREATE`/`DROP`/`TRUNCATE`/
   `RENAME TABLE`/`UPDATE`/`INSERT`/`DELETE`/`REPLACE INTO`/`LOAD DATA`/
   `GRANT`/`REVOKE`/`SET PASSWORD`, via mysql CLI, phpMyAdmin ou qualquer
   cliente) nem por caminho indireto que produza o mesmo efeito — script PHP
   avulso que usa a camada de Model/PDO da própria aplicação (`php -r`, `php
   arquivo.php`), ou requisição HTTP/curl contra um endpoint da aplicação
   rodando que escreva no banco como efeito colateral (ex.: "testar" uma
   feature de cadastro fazendo `POST` direto no controller). Não importa o
   motivo (schema novo, seed de referência, correção pontual de uma linha,
   reset de senha, dado de teste) nem o ambiente (vale para local/dev igual a
   produção), e **não existe exceção mesmo que o usuário peça explicitamente
   pra rodar direto "só dessa vez"** — a resposta é sempre gerar a migration.
   Toda alteração no banco vira arquivo `.sql` em `db/migrations/`, para o
   usuário revisar e executar manualmente. Leitura (`SELECT`/`SHOW`) continua
   liberada normalmente para investigação. Ver `docs/07-banco-de-dados.md`.
2. **Seguir o padrão existente**: arquitetura, nomenclatura, assinatura de métodos,
   forma de montar SQL, forma de estruturar views/components — tudo isso já está
   estabelecido no projeto. Implementações novas ou manutenções devem seguir os
   mesmos padrões descritos nos documentos abaixo, mesmo quando o padrão não é o
   "ideal" academicamente. Não introduzir um framework, lib ou estilo novo sem
   alinhar com o usuário antes.
3. **Evitar duplicidade**: antes de criar um método, função, componente, classe JS
   ou regra de CSS novo, procurar (grep/Explore) se já existe algo reaproveitável
   no módulo atual ou em módulos parecidos. Só criar algo novo se genuinamente não
   existir. Este projeto já tem duplicidades históricas conhecidas — ver
   `docs/11-duplicidades-legado.md` antes de reaproveitar algo com nome ambíguo ou
   sufixo numérico estranho (ex.: `TableComponent` vs `TableComponent5432`).
4. **Nunca no achismo**: se a tarefa for ambígua, se houver mais de um padrão
   plausível a seguir, ou se faltar informação de negócio (regra de comissão,
   perfil de acesso, cálculo financeiro etc.), perguntar ao usuário antes de
   implementar. Não presumir regra de negócio a partir do nome de uma coluna/tabela.

## Índice por tópico

- **Visão geral do produto e dos dois domínios de negócio** (imobiliário + revenda
  de veículos) → [`docs/01-visao-geral.md`](docs/01-visao-geral.md)
- **Arquitetura MVC caseira, roteamento de URL, ciclo de vida de uma requisição**
  (`Application`, `Controller`, `Model`, `Ajax`) → [`docs/02-arquitetura-fluxo.md`](docs/02-arquitetura-fluxo.md)
- **Padrão de Controllers** (`project` vs `ajax`, CRUD completo, upload de imagem,
  transação, exemplo comentado) → [`docs/03-controllers.md`](docs/03-controllers.md)
- **Padrão de Models** (`Model` base/query-builder, `ModelGenerico`, `GerenciaPost`,
  convenções de método por tabela) → [`docs/04-models.md`](docs/04-models.md)
- **Views e Components reutilizáveis** (AdminLTE 2, estrutura de pasta por módulo,
  como montar tabela/botão/paginação) → [`docs/05-views-componentes.md`](docs/05-views-componentes.md)
- **Assets de front-end**: CSS/JS/plugins, versionamento `v_01`, onde ficam os
  scripts por módulo → [`docs/06-frontend-assets.md`](docs/06-frontend-assets.md)
- **Banco de dados**: tabelas existentes, convenções de coluna, e o processo de
  criar migrations manuais em `db/migrations/` → [`docs/07-banco-de-dados.md`](docs/07-banco-de-dados.md)
- **Autenticação, sessão (`$_SESSION['RR']`) e controle de acesso/permissões**
  (`Secure`, `Authentication`, `menu_access`) → [`docs/08-autenticacao-permissoes.md`](docs/08-autenticacao-permissoes.md)
- **Bibliotecas utilitárias** (`src/libs/*`: máscara, data, upload, e-mail, PDF,
  comissão, centro de custo recursivo, etc.) → [`docs/09-bibliotecas-libs.md`](docs/09-bibliotecas-libs.md)
- **Módulos de negócio existentes** (o que cada área do sistema faz e onde fica)
  → [`docs/10-modulos-negocio.md`](docs/10-modulos-negocio.md)
- **Duplicidades e código legado conhecido** (versões concorrentes de components/
  métodos, o que é "Descontinuar", como decidir o que reaproveitar)
  → [`docs/11-duplicidades-legado.md`](docs/11-duplicidades-legado.md)
- **Ambiente, configuração e instalação local** (`config.php`, Composer, `.htaccess`)
  → [`docs/12-ambiente-configuracao.md`](docs/12-ambiente-configuracao.md)

## Como manter este índice

Sempre que uma tarefa revelar um padrão novo, uma duplicidade nova, ou uma decisão
de arquitetura relevante, atualizar o arquivo de `docs/` correspondente (não este
índice, a menos que um tópico novo precise ser criado). Manter cada linha do índice
curta — os detalhes vivem nos arquivos apontados, não aqui.
