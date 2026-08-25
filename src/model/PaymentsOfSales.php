<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Util;

class PaymentsOfSales extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'payments_of_sales';
        $joins = [
            (object)[
                'table' => 'form_of_payment',
                'join' => 'left',
                'where' => "form_of_payment.id = {$this->table}.id_form_of_payment",
            ],
            (object)[
                'table' => 'sales',
                'join' => 'inner',
                'where' => "sales.id = {$this->table}.id_sale",
            ],
            (object)[
                'table' => 'customer',
                'join' => 'inner',
                'where' => "customer.id = sales.id_customer",
            ],
            (object)[
                'table' => 'bank_accounts',
                'join' => 'left',
                'where' => "{$this->table}.id_account = bank_accounts.id",
            ],
            (object)[
                'table' => 'banks',
                'join' => 'left',
                'where' => "{$this->table}.id_bank_finance = banks.id OR {$this->table}.bank_paymentOrder = banks.id OR bank_accounts.id_bank = banks.id",
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    public function checkSaleFromBillReceive(int $receiveId): object
    {
        $sale = (new Sales())->getItemWithFilters([(object)['columns' => ['id_bill_receive' => (object)['value' => $receiveId]]]]);

        if ($sale) return (object)['error' => false, 'data' => $sale];
        return (object)['error' => true, 'data' => null];
    }

    public function getAllPortionsBySale(int $saleId)
    {
        $filters = [(object)[
            'columns' => [
                'id_sale' => (object)['value' => $saleId],
                'status' => (object)['value' => 1]
            ]
        ]];

        $columns = [
            (object)['columns' => ["*"]],
            (object)['table' => 'sales', 'columns' => ['id' => ['id_sale']]],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'banks', 'columns' => ['name' => ['nameBank'], 'bank_code' => ['banks_bank_code']]],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'person_registration' => ['customer_CPF_CNPJ']]],
            (object)['table' => 'bank_accounts', 'columns' => ['agency' => ['bank_accounts_agency'], 'account_number' => ['bank_accounts_account_number']]]
        ];

        $options = ['orderBy' => 'portion_number ASC'];

        return $this->getWithFiltersAllItems($filters, $columns, $options)->data;
    }

    public function addPayment(int $saleId, $post): object
    {
        try {
            $this->db->beginTransaction();

            if (isset($post['number_of_installments']) && $post['value_reference'] == 1) {
                $valueOfInstallments = Util::unmaskMoney($post['portion_value']) / $post['number_of_installments'];

                list($year, $month, $day) = explode("-", $post['due_date']);

                for ($p = $post['portion_number']; $p < ($post['number_of_installments'] + $post['portion_number']); $p++) {
                    $arrPost = [
                        'type_of_payment' => $post['type_of_payment'],
                        'value' => $valueOfInstallments,
                        'portion_number' => preg_replace("/[^0-9]/", '', $p),
                        'created_by' => $_SESSION['RR']->user->id,
                        'due_date' => !empty(trim($post['due_date'])) ? $post['due_date'] : NULL,
                        'observation' => $post['observation'],
                        'status' => 1,
                        'id_sale' => $saleId
                    ];

                    if ($arrPost['type_of_payment'] == 1) {
                        $arrPost['id_form_of_payment'] = $post['id_form_of_payment'];
                        $arrPost['status_portion'] = $post['status'];
                    }

                    if ($arrPost['type_of_payment'] == 2) {
                        $arrPost['id_property'] = $post['property'];
                        $arrPost['status_portion'] = 1;
                    }

                    if ($arrPost['type_of_payment'] == 3) {
                        $arrPost['vehicle'] = $post['vehicle'];
                        $arrPost['license_plate'] = $post['license_plate'];
                        $arrPost['status_portion'] = 1;
                    }

                    if ($arrPost['status_portion'] == 1) {
                        $arrPost['amount_paid'] = Util::unmaskMoney($post['amount_paid']);
                        $arrPost['pay_day'] = date('Y-m-d');
                    }

                    $customer = (new Customer)->getCustomerBySale($saleId);

                    /**Adicionado valores da parcela */
                    switch ($post['id_form_of_payment']) {
                        case '1': //Boleto

                            break;
                        case '2': //Transferencia
                            $arrPost['id_account'] = $post['id_account'];
                            $account = (new BankAccounts)->getBankAccountsById($post['id_account']);
                            break;
                        case '3': //Cheque

                            $arrPost['own_paymentOrder'] = $post['own_paymentOrder'];
                            if ($post['own_paymentOrder'] == 0) {
                                $arrPost['owner_paymentOrder'] = $post['owner_paymentOrder'];
                                $arrPost['cpfcnpj_paymentOrder'] = Util::removeNonNumericCharacters($post['cpfcnpj_paymentOrder']);
                            } else {
                                $arrPost['owner_paymentOrder'] = $customer->name;
                                $arrPost['cpfcnpj_paymentOrder'] = $customer->cpf;
                            }
                            $arrPost['bank_paymentOrder'] = $post['bank_paymentOrder'];
                            $arrPost['number_paymentOrder'] = $post['number_paymentOrder'];
                            $arrPost['agency_paymentOrder'] = Util::removeNonNumericCharacters($post['agency_paymentOrder']);
                            $arrPost['number_account_paymentOrder'] = Util::removeNonNumericCharacters($post['number_account_paymentOrder']);

                            $bank = (new Banks)->getBanksById($post['bank_paymentOrder']);
                            break;
                        case '4': //Financiamento
                            $arrPost['id_bank_finance'] = $post['id_bank_finance'];
                            $bank = (new Banks)->getBanksById($post['id_bank_finance']);
                            break;
                        default:
                            break;
                    }

                    $contractsSale = (new Contract)->getAllContractByIdSale($saleId);

                    $month++;
                    if ((int) $month == 13) {
                        $year++;
                        $month = 1;
                    }

                    $remove = $day;
                    while (!checkdate($month, $remove, $year)) {
                        $remove--;
                    }

                    $post['due_date'] = sprintf("%02d-%02d-%02d", $year, $month, $remove);

                    $response = (new PaymentsOfSales())->insert($arrPost);

                    if (!$response->error) (new PaymentsOfSales)->addBillReceiveInstallmentFromPortion($response->lastId, $saleId);
                }
            } else {
                $valueOfInstallments = Util::unmaskMoney($post['portion_value']);

                list($year, $month, $day) = explode("-", $post['due_date']);

                for ($p = $post['portion_number']; $p < ($post['number_of_installments'] + $post['portion_number']); $p++) {
                    $arrPost = [
                        'type_of_payment' => $post['type_of_payment'],
                        'value' => $valueOfInstallments,
                        'portion_number' => preg_replace("/[^0-9]/", '', $p),
                        'created_by' => $_SESSION['RR']->user->id,
                        'due_date' => !empty(trim($post['due_date'])) ? $post['due_date'] : NULL,
                        'observation' => $post['observation'],
                        'status' => 1,
                        'id_sale' => $saleId
                    ];

                    if ($arrPost['type_of_payment'] == 1) {
                        $arrPost['id_form_of_payment'] = $post['id_form_of_payment'];
                        $arrPost['status_portion'] = $post['status'];
                    }

                    if ($arrPost['type_of_payment'] == 2) {
                        $arrPost['id_property'] = $post['property'];
                        $arrPost['status_portion'] = 1;
                    }

                    if ($arrPost['type_of_payment'] == 3) {
                        $arrPost['vehicle'] = $post['vehicle'];
                        $arrPost['license_plate'] = $post['license_plate'];
                        $arrPost['status_portion'] = 1;
                    }

                    if ($arrPost['status_portion'] == 1) {
                        $arrPost['amount_paid'] = Util::unmaskMoney($post['amount_paid']);
                        $arrPost['pay_day'] = date('Y-m-d');
                    }

                    $customer = (new Customer)->getCustomerBySale($saleId);

                    /**Adicionado valores da parcela */
                    switch ($post['id_form_of_payment']) {
                        case '1': //Boleto

                            break;
                        case '2': //Transferencia
                            $arrPost['id_account'] = $post['id_account'];
                            $account = (new BankAccounts)->getBankAccountsById($post['id_account']);
                            break;
                        case '3': //Cheque

                            $arrPost['own_paymentOrder'] = $post['own_paymentOrder'];
                            if ($post['own_paymentOrder'] == 0) {
                                $arrPost['owner_paymentOrder'] = $post['owner_paymentOrder'];
                                $arrPost['cpfcnpj_paymentOrder'] = Util::removeNonNumericCharacters($post['cpfcnpj_paymentOrder']);
                            } else {
                                $arrPost['owner_paymentOrder'] = $customer->name;
                                $arrPost['cpfcnpj_paymentOrder'] = $customer->cpf;
                            }
                            $arrPost['bank_paymentOrder'] = $post['bank_paymentOrder'];
                            $arrPost['number_paymentOrder'] = $post['number_paymentOrder'];
                            $arrPost['agency_paymentOrder'] = Util::removeNonNumericCharacters($post['agency_paymentOrder']);
                            $arrPost['number_account_paymentOrder'] = Util::removeNonNumericCharacters($post['number_account_paymentOrder']);

                            $bank = (new Banks)->getBanksById($post['bank_paymentOrder']);
                            break;
                        case '4': //Financiamento
                            $arrPost['id_bank_finance'] = $post['id_bank_finance'];
                            $bank = (new Banks)->getBanksById($post['id_bank_finance']);
                            break;
                        default:
                            break;
                    }

                    $contractsSale = (new Contract)->getAllContractByIdSale($saleId);

                    $month++;
                    if ((int) $month == 13) {
                        $year++;
                        $month = 1;
                    }

                    $remove = $day;
                    while (!checkdate($month, $remove, $year)) {
                        $remove--;
                    }

                    $post['due_date'] = sprintf("%02d-%02d-%02d", $year, $month, $remove);

                    $response = (new PaymentsOfSales())->insert($arrPost);

                    if (!$response->error) (new PaymentsOfSales)->addBillReceiveInstallmentFromPortion($response->lastId, $saleId);
                }
            }

            $portions = (new Sales)->getAmountPortionsBySale($saleId);

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === "development") {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Erro ao atualizar contrato.'];
        }
    }

    public function editPayment(int $saleId): object
    {
        $contractModel = new Contract();
        $customerModel = new Customer();
        $portion = $this->getItemById($_POST['id_portion']);

        $arrPost = [
            'type_of_payment' => $_POST['type_of_payment'],
            'value' => Util::removeNumberFormatting($_POST['portion_value']),
            'portion_number' => preg_replace("/[^0-9]/", '', $_POST['portion_number']),
            'created_by' => $_SESSION['RR']->user->id,
            'due_date' => $_POST['due_date'],
            'observation' => $_POST['observation'],
            'status' => 1,
            'id_sale' => $saleId
        ];

        if ($arrPost['type_of_payment'] == 1) {
            $arrPost['id_form_of_payment'] = $_POST['id_form_of_payment'];
            $arrPost['status_portion'] = $_POST['status'];
        }

        if ($arrPost['type_of_payment'] == 2) {
            $arrPost['id_property'] = $_POST['property'];
            $arrPost['status_portion'] = 1;
        }

        if ($arrPost['type_of_payment'] == 3) {
            $arrPost['vehicle'] = $_POST['vehicle'];
            $arrPost['status_portion'] = 1;
        }

        if ($arrPost['status_portion'] == 1) $arrPost['amount_paid'] = Util::unmaskMoney($_POST['amount_paid']);
        if ($portion->status_portion != 1) $arrPost['pay_day'] = date("Y-m-d");

        $customer = $customerModel->getCustomerBySale($saleId);
        /**Atribuindo e limpando campos da parcela */
        switch ($_POST['id_form_of_payment']) {
            case '1': //Boleto
                $arrPost['id_account'] = NULL;
                $arrPost['number_paymentOrder'] = "";
                $arrPost['id_bank_finance'] = NULL;
                $arrPost['bank_paymentOrder'] = NULL;
                $arrPost['owner_paymentOrder'] = "";
                $arrPost['cpfcnpj_paymentOrder'] = "";
                $arrPost['agency_paymentOrder'] = "";
                $arrPost['number_account_paymentOrder'] = "";
                break;
            case '2': //Transferencia
                $arrPost['id_account'] = $_POST['id_account'];

                $arrPost['id_bank_finance'] = NULL;
                $arrPost['bank_paymentOrder'] = NULL;
                $arrPost['number_paymentOrder'] = "";
                $arrPost['owner_paymentOrder'] = "";
                $arrPost['cpfcnpj_paymentOrder'] = "";
                $arrPost['agency_paymentOrder'] = "";
                $arrPost['number_account_paymentOrder'] = "";
                break;
            case '3': //Cheque
                $arrPost['own_paymentOrder'] = $_POST['own_paymentOrder'];
                if ($_POST['own_paymentOrder'] == 0) {
                    $arrPost['owner_paymentOrder'] = $_POST['owner_paymentOrder'];
                    $arrPost['cpfcnpj_paymentOrder'] = Util::removeNonNumericCharacters($_POST['cpfcnpj_paymentOrder']);
                } else {
                    $arrPost['owner_paymentOrder'] = $customer->name;
                    $arrPost['cpfcnpj_paymentOrder'] = $customer->cpf;
                }
                $arrPost['agency_paymentOrder'] = Util::removeNonNumericCharacters($_POST['agency_paymentOrder']);
                $arrPost['number_account_paymentOrder'] = Util::removeNonNumericCharacters($_POST['number_account_paymentOrder']);
                $arrPost['bank_paymentOrder'] = $_POST['bank_paymentOrder'];
                $arrPost['number_paymentOrder'] = $_POST['number_paymentOrder'];

                $arrPost['id_account'] = NULL;
                $arrPost['id_bank_finance'] = NULL;
                break;
            case '4': //Financiamento
                $arrPost['id_bank_finance'] = $_POST['id_bank_finance'];

                $arrPost['id_account'] = NULL;
                $arrPost['number_paymentOrder'] = "";
                $arrPost['bank_paymentOrder'] = NULL;
                $arrPost['owner_paymentOrder'] = "";
                $arrPost['cpfcnpj_paymentOrder'] = "";
                $arrPost['agency_paymentOrder'] = "";
                $arrPost['number_account_paymentOrder'] = "";
                break;
            default:
                $arrPost['id_account'] = NULL;
                $arrPost['number_paymentOrder'] = "";
                $arrPost['id_bank_finance'] = NULL;
                $arrPost['bank_paymentOrder'] = NULL;
                $arrPost['owner_paymentOrder'] = "";
                $arrPost['cpfcnpj_paymentOrder'] = "";
                $arrPost['agency_paymentOrder'] = "";
                $arrPost['number_account_paymentOrder'] = "";
                break;
        }

        $contractsSale = $contractModel->getAllContractByIdSale($saleId);

        try {
            $this->db->beginTransaction();
            $response = (new PaymentsOfSales)->update($arrPost, 'id', $_POST['id_portion']);

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Erro ao atualizar variáveis do contrato.'];
        }
    }


    public function inactiveStatus(int $paymentId): object
    {
        $modelGenerico = new ModelGenerico();
        $contractModel = new Contract();
        $portion = (new Sales)->getPortionById($paymentId);

        $item = (new Sales)->getItemById($portion->id_sale);
        $contractsSale = $contractModel->getAllContractByIdSale($portion->id_sale);

        try {
            $this->db->beginTransaction();
            $response = $this->update(['status' => '0'], "id", $paymentId);
            $update = (new BillReceiveInstallment())->update(['status_payment' => '9'], "id", $portion->id_bill_receive_installment);
            if ($response->error || $update->error) throw new PDOException($response->message);

            $payments = Self::getAllPortionsBySale($portion->id_sale);

            $this->db->commit();
            return (object)['error' => $response->error, 'message' => !$response->error ? 'Inativado com sucesso' : 'Erro ao inativar esse item'];
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Erro ao atualizar variáveis de contrato.'];
        }
    }

    public function addBillReceiveInstallmentFromPortion(int $portionId, int $saleId): object
    {
        $item = (new Sales)->getItemById($saleId);
        $portion = $this->getItemById($portionId);

        $type_of_payment = [
            1 => 'Moeda Corrente',
            2 => 'Imóvel',
            3 => 'Veículo',
        ];

        $arrPost = [
            'number_portion' => $portion->portion_number,
            'id_bill_receive' => $item->id_bill_receive,
            'id_form_of_payment' => $portion->type_of_payment == 1 ? $portion->id_form_of_payment : 1,
            'due_date' => !empty(trim($portion->due_date)) ? $portion->due_date : NULL,
            'value_installment' => $portion->value,
            'status_payment' => $portion->status_portion == 0 ? 1 : 2,
            'description' => $portion->type_of_payment == 1 ? $portion->observation : "Parcela referente ao pagamento do Imóvel com um " . $type_of_payment[$portion->type_of_payment],
            'created_by' => $_SESSION['RR']->user->id,
        ];

        if ($portion->status_portion != 1) $arrPost['pay_day'] = date('Y-m-d');
        if ($arrPost['status_payment'] == 2) {
            $arrPost['amount_paid'] = $portion->amount_paid;
        }

        try {
            $this->db->beginTransaction();

            $response = (new BillReceiveInstallment())->insert($arrPost);
            $update = $this->update(['id_bill_receive_installment' => $response->lastId], 'id', $portion->id);
            if ($response->error || $update->error) throw new PDOException('Erro ao inserir parcela de conta a receber.');

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao inserir parcela de conta a receber.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }

    public function editBillReceiveInstallmentFromPortion(int $portionId, int $saleId): object
    {
        $item = (new Sales)->getItemById($saleId);
        $portion = $this->getItemById($portionId);

        $arrPost = [
            'number_portion' => $portion->portion_number,
            'id_bill_receive' => $item->id_bill_receive,
            'id_form_of_payment' => $portion->id_form_of_payment,
            'due_date' => !empty(trim($portion->due_date)) ? $portion->due_date : NULL,
            'value_installment' => $portion->value,
            'status_payment' => $portion->status_portion == 0 ? 1 : 2,
            'description' => $portion->observation,
            'updated_by' => $_SESSION['RR']->user->id,
        ];

        if ($portion->status_portion != 1) $arrPost['pay_day'] = date('Y-m-d');
        if ($arrPost['status_payment'] == 2) {
            $arrPost['amount_paid'] = $portion->amount_paid;
        } else {
            $arrPost['pay_day'] = NULL;
            $arrPost['amount_paid'] = NULL;
        }

        try {
            $this->db->beginTransaction();

            $response = (new BillReceiveInstallment())->update($arrPost, 'id', $portion->id_bill_receive_installment);
            if ($response->error) throw new PDOException('Erro ao inserir parcela de conta a receber.');

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao inserir parcela de conta a receber.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }

    public function submitAddPortionFromBillReceive(int $billReceiveId, int $installmentId)
    {
        $sale = Self::checkSaleFromBillReceive($billReceiveId);
        if ($sale->error) return;
        $sale = $sale->data;
        $installment = (new BillReceiveInstallment())->getItemById($installmentId);

        $arrInsert = [
            'id_sale' => $sale->id,
            'portion_number' => $installment->number_portion,
            'id_form_of_payment' => $installment->id_form_of_payment,
            'due_date' => $installment->due_date,
            'value' => $installment->value_installment,
            'status_portion' => 0,
            'observation' => $installment->description,
            'id_bill_receive_installment' => $installment->id,
            'created_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arrInsert);
            if ($response->error) throw new PDOException('Erro ao inserir parcela de venda.');

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao inserir parcela de venda.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }

    public function submitEditPaymentOfSaleFromInstallment(int $installmentId)
    {
        $installment = (new BillReceiveInstallment)->getItemById($installmentId);
        $sale = Self::checkSaleFromBillReceive($installment->id_bill_receive);
        if ($sale->error) return;

        $arrUpdate = [
            'due_date' => $installment->due_date,
            'value' => $installment->value_installment,
            'observation' => $installment->description,
        ];

        try {
            $this->db->beginTransaction();

            $response = $this->update($arrUpdate, 'id_bill_receive_installment', $installment->id);
            if ($response->error) throw new PDOException('Erro ao atualizar parcela de venda.');

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao atualizar parcela de venda.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }

    public function submitStatusOfPaymentFromAnInstallment(int $installmentId, $status_payment)
    {
        $installment = (new BillReceiveInstallment)->getItemById($installmentId);
        $sale = Self::checkSaleFromBillReceive($installment->id_bill_receive);
        if ($sale->error) return;

        $arrUpdate = [
            'status_portion' => (string)$status_payment,
            'updated_by' => $_SESSION['RR']->user->id,
        ];

        if ($status_payment == 1) {
            $arrUpdate['pay_day'] = date('Y-m-d');
            $arrUpdate['amount_paid'] = $installment->amount_paid;
        } else {
            $arrUpdate['pay_day'] = NULL;
            $arrUpdate['amount_paid'] = NULL;
        }

        try {
            $this->db->beginTransaction();

            $response = $this->update($arrUpdate, 'id_bill_receive_installment', $installmentId);
            if ($response->error) throw new PDOException('Erro ao atualizar parcela de venda.');

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao atualizar parcela de venda.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }
}
