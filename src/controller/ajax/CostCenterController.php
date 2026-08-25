<?php

namespace RR\controller\ajax;

use RR\core\Ajax;

use RR\libs\RecursiveCostCenter;

class CostCenterController extends Ajax
{
    public function recursiveCostCenterView()
    {
        $filtersRecursiveTree = ['status' => 1, 'id_type' => $_POST['idType']];

        $items = (new RecursiveCostCenter())->recursiveTree(0, $filtersRecursiveTree);

        $options = (new RecursiveCostCenter())->recursiveOptionView($items, $_POST['itemSelected'], isset($_POST['ownId']) ? $_POST['ownId'] : "", isset($_POST['showChildren']) ? $_POST['showChildren'] : true);
        echo json_encode(['error' => false, 'options' => $options]);
        exit;
    }

    public function recursiveCostCenterList()
    {
        /**
         * @param int/id_cost_center
         * @param string/tree
         */

        $cost_centers = (new RecursiveCostCenter())->recursiveTree($_POST['id_cost_center'], ['status' => 1, 'id_type' => 1]);

        $data = (new RecursiveCostCenter())->recursiveTreeViewNoAction($cost_centers, "", null, "", $_POST['tree']);
        echo json_encode((object)['error' => false, 'data' => $data]);
        exit;
    }
}
