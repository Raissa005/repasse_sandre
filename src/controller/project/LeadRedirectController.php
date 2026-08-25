<?php

namespace RR\controller\project;

use RR\libs\Util;
use RR\model\Lead;
use RR\model\User;
use RR\model\Property;
use RR\model\Attendance;
use RR\model\Integrations;
use RR\model\LeadConfig;
use RR\model\LeadRandom;
use RR\model\LeadWorkingDate;

class LeadRedirectController
{
    public function leadsFaceBook()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Search the bank for the token.
            $urlIntegrations = (new Integrations)->getItemWithFilters([], [(object)['columns' => ['token']]]);

            // Separate the URL into bars.
            $getUrl = explode("/", $_GET['url']);

            // Compares the two tokens, bank and URL.
            if (end($getUrl) === $urlIntegrations->token) {
                // Receives the data sent in the request body.
                $leadsData = file_get_contents('php://input');

                // Instantiates the variables.
                $rows = [];
                $arrPost = [];
                $ctrlFixedVariable = 0;

                // Read the file and separate it by rows within the variable.
                $rows = explode(";", $leadsData);

                // Increment the variables to save in the database.
                foreach ($rows as $row) {
                    $explodeRow = explode(":", $row, 2);

                    $key = trim($explodeRow[0]);
                    $val = !empty($explodeRow[1]) ? trim($explodeRow[1]) : null;

                    $val = str_replace('r$', 'R$', $val);
                    $arrPost["id_communication_channel"] = 3;

                    if (!empty($val)) {
                        $val = implode(" ", explode("_", $val));
                    }

                    if (Util::removeAccentsTransformLowercase($key) == "nome") {
                        $arrPost["name"] = $val;

                        $ctrlFixedVariable = 1;
                    }

                    if (Util::removeAccentsTransformLowercase($key) == "telefone") {
                        if (strlen($val) > 13) {
                            $arrPost["phone"] = substr($val, 3);
                        } else {
                            $arrPost["phone"] = $val;
                        }

                        $ctrlFixedVariable = 1;
                    }

                    if (Util::removeAccentsTransformLowercase($key) == "email") {
                        $arrPost["email"] = $val;

                        $ctrlFixedVariable = 1;
                    }

                    if (Util::removeAccentsTransformLowercase($key) == "data de cadastro") {
                        $url_parts = str_replace("T", ' ', explode(".", $val));
                        $arrPost["created_at"] = $url_parts[0];

                        $ctrlFixedVariable = 1;
                    }

                    if ($ctrlFixedVariable == 0) {
                        if ($key != "" && $val != "") {
                            isset($arrPost["message"]) ? $arrPost["message"] .= $key . ": " . $val . "\n" :  $arrPost["message"] = $key . ": " . $val . "\n";
                        }
                    }

                    if (isset($arrPost['name']) || isset($arrPost['phone']) || isset($arrPost['email']) || isset($arrPost['created_at'])) {
                        $ctrlFixedVariable = 0;
                    }
                }

                (new Lead)->insert($arrPost);
                $this->leadDistribution();
                return;
            } else {
                // Return to the user if the token is wrong.
                echo "Token invalido!";
                die(); // Kills execution.
            }
        } else {
            // If the request is not of type POST, returns an error.
            http_response_code(405); // Method not allowed.
            echo "Método não permitido. Utilize POST para enviar dados.<br>";
        }
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

                if (!empty($leads->data[$i]->id_product)) {
                    $product = (new Property)->getProductsById($leads->data[$i]->id_product);
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

                    if (!empty($leads->data[$i]->id_product)) {
                        $product = (new Property)->getProductsById($leads->data[$i]->id_product);
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

                if (!empty($leads->data[$i]->id_product)) {
                    $product = (new Property)->getProductsById($leads->data[$i]->id_product);
                }

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
