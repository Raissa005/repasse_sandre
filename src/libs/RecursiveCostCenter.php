<?php

namespace RR\libs;

use RR\model\CostCenter;
use RR\model\GerenciaPost;

class RecursiveCostCenter
{
    public $model;
    public $cost_centers;

    public function __construct()
    {
        $this->model = new CostCenter();
        $this->cost_centers = $this->model->getWithFiltersAllItems();
    }

    public function recursiveTree(int $fatherId, array $filters = [])
    {
        $filters['id_father'] = $fatherId;
        $items = $this->model->getAllAndFilterItems($filters);

        if (!empty($items)) {
            $arrayItems = [];
            foreach ($items as $i) {
                $i->children = $this->recursiveTree($i->id, $filters);
                array_push($arrayItems, $i);
            }
            return $arrayItems;
        }
        return [];
    }

    public function recursiveGetChildren(int $father_id, array $filters = [])
    {
        // Add Id do pai ao $filters.
        array_push($filters, (object)['columns' => ['id_father' => (object)['comparison' => 'EQUAL', 'value' => $father_id]]]);
        
        // Retorna todos os filhos.
        $costCenters = (new CostCenter)->getWithFiltersAllItems($filters, [(object)['columns' => ['id']]])->data;

        if (!empty($costCenters)) {
 
            if(!isset($arrayItems)){
                $arrayItems = array($father_id);
            }

            foreach ($costCenters as $costCenter) {
                
                // Adicionar centro de custo Pai.
                array_push($arrayItems, $costCenter->id);

                
                // Busca filhos do centro de custo.
                $children = $this->recursiveGetChildren($costCenter->id, $filters);
                if (!empty($children)) {
                    $arrayItems = array_merge($arrayItems, $children);
                }
            }
            return $arrayItems;
        }
        return [$father_id];
    }

    public function recursiveClone(int $clonedItemId, array $children, int $idType = 1)
    {
        if (!empty($children)) {
            foreach ($children as $child) {
                $arrayPost = array('name' => $child->name, 'id_father' => $clonedItemId, 'id_type' => $idType, 'created_by' => $_SESSION['RR']->user->id);
                $itemId = (new GerenciaPost())->insert7181($arrayPost, 'cost_center', true, false);
                if (!empty($child->children)) {
                    $this->recursiveClone($itemId, $child->children, $idType);
                }
            }
        }
    }

    public function recursiveTreeView(array $items, int $idType, int $id = null, string $indexes = "")
    {
        $index = 1;
        $html = "";

        /**Se for raiz ? exibe os elementos que a pertence : se não mantem fechado
         * id = 0 é a raiz
         */

        if ($id == 0) {
            $html = "<div class='btn-group' role='group'>
                        <button id='0' id-type='{$idType}' class='btn-add-item btn btn-xs btn-info'>
                            <i class='fas fa-plus'></i>
                        </button>
                        <button class='btn btn-xs'>Raiz</button>
                    </div>";
        }

        $openDefaultClass = $id == 0 ? "open" : "close";
        $openDefaultStyle = $id == 0 ? "" : "display: none;";

        $html .= "<ul id='{$id}' class='list-group {$openDefaultClass}' style='{$openDefaultStyle}'>";
        foreach ($items as $item) {
            /**Enumeração */
            $strIndex = $indexes . (!empty($indexes) ? "." : "") . "$index";

            /**Html */
            $html .= "<li id='{$item->id}' class='list-group-item'>";

            /**Botões */
            $html .= "<div class='btn-group' role='group'>";

            if ($item->status == 1) {
                /**Adicionar */
                $html .= "<button id='{$item->id}' title='Adicionar' id-type='{$idType}' class='btn-add-item btn btn-xs btn-info'>
                            <i class='fas fa-plus'></i>
                          </button>";
            }

            /**Editar */
            $html .= "<button id='{$item->id}' title='Editar' id-type='{$idType}' class='btn-edit-item btn btn-xs btn-primary'>
                            <i class='fa fa-pencil-alt'></i>
                      </button>";

            /**Elementos filhos */
            $children = !empty($item->children) ? true : false;

            if ($item->status == 1 && $children) {
                /**Clonar */
                $html .= "<button id='{$item->id}' title='Clonar Estrutura' class='btn-clone-item btn btn-xs btn-warning'>
                            <i class='far fa-clone'></i>
                          </button>";
            }

            if ($children) {
                /**Se tiver tiver elementos filhos exibe botão para expandir */
                $html .= "<a class='btn btn-xs btn-default itemWithChildren'>
                            <i class='fas fa-angle-right'></i>
                          </a>";
            }

            /**Background do elemento */
            $bgElement = ($item->status == 1 ? "bg-default" : "bg-danger");
            $html .= "      <button class='btn btn-xs {$bgElement}'>";

            /**text do Elemento */
            $html .= "  {$strIndex} - {$item->name}";

            $html .= "</button>";
            /**Botões fim*/
            $html .= "</div>";

            if ($children) {
                $html .= $this->recursiveTreeView($item->children, $idType, $item->id, $strIndex);
            }

            $html .= "</li>";

            $index++;
        }

        $html .= "</ul>";

        return $html;
    }

    public function recursiveTreeViewNoAction($items, $itemIdSelected = "", $id = null, $indexes = "", $btnClass = "tree")
    {
        $index = 1;
        $html = "";

        /**Se for raiz ? exibe os elementos que a pertence : se não mantem fechado
         * id = 0 é a raiz
         */

        $openDefaultClass = $id == 0 ? "open" : "close";
        $openDefaultStyle = $id == 0 ? "" : "display: none;";

        $html .= "<ul id='{$id}' father-id='{$id}' class='list-group {$openDefaultClass}' style='{$openDefaultStyle}'>";
        foreach ($items as $item) {
            /**Enumeração */
            $strIndex = $indexes . (!empty($indexes) ? "." : "")  . "$index";

            $html .= "<li id='{$item->id}' class='list-group-item'>";

            /**Botões */
            $html .= "<div class='btn-group' role='group'>";

            /**Elementos filhos */
            $children = !empty($item->children) ? true : false;

            if ($children) {
                /**Se tiver tiver elementos filhos exibe botão para expandir */
                $html .= "<a class='btn btn-xs btn-default itemWithChildren'>
                            <i class='fas fa-angle-right'></i>
                          </a>";
            }

            /**Background do elemento */
            $bgElement = $itemIdSelected == $item->id ? "btn-primary" : "btn-default";
            if (is_array($itemIdSelected)) {
                $bgElement = in_array($item->id, $itemIdSelected) ? "btn-primary" : "btn-default";
            }
            $html .= "<button type='button' class='btn btn-xs {$btnClass} {$bgElement}' cost-center-id='{$item->id}'>";

            /**text do Elemento */
            $html .= "  {$strIndex} - {$item->name}";

            $html .= "</button>";
            /**Botões fim*/

            $html .= "</div>";

            if ($children) {
                $html .= $this->recursiveTreeViewNoAction($item->children, $itemIdSelected, $item->id, $strIndex, $btnClass);
            }

            $html .= "  </li>";
            $index++;
        }
        $html .= "</ul>";

        return $html;
    }

    public function recursiveOptionView($items, $itemIdSelected = "", $ownId = "", $showChildren = true, $indexes = "")
    {
        $index = 1;
        $html = "";
        foreach ($items as $item) {
            $strIndex = $indexes . (!empty($indexes) ? "." : "")  . "$index";
            if ($ownId != $item->id) {
                $html .= "<option value=\"$item->id\"" . (!empty($itemIdSelected) && $itemIdSelected == $item->id ? 'selected' : '') . ">{$strIndex} - {$item->name}</option>";
            }

            if (!empty($item->children)) {
                if ($ownId != $item->id) {
                    $html .= $this->recursiveOptionView($item->children, $itemIdSelected, $ownId, $showChildren, $strIndex);
                } else if ($showChildren) {
                    $html .= $this->recursiveOptionView($item->children, $itemIdSelected, $ownId, $showChildren, $strIndex);
                }
            }

            $index++;
        }

        return $html;
    }

    public function recursiveTreeArray($items, $itemIdSelected = "", $ownId = "", $showChildren = true, $indexes = "")
    {
        $index = 1;
        $array = [];

        foreach ($items as $item) {
            $strIndex = $indexes . (!empty($indexes) ? "." : "")  . "$index";
            if ($ownId != $item->id) {
                $array[] = $strIndex . " - " . $item->name;
            }

            if (!empty($item->children)) {
                if ($ownId != $item->id) {
                    $array[] = $this->recursiveTreeArray($item->children, $itemIdSelected, $ownId, $showChildren, $strIndex);
                } else if ($showChildren) {
                    $array[] = $this->recursiveTreeArray($item->children, $itemIdSelected, $ownId, $showChildren, $strIndex);
                }
            }
            $index++;
        }
        return $array;
    }

    public function findIndex(array $items, $itemIdSelected = "", $ownId = "", string $indexes = ""): string
    {
        $index = 1;
        $html = '';
        foreach ($items as $item) {
            $strIndex = $indexes . (!empty($indexes) ? "." : "")  . "{$index}";
            if ($ownId == $item->id) {
                $html .= $strIndex . " - " . $item->name . " {{active}} \n";
            } else {
                $html .= $strIndex . " - " . $item->name . "\n";
            }

            if (!empty($item->children)) {
                $html .= $this->recursiveTreeString($item->children, $itemIdSelected, $ownId, $strIndex, "", true);
            }
            $index++;
        }

        $html = explode("\n", $html);
        $html = array_filter($html, function ($value) {
            return strpos($value, '{{active}} ') > 1;
        });

        if (empty($html)) return false;
        return str_replace('{{active}}', '', reset($html));
    }

    public function recursiveTreeString($items, $itemIdSelected = "", $ownId = "", $showChildren = true, $indexes = "", $writeActive = false): string
    {
        $index = 1;
        $html = '';
        foreach ($items as $item) {
            $strIndex = $indexes . (!empty($indexes) ? "." : "")  . "{$index}";
            if ($ownId != $item->id) {
                $html .= $strIndex . " - " . $item->name . "\n";
            } else if ($writeActive) {
                $html .= $strIndex . " - " . $item->name . " {{active}} \n";
            }

            if (!empty($item->children)) {
                if ($ownId != $item->id) {
                    $html .= $this->recursiveTreeString($item->children, $itemIdSelected, $ownId, $showChildren, $strIndex, $writeActive);
                } else if ($showChildren) {
                    $html .= $this->recursiveTreeString($item->children, $itemIdSelected, $ownId, $showChildren, $strIndex, $writeActive);
                }
            }

            $index++;
        }

        return $html;
    }

    public function recursiveOptionArray(array $items, int $active = null, int $ownId = null, bool $showChildren = true, string $indexes = "")
    {
        $index = 1;
        $cost_centers = [];
        foreach ($items as $item) {
            $strIndex = $indexes . (!empty($indexes) ? "." : "")  . "$index";

            if ($ownId != $item->id) {
                $cost_center = (object)[
                    'value' => $item->id,
                    'text' => $strIndex . " - " . $item->name,
                    'active' => $active === $item->id ? true : false
                ];
            }
            array_push($cost_centers, $cost_center);
            if (!empty($item->children)) {
                if ($ownId != $item->id) {
                    $html = $this->recursiveOptionArray($item->children, $active, $ownId, $showChildren, $strIndex);
                } else if ($showChildren) {
                    $html = $this->recursiveOptionArray($item->children, $active, $ownId, $showChildren, $strIndex);
                }
            }

            $index++;
        }

        return $cost_centers;
    }
}
