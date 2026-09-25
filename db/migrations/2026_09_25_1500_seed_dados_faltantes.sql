-- ============================================================
-- Migration: Seed de dados faltantes (produção já rodando, com dados reais)
-- Data: 2026-09-25
-- Objetivo: a migration 2026_09_25_1000_seed_inicial_producao.sql cobriu o
--   mínimo pra login/menu/cadastro de veículo, mas não cobria várias tabelas
--   de referência que o código lê por ID fixo ou usa em INNER JOIN — sem
--   elas, telas inteiras (Atendimento, Contas a Receber/Pagar, Controle de
--   Cheque, Cartão Digital, Campos Obrigatórios) ficam com warning ou
--   retornam lista vazia mesmo já havendo dado real cadastrado.
-- Tabelas afetadas: digital_card, customer_required_field,
--   network_card_digital, payment_status, form_of_payment, attendance_status,
--   communication_channels, attendance_filter_type, cost_center,
--   standard_contract, status_check.
-- Todo o conteúdo veio do banco de desenvolvimento local (veiculos_repasse),
--   que já tem esses dados corretos e adaptados ao domínio de veículos desde
--   a migração do domínio imobiliário (ver docs/migracao-imobiliario-veiculos.md).
--   INSERTs idempotentes (ON DUPLICATE KEY UPDATE) — seguro rodar mesmo com
--   produção já tendo dados reais em outras tabelas.
--
-- NÃO INCLUÍDO NESTA MIGRATION (decisão do usuário em 2026-09-25):
--   - payment_status id=9 / form_of_payment id=9 ("pago com o crédito do
--     cliente", referenciado em Customer::calculateCustomerCredit() e
--     ajax/CustomerController.php) — não existem em NENHUM ambiente, nem no
--     dev. Não é resíduo desta migração, é gap pré-existente. Não inventado
--     por decisão explícita do usuário — ver relatório separado.
--   - tabela `currencies` — não existe no banco real (nem dev), mas
--     CurrenciesController/Currencies.php/Util::formatMoneyWithCurrency()
--     fazem SELECT nela. É criação de tabela (schema novo), não seed de
--     dado — fora do escopo desta migration por decisão do usuário.
--
-- >>> ROLLBACK (executar manualmente se precisar reverter):
-- DELETE FROM status_check WHERE id IN (1,2,3,4);
-- DELETE FROM standard_contract WHERE id = 17;
-- DELETE FROM cost_center WHERE id IN (14,15,16,17,18,19,20);
-- DELETE FROM attendance_filter_type WHERE id IN (1,2,3);
-- DELETE FROM communication_channels WHERE id IN (1,2,3,4,5,6,7,8,9,10);
-- DELETE FROM attendance_status WHERE id IN (7,8,9,10,11,12,13);
-- DELETE FROM form_of_payment WHERE id IN (1,2,3,4,5,6,7,8);
-- DELETE FROM payment_status WHERE id IN (1,2,3);
-- DELETE FROM network_card_digital WHERE id IN (1,2,3,4,5,6);
-- DELETE FROM customer_required_field WHERE id IN (1,2);
-- DELETE FROM digital_card WHERE id = 1;
-- ============================================================

-- ------------------------------------------------------------
-- 1) digital_card — modelo/template do cartão digital do vendedor.
--    users.card_digital tem DEFAULT 1, ou seja, todo usuário já nasce
--    apontando pra esse id; CardPDFController.php:28 faz
--    getItemById8161($user->card_digital) sem checar se veio vazio.
--    NOTA: capa_fundo/capa_logo zerados de propósito — o dev tem um plano
--    de fundo já enviado (arquivo físico em img/card_digital/1/), mas esse
--    arquivo não existe no servidor novo; melhor começar sem imagem do que
--    apontar pra um arquivo inexistente. As cores/fontes vêm do dev.
--    NOTA 2: bug de UI encontrado à parte (não corrigido aqui) — o botão
--    "Adicionar" da tela de listagem está comentado no HTML
--    (src/view/digital-card/index.php:14), então mesmo com este template
--    existindo, a tela continua parecendo "só edição" pra quem usa.
-- ------------------------------------------------------------
INSERT INTO digital_card
  (id, name, cor_fundo, cor_fonte_usuario, cor_fonte_ocupacao, cor_fonte_creci,
   cor_fonte_imobiliaria, cor_fonte_legenda, cor_borda_usuario, cor_fundo_logo,
   cor_borda_icone, fonte_usuario, tamanho_fonte_usuario, fonte_ocupacao,
   tamanho_fonte_ocupacao, fonte_creci, tamanho_fonte_creci, fonte_imobiliaria,
   tamanho_fonte_imobiliaria, fonte_legenda, tamanho_fonte_legenda,
   capa_fundo, cont_fundo, ext_fundo, capa_logo, cont_logo, ext_logo,
   status, created_by)
VALUES
  (1, 'Cartão Padrão', '#3c8dbc', '#9a2eb5', '#23e06e', '#c42929',
   '#239421', '#ffffff', '#000000', '#ffffff',
   '#ffffff', 'gotham', 100, 'lato-black',
   63, 'gotham', 37, 'lato-black',
   100, 'gotham', 50,
   0, 0, NULL, 0, 0, NULL,
   1, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);

-- ------------------------------------------------------------
-- 2) customer_required_field — RequiredFieldSettingsController::index()
--    busca por ID fixo (1=cliente, 2=cônjuge) e o handleSubmit só faz
--    UPDATE, nunca INSERT. Sem essas 2 linhas, todo campo da tela
--    "Campos Obrigatórios" vira warning (era exatamente o problema
--    reportado). Valores: nenhum campo obrigatório por padrão (0), igual
--    ao estado inicial do dev — ajustar depois pela própria tela.
-- ------------------------------------------------------------
INSERT INTO customer_required_field
  (id, name, nationality, rg, cpf, cellphone, email, phone, cep, zip,
   neighborhood, address, number_address, complement, birth_date, updated_by, branches)
VALUES
  (1, 'customer', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1),
  (2, 'spouse', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ------------------------------------------------------------
-- 3) network_card_digital — User::submitAddForm() (model/User.php:79)
--    percorre TODA essa tabela pra criar automaticamente uma linha em
--    user_networks pra cada rede, sempre que um usuário novo é cadastrado.
--    cardPDF/index.php também assume ids fixos (3=Email, 4=WhatsApp) pra
--    montar o link do ícone. Sem essa tabela, usuário novo nunca ganha
--    linhas de rede social pra preencher no cartão digital.
-- ------------------------------------------------------------
INSERT INTO network_card_digital (id, name, status, filename) VALUES
  (1, 'Facebook', 1, 'facebook'),
  (2, 'Instagram', 1, 'instagram'),
  (3, 'Email', 1, 'email'),
  (4, 'WhatsApp', 1, 'whatsapp'),
  (5, 'Linkedln', 1, 'linkedln'),
  (6, 'Twitter', 1, 'twitter')
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);

-- ------------------------------------------------------------
-- 4) payment_status — INNER JOIN em BillReceiveInstallment (model, linha
--    ~39) e equivalente de Contas a Pagar. Com a tabela vazia, TODA
--    listagem de parcela (a receber ou a pagar) volta zero linhas, mesmo
--    já existindo conta/parcela real cadastrada — é o achado mais grave
--    da varredura. Não existe tela administrativa pra essa tabela
--    (nenhum PaymentStatusController no projeto).
--    id=9 ("pago com crédito") deixado de fora, ver nota no topo do arquivo.
-- ------------------------------------------------------------
INSERT INTO payment_status (id, name, status) VALUES
  (1, 'Aguardando Pagamento', 1),
  (2, 'Pago', 1),
  (3, 'Cancelado', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);

-- ------------------------------------------------------------
-- 5) form_of_payment — id=1 ("Boleto") e id=3 ("Cheque") são comparados
--    por número fixo em CheckControlController (ajax, linha ~52) e
--    BillsToPayInstallmentController.php:646. id=9 ("pago com crédito")
--    deixado de fora, ver nota no topo do arquivo.
-- ------------------------------------------------------------
INSERT INTO form_of_payment
  (id, name, status, form_payment_sale, restricted, form_payment_accounts_payable)
VALUES
  (1, 'Boleto', 1, 1, 1, 1),
  (2, 'Transferência Bancária', 1, 1, 1, 1),
  (3, 'Cheque', 1, 1, 1, 1),
  (4, 'Financiamento', 1, 1, 1, 0),
  (5, 'Pix', 1, NULL, NULL, 1),
  (6, 'Dinheiro', 1, NULL, NULL, 1),
  (7, 'Cartão de Crédito', 1, NULL, NULL, 1),
  (8, 'Cartão de Débito', 1, NULL, NULL, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);

-- ------------------------------------------------------------
-- 6) attendance_status — INNER JOIN em Attendance (model, linha ~33).
--    Com a tabela vazia, TODA listagem de Atendimento volta zero linhas,
--    e o <select> de status no formulário de novo atendimento fica vazio
--    (impossível criar atendimento). ids 10/11 também comparados por
--    número fixo em AttendanceController.php:236 (bloqueia edição quando
--    concluído/cancelado) — por isso os ids abaixo são os mesmos do dev,
--    não uma sequência 1..7 nova.
-- ------------------------------------------------------------
INSERT INTO attendance_status (id, name, status, icon_status, box_color, `order`, standard_filter) VALUES
  (7, 'Aberto', 1, 'far fa-comment-dots', '#11a0de', 1, 1),
  (8, 'Negociando', 1, 'fas fa-comments-dollar', '#ff0422', 4, 1),
  (9, 'Fechado', 1, 'far fa-handshake', '#0b1194', 5, 1),
  (10, 'Concluído', 1, 'fas fa-check-double', '#15940a', 6, 0),
  (11, 'Cancelado', 1, 'far fa-times-circle', '#5c5c5c', 7, 0),
  (12, 'Em Andamento', 1, 'fas fa-briefcase', '#f5ed37', 2, 1),
  (13, 'Aguardando Terceiros', 1, 'fas fa-pause', '#fa7210', 3, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);

-- ------------------------------------------------------------
-- 7) communication_channels — INNER JOIN em Attendance (model, linha ~28),
--    mesmo efeito de "lista sempre vazia" que attendance_status.
-- ------------------------------------------------------------
INSERT INTO communication_channels (id, name, status) VALUES
  (1, 'Cliente / Fluxo', 1),
  (2, 'Cliente / Prospecção', 1),
  (3, 'Facebook', 1),
  (4, 'Indicação', 1),
  (5, 'Instagram', 1),
  (6, 'Jornal', 1),
  (7, 'Ligação', 1),
  (8, 'Site', 1),
  (9, 'Rádio', 1),
  (10, 'Outros', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);

-- ------------------------------------------------------------
-- 8) attendance_filter_type — INNER JOIN em Attendance.php:363/383 (aba
--    "Interesses" do atendimento). ids 2 e 3 comparados por número fixo em
--    AttendanceController.php:651/658.
-- ------------------------------------------------------------
INSERT INTO attendance_filter_type (id, name, status) VALUES
  (1, 'Marca e Modelo', 1),
  (2, 'Faixa de Preço', 1),
  (3, 'Ano/Km', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);

-- ------------------------------------------------------------
-- 9) cost_center — INNER JOIN em BillReceiveInstallment (via bill_receive.
--    id_cost_center) e equivalente de Contas a Pagar — mesmo efeito de
--    "lista sempre vazia" do achado #4, em cadeia (falta ESSA tabela E
--    payment_status simultaneamente pra qualquer parcela aparecer).
--    Hierarquia já adaptada ao domínio de veículo pelo próprio usuário no
--    dev (não é resíduo de imóvel): "A pagar"/"A receber" como raízes,
--    com "Compra de Veículos"/"Comissão"/"Venda de veículo"/
--    "Comissão Fácilvel" como filhos.
-- ------------------------------------------------------------
INSERT INTO cost_center (id, name, id_father, status, created_by, id_type) VALUES
  (18, 'A pagar', 0, 1, 1, 1),
  (19, 'A receber', 0, 1, 1, 2),
  (14, 'Venda de veículo', 19, 1, 1, 2),
  (15, 'Comissões', 14, 1, NULL, 2),
  (16, 'Compra de Veiculos', 18, 1, 1, 1),
  (17, 'Comissão', 18, 1, 1, 1),
  (20, 'Comissão Fácilvel', 19, 1, 1, 2)
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);

-- ------------------------------------------------------------
-- 10) standard_contract — só o id=17 ("Recibo Simples"), o único template
--     realmente em uso hoje (ver
--     db/migrations/2026_09_21_1010_desativar_standard_contract_imovel.sql,
--     que desativou os outros 8 por serem resíduo de imóvel/teste — não
--     estão sendo reproduzidos aqui de propósito). Usado no botão "Gerar
--     Recibo" de Contas a Receber/Pagar
--     (BillReceiveInstallmentController.php:477/967 e equivalente de
--     Contas a Pagar) — sem essa linha, o dropdown de template fica vazio
--     e a geração de recibo em PDF não funciona.
-- ------------------------------------------------------------
INSERT INTO standard_contract (id, name, text, type_contract, status, created_by) VALUES
  (17, 'Recibo Simples', '<p>Recibo refente a parcela n&ordm; {%numero_parcela%}.</p>\r\n\r\n<p>Favorecido: <strong>{%nome_filial%}</strong>.</p>\r\n\r\n<p>Valor: <strong>{%valor_parcela%}</strong>.</p>\r\n\r\n<p>Data de pagamento: <strong>{%data_pagamento%}</strong>.</p>\r\n\r\n<p>Forma de pagamento: <strong>{%forma_pagamento%}</strong>.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>{%cidade%}, {%data_atual%}.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>_________________________________________</p>\r\n\r\n<p>{%nome_filial%}</p>\r\n', 4, 1, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);

-- ------------------------------------------------------------
-- 11) status_check — dropdown de status do Controle de Cheque
--     (CheckControlController.php:57/323, `(new StatusCheck)->getWithFiltersAllItems()`).
--     Sem essas linhas, o formulário de trocar status de um cheque fica
--     sem opção nenhuma pra escolher (a listagem em si não quebra, o valor
--     já gravado no cheque é só um int, mas não dá pra trocar via tela).
-- ------------------------------------------------------------
INSERT INTO status_check (id, name, created_by, status) VALUES
  (1, 'Aberto', 1, 1),
  (2, 'Compensado', 1, 1),
  (3, 'Repassado', 1, 1),
  (4, 'S/ Fundo', 1, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);
