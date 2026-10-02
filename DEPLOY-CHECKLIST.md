# Checklist de deploy — Repasse Sandré

Arquivo único para seguir no dia do deploy, na ordem. Cada item aponta para o
item de origem no `PLANO-CORRECOES.md`; quando um item novo de deploy surgir
no plano, ele deve ser **copiado para cá** (não só citado lá).

Marque `[x]` conforme for fazendo. Se algum passo de verificação falhar, pare
e vá para a seção **Plano de volta**.

---

## 0. Antes de começar

- [ ] **Backup completo do banco** de produção, antes de qualquer outra coisa:
      `mysqldump --single-transaction --routines --triggers -u USUARIO -p BANCO > backup_AAAA-MM-DD.sql`
- [ ] **Backup completo dos arquivos** do servidor, no mínimo:
      - todo o código publicado (a pasta do sistema inteira);
      - `src/config/config.php` (não é versionado — é único do servidor);
      - pastas de upload: `public/img/`, `public/attachments/`, `public/vehicle/`.
- [ ] Conferir que os dois backups abrem (tamanho coerente; o `.sql` termina
      com `-- Dump completed`; o pacote de arquivos lista as pastas de upload).
- [ ] Guardar os backups **fora** do servidor da aplicação.
- [ ] Anotar o commit que está em produção hoje (para o plano de volta).

## 1. Servidor: PHP

- [ ] Extensão **`fileinfo`** habilitada (`php -m | grep fileinfo`). Sem ela,
      todo upload quebra: a validação de tipo do C4 etapa 2 usa `finfo`.
      *(Plano: C4 etapa 2)*
- [ ] **GD** com suporte a JPEG e PNG (`php -r 'print_r(gd_info());'`).
      *(Plano: C4 etapa 2 — fotos, logos e avatar são redimensionados com GD)*
- [ ] `upload_max_filesize` **≥ 20M** e `post_max_size` **maior** que isso
      (local usa 40M/40M). Abaixo disso, o sistema avisa com o limite menor,
      e acima do `post_max_size` o formulário é descartado com erro genérico.
      *(Plano: M6)*

## 2. Servidor: `src/config/config.php` (não versionado)

- [ ] `define('ENVIRONMENT', 'production');` *(Plano: M5)*
- [ ] Bloco `else` igual ao do `config.example.php` (esconde erro da tela,
      mantém no log). *(Plano: M5)*

  ```php
  if (ENVIRONMENT === 'development') {
      error_reporting(E_ALL);
      ini_set("display_errors", 1);
  } else {
      error_reporting(E_ALL);
      ini_set("display_errors", 0);
      ini_set("display_startup_errors", 0);
      ini_set("log_errors", 1);
  }
  ```

- [ ] Conferir as constantes de banco (`DB_*`) e de URL do servidor.
- [ ] **`JWT_KEY`** (chave dos links de recuperação de senha) definida, igual
      ao `config.example.php`, com valor **novo e só deste servidor**:
      `php -r 'echo bin2hex(random_bytes(32)), "\n";'`. Sem ela, "Esqueci a
      senha" dá erro. Links de recuperação enviados antes da troca deixam de
      funcionar (mostram "Link inválido") — avisar a equipe. *(Plano: M4)*

## 2.1 Servidor: dependências (`vendor/`, não versionada)

- [ ] Publicar a pasta `vendor/` atualizada (ou rodar `composer install
      --no-dev` no servidor com o `composer.lock` do commit). Ela precisa conter
      **`ezyang/htmlpurifier`** — sem ela, as telas de Observações do veículo e
      de Transferência dão erro fatal. *(Plano: A3)*

## 3. Servidor: Apache

- [ ] `mod_rewrite` carregado (o sistema inteiro depende dele).
- [ ] **`mod_headers` carregado** (`apachectl -M | grep headers`). A regra de
      cabeçalhos dos anexos está dentro de `<IfModule mod_headers.c>`: **sem o
      módulo, ela é ignorada em silêncio**, sem erro. *(Plano: C4 etapa 2)*
- [ ] `AllowOverride` do vhost permite `.htaccess` em `public/`: precisa de
      pelo menos `FileInfo` (rewrite e cabeçalhos) **e** `Options` (o arquivo
      usa `Options -MultiViews`/`-Indexes`; sem `Options` o Apache responde
      erro 500 em tudo). Se o vhost usar `AllowOverride None`, nada do
      `public/.htaccess` vale e as regras precisam ir para o vhost.
      *(Plano: C4 etapas 1 e 2)*
- [ ] Nenhuma pasta de upload tem `.htaccess` próprio com `RewriteEngine` — isso
      desligaria o bloqueio de scripts nela. *(Plano: C4 etapa 1)*
- [ ] As pastas `public/img/`, `public/attachments/` e `public/vehicle/` são
      graváveis pelo usuário do Apache.

## 4. HTTPS

- [ ] Certificado válido e o site abre em `https://`. *(Plano: M6)*
- [ ] `http://` redireciona para `https://`.
- [ ] Se houver proxy/balanceador na frente, ele envia `X-Forwarded-Proto: https`.
- [ ] No login, o `Set-Cookie` da sessão vem com `secure; HttpOnly; SameSite=Lax`
      (DevTools → Rede → resposta do login). Sem HTTPS o cookie sai sem
      `secure`. *(Plano: M6)*

## 5. Banco: migrations pendentes, na ordem

Nenhuma migration roda sozinha (ver `db/migrations/README.md`). Antes de rodar
qualquer uma, **confirmar no banco de produção quais já foram aplicadas**: o
repositório não registra isso, e as anotações divergem (a de 25/09 15h diz
"produção já rodando, com dados reais"; o item A9 do plano diz que o seed
inicial "ainda não rodou"). Os arquivos `OK_*` são de antes de 25/09 e
presume-se que já rodaram — confirmar também.

Ordem (cronológica, pelo nome do arquivo):

- [ ] `2026_09_18_1615_criar_tabela_linked_check_control.sql`
- [ ] `2026_09_21_1000_remover_menu_integration_config.sql`
- [ ] `2026_09_21_1010_desativar_standard_contract_imovel.sql`
- [ ] `2026_09_21_1020_remover_campo_creci.sql`
- [ ] `2026_09_25_1000_seed_inicial_producao.sql` — **só em banco novo/vazio**
      (ver cabeçalho do arquivo). Antes, corrigir nele o `branch.email`
      (item A9, abaixo).
- [ ] `2026_09_25_1500_seed_dados_faltantes.sql` (idempotente)
- [ ] `2026_10_02_0921_criar_tabela_login_attempts.sql` — **antes** de
      publicar o código do A4 (bloqueio de login): o login passa a usar esta
      tabela e quebra sem ela. *(Plano: A4)*
- [ ] Qualquer migration nova criada depois desta lista (conferir a pasta).

## 6. Dados reais (bloqueados até o usuário informar)

- [ ] **SMTP de envio** (tabela `configuracao_email`): ainda aponta para o
      fornecedor antigo. Sem isso, recuperação de senha e e-mails do sistema
      saem pelo servidor errado ou não saem. *(Plano: C7 — ⚠️ bloqueado)*
- [ ] **Telefone de suporte** (`src/view/_templates/header.php:56`,
      `src/core/Model.php:18`) e **e-mail de contato da filial**
      (`branch.email`, inclusive no seed de produção). Corrigir código e dado
      juntos. *(Plano: A9 — ⚠️ bloqueado)*
- [ ] **Se o M4 tiver sido feito** (chave JWT fora do código): trocar a chave
      invalida os links de recuperação de senha já enviados e não usados —
      avisar a equipe antes. *(Plano: M4)*

## 7. Verificações de segurança no servidor (depois de publicar o código)

Troque `https://SITE` pela URL real. Se o sistema roda numa subpasta, inclua
o caminho.

### 7.1 Bloqueio de script nas pastas de upload — em CADA pasta *(Plano: C4 etapa 1)*

Para cada pasta abaixo, criar pelo terminal do servidor um arquivo de teste
`teste-bloqueio.php` com o conteúdo `<?php echo "EXECUTOU";`, abrir no
navegador ou com `curl -s -o /dev/null -w '%{http_code}\n' URL`, e **apagar o
arquivo em seguida**. Esperado: **403** em todas (nunca a palavra "EXECUTOU").

- [ ] `public/img/` (e uma subpasta, ex.: `public/img/users/1/`)
- [ ] `public/attachments/billsToPay/<id>/`
- [ ] `public/attachments/bill-receive/<id>/`
- [ ] `public/attachments/attendance/<id>/`
- [ ] `public/attachments/customer/<id>/`
- [ ] `public/vehicle/<id>/attachments/`
- [ ] `public/vehicle/<id>/images/`
- [ ] Repetir 2 ou 3 delas com as extensões `.phtml` e `.php5` → 403.
- [ ] Conferir que os arquivos de teste foram apagados.

### 7.2 Cabeçalhos dos anexos *(Plano: C4 etapa 2)*

Usar arquivos que já existam (ou subir um de cada pela tela). Comando:
`curl -sI URL_DO_ARQUIVO | grep -iE '^(HTTP|content-type|x-content-type-options|content-disposition)'`

- [ ] PDF em `attachments/customer/<id>/...pdf` → `X-Content-Type-Options: nosniff`
      **e** `Content-Disposition: attachment`.
- [ ] PDF em `vehicle/<id>/attachments/...pdf` → os dois cabeçalhos.
- [ ] XML, DOCX ou XLSX em qualquer pasta de anexo → os dois cabeçalhos.
- [ ] PNG/JPG em pasta de anexo → **só** `nosniff` (sem `Content-Disposition`;
      a imagem abre no navegador).
- [ ] **Controles (nenhum dos dois cabeçalhos)**: foto de veículo
      (`vehicle/<id>/images/...png`), logo (`img/settings/...png`) e as telas
      `vehicles/attachments/<id>` e `customer/attachment/<id>` (logado: devem
      abrir normalmente, **sem baixar a página**).
- [ ] Se nenhum anexo vier com `nosniff`: o `mod_headers` não está ativo ou o
      `AllowOverride` não permite — voltar à seção 3.

### 7.3 Extensões de anexos já existentes em produção *(Plano: C4 etapa 2)*

A whitelist só vale para uploads novos; arquivos antigos continuam acessíveis
(baixando). Mesmo assim, levantar o que já existe, só leitura:

```sql
SELECT 'veiculo' t, extencion e, COUNT(*) FROM vehicle_attachments GROUP BY 2
UNION ALL SELECT 'foto', extension, COUNT(*) FROM vehicle_images GROUP BY 2
UNION ALL SELECT 'pagar', extension, COUNT(*) FROM bills_to_pay_installments_attachment GROUP BY 2
UNION ALL SELECT 'receber', extension, COUNT(*) FROM bill_receive_installment_attachment GROUP BY 2
UNION ALL SELECT 'atendimento', extension, COUNT(*) FROM attendance_attachments GROUP BY 2
UNION ALL SELECT 'cliente', extension, COUNT(*) FROM customer_attachments GROUP BY 2;
```

- [ ] Se aparecer extensão fora de pdf/jpg/jpeg/png/xml/docx/xlsx (ex.: `php`,
      `html`, `svg`), registrar no plano e decidir o que fazer com o arquivo.

### 7.4 Links e assets que só aparecem no servidor real *(Plano: M7)*

- [ ] Favicon carrega (inclusive no fallback, sem favicon configurado).
- [ ] Download de anexo de veículo (`vehicles/attachments/<id>`) aponta para
      o caminho certo (diferença de prefixo `public/` entre XAMPP e servidor).

## 8. Smoke test pós-deploy

Com um usuário Administrador e, onde indicado, um usuário Vendedor:

- [ ] Login, navegação pelo menu e logout. Sessão expirada volta para o login.
- [ ] Recuperar senha: o e-mail chega (depende do item 6/C7) e o link só
      funciona uma vez. *(Plano: C6)*
- [ ] Listagens com busca: Veículos, Vendidos, Comprados, Histórico, Pedidos
      de Compra/Venda, Cheques, Clientes. Busca com apóstrofo (`d'a`) não dá
      erro. *(Plano: C2)*
- [ ] Upload, um de cada:
      - anexo de veículo PDF → aceito; arquivo `.php` ou `.html` → recusado com
        mensagem;
      - foto de veículo JPG → aceita e aparece nos 3 tamanhos;
      - anexo de Contas a Pagar e a Receber, de cliente e de atendimento (PDF) → aceitos;
      - logo do sistema PNG → aceito;
      - marca d'água JPG → recusada (só PNG).
- [ ] Baixar um anexo PDF → o navegador **baixa** (não abre como página).
- [ ] Recibo/impressão de parcela de Contas a Receber e a Pagar abre.
      *(Plano: A6, A8)*
- [ ] Como Vendedor: telas administrativas redirecionam para a home.
- [ ] Observações do veículo: as observações antigas aparecem **com a
      formatação** (negrito, listas, tabela). *(Plano: A3)*
- [ ] Recuperar senha com um e-mail que **não** existe → mesma mensagem de
      sucesso de um e-mail existente. Com um e-mail real, o link abre e aceita
      nova senha de 8+ caracteres; com menos, recusa. *(Plano: M4/A4)*
- [ ] Log de erros do PHP/Apache sem erro novo durante o teste.

## 9. Permissões por perfil — antes de liberar os usuários *(Plano: C3)*

Feito pelo **Sandré (Administrador)**, em **Configurações → Menus**, depois do
smoke test e **antes** de passar login e senha aos demais usuários.

- [ ] Para cada perfil — **Gerente Geral, Gerente de Filial, Gerente de Vendas,
      Secretária, Vendedor** — marcar cada tela como **Ativo** ou **Inativo**.
      Hoje os três perfis de Gerente não têm nenhuma tela configurada.
- [ ] Lembrar: para esses 5 perfis, tela em **"Padrão do sistema"** (botão
      azul `?`) fica **bloqueada** — some do menu e, aberta pela URL, volta para a
      Home. Só fica liberado o que estiver **Ativo**. Um perfil sem nada
      configurado só acessa a Home e o próprio "Perfil".
- [ ] **Clientes**: os 5 itens (Comprador, Fornecedor, Vendedor, Colaborador,
      Todos) usam a mesma tela. Desativar um deles **só o tira do menu** — pela
      URL o servidor segue o primeiro (Comprador). Para bloquear Clientes de um
      perfil, desativar o grupo inteiro (ou pelo menos Comprador).
- [ ] Clicar num menu pai (ex.: "Financeiro") aplica o mesmo estado em todos os
      submenus dele.
- [ ] O sistema não deixa o Administrador desativar "Configurações" no próprio
      perfil (aviso na tela). Os perfis Superadm e Desenvolvedor não aparecem
      para o Administrador.
- [ ] Conferir cada perfil entrando com um usuário dele (ou pelo Superadm, com
      "Ver como este usuário"): o menu mostra só o combinado e uma tela
      desativada, aberta pela URL, volta para a Home.
- [ ] Conferir que o botão **"Perfil"** do topo continua abrindo para todos os
      perfis.

## 10. Plano de volta

Usar se qualquer verificação das seções 7, 8 ou 9 falhar e não der para
corrigir na hora:

1. Colocar o sistema em manutenção (ou avisar os usuários).
2. Restaurar o código do backup (ou voltar para o commit anotado na seção 0).
3. Restaurar `src/config/config.php` do backup.
4. **Banco**: só restaurar o dump se alguma migration já tiver rodado, ou se
   houver dado corrompido. Atenção: restaurar o dump **apaga o que foi gravado
   depois do backup** — se usuários já usaram o sistema após o deploy, avaliar
   antes de restaurar.
5. Pastas de upload: só restaurar se algo foi apagado/alterado nelas.
6. Repetir o smoke test (seção 8) na versão restaurada.
7. Registrar no `PLANO-CORRECOES.md` o que falhou.
