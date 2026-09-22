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

    public function addBillReceiveInstallmentFromPortion(int $portionId, int $saleId): object
    {
        $item = (new Sales)->getItemById($saleId);
        $portion = $this->getItemById($portionId);

        $type_of_payment = [
            1 => 'Moeda Corrente',
            3 => 'Veículo',
        ];

        $arrPost = [
            'number_portion' => $portion->portion_number,
            'id_bill_receive' => $item->id_bill_receive,
            'id_form_of_payment' => $portion->type_of_payment == 1 ? $portion->id_form_of_payment : 1,
            'due_date' => !empty(trim($portion->due_date)) ? $portion->due_date : NULL,
            'value_installment' => $portion->value,
            'status_payment' => $portion->status_portion == 0 ? 1 : 2,
            'description' => $portion->type_of_payment == 1 ? $portion->observation : "Parcela referente ao pagamento com um " . $type_of_payment[$portion->type_of_payment],
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
