<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Util;
use RR\model\Branch;
use RR\model\Contract;
use RR\model\Customer;
use PDOException;
use RR\libs\Secure;
use RR\model\Property;
use RR\libs\Date;

class PrintController
{
    public function contract($customerId, $code)
    {
        $contractModel = new Contract();
        $systemConfig = (new ModelGenerico())->getItemById8161(1, "system_config");

        $logoFavicon = "img/settings/logo_favicon-{$systemConfig->logo_favicon_cont}.{$systemConfig->logo_favicon_ext}";

        $contract = $contractModel->getContractCustomerByCode($code);

        $contractProducts = (new ModelGenerico())->getItemByGenericField($contract->id, "property_involved_authorization_contract", "id_property_authorization_contract");

        $contractVariables = $contractModel->getContractVariablesByCostumerId($contract->id_customer);

        $contractText = Self::replaceVariables($contract->contract_text, $contractVariables, $contractProducts);

        require APP . 'view/customer/print.php';

    }

    public function replaceVariables($contractText, $contractVariables, $contractProducts)
    {
        $contractText = str_replace("breakPage", "<span class='break-page-print-after'></span>", $contractText);

        foreach ($contractVariables as $key => $value) {

            if($key == 'rg_cliente') {
                $rgCliente = Util::maskRg($value);
                $contractVariables->rg_cliente = $rgCliente;
            }

            if($key == 'cpf_cliente') {
                $cpfCliente = Util::maskCpf($value);
                $contractVariables->cpf_cliente = $cpfCliente;
            }

            if($key == 'data_criacao') {
                foreach ($contractProducts as $contract){
                    $authorization = (new Contract)->getContractById($contract->id_property_authorization_contract);
                }
                $value = $authorization->created_at;
                
                $creationDay = date("d", strtotime($value));
                $creationMonth = date("m", strtotime($value));
                $creationYear = date("Y", strtotime($value));

                $contractVariables->dia = $creationDay;
                $contractVariables->mesExtenso = Date::month_full($creationMonth);
                $contractVariables->ano = $creationYear;
            }

            if($key == 'autorizacaoImovel_produtos') {
                $value = '';
                foreach ($contractProducts as $products){
                    $product = (new Property)->getItemById1002($products->id_product);

                    $product->value = intval($product->value);
                    $value = $value . '<p><strong> Código (' . $product->cod . ') ' . $product->property_type_name . ' ' . $product->name . '</strong> Localizado em ' . $product->city_name . ', ' . $product->state_name . ' Sob o valor de: <strong>' . Util::maskMoney($product->value) . '</strong> (' . Util::converte($product->value) . '). </p>';

                }
            }

            if($key == 'comissao_filial') {
                $value = intval($value);
                $contractVariables->comissaoExtenso_filial = Util::converte($value, false, false) . ' porcento';
                $value = $value . '%';
            }

            if($key == 'diasRecadastro_filial') {
                foreach ($contractProducts as $products){
                    $product = (new Property)->getItemById1002($products->id_product);
                    $value = $product->re_registered_at;
                    $branch = (new Branch)->getItemById($product->id_branch);
                }

                $valueNew = date("Y-m-d", strtotime("+$branch->immovable_record month", strtotime($value)));

                $value = Date::rangeOfDays($value, $valueNew);
                
                $contractVariables->diasRecadastroExtenso_filial = Util::converte($value, false, false);
            }

            $contractText = str_replace("{%" . $key . "%}", $value, $contractText);
        }

        return $contractText;
    }

    public function signed($contractId)
    {
        $contract = (new ModelGenerico())->getItemById8161($contractId, "property_authorization_contract");
        $customerModel = new Customer();
        $customer = $customerModel->getCustomerById($contract->id_customer);

        Secure::check_post_method("print/contract/" . $customer->id . '/' . $contract->code);

        $gerenciaPost = new GerenciaPost();
        $branchModel = new Branch();
        $productModel = new Property();

        $contractProducts = (new ModelGenerico())->getItemByGenericField($contract->id, "property_involved_authorization_contract", "id_property_authorization_contract");
        $branch = $branchModel->getItemById8161($customer->id_branch);

        $arrPost = array(
            'name' => $_POST['name'],
            'birth_date' => $_POST['birth_date'],
            'cpf' => $_POST['cpf'],
            'signed' => true,
            'signed_at' => date("Y-m-d H:i:s"),
            'ip' => Util::getIp(),
        );

        $dateReRegisteredAt = date("Y-m-d", strtotime("+$branch->immovable_record months"));

        try {
            $gerenciaPost->update8191($arrPost, 'property_authorization_contract', 'id', $contractId, false);

            foreach ($contractProducts as $contractProduct) {
                $product = $productModel->getProductsById($contractProduct->id_product);

                $arrPostTimelineReRegisteredAtProducts = array(
                    "id_product" => $product->id,
                    "id_authorization_contract" => $contractId,
                    "created_by" => $contract->created_by,
                    "new_re_registered_at" => $dateReRegisteredAt,
                    "last_re_registered_at" => $product->re_registered_at,
                );
                $gerenciaPost->insert7181($arrPostTimelineReRegisteredAtProducts, "timeline_re_registered_at_products", false, false);

                $arrPostReRegisteredAt = array('re_registered_at' => $dateReRegisteredAt);
                $gerenciaPost->update8191($arrPostReRegisteredAt, "products", "id", $product->id, false);
            }

            header('location:' . URL . "print/contract/" . $customer->id . '/' . $contract->code);
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . "print/contract/" . $customer->id . '/' . $contract->code);
            exit;
        }
    }
}
