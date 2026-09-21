<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Date;
use RR\libs\Util;
use RR\model\Lead;
use RR\model\User;
use RR\model\Property;
use RR\model\LeadWorkingDate;
use RR\controller\project\LeadRedirectController;

class LeadController extends Ajax
{
    /**
     * Módulo Lead pausado a pedido do usuário (2026-09-02): as tabelas
     * `lead`/`lead_working_date` não existem neste banco. Ver mesmo guard
     * em src/controller/project/LeadController.php.
     */
    public function __construct()
    {
        parent::__construct();

        $this->error = true;
        $this->message = 'Recurso desativado.';
        $this->sendResponse();
    }

    public function getDutySeller()
    {
        $sellers = (new LeadWorkingDate)->getWithFiltersAllItems();

        $data = array_merge(
            array_map(function ($element) {
                $id = $element->id;
                $id_group = $element->id_user;
                $title = $element->name;
                $start = date('Y-m-d H:i:s', strtotime($element->date_start));
                $end = date('Y-m-d H:i:s', strtotime($element->date_end));
                $color = $element->color;
                $allDay = $element->all_day;

                return (object)[
                    'id' => $id,
                    'id_group' => $id_group,
                    'title' => $title,
                    'start' => $start,
                    'end' => $end,
                    'color' => $color,
                    'allDay' => $allDay,
                ];
            }, $sellers->data),
        );

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function insertDutySeller()
    {
        $seller = (new LeadWorkingDate)->getWithFiltersAllItems([
            (object)['where' => " AND (date_start = '{$_POST['start']}' OR DATE_SUB(date_end, INTERVAL 1 DAY) = '{$_POST['start']}')"],
            (object)['columns' => ['id_user' => (object)['comparison' => 'EQUAL', 'value' => $_POST['id_group']]]]
        ])->data;

        if (empty($seller)) {
            $arrPost = array(
                'id_user' => $_POST['id_group'],
                'name' => $_POST['title'],
                'date_start' => $_POST['start'],
                'color' => $_POST['color'] ?? '',
                'all_day' => $_POST['allDay'] ? 1 : 0,
            );

            (new LeadWorkingDate())->insert($arrPost);
            echo json_encode(['error' => false]);
            exit;
        } else {
            echo json_encode(['error' => true]);
            exit;
        }
    }

    public function upDateDutySeller()
    {
        $arrPost = array(
            'date_start' => $_POST['start'],
            'date_end' => $_POST['end'],
            'all_day' => $_POST['allDay'] ? 1 : 0,
        );

        (new LeadWorkingDate)->update($arrPost, 'id', $_POST['id']);
    }

    public function upDateColorSeller()
    {
        $arrPost = array(
            'color' => $_POST['color'],
        );

        (new User)->update($arrPost, 'id', $_POST['id_group']);
        (new LeadWorkingDate)->update($arrPost, 'id_user', $_POST['id_group']);
    }

    public function deleteDutySeller()
    {
        (new LeadWorkingDate)->delete($_POST['id']);
    }

    public function saveImportedLeads()
    {
        $leads = (object)[
            'data' => $_POST['csvResult'],
            'count' => count($_POST['csvResult'])
        ];

        for ($i = 0; $i < $leads->count; $i++) {
            $product = (new Property)->getItemWithFilters(
                [(object)['columns' => ['cod' => (object)['comparison' => 'LIKE', 'value' => $leads->data[$i][20]]]]],
                [(object)['columns' => ['id']]]
            );

            if ($i != 0) {
                $arrPost = [
                    'id_communication_channel' => 3,
                    'name' =>  str_replace('"', '', $leads->data[$i][16]) ?? null,
                    'phone' => str_replace('p:+55', '', $leads->data[$i][18]) ?? null,
                    'email' => $leads->data[$i][17] ?? null,
                    'message' => str_replace('"', '', $leads->data[$i][3]) ?? null,
                    'created_at' => $leads->data[$i][1] ?? new Date('now'),
                    'id_product' => $product->id ?? null,
                ];

                (new Lead)->insert($arrPost);
            }
        }

        (new LeadRedirectController)->leadDistribution();
        exit;
    }
}
