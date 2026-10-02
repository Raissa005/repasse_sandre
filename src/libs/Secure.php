<?php

namespace RR\libs;

use RR\model\MenuAccess;

use function RR\Controller\redirect;

class Secure
{

    public static function redirectFunction($redirect = true, $locationController = "home", $get = "")
    {
        if ($redirect) {
            if ($get === 'authorization=false') {
                Toast::unauthorized();
                $get = '';
            }

            header('location: ' . URL . $locationController . ($get != "" ? "?$get" : ""));
            exit;
        }
    }

    /**Function for SuperAdm */
    public static function restricted_superAdm($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->branch->current->id == 0) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
            } else {
                return true;
            }
            exit;
        }
        return false;
        exit;
    }

    public static function unrestricted_superAdm($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->branch->current->id != 0) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
            } else {
                return false;
            }
            exit;
        }
        return true;
        exit;
    }

    public static function access_generic($access, $location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->profile->access > $access) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
                exit;
            }
            return false;
            exit;
        }
        return true;
        exit;
    }

    public static function access_dev($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->profile->access > 1) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
                exit;
            }
            return false;
            exit;
        }
        return true;
        exit;
    }

    public static function access_superAdm($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->profile->access > 5) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
                exit;
            }
            return false;
        }
        return true;
    }

    public static function access_admin($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->profile->access > 10) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
                exit;
            }
            return false;
            exit;
        }
        return true;
        exit;
    }

    public static function access_manager($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->profile->access > 20) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
                exit;
            }
            return false;
            exit;
        }
        return true;
        exit;
    }

    public static function access_secretary($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->profile->access > 25) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
                exit;
            }
            return false;
            exit;
        }
        return true;
        exit;
    }

    public static function access_seller($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->profile->access > 30) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
                exit;
            }
            return false;
            exit;
        }
        return true;
        exit;
    }

    public static function is_seller($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->profile->access == 30) {
            return true;
        }
        if ($location) redirect($locationController);
        return false;
    }

    public static function seller_manager($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->profile->access == 20) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
                exit;
            }
            return false;
            exit;
        }
        return true;
        exit;
    }

    public static function secretary($location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->profile->access === "25") {
            if ($location) {
                header('location: ' . URL . "$locationController/");
                exit;
            }
            return false;
            exit;
        }
        return true;
        exit;
    }

    public static function protect_managers($location = false, $locationController = "home")
    {
        if (in_array($_SESSION['RR']->profile->id, ['3', '6', '7'])) {
            if ($location) {
                header('location: ' . URL . "$locationController/");
                exit;
            }
            return false;
            exit;
        }
        return true;
        exit;
    }

    /**
     * Criador desse item
     * @return boolean
     */
    public static function creator($created, $location = false, $locationController = "home")
    {
        if ($_SESSION['RR']->user->id != $created) {
            if ($location) {
                header('location: ' . URL . "$locationController?authorization=false");
            } else {
                return false;
            }
            exit;
        }
        return true;
        exit;
    }

    /**
     * @param array|int $idBranches
     * @param string $locationController
     */
    public static function productsBranches($branchId, $locationController = "home")
    {
        if (!in_array($_SESSION['RR']->branch->current->id, $branchId) && !empty($branchId)) {
            header('location: ' . URL . "$locationController");
            exit;
        }
    }

    public static function customerBranches(array $branchesId, string $locationController = "home")
    {
        if (!in_array($_SESSION['RR']->branch->current->id, $branchesId)) {
            header('location: ' . URL . "$locationController/");
            exit;
        }
    }

    public static function branch($branchId, $locationController = "home")
    {
        if ($_SESSION['RR']->branch->current->id != $branchId && $branchId != "") {
            header('location: ' . URL . "$locationController/");
            exit;
        }
    }

    public static function userBranches($branches, $userId, $access, $locationController = "home")
    {
        if (!self::access_superAdm()) {
            if (!self::access_secretary() && !self::creator($userId)) {
                header('location: ' . URL . "home/?authorization=false");
                exit;
            } else if (!empty($branches)) {
                if (!Util::findArrayObjectElement($branches, "id", $_SESSION['RR']->branch->current->id) || !self::access_generic($access)) {
                    header('location: ' . URL . "$locationController/?authorization=false");
                    exit;
                }
            }
        } else if (!self::access_generic($access)) {
            header('location: ' . URL . "$locationController/?authorization=false");
            exit;
        }
    }

    public static function check_post_method(string $path = "home"): void
    {
        if (empty($_POST)) {
            Toast::errorToast('Tente novamente');
            redirect($path);
        }
    }

    public static function individual_menu_access(int $menuId, string $locationController = "home")
    {
        $response = (new MenuAccess)->getWithFiltersAllItems([(object)['columns' => ['id_menu' => (object)['value' => $menuId], 'id_profile' => (object)['value' => $_SESSION['RR']->profile->id]]]])->data;

        // C3: sem linha em menu_access = liberado só para Superadm, Administrador e Desenvolvedor; para os demais perfis, negado
        if (empty($response)) {
            if (self::access_admin()) return;
            redirect($locationController);
        }

        array_filter($response, function ($item) use ($locationController) {
            if ($item->status == 0) redirect($locationController);
        });
    }
}
