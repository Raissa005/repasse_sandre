<?php

namespace RR\controller\project;

use RR\model\Lead;
use RR\model\User;
use RR\model\Attendance;
use RR\model\LeadConfig;
use RR\model\LeadRandom;
use RR\model\LeadWorkingDate;

class LeadRedirectController
{
    /**
     * Módulo Lead pausado a pedido do usuário (2026-09-02): captação de leads
     * não será usada por enquanto. As tabelas `lead`/`lead_config`/`lead_random`/
     * `lead_working_date` não existem neste banco, então esses métodos já
     * quebrariam com erro fatal de SQL — mas esta classe não estende nenhum
     * Controller base, ou seja, sem este guard qualquer requisição externa
     * chegaria a rodar SQL com dado não validado antes de falhar. Não remover
     * o código: é só desativação, para reativar depois que as tabelas forem
     * recriadas e a fonte de captação for redefinida (ver
     * docs/10-modulos-negocio.md e memória do projeto
     * "attendance-vehicle-interest"/"property-domain..."). O webhook de
     * Facebook Lead Ads (`leadsFaceBook`) e a tabela `integrations` foram
     * removidos em 2026-09-21 a pedido do usuário — sem previsão de uso.
     */
    public function __construct()
    {
        http_response_code(404);
        exit;
    }

    public function leadDistribution()
    {
        $leadsConfig = (new LeadConfig)->getWithFiltersAllItems()->data;

        foreach ($leadsConfig as $leadConfig) {
            switch ($leadConfig->distribution_type) {
                case '0':
                    // função aleatória.
                    $this->randomLeadsDistribution();
                    break;
                case '1':
                    // busca no plantão e distribui.
                    $this->onDutyLeadsDistribution();
                    break;
                case '2':
                    // busca os com menos atendimento em aberto.
                    $this->distributionOfLeadsByOpenService();
                    break;
                case '3':
                    // secretária distribui os atendimentos.
                    $this->manualLeadsDistribution();
                    break;
            }
        }
    }

    public function randomLeadsDistribution()
    {
        $leads = (new Lead)->getWithFiltersAllItems([
            (object)['columns' => ['status' => ['comparison' => 'EQUAL', 'value' => 1]]],
            (object)['where' => " AND id_attendance IS NULL"]
        ]);

        if (!empty($leads)) {
            for ($i = 0; $i < $leads->count; $i++) {
                $leadRandom = (new LeadRandom)->getWithFiltersAllItems();

                if (!empty($leadRandom->data)) {
                    $users = (new User)->getWithFiltersAllItems(
                        [(object)[
                            'columns' => [
                                'id_profile' => ['comparison' => 'EQUAL', 'value' => 4],
                                'id' => ['comparison' => 'NOT_IN', 'value' => array_column($leadRandom->data, 'id_user')]
                            ]
                        ]],
                        [],
                        ['order' => 'rand()', 'limit' => 1, 'page' => 1]
                    );
                } else {
                    $users = (new User)->getWithFiltersAllItems(
                        [(object)['columns' => ['id_profile' => ['comparison' => 'EQUAL', 'value' => 4]]]],
                        [],
                        ['order' => 'rand()', 'limit' => 1, 'page' => 1]
                    );
                }

                $arrPostAttendance = array(
                    'id_branch' => !empty($leads->data[$i]->id_product) ? $product->id_branch : '',
                    'opening_date' => $leads->data[$i]->created_at,
                    'id_communication_channel' => $leads->data[$i]->id_communication_channel,
                    'id_status' => 7,
                    'name' => strtoupper($leads->data[$i]->name),
                    'email' => $leads->data[$i]->email,
                    'description' => $leads->data[$i]->message,
                    'created_by' => $users->data[0]->id
                );

                $responseAttendance = (new Attendance)->insert($arrPostAttendance);

                $arrPostUpdate = [
                    'created_by' => $responseAttendance->item->created_by,
                    'id_attendance' => $responseAttendance->lastId,
                ];

                (new Lead)->update($arrPostUpdate, 'id', $leads->data[$i]->id);

                $arrPostLead = [
                    'id_user' => $responseAttendance->item->created_by,
                    'attendance' => 1
                ];

                if ($leadRandom->count < $users->count) {
                    $responseLead = (new LeadRandom)->insert($arrPostLead);
                } else {
                    (new LeadRandom)->delete(['id']);
                }
            }
        }
    }

    public function onDutyLeadsDistribution()
    {
        $leads = (new Lead)->getWithFiltersAllItems([
            (object)['columns' => ['status' => ['comparison' => 'EQUAL', 'value' => 1]]],
            (object)['where' => " AND id_attendance IS NULL"]
        ]);

        if (!empty($leads)) {
            $dutySeller = (new LeadWorkingDate)->getWithFiltersAllItems();

            if (!empty($dutySeller->data)) {
                for ($i = 0; $i < $leads->count; $i++) {
                    $leadRandom = (new LeadRandom)->getWithFiltersAllItems();

                    if ($dutySeller->count > 1) {
                        if (!empty($leadRandom->data)) {
                            $users = (new LeadWorkingDate)->getWithFiltersAllItems(
                                [
                                    (object)['columns' => [
                                        'id_user' => ['comparison' => 'NOT_IN', 'value' => array_column($leadRandom->data, 'id_user')],
                                    ]],
                                    (object)['where' => " AND (date_start >= CURDATE() OR date_end > CURDATE())"]
                                ],
                                [],
                                ['order' => 'rand()', 'limit' => 1, 'page' => 1]
                            );
                        } else {
                            $users = (new LeadWorkingDate)->getWithFiltersAllItems(
                                [(object)['where' => " AND (date_start >= CURDATE() OR date_end > CURDATE())"]],
                                [],
                                ['order' => 'rand()', 'limit' => 1, 'page' => 1]
                            );
                        }
                    } else if ($dutySeller->data[$i]->date_start >= $leads->data[$i]->created_at || $dutySeller->data[$i]->date_end >= $leads->data[$i]->created_at) {
                        $users = (new LeadWorkingDate)->getWithFiltersAllItems([(object)['where' => " AND (date_start >= CURDATE() OR date_end > CURDATE())"]]);
                    } else {

                        $this->manualLeadsDistribution();
                        break;
                    }

                    $arrPostAttendance = array(
                        'id_branch' => !empty($leads->data[$i]->id_product) ? $product->id_branch : '',
                        'opening_date' => $leads->data[$i]->created_at,
                        'id_communication_channel' => $leads->data[$i]->id_communication_channel,
                        'id_status' => 7,
                        'name' => strtoupper($leads->data[$i]->name),
                        'email' => $leads->data[$i]->email,
                        'description' => $leads->data[$i]->message,
                        'created_by' => $users->data[0]->id_user
                    );

                    $responseAttendance = (new Attendance)->insert($arrPostAttendance);

                    $arrPostUpdate = [
                        'created_by' => $responseAttendance->item->created_by,
                        'id_attendance' => $responseAttendance->lastId,
                    ];

                    (new Lead)->update($arrPostUpdate, 'id', $leads->data[$i]->id);

                    $arrPostLead = [
                        'id_user' => $responseAttendance->item->created_by,
                        'attendance' => 1
                    ];

                    if ($dutySeller->count > 1) {
                        if ($leadRandom->count < $users->count) {
                            $responseLead = (new LeadRandom)->insert($arrPostLead);
                        } else {
                            (new LeadRandom)->delete(['id']);
                        }
                    }
                }
            } else {
                $this->manualLeadsDistribution();
            }
        }
    }

    public function distributionOfLeadsByOpenService()
    {
        $leads = (new Lead)->getWithFiltersAllItems([
            (object)['status' => 1],
            (object)['where' => " AND id_attendance IS NULL"]
        ]);

        if (!empty($leads)) {
            for ($i = 0; $i < $leads->count; $i++) {
                $users = (new User)->getWithFiltersAllItems(
                    [(object)['columns' => ['id_profile' => ['comparison' => 'EQUAL', 'value' => 4]]]]
                );

                foreach ($users->data as $user) {
                    $attendance = (new Attendance)->getWithFiltersAllItems(
                        [(object)['columns' => [
                            'created_by' => (object)['comarison' => 'IN', 'value' => $user->id],
                            'id_status' => (object)['comarison' => 'EQUAL', 'value' => 7],
                            'status' => (object)['comarison' => 'EQUAL', 'value' => 1]
                        ]]],
                        [(object)['columns' => ['created_by']]]
                    )->count;

                    $attendances[] = (object)[
                        'id_user' => $user->id,
                        'count' => $attendance
                    ];
                }

                foreach ($attendances as $key => $item) {
                    $allCout[] = $item->count;
                    $count = $item->count;
                    $id_user = $item->id_user;

                    if (isset($attendanceGroup[$count])) {
                        $attendanceGroup[$count][] = $item;
                    } else {
                        $attendanceGroup[$count] = array($item);
                    }
                }

                $min = min($allCout);

                foreach ($attendanceGroup as $key => $values) {
                    if ($min == $key) {
                        $countAttendanceGroup = count($values);

                        $arrPostAttendance = array(
                            'id_branch' => !empty($leads->data[$i]->id_product) ? $product->id_branch : '',
                            'opening_date' => $leads->data[$i]->created_at,
                            'id_communication_channel' => $leads->data[$i]->id_communication_channel,
                            'id_status' => 7,
                            'name' => strtoupper($leads->data[$i]->name),
                            'email' => $leads->data[$i]->email,
                            'description' => $leads->data[$i]->message,
                            'created_by' => $values[$i]->id_user
                        );
                    }
                }

                $responseAttendance = (new Attendance)->insert($arrPostAttendance);

                $arrPostUpdate = [
                    'created_by' => $responseAttendance->item->created_by,
                    'id_attendance' => $responseAttendance->lastId,
                ];

                (new Lead)->update($arrPostUpdate, 'id', $leads->data[$i]->id);

                $arrPostLead = [
                    'id_user' => $responseAttendance->item->created_by,
                    'attendance' => 1
                ];
            }
        }
    }

    public function manualLeadsDistribution()
    {
    }
}
