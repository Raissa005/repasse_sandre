<div id="generic-message-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn" id="btn-confirm" data-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>

<div id="add-installment-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center" id="myModalLabel">Adicionar Parcela</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_form_of_payment">Forma de Pagamento <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_form_of_payment" id="id_form_of_payment" required>
                                <?php foreach ($formOfPayments as $payment) { ?>
                                    <option value="<?= $payment->id ?>"><?= $payment->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="commissionTrue" value="<?= isset($commission) && $commission == 1 ? '1' : '0' ?>">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="value_of_installments">Valor <span class="text-danger">*</span></label>
                            <input type="text" id="value_of_installments" name="value_of_installments" class="form-control" data-mask-money required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cost_center">Centro de Custo <span class="text-danger">*</span></label>
                            <select id="cost_center" name="cost_center" class="form-control" required>
                                <option value="">Selecione</option>
                                <?php foreach($costCenters as $cost){ ?>
                                    <option value="<?= $cost->id ?>"><?= $cost->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php if(isset($commission) && $commission == 1){ ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="purchaseBroker">Repassador <span class="text-danger">*</span></label>
                                <select name="purchaseBrokerId" id="purchaseBrokerId" class="form-control" require>
                                    <option value="">Selecione um vendedor...</option>
                                    <?php foreach ($purchasingBrokers as $purchasingBroker) { ?>
                                        <option value="<?= $purchasingBroker->id ?>" <?= isset($billsToPay) && $billsToPay->id_customer == $purchasingBroker->id ? 'selected' : ' '?>><?= $purchasingBroker->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="due_date">Data de Vencimento <span class="text-danger">*</span></label>
                            <input type="date" id="due_date" name="due_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Descrição </label>
                            <textarea name="description" id="description" class="form-control" rows="4" style="resize: none;"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                </div>
                <button type="submit" class="btn btn-primary" id="btn-submit">Cadastrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="sellerCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Vendedores / Fornecedores</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="pageSellerCustomerJax" value="1">
                    <div class="col-md-4 col-lg-4">
                        <label>Nome</label>
                        <div class="form-group">
                            <input type="text" autocomplete="off" class="form-control" placeholder="Nome do Cliente" name="searchName" id="searchNameSellerCustomer" value="">
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-8" style="margin-top:25px;">
                        <button type="button" class="btn btn-primary pull-right" name="filter" id="searchSellerCustomer"><i class="fa fa-search"></i> Pesquisar</button>
                    </div>
                    <div class="col-md-12 col-lg-12">
                        <table class="table table-condensed table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50" class="text-center align-middle">Cód</th>
                                    <th class="align-middle">Nome</th>
                                    <th class="text-center align-middle">CPF/CNPJ</th>
                                    <th class="align-middle">Localidade</th>
                                    <th width="100" class="text-center align-middle">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="sellerCustomerTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <ul class="pagination pagination-sm no-margin modal-pagination-seller-customer"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
