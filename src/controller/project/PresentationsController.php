<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Util;
use RR\model\Attendance;
use RR\model\Branch;
use RR\model\PresentationCard;
use RR\model\Property;
use RR\model\User;

class PresentationsController
{
    public function index($slugify, $code)
    {
        if (isset($code) && !empty($code)) {
            $item = (new ModelGenerico)->getItemByGenericField($code, "displayed_properties", "code", true);
            $propertyApply = $item[0];
            $presentationCard = (new PresentationCard())->getItemById8161(1);
            $system = (new ModelGenerico)->getItemById8161(1, "system_config");
            $site = (new ModelGenerico)->getItemById8161(1, "configuracao");

            if (empty($propertyApply) || $propertyApply->status_code == 0) {
                header('location: ' . URL . 'login/index');
                exit();
            }

            $product = (new Property)->getProductsById($propertyApply->id_product);

            $filtersFeatures = array(
                'id_product' => $item[0]->id_product,
                'product_status' => 1,
                'immovable_resource_status' => 1,
                'property_type_status' => 1,
                'valued' => 1,
                'order' => " ir.name ASC "

            );

            $features = (new Property)->getAndFilterProductOwnershipFeature($filtersFeatures);
            $images = (new Property)->getProductsImages($product->id);

            $attendance = (new Attendance)->getAttendanceById($propertyApply->id_attendance);
            $user = (new User)->getUserById($attendance->created_by);
            $branch = (new Branch)->getItemById8161($attendance->id_branch);

            session_start();
            if (!isset($_SESSION['view']['id']) || !in_array($product->id, $_SESSION['view'])) {

                if (empty($_SESSION['view']['id'])) {
                    $_SESSION['view']['id'] = $product->id;
                }

                $IP = Util::getIp();
                (new gerenciaPost())->insert7181(["IP" => $IP, "id_displayed_properties" => $propertyApply->id], "presentations", false, false);
            }

            require APP . 'view/presentations/index.php';
        } else {

            header('location: ' . URL . 'login/index');
            exit();
        }
    }
}
