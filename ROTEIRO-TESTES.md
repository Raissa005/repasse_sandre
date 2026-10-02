# Roteiro de testes manuais — homologação

Junta, num lugar só, todos os testes manuais que ficaram pendentes no
`PLANO-CORRECOES.md` (gravações que não foram executadas durante as correções
para não escrever no banco, mais os testes pedidos em cada item). Cada teste
indica o item de origem do plano entre colchetes, ex.: **[C5]**.

- **Parte A — Dinheiro**: pagamentos, estornos, cheques, lançamentos e
  impressões financeiras. Fazer **primeiro**.
- **Parte B — Por perfil, e dentro de cada perfil por tela**.
- **Parte C — Matriz rápida de acesso** (o que cada perfil abre ou não).

O smoke test do dia do deploy continua no `DEPLOY-CHECKLIST.md` §8; este
roteiro é a homologação completa, antes dele.

---

## 0. Preparação

- [ ] Ambiente de **homologação** com banco de **teste** (nunca produção com
      dados reais: quase todos os testes abaixo gravam).
- [ ] Migrations aplicadas nesse banco, inclusive
      `2026_10_02_0921_criar_tabela_login_attempts.sql` (sem ela o login quebra).
- [ ] `src/config/config.php` com `JWT_KEY` preenchida (64 caracteres hex).
- [ ] `composer install` feito (precisa de `ezyang/htmlpurifier` e `dompdf/dompdf` 3.1.x).
- [ ] Pasta `storage/dompdf-fonts/` gravável pelo usuário do Apache.
- [ ] Um usuário de cada perfil, ativo. No banco local de dev já existem:
      Superadm (id 1, "Suporte Ydeal"), Administrador (id 6), Gerente Geral
      (id 8), Secretária (id 11), Vendedor (ids 12 e 13). Gerente de Filial e
      Gerente de Vendas: criar, se for testar esses perfis.
- [ ] Um e-mail **de teste** para o bloqueio de login (fica 15 min bloqueado).
- [ ] Deixar o log de erros do PHP/Apache aberto durante os testes. Avisos já
      conhecidos (ver N16) **não** contam como falha: `vehicles/editItem`,
      `vehicles/vehicleCosts`, `vehicles/attachments`, `report-dre`, listagem de
      `attendance` e listagem de `check-control`.

### Kit de arquivos de teste

| Arquivo | Para quê |
|---|---|
| `teste.pdf` (pequeno, válido) | anexos — aceito |
| `teste.jpg` e `teste.png` (fotos comuns, ex. 800×600) | anexos, fotos, logos — aceitos |
| `transparente.png` (PNG com fundo transparente, ex. 400×200) | marca d'água |
| `teste.xml` (começando com `<?xml ...?>`), `teste.docx`, `teste.xlsx` | anexos — aceitos |
| `teste.php` com o conteúdo `<?php echo 1;` renomeado para `falso.jpg` | deve ser recusado |
| `foto.php.jpg` (JPG verdadeiro com esse nome) | deve ser recusado |
| `pagina.html`, `imagem.svg`, `pacote.zip`, `foto.heic`, `texto.txt` | devem ser recusados |
| imagem de **mais de 2 MB** | logos/avatar/cartão — recusada pelo tamanho |
| foto de **mais de 8 MB** | foto de veículo — recusada pelo tamanho |
| PDF de **mais de 20 MB** | anexos — recusado pelo tamanho |

Mensagens esperadas na recusa:
- tipo: `O arquivo "<nome>" não é de um tipo aceito. Tipos aceitos: ...`
- tamanho: `O arquivo "<nome>" é maior que o limite de N MB por arquivo. ...`
- sem arquivo: `Nenhum arquivo foi escolhido. Selecione o arquivo e depois clique em "Adicionar".`

---

## Parte A — Dinheiro (fazer primeiro)

Perfil: **Administrador**, salvo quando indicado. Antes de cada bloco, anotar
os valores da parcela/conta (valor, vencimento, status), para comparar depois.

### A.1 Financeiro → Contas Receber (parcela)

Registrar pagamento **[C5]** — a gravação nunca foi executada durante as correções.
- [ ] **Pagamento integral**: abrir uma parcela em aberto → "Pagar Parcela" com o
      valor total, forma de pagamento comum (ex. PIX/dinheiro) → mensagem de
      sucesso; parcela fica paga com o valor e a data informados; **nenhuma**
      parcela nova é criada.
- [ ] **Pagamento parcial**: outra parcela em aberto → pagar **menos** que o
      valor → a parcela original fica paga com o valor pago, e aparece uma
      **parcela nova** com a diferença (botão "Ir para nova Parcela"). Conferir:
      valor original = valor pago + valor da nova parcela.
- [ ] **Pagamento com cheque**: pagar uma parcela com forma "cheque" →
      o cheque aparece em Financeiro → Controle Cheques, com a linha do tempo
      registrando o vínculo com a parcela (link para a parcela funciona).
      Botão "Ver Cheque" na parcela abre o cheque certo.
- [ ] Depois de cada pagamento, a lista de Contas Receber e o relatório
      Contas Receber mostram os mesmos valores. Nenhuma parcela "pela metade"
      (paga sem a nova parcela da diferença, ou nova parcela sem a original paga).
- [ ] **Estornar** um dos pagamentos → parcela volta para em aberto; conferir o
      que acontece com a parcela da diferença (anotar o comportamento).
- [ ] **Cancelar Parcela** e depois **Ativar Parcela** → status muda e volta.
- [ ] Não testar forma de pagamento 9 ("crédito do cliente"): ela não existe em
      nenhum banco (decisão registrada no seed de 25/09).

Recibo **[A6] [M11]**
- [ ] "Imprimir Recibo" com o modelo **Recibo Simples** para cliente **PF** →
      recibo abre completo (nome, documento, valor, filial).
- [ ] Mesmo recibo para cliente **PJ** → abre completo, sem campos quebrados.
- [ ] Modelo inválido: na tela do recibo, abrir o DevTools, trocar o `value` da
      opção do modelo para outro número (ex. `5`) e imprimir → aviso
      `Modelo de recibo inválido.` e volta para a parcela.

Anexos da parcela **[C4] [M6] [N1] [N11]**
- [ ] Anexar `teste.pdf` → aceito, aparece na lista; clicar → o navegador **baixa**.
- [ ] Anexar `teste.jpg` → aceito; abre no navegador.
- [ ] Clicar "Adicionar" **sem escolher arquivo** → mensagem "Nenhum arquivo foi
      escolhido..." e **nenhum** registro novo na lista.
- [ ] `falso.jpg`, `foto.php.jpg`, `pagina.html` → recusados com a mensagem de tipo.
- [ ] PDF > 20 MB → recusado com a mensagem de tamanho.
- [ ] Excluir um anexo → some da lista.

### A.2 Financeiro → Contas Pagar (parcela)

- [ ] **Pagar com cheque recebido** (usa a tabela `linked_check_control`):
      "Pagar Parcela" → "Cheques Recebidos" → escolher um cheque ativo → pagar.
      A parcela fica paga e o cheque aparece vinculado a ela.
- [ ] **Estornar** esse pagamento → parcela volta para em aberto e o cheque é
      desvinculado (fica disponível de novo na lista de cheques recebidos).
- [ ] Pagamento integral e parcial, como em A.1 (conferir a parcela da diferença).
- [ ] "Cancelar Parcelas" / "Cancelar Parcela" e "Ativar Parcela".
- [ ] Recibo PF, PJ e modelo inválido, como em A.1 **[A6] [M11]**.
- [ ] Anexos, os mesmos 6 casos de A.1 **[C4] [M6] [N1] [N11]**.

### A.3 Financeiro → Controle Cheques

- [ ] **Inativar** um cheque pela listagem → some da lista de ativos e aparece em
      inativos; **Ativar** → volta, e a tela fica na lista de inativos **[N10]**.
- [ ] Com o cheque **inativo**, abrir "Pagar Parcela" (Contas Receber ou Pagar)
      → ele **não** aparece entre os cheques disponíveis **[N10]**.
- [ ] **Cheque devolvido**: na edição do cheque, registrar a devolução → é criada a
      parcela/conta correspondente e a linha do tempo registra com link
      funcionando **[C2 lote 3]**.
- [ ] Cadastrar e editar um cheque (Administrador) → grava normalmente **[C2 lote 3]**.
- [ ] Comentário na linha do tempo com o texto `<b>teste</b> <script>alert(1)</script>`
      → aparece **como texto**, sem alerta; links que o próprio sistema grava
      (para a parcela) continuam clicáveis **[A3]**.
- [ ] Excluir comentário da linha do tempo: como **Superadm** → exclui; como
      **Administrador**, o botão não aparece **[C2 lote 3]**.
- [ ] Busca: pelo titular (`owner_check`) e pelo nome de quem repassou → acha;
      termo com apóstrofo (`d'a`) → lista vazia, sem erro **[C2 lote 3]**.

### A.4 Pedidos → Pedidos Venda / Pedidos Compra (Financeiro, Comissão, impressões)

Repetir em **Venda** e em **Compra**.
- [ ] Aba **Financeiro**: lançar a conta (a receber na venda, a pagar na compra)
      e gerar as parcelas → gravam, e aparecem em Contas Receber/Pagar **[N8]**.
- [ ] Aba **Comissão**: lançar comissão e parcelas → gravam **[N8]**.
- [ ] Impressões **com** lançamento: parcelas (Financeiro), parcelas do veículo
      (Dados Gerais) e comissão → abrem com as parcelas **[N9]**.
- [ ] Impressões **sem** lançamento (pedido sem financeiro/comissão; abrir a URL
      da impressão direto) → aviso "Este pedido não tem financeiro lançado /
      comissão lançada para imprimir." e volta para a aba **[N9]**.
- [ ] Impressão com id inexistente (ex. `.../installmentPrinting/999`) →
      "Pedido não encontrado." **[N9]**.
- [ ] Aba **Veículos**: adicionar, editar e **excluir** um veículo do pedido →
      o botão excluir funciona (antes não funcionava) **[A7] [N8]**.
- [ ] Editar, Inativar e Ativar o pedido pela listagem **[N8]**.
- [ ] Busca com apóstrofo e com `x' OR '1'='1` → lista vazia, sem erro **[C2 lote 2]**.

### A.5 Financeiro → Lançamentos Pagar / Lançamentos Receber e relatórios

- [ ] Criar um lançamento com descrição `<img src=x onerror=alert(1)> teste`
      → na edição (textarea), no relatório e na impressão a descrição aparece
      **como texto**, sem alerta **[A3]**.
- [ ] Relatórios → **Contas Receber**: o número da parcela abre
      `bill-receive-installment/edit/{id}` (a parcela a **receber**) **[A8]**.
- [ ] Relatórios → **Contas Pagar**: o número abre a parcela a pagar.
- [ ] "Imprimir Relatório" dos dois, com filtro de data → abre com as mesmas linhas.

### A.6 Relatório DRE e custos do veículo

- [ ] Administrador: **Relatório DRE** aparece no menu e abre **[A5/M2]**.
- [ ] Veículos → abrir um veículo → aba **Compra**: registrar a compra → grava **[M2]**.
- [ ] Aba **Custos**: adicionar, editar e excluir um custo → gravam **[M2]**.
- [ ] Cadastros → **Centro Custo**: cadastrar, desativar e ativar **[A5]**.

### A.7 Dinheiro — bloqueios para quem não é admin

Como **Vendedor** e como **Secretária** (digitando a URL):
- [ ] `report-dre` → volta para a Home.
- [ ] `bill-receive-installment`, `bills-to-pay-installment`, `check-control` → Home.
- [ ] Pedido aberto: `sale-requests/saleFinancial/{id}`,
      `sale-requests/saleCommission/{id}`, `sale-requests/installmentPrinting/{id}`
      (e os equivalentes de compra) → Home **[N8]**.
- [ ] Veículo: `vehicles/purchaseVehicles/{id}` e `vehicles/vehicleCosts/{id}` → Home **[M2]**.

---

## Parte B — Por perfil

### B.1 Sem login (navegador anônimo)

Login / bloqueio **[A4]**
- [ ] Com o e-mail **de teste**, errar a senha 4 vezes → "E-mail ou senha invalido!".
- [ ] 5ª vez → "Muitas tentativas de login. Tente novamente em 15 minutos ou use
      "Esqueci a senha"."
- [ ] Durante o bloqueio, a senha **certa** também é recusada com a mesma mensagem.
- [ ] Um e-mail que **não existe**, errado 5 vezes → mesma mensagem de bloqueio
      (não revela que o e-mail não existe).
- [ ] Esperar 15 min (ou concluir a recuperação de senha, abaixo) → login volta a funcionar.
- [ ] Login certo logo depois de 2 erros → entra (o contador zera).

Recuperação de senha **[C6] [M4] [A4]**
- [ ] "Esqueci a senha" com e-mail inexistente → "Se o e-mail estiver
      cadastrado, você receberá o link de recuperação."
- [ ] Com e-mail existente → a **mesma** mensagem. O e-mail só chega com a conta
      e a senha do SMTP da Ydeal preenchidas (migration
      `2026_10_02_1137_configuracao_email_smtp_ydeal.sql`, DEPLOY-CHECKLIST §6)
      **[C7]**. Sem elas, o link pode ser montado a partir da tabela `tokens`
      (só leitura):
      `SELECT id_unique FROM tokens WHERE email = '...' AND status = 1;` →
      `URL/login/changePassWord?token=<id_unique>`.
- [ ] Nova senha com 7 caracteres → "A senha deve ter pelo menos 8 caracteres."
- [ ] Nova senha com 8+ e confirmação igual → "Senha alterada com successo!"
      e login funciona com ela; se o e-mail estava bloqueado, desbloqueia.
- [ ] Abrir o **mesmo link de novo** → "Link inválido ou não encontrado!".
- [ ] Link com o token adulterado (trocar 1 caractere) → "Link inválido", sem erro fatal.

Rotas que exigem login
- [ ] `cardPDF/index/1` → vai para o login **[N17/N18]**.
- [ ] Sessão expirada: logar, apagar o cookie `PHPSESSID` no DevTools e usar
      qualquer tela que carregue dados por AJAX (ex. trocar de filial no topo)
      → volta para o login **[C1]**.
- [ ] No login, o `Set-Cookie` vem com `HttpOnly; SameSite=Lax` (e `secure` se
      for HTTPS) **[M6]**.

### B.2 Superadm

Usuários / "Ver como" **[C3] [N14]**
- [ ] Usuários → editar um Vendedor → botão **"Ver como este usuário"** aparece →
      clicar → o menu passa a ser o do Vendedor.
- [ ] Durante o "Ver como", digitar `users/turnUser/6` → recusado (Home).
- [ ] Topo → "Retornar às permissões de suporte" → volta para o Superadm.
- [ ] Editar o **próprio** cadastro e trocar o tipo de usuário → aceito.
- [ ] Criar um usuário Administrador com senha de 7 caracteres → recusado;
      com 8 → criado **[M4]**.

Controle de Cheques
- [ ] Excluir comentário da linha do tempo → funciona (ver A.3).

Moedas **[M12]**
- [ ] `currencies` → "A tela de Moedas não está disponível." e Home.

### B.3 Administrador

Configurações → Configuração → aba **Menus** (permissões) **[C3] [N15]**
- [ ] A lista de perfis **não** mostra Superadm nem Desenvolvedor.
- [ ] Escolher **Vendedor** → clicar no pai **Financeiro** → todos os submenus de
      Financeiro ficam com o **mesmo** estado do pai (antes, cada um invertia).
- [ ] Clicar de novo → todos voltam juntos.
- [ ] No **próprio** perfil (Administrador), tentar desativar **Configurações**
      → aviso "Não é possível desativar "Configurações" para o seu próprio perfil."
- [ ] Desativar uma tela para Vendedor → logar como Vendedor (ou Superadm "Ver
      como") → a tela some do menu e, pela URL, volta para a Home.
- [ ] Reativar o que foi desativado no teste.
- [ ] Atenção (achado nesta verificação): as telas **Veículos Vendidos, Veículos
      Comprados, Pedidos de Venda (relatório)** e **Usuários** exigem Administrador
      no próprio código, e **Histórico do Veículo** exige Secretária ou acima.
      Ativar essas telas para o Vendedor (ou Usuários para a Secretária) faz o
      link aparecer no menu, mas ao clicar volta para a Home. Hoje o banco local
      já tem esses itens liberados no menu desses perfis.

Usuários **[N14] [M4]**
- [ ] Criar usuário com tipo Superadm (alterando o `value` no DevTools) →
      "Tipo de usuário não permitido." ou "Sem permissão para alterar usuários
      Superadm ou Desenvolvedor."
- [ ] Editar, inativar ou ativar o usuário Superadm (id 1) pela URL → recusado.
- [ ] Editar o próprio cadastro trocando o tipo (DevTools) → "Você não pode
      alterar o próprio tipo de usuário."
- [ ] Editar um Vendedor com senha nova de 8+ → salva; deixar a senha em branco
      → salva sem mudar a senha.

Filiais **[A5] [A9]**
- [ ] **Filiais** aparece no menu → editar "Repasse Sandré" → trocar e-mail e
      CNPJ → salva (em produção: preencher os dados reais, DEPLOY-CHECKLIST §8).
- [ ] Logos da filial (3 campos): `teste.jpg` 800×600 → aceito e exibido no
      tamanho certo; `teste.png` → aceito; `falso.jpg` → recusado; imagem > 2 MB →
      recusada **[C4] [M6] [N12]**.

Configurações → Configuração (logos) **[C4] [M6] [N12]**
- [ ] Cada um dos 5 campos de logo: JPG fora da dimensão exata → aceito e
      exibido recortado no tamanho certo (antes dava erro fatal); PNG → aceito;
      arquivo inválido → recusado sem apagar a logo atual.

Marca d'água **[C4] [N12]**
- [ ] `transparente.png` → aceita; a transparência é mantida (fundo não fica preto/branco).
- [ ] `teste.jpg` → recusada (só PNG).
- [ ] Foto de veículo enviada depois disso → sai com a marca d'água nova.

Cadastros → Cartão Digital (modelos) **[C4] [N12] [N18]**
- [ ] Cadastrar e editar um modelo com fundo e logo JPG/PNG fora da dimensão
      exata → aceitos.
- [ ] Modelo com fundo **AVIF** antigo (o modelo 1 do banco local) → reenviar o
      fundo em JPG/PNG; sem isso o fundo não sai no PDF (DEPLOY-CHECKLIST §8).
- [ ] Gerar o cartão de **outro** usuário (`cardPDF/index/12`) → PDF abre **[N17]**.

Veículos **[C4] [M6] [N11] [N13] [A3]**
- [ ] Aba **Fotos**: `teste.jpg` → aceita e aparece nos 3 tamanhos (com marca
      d'água); foto > 8 MB → recusada; `falso.jpg` → recusada.
- [ ] Aba **Anexos**: `teste.pdf`, `teste.xml`, `teste.docx`, `teste.xlsx` →
      aceitos; `pagina.html`, `pacote.zip`, `foto.heic` → recusados;
      "Adicionar" sem arquivo → mensagem e nada gravado; depois de adicionar,
      volta para a aba Anexos (antes ia para uma rota inexistente).
- [ ] Excluir um anexo (lixeira) → exclui.
- [ ] Clicar num anexo PDF → o navegador **baixa** (não abre como página).
- [ ] Aba **Observações**: as observações antigas aparecem **com a formatação**
      (negrito, listas, tabela); salvar uma nova com negrito e tabela pelo editor
      → mantida; colar pelo "Código-fonte" `<script>alert(1)</script>` → não executa.
- [ ] Histórico do Veículo → observação de transferência: mesmo teste do editor.
- [ ] Descrição de anexo/foto com `"><script>alert(1)</script>` → aparece como texto.

Clientes **[C4] [M6] [N1] [A3]**
- [ ] Aba Logo: JPG/PNG aceitos (recortados no tamanho), inválido recusado.
- [ ] Aba Anexos: PDF aceito; sem arquivo → mensagem clara; `.html` recusado.
- [ ] Observação do cliente com HTML → aparece como texto.
- [ ] A aba **Vendas** não aparece na navegação do cliente **[M1]**.

Atendimentos **[N1] [N2] [A3] [A10] [M2]**
- [ ] Anexar PDF a um atendimento → aceito; sem arquivo → mensagem; inválido → recusado.
- [ ] Excluir um anexo pela lixeira → abre modal de confirmação e exclui (antes
      dava erro fatal). Os anexos antigos sem arquivo (nº 3 e 4 do banco local)
      podem ser excluídos assim.
- [ ] Comentário na linha do tempo e descrição com `<b>x</b><script>alert(1)</script>`
      → aparecem como texto.
- [ ] Aba **Interesses**, filtro **Marca e Modelo**: adicionar uma marca/modelo e
      depois uma **segunda** → as duas gravam (antes a 2ª dava erro fatal).
- [ ] Alterar pelo DevTools o `value` da marca para `1' OR '1'='1` → aviso de
      erro e nada gravado.

Notificações **[A2] [M3]**
- [ ] Sino do topo → clicar numa notificação → abre o detalhe (antes erro fatal).
      Hoje só o módulo Lead (desativado) cria notificações; se o banco de
      homologação não tiver nenhuma, registrar "sem dados para testar".
- [ ] `notification/index/999` → "Notificação não encontrada." e volta para a lista.

Outros
- [ ] Usuários → editar o próprio perfil → **avatar** JPG fora da dimensão →
      aceito e aparece no topo (antes o arquivo era gravado com nome errado e a
      foto não aparecia) **[C4] [N12]**.
- [ ] Busca com apóstrofo (`d'a`) em Veículos, Vendidos, Comprados, Histórico,
      Pedidos, Cheques, Clientes → sem erro **[C2]**.
- [ ] Busca com `"><script>alert(1)</script>` → o campo de busca mostra o texto,
      sem alerta; paginação continua levando o filtro para a página 2 **[N6]**.
- [ ] "Imprimir Relatório" de Vendidos/Comprados/Histórico com filtro → abre com
      o mesmo filtro **[N6]**.

### B.4 Gerente Geral / Gerente de Filial / Gerente de Vendas

Antes de o Sandré configurar as permissões (DEPLOY-CHECKLIST §9):
- [ ] O menu lateral fica **vazio** (só Home); botão "Perfil" do topo abre o
      próprio cadastro **[C3]**.
- [ ] Qualquer tela pela URL (ex. `vehicles`, `customer`) → Home.
- [ ] `users/turnUser/1` → Home, sessão continua a do Gerente **[C3]**.

Depois de configurar (repetir para cada gerente):
- [ ] O menu mostra só as telas ativadas; uma tela desativada, pela URL → Home.

### B.5 Secretária

Atendimentos **[M8]**
- [ ] Cadastrar atendimento escolhendo **outro** vendedor como responsável →
      grava com esse vendedor; sem escolher → fica com a própria Secretária.
- [ ] Editar um atendimento e reatribuir para outro vendedor → grava; deixar o
      campo vazio → mantém o responsável atual.

Relatórios **[N4]**
- [ ] Histórico do Veículo → abre; "Imprimir Relatório" → abre.

Bloqueios **[C3] [M2] [N8] [N13]**
- [ ] Telas fora do menu dela, pela URL (Financeiro, DRE, Configurações,
      Filiais, Atendimentos) → Home.
- [ ] Veículo: abas Compra e Custos pela URL → Home.
- [ ] `vehicles/handleDeleteAttachment/{veículo}/{anexo}` → Home, anexo continua.
- [ ] `settings/menus` → Home.
- [ ] `networks-site`, `lead`, `lead-config` → Home **[N17]**.

### B.6 Vendedor

Perfil e cartão digital **[N14] [N18] [N17] [A2]**
- [ ] Botão "Perfil" do topo → abre e salva o próprio cadastro (sem trocar tipo).
- [ ] Trocar o tipo no DevTools para `1` e salvar → "Você não pode alterar o
      próprio tipo de usuário." e continua Vendedor.
- [ ] Aba **Cartão Digital** → enviar foto JPG fora da dimensão → aceita; enviar
      de novo → a foto antiga é apagada (não sobra arquivo) **[N12]**.
- [ ] "Gerar Cartão Digital" → abre o PDF do **próprio** cartão, com foto, fontes
      e redes.
- [ ] Nome no cadastro com `<b>João</b>` → no PDF aparece o texto literal.
- [ ] `cardPDF/index/13` (outro vendedor) → Home.
- [ ] `cardPDF/index/1abc` → Home.

Atendimento **[A10] [M8]**
- [ ] Cadastrar atendimento → fica com o próprio Vendedor (o campo de
      responsável não aparece).
- [ ] Interesses → duas marcas/modelos no mesmo atendimento → gravam.

Bloqueios **[C3] [C1] [M2] [N8] [N13] [N14]**
- [ ] `users/turnUser/1` → Home, continua Vendedor.
- [ ] `settings/menus` e `settings/updateMenu/4/1` → Home, nada muda.
- [ ] `networks-site`, `lead`, `lead-config` → Home **[N17]**.
- [ ] Telas administrativas pela URL (Financeiro, DRE, Configuração, Filiais,
      Bancos, Centro Custo, Marca d'água, Pedidos Compra) → Home.
- [ ] Pedido de venda aberto pela URL: abas Financeiro/Comissão e impressões de
      parcelas → Home.
- [ ] Botão de excluir veículo dentro do pedido → desabilitado.
- [ ] `vehicles/handleDeleteAttachment/{veículo}/{anexo}` → Home.
- [ ] Busca com apóstrofo nas listagens a que tem acesso → sem erro.

---

## Parte C — Matriz rápida de acesso (pela URL, antes do §9 do checklist)

`✓` abre · `→H` volta para a Home. Situação do banco local; depois que o Sandré
configurar as permissões, a coluna de cada perfil passa a ser a configuração dele.

| Tela (rota) | Superadm | Admin | Gerentes | Secretária | Vendedor |
|---|---|---|---|---|---|
| Home, "Perfil" (`users/edit-item/{próprio}`) | ✓ | ✓ | ✓ | ✓ | ✓ |
| Veículos (`vehicles`) | ✓ | ✓ | →H | ✓ | ✓ |
| Abas Compra/Custos do veículo | ✓ | ✓ | →H | →H | →H |
| Clientes (`customer`) | ✓ | ✓ | →H | ✓ | ✓ |
| Atendimentos (`attendance`) | ✓ | ✓ | →H | →H | ✓ |
| Pedidos Venda (`sale-requests`) | ✓ | ✓ | →H | ✓ | ✓ |
| Pedidos Compra (`purchase-requests`) | ✓ | ✓ | →H | ✓ | →H |
| Abas Financeiro/Comissão do pedido | ✓ | ✓ | →H | →H | →H |
| Financeiro (Contas, Lançamentos, Cheques) | ✓ | ✓ | →H | →H | →H |
| Relatório DRE | ✓ | ✓ | →H | →H | →H |
| Vendidos / Comprados / Pedidos de Venda (relatórios) | ✓ | ✓ | →H | →H | →H |
| Histórico do Veículo | ✓ | ✓ | →H | ✓ | →H |
| Usuários (lista) | ✓ | ✓ | →H | →H | →H |
| Configurações → Menus | ✓ | ✓ | →H | →H | →H |
| Filiais | ✓ | ✓ | →H | →H | →H |
| Redes sociais (`networks-site`) | ✓ | ✓ | →H | →H | →H |
| Moedas (`currencies`) | →H | →H | →H | →H | →H |
| `cardPDF/index/{próprio id}` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `cardPDF/index/{outro id}` | ✓ | ✓ | →H | →H | →H |
