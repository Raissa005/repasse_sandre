<?php

namespace RR\components;

use RR\libs\Secure;
use RR\libs\Util;
use RR\model\Branch;
use RR\model\Customer;
use RR\model\ImmovableResource;
use RR\model\Property;
use RR\model\PropertyCategory;
use RR\model\PropertyFilter;
use RR\model\PropertyType;
use RR\model\User;

class PropertyFilterComponent
{
    public function __construct()
    {
        $this->render();
    }

    private function render()
    {
        $features = (new ImmovableResource())->getAndFilterAllItem(["status" => true, "filter" => true], 0)->data;
        array_map(function ($feature) {
            switch ($feature->data_type) {
                case '1':
                    /**Texto */
                    $feature->inputType = "text";
                    $feature->inputAttr = "";
                    break;
                case '2':
                    /**Número */
                    $feature->inputType = "number";
                    $feature->inputAttr = "";
                    break;
                case '3':
                    /**Data */
                    $feature->inputType = "date";
                    $feature->inputAttr = "";
                    break;
                case '4':
                    /**Sim / Não */

                    break;

                default:
                    $feature->inputType = "text";
                    $feature->inputAttr = "";
                    break;
            }
        }, $features);

        $html = '';
        $filters = (new PropertyFilter())->getWithFiltersAllItems([(object)['columns' => ['status' => ['comparison' => 'EQUAL', 'value' => 1]]]], [(object)['columns' => ['id', 'name', 'name_column', 'item_order', 'status']]], ['orderBy' => 'property_filter.item_order ASC'])->data;

        $html .= '<div class="col-lg-12"><div class="row">';

        foreach ($filters as $key => $filter) {
            $model = [];
            $option = '';

            if ($key % 4 == 0) {
                $html .= '</div><div class="row">';
            }

            if (in_array($filter->name_column, ['id_city', 'id_branch', 'created_by', 'id_user_owner', 'id_residential_type', 'id_property_category', 'id_owner', 'property_branch'])) {
                /** @see [ Creating models for options ] */
                switch ($filter->name_column) {
                    case 'id_city':
                        $model = (new Property)->getCitiesForProperties(['status' => true, 'id_branch' => $_SESSION['RR']->branch->current->id]);
                        array_map(function ($city) {
                            $city->name = Util::titleCase($city->name);
                        }, $model);
                        break;

                    case in_array($filter->name_column, ['created_by', 'id_user_owner']):
                        if (Secure::access_secretary()) {
                            $model =  (new User())->getAndFilterAllUsers(0, ["id_branch_and_profile" => $_SESSION['RR']->branch->current->id, "status" => true, "order" => " up.access ASC, u.name ASC"], 0)->data;
                        }
                        break;

                    case 'property_branch':
                        $model = (new Branch)->getBranchesByProperties();
                        break;

                    case 'id_residential_type':
                        $model = (new PropertyType())->getAndFilterAllItem(['status' => true], 0)->data;
                        break;

                    case 'id_property_category':
                        $model = (new PropertyCategory())->getAndFilterAllPropertyCategory(0, ['status' => true], 0)->data;
                        break;

                    case 'id_owner':
                        $model = (new Customer())->getAllCustomerWithProperties(['status' => 1, 'id_branch' => $_SESSION['RR']->branch->current->id]);;
                        break;
                }

                /** @see [ Options / Validation one to one ] */
                if (in_array($filter->name_column, ['id_city', 'created_by', 'id_user_owner'])) {
                    foreach ($model as $arrOption) {
                        $option .= "<option value=\"{$arrOption->id}\" " . (isset($_GET[$filter->name_column]) && $_GET[$filter->name_column] == $arrOption->id ? 'selected' : '') . ">{$arrOption->name}</option>\n";
                    }
                }

                /** @see [ Options / Validation multiple ] */
                if (in_array($filter->name_column, ['id_branch', 'id_residential_type', 'id_property_category', 'id_owner', 'property_branch'])) {
                    foreach ($model as $arrOption) {
                        $arrOption->name = (in_array($filter->name_column, ['id_owner']) ? $arrOption->identifier_name : $arrOption->name);
                        $option .= "<option value=\"{$arrOption->id}\" " . (in_array($arrOption->id, (isset($_GET[$filter->name_column]) ? $_GET[$filter->name_column] : [])) ? 'selected' : '') . ">{$arrOption->name}</option>\n";
                    }
                }
            }

            if (in_array($filter->name_column, ['status', 'site_status'])) {
                $selectAll = '';
                /** @see [ Option Able / Disable ] */
                if (in_array($filter->name_column, ['site_status'])) {
                    $selectAll = "<option value=\"\">Todos</option>";
                }
                $html .= "<div class=\"col-md-3\">
                                <div class=\"form-group\">
                                    <label for=\"{$filter->name_column}\">{$filter->name}</label>
                                    <select class=\"form-control\" id=\"{$filter->name_column}\" name=\"{$filter->name_column}\">
                                        {$selectAll}
                                        <option value=\"1\"" . (isset($_GET[$filter->name_column]) && $_GET[$filter->name_column] == '1' ? 'selected' : '') . ">Ativo</option>
                                        <option value=\"0\"" . (isset($_GET[$filter->name_column]) && $_GET[$filter->name_column] == '0' ? 'selected' : '') . ">Inativo</option>
                                    </select>
                                </div>
                            </div>";
            } else if (in_array($filter->name_column, ['availability'])) {
                $selectAll = '';
                /** @see [ Option Able / Disable ] */
                $html .= "<div class=\"col-md-3\">
                                <div class=\"form-group\">
                                    <label for=\"{$filter->name_column}\">{$filter->name}</label>
                                    <select class=\"form-control\" id=\"{$filter->name_column}\" name=\"{$filter->name_column}\">
                                        {$selectAll}
                                        <option value=\"1\"" . (isset($_GET[$filter->name_column]) && $_GET[$filter->name_column] == '1' ? 'selected' : '') . ">Disponível</option>
                                        <option value=\"0\"" . (isset($_GET[$filter->name_column]) && $_GET[$filter->name_column] == '0' ? 'selected' : '') . ">Indisponível</option>
                                    </select>
                                </div>
                            </div>";
            } else if (in_array($filter->name_column, ['cod', 'name', 'address'])) {
                /** @see [ Input Text ] */
                $html .= "<div class=\"col-md-3\">
                            <div class=\"form-group\">
                                <label for=\"{$filter->name_column}\">{$filter->name}</label>
                                <input type=\"text\" autocomplete=\"off\" class=\"form-control\" placeholder=\"$filter->name\" id=\"{$filter->name_column}\" name=\"{$filter->name_column}\" value=\"" . (isset($_GET[$filter->name_column]) ? $_GET[$filter->name_column] : '') . "\">
                            </div>
                        </div>";
            } else if (in_array($filter->name_column, ['id_city', 'created_by', 'id_user_owner'])) {
                /** @see [ Select ] */
                $html .= "<div class=\"col-md-3\">
                            <div class=\"form-group\">
                                <label for=\"{$filter->name_column}\">{$filter->name}</label>
                                <select class=\"form-control\" id=\"{$filter->name_column}\" name=\"{$filter->name_column}\">
                                    <option value=\"\">Todos</option>
                                    {$option}
                                </select>
                            </div>
                        </div>";
            } else if (in_array($filter->name_column, ['id_residential_type', 'id_property_category', 'id_owner', 'property_branch'])) {
                $colRow = in_array($filter->name_column, ['property_branch']) ? 'col-md-12' : 'col-md-3';
                /** @see [ Select Multiple ] */
                $html .= "<div class=\"{$colRow}\">
                            <div class=\"form-group\">
                                <label for=\"{$filter->name_column}\">{$filter->name}</label>
                                <select class=\"form-control\" name=\"{$filter->name_column}[]\" id=\"{$filter->name_column}\" multiple>
                                {$option}
                                </select>
                            </div>
                        </div>";
            } else if (in_array($filter->name_column, ['price_start', 'price_end'])) {
                /** @see [ Input Number ] */
                $html .= "<div class=\"col-md-3\">
                            <div class=\"form-group\">
                                <label for=\"{$filter->name_column}\">{$filter->name}</label>
                                <input type=\"text\" autocomplete=\"off\" class=\"form-control\" placeholder=\"{$filter->name}\" id=\"{$filter->name_column}\" name=\"price_m[" . ($filter->name_column == 'price_start' ? 'start' : 'end') . "]\" value=\"" . (isset($_GET['price_m'][$filter->name_column == 'price_start' ? 'start' : 'end']) ? $_GET['price_m'][$filter->name_column == 'price_start' ? 'start' : 'end'] : '') . "\" data-mask-money>
                            </div>
                        </div>";
            } else if (in_array($filter->name_column, ['order'])) {
                /** @see [ Select Order ] */
                $html .= "<div class=\"col-md-3\">
                            <div class=\"form-group\">
                                <label for=\"order\">Ordenar por</label>
                                <select class=\"form-control\" id=\"order\" name=\"order\">
                                    <option value=\"1\"" . (isset($_GET['order']) && $_GET['order'] == '1' ? 'selected' : '') . ">Cod. Crescente</option>
                                    <option value=\"2\"" . (isset($_GET['order']) && $_GET['order'] == '2' ? 'selected' : '') . ">Cod. Decrescente</option>
                                    <option value=\"3\"" . (isset($_GET['order']) && $_GET['order'] == '3' ? 'selected' : '') . ">Nome Crescente</option>
                                    <option value=\"4\"" . (isset($_GET['order']) && $_GET['order'] == '4' ? 'selected' : '') . ">Nome Decrescente</option>
                                    <option value=\"5\"" . (isset($_GET['order']) && $_GET['order'] == '5' ? 'selected' : '') . ">Valor Crescente</option>
                                    <option value=\"6\"" . (isset($_GET['order']) && $_GET['order'] == '6' ? 'selected' : '') . ">Valor Decrescente</option>
                                    <option value=\"7\"" . (isset($_GET['order']) && $_GET['order'] == '7' ? 'selected' : '') . ">Data de Cadastro Crescente</option>
                                    <option value=\"8\"" . (isset($_GET['order']) && $_GET['order'] == '8' ? 'selected' : '') . ">Data de Cadastro Decrescente</option>
                                </select>
                            </div>
                        </div>";
            } elseif (in_array($filter->name_column, ['neighborhood', 'allotment'])) {
                /** @see [ Select from Javascript ] */
                $html .= "<div class=\"col-md-3\">
                            <div class=\"form-group\">
                                <input type=\"hidden\" id=\"get-{$filter->name_column}\" value=\"" . (isset($_GET[$filter->name_column]) ? $_GET[$filter->name_column] : '') . "\"\>
                                <label for=\"{$filter->name_column}\">{$filter->name}</label>
                                <select class=\"form-control\" id=\"{$filter->name_column}\" name=\"{$filter->name_column}\"></select>
                            </div>
                        </div>";
            }
        }

        $html .= '</div></div>';

?>
        <form action="<?= URL . 'property' ?>" method="GET">
            <input type="hidden" name="b" value="s">
            <div class="box box-info <?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'collapsed-box' ?>">
                <div class="box-header with-border" data-widget="collapse">
                    <h3 class="box-title">Filtros</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['b']) && $_GET['b'] == 's' ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                    </div>
                </div>
                <div class="box-body" style="<?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'display: none;' ?>">
                    <div class="row">
                        <!-- <filters> -->
                        <?= $html ?>
                        <!-- </filters> -->
                        <div class="col-lg-12">
                            <div class="panel-group no-margin" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-info">
                                    <div class="panel-heading" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <h4 class="panel-title text-center">
                                            <span role="button">Filtros Avançados</span>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse <?= !empty($featuresFilter) ? "in" : "" ?>" role="tabpanel" aria-labelledby="headingOne">
                                        <div class="panel-body">
                                            <div class="row">
                                                <?php foreach ($features as $feature) { ?>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label class=""><?= Util::titleCase($feature->name) . ":" ?></label>
                                                            <?php if ($feature->data_type == 4) { ?>
                                                                <select class="form-control" name="features[<?= $feature->id ?>]">
                                                                    <option value="" <?= (isset($_GET['features'][$feature->id]) && empty($_GET['features'][$feature->id]) ? "selected" : '') ?>>Todos</option>
                                                                    <option value="Não" <?= (isset($_GET['features'][$feature->id]) && $_GET['features'][$feature->id] == 'Não' ? "selected" : '') ?>>Não</option>
                                                                    <option value="Sim" <?= (isset($_GET['features'][$feature->id]) && $_GET['features'][$feature->id] == 'Sim' ? "selected" : '') ?>>Sim</option>
                                                                </select>
                                                            <?php } else { ?>
                                                                <input type="<?= $feature->inputType ?>" autocomplete="off" class="form-control" placeholder="" name="features[<?= $feature->id ?>]" value="<?= (isset($_GET['features'][$feature->id]) ? $_GET['features'][$feature->id] : ''); ?>">
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <h5 class="text-bold">Exibir Colunas</h5>
                                <label for="columnPropertyIdName">Código - Nome
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyIdName" id="columnPropertyIdName" <?= isset($_GET['columnPropertyIdName']) && $_GET['columnPropertyIdName'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyType">Tipo - Categoria
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyType" id="columnPropertyType" <?= isset($_GET['columnPropertyType']) && $_GET['columnPropertyType'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyLocation">Localização
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyLocation" id="columnPropertyLocation" <?= isset($_GET['columnPropertyLocation']) && $_GET['columnPropertyLocation'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyValue">Valor
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyValue" id="columnPropertyValue" <?= isset($_GET['columnPropertyValue']) && $_GET['columnPropertyValue'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyStatus">Status
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyStatus" id="columnPropertyStatus" <?= isset($_GET['columnPropertyStatus']) && $_GET['columnPropertyStatus'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyBranch">Filiais
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyBranch" id="columnPropertyBranch" <?= isset($_GET['columnPropertyBranch']) && $_GET['columnPropertyBranch'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyCreationDate">Data Criação
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyCreationDate" id="columnPropertyCreationDate" <?= isset($_GET['columnPropertyCreationDate']) && $_GET['columnPropertyCreationDate'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyUser">Usuário
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyUser" id="columnPropertyUser" <?= isset($_GET['columnPropertyUser']) && $_GET['columnPropertyUser'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyNeighborhood">Bairro
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyNeighborhood" id="columnPropertyNeighborhood" <?= isset($_GET['columnPropertyNeighborhood']) && $_GET['columnPropertyNeighborhood'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyClassification">Classificação
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyClassification" id="columnPropertyClassification" <?= isset($_GET['columnPropertyClassification']) && $_GET['columnPropertyClassification'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyTotalArea">Área Total
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyTotalArea" id="columnPropertyTotalArea" <?= isset($_GET['columnPropertyTotalArea']) && $_GET['columnPropertyTotalArea'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="columnPropertyOwner">Proprietário
                                    <input type="checkbox" class="custon-checkbox" name="columnPropertyOwner" id="columnPropertyOwner" <?= isset($_GET['columnPropertyOwner']) && $_GET['columnPropertyOwner'] == 'on' ? 'checked' : '' ?>>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a href="<?= URL . 'property' ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
                    <button type="submit" class="btn pull-right btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                </div>
            </div>
        </form>
<?php }
}
?>