<?php

namespace RR\Controller\ajax;

use RR\core\Ajax;
use RR\model\ModelGenerico;

class GlobalController extends Ajax
{
    /** Tabelas que o front-end realmente consulta por id via getGenericoById. */
    private const ALLOWED_BY_ID = [
        'users',
        'attendance',
        'attendance_status',
        'bank_accounts',
        'bill_receive_installment',
        'bills_to_pay_installments',
        'cost_center',
        'system_config',
    ];

    public function getGenericoById()
    {
        $table = $_POST['table'] ?? '';

        if (!in_array($table, self::ALLOWED_BY_ID, true)) {
            $this->error = true;
            $this->message = 'Consulta não permitida.';
            echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => null]);
            exit;
        }

        $data = (new ModelGenerico())->getItemById8161($_POST['id'] ?? 0, $table);

        // users carrega o hash da senha; o front-end só usa o nome.
        if ($table === 'users' && $data) {
            $data = (object)['id' => $data->id, 'name' => $data->name];
        }

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        exit;
    }

    public function getItemByGenericFieldArray()
    {
        $table = $_POST['table'] ?? '';
        $array = $_POST['array'] ?? [];

        // Único uso real: filtros de interesse de um atendimento (aba Interesses).
        $allowedColumns = ['id_attendance_filter_type', 'id_attendance'];
        if ($table !== 'attendance_filters_interests' || array_diff(array_keys((array)$array), $allowedColumns)) {
            $this->error = true;
            $this->message = 'Consulta não permitida.';
            echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => []]);
            exit;
        }

        $data = (new ModelGenerico())->getItemByGenericFieldArray($array, $table);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        exit;
    }
}
