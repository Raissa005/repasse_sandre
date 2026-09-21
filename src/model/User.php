<?php

namespace RR\model;

use PDOException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use RR\core\Model;
use RR\libs\Secure;
use RR\libs\Util;

use function RR\Controller\redirect;

class User extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'users';
        $joins = [
            (object)[
                'table' => 'user_branches',
                'join' => 'inner',
                'where' => "{$this->table}.id = user_branches.id_user"
            ],
            (object)[
                'table' => 'users_profiles',
                'join' => 'inner',
                'where' => "{$this->table}.id_profile = users_profiles.id"
            ],
            (object)[
                'table' => 'customer',
                'join' => 'left',
                'where' => "{$this->table}.id_customer = this->table.id"
            ],
            (object)[
                'table' => 'manager_team',
                'join' => 'inner',
                'where' => "{$this->table}.id = this->table.id_manager"
            ],
            (object)[
                'table' => 'lead_random',
                'join' => 'inner',
                'where' => "{$this->table}.id = this->table.id_user"
            ],
        ];

        parent::__construct($this->table, $joins);
    }

    public function submitAddForm()
    {
        $arrayPost = array(
            'name' => $_POST['name'],
            'creci' => $_POST['creci'],
            'email' => strtolower($_POST['email']),
            'id_profile' => $_POST['id_profile'],
            'cpf' => Util::removeNonNumericCharacters($_POST['cpf']),
            'phone' => Util::removeNonNumericCharacters($_POST['phone']),
            'id_branch' => !empty($_POST['id_branch']) ? $_POST['id_branch'][0] : 0,
            'password' => password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 12)),
            'id_branch' => (isset($_POST['id_branch']) ? $_POST['id_branch'][0] : "0"),
            'created_by' => $_SESSION['RR']->user->id,
        );
        try {
            $this->db->beginTransaction();
            $response = $this->insert($arrayPost);

            foreach (($_POST['id_profile'] == 1 || $_POST['id_profile'] == 5 ? (new Branch)->getWithFiltersAllItems()->data : $_POST['id_branch']) as $branch) {
                (new UserBranch)->insert(['id_user' => $response->lastId, 'id_branch' => ($_POST['id_profile'] == 1 || $_POST['id_profile'] == 5 ? $branch->id : $branch)]);
            }

            if (isset($_POST['id_seller']) && $_POST['id_profile'] == 7) {
                foreach ($_POST['id_seller'] as $seller) {
                    (new ManagerTeam)->insert(['id_manager' => $response->lastId, 'id_seller' => $seller, 'created_by' => $_SESSION['RR']->user->id]);
                }
            }

            foreach ((new NetworkCardDigital)->getWithFiltersAllItems()->data as $network) {
                $network_post = ["id_user" => $response->lastId, "id_networks_card_digital" => $network->id];

                switch ($network->id) {
                    case '3':
                        $network_post['link'] = $arrayPost['email'];
                        break;
                    case '4':
                        $network_post['link'] = $arrayPost['phone'];
                        break;
                }

                (new UserNetwork)->insert($network_post);
            }

            $this->db->commit();
            return $response;
            exit;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Erro ao cadastrar usuário, tente novamente.'];
            exit;
        }
    }

    public function submitEditForm($itemId, $post, $files)
    {
        $item = $this->getItemById($itemId, [(object)['columns' => ['*']], (object)['table' => 'users_profiles', 'columns' => ['access' => ['users_profiles_access']]]]);
        $branches_user = (new Branch)->getWithFiltersAllItems(
            [
                (object)['table' => 'user_branches', 'columns' => ['id_user' => (object)['comparison' => '=', 'value' => $itemId]]],
                (object)['columns' => ['status' => (object)['comparison' => '=', 'value' => 1]]]
            ]
        )->data;

        Secure::userBranches($branches_user, $itemId, $item->users_profiles_access, 'users');

        $cache = new FilesystemAdapter();
        $cache->clear();

        $arrayPost = array(
            'name' => $post['name'],
            'email' => strtolower($post['email']),
            'creci' => $post['creci'],
            'id_profile' => $post['id_profile'],
            'id_customer' => !empty($post['id_customer']) ? $post['id_customer'] : '',
            'phone' => Util::removeNonNumericCharacters($post['phone']),
            'cpf' => Util::removeNonNumericCharacters($post['cpf']),
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date("Y-m-d H:i:s"),
        );

        if (Secure::access_admin()) {
            $arrayPost['id_branch'] = $post['id_branch'][0] ?? $item->id_branch;
        }

        if ($post['password_confirm'] === $post['password'] && $post['password'] != "") {
            $arrayPost['password'] = password_hash($post['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }

        try {
            $this->db->beginTransaction();
            $response = $this->update($arrayPost, 'id', $itemId);

            if (Secure::access_admin()) {
                (new UserBranch)->delete(['id_user' => $itemId]);
                foreach ((in_array($post['id_profile'], [1, 5]) ? (new Branch)->getWithFiltersAllItems()->data : $post['id_branch']) as $branch) {
                    (new UserBranch)->insert(['id_user' => $itemId, 'id_branch' => (in_array($post['id_profile'], [1, 5]) ? $branch->id : $branch)]);
                }
            }

            (new ManagerTeam)->delete(['id_manager' => $itemId]);
            if (isset($post['id_seller']) && $post['id_profile'] == 7) {
                foreach ($post['id_seller'] as $seller) {
                    (new ManagerTeam)->insert(['id_manager' => $itemId, 'id_seller' => $seller, 'created_by' => $_SESSION['RR']->user->id]);
                }
            }

            if (!empty($files['profile_picture']['tmp_name'])) {
                require_once APP . 'libs/wideImage/lib/WideImage.php';
                require_once APP . 'libs/wideImage/wide.php';
                require_once APP . 'libs/Resizer.php';

                if (!file_exists("img/users/$itemId/")) {
                    mkdir("img/users/$itemId/", 0777, true);
                }

                if (file_exists("img/users/$itemId/$itemId-profile-$item->profile_cont.$item->profile_ext")) {
                    unlink("img/users/$itemId/$itemId-profile-$item->profile_cont.$item->profile_ext");
                }

                $extension = str_replace(".", "", substr($files['profile_picture']['name'], -4));
                $this->update(["profile_capa" => true, "profile_cont" => ++$item->profile_cont, "profile_ext" => $extension], 'id', $itemId);

                if ($itemId == $_SESSION['RR']->user->id) {
                    $_SESSION['RR']->user->profileURL = "img/users/{$item->id}/{$item->id}-profile-{$item->profile_cont}.{$extension}";
                }

                $filename = $files['profile_picture']['tmp_name'];
                $path = "img/users/$itemId/";

                $size = getimagesize($files['profile_picture']['tmp_name']);
                if ($size[0] == 160 && $size[1] == 160) {
                    copy($files['profile_picture']['tmp_name'], $path . "$itemId-dc-$item->profile_cont.$extension");
                } else if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                    $profilePicture = wideImagePhoto($filename, $path, 160, 160, "$itemId-profile-$item->profile_cont", ".$extension", 100);
                } else if ($extension == "png" || $extension == "PNG") {
                    $profilePicture = wideImagePhoto($filename, $path, 160, 160, "$itemId-profile-$item->profile_cont", ".$extension", 9);
                }
            }
            $this->db->commit();
            return $response;
            exit;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Ocorreu um erro ao editar o usuário. Consulte o administrador do sistema.'];
            exit;
        }
    }

    public function submitRhForm($itemId)
    {

        $item = $this->getItemById($itemId);

        $arrayPost = [
            'admission_date' => $_POST['admission_date'],
            'demission_date' => $_POST['demission_date'],
            'experience_date' => $_POST['experience_date'],
            'last_vacation_date' => $_POST['last_vacation_date'],
            'creci' => $_POST['creci'],
            'salary' => Util::unmaskMoney($_POST['salary']),
            'percentage_commission' => $_POST['percentage_commission'],
            'user_observation' => $_POST['user_observation'],
        ];

        try {
            $this->db->beginTransaction();

            $response = (new Customer)->update($arrayPost, 'id', $item->id_customer);

            $this->db->commit();
            return $response;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Ocorreu um erro ao editar o usuário. Consulte o administrador do sistema.'];
        }
    }

    public function getAndFilterAllUsers($filters, $options = []): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND u.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(u.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != "") {
            $filtersQuery .= " AND ub.id_branch = :idbranch";
            $parameters[':idbranch'] = $filters['id_branch'];
        }

        if (isset($filters['id_branch_and_profile']) && $filters['id_branch_and_profile'] != "") {
            $filtersQuery .= " AND (ub.id_branch = :idbranch OR up.access <= 5) AND up.access > 1";
            $parameters[':idbranch'] = $filters['id_branch_and_profile'];
        }

        if (isset($filters['id_profile']) && $filters['id_profile'] != "") {
            $filtersQuery .= " AND up.id >= 1 AND up.id <= :id_profile";
            $parameters[':id_profile'] = $filters['id_profile'];
        }

        if (isset($filters['start_access']) && $filters['start_access'] != "") {
            $filtersQuery .= " AND up.access >= :start_access";
            $parameters[':start_access'] = $filters['start_access'];
        }

        if (isset($filters['id_seller_manager']) && $filters['id_seller_manager'] != "") {
            $filtersQuery .= " AND ( u.created_by = :id_seller_manager OR mst.id_manager = :id_seller_manager) ";
            $parameters[':id_seller_manager'] = $filters['id_seller_manager'];
        }

        $sql = "SELECT
                    u.*,
                    up.name AS profile_name,
                    up.access,
                    bra.name as branch,
                    GROUP_CONCAT(bra.name SEPARATOR ' / ') AS concatenated_branches
                FROM users u
                INNER JOIN users_profiles up ON up.id = u.id_profile
                LEFT JOIN user_branches ub ON ub.id_user = u.id
                LEFT JOIN branch bra ON bra.id = ub.id_branch
                LEFT JOIN manager_team mst ON mst.id_seller = u.created_by
                WHERE TRUE $filtersQuery
                GROUP BY u.id
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " up.access, u.name ASC";

        $sqlRows = $sql;

        if (isset($options['limit'])) {
            $offset = ($options['page'] - 1) * $options['limit'];
            $sql .= " LIMIT {$options['limit']} OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        $queryRows = $this->db->prepare($sqlRows);
        $queryRows->execute($parameters);

        return (object)['data' => $query->fetchAll(), 'count' => $queryRows->rowCount()];
    }

    public function getAllNetworksById($userId)
    {
        $sql = "SELECT
                    n.*, ncd.name, ncd.filename
                FROM user_networks n
                INNER JOIN network_card_digital ncd on ncd.id = n.id_networks_card_digital
                WHERE n.id_user = {$userId}
                ORDER BY n.item_order ASC";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getAllUsersProfilesBellow($access)
    {
        $sql = "SELECT
                    up.name, up.access, up.id
                FROM users_profiles up
                WHERE TRUE
                AND up.access >= :access
                AND up.access != 1
                AND up.status = 1
                ORDER BY up.access";

        $query = $this->db->prepare($sql);
        $parameters = array(':access' => $access);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllSellersFromManagerId($managerId, $branchId)
    {
        $sql = "SELECT
                    users.*,
                    up.name AS profile_name, up.access,
                    bra.name as branch,
                    GROUP_CONCAT(bra.name SEPARATOR ' / ') AS concatenated_branches,
                    manager_team.id_manager
                FROM manager_team
                INNER JOIN users ON users.id = manager_team.id_seller
                INNER JOIN users_profiles up ON up.id = users.id_profile
                LEFT JOIN user_branches ub ON ub.id_user = users.id
                LEFT JOIN branch bra ON bra.id = ub.id_branch
                WHERE TRUE
                AND manager_team.id_manager = :managerId
                AND ub.id_branch = :branchId
                GROUP BY users.id";

        $query = $this->db->prepare($sql);
        $parameters = [':managerId' => $managerId, ':branchId' => $branchId];
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getUserById($id)
    {
        $sql = "SELECT
                    u.*,
                    up.name AS profile_name, up.access,
                    bra.name as branch_name
                FROM users u
                INNER JOIN users_profiles up ON up.id = u.id_profile
                LEFT JOIN branch bra ON bra.id = u.id_branch
                WHERE u.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getUserForContractById($userId)
    {
        $sql = "SELECT
                    u.name AS nome, u.cpf, u.creci, u.email, u.phone AS celular
                FROM users u
                WHERE u.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $userId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getUserByEmail($email)
    {
        $sql = "SELECT
                    u.*,
                    up.name AS profile_name, up.access,
                    bra.name AS branch_name
                FROM users u
                INNER JOIN users_profiles up ON up.id = u.id_profile
                LEFT JOIN branch bra ON bra.id = u.id_branch
                WHERE ucase(u.email) LIKE (:email)
                AND u.status = 1";

        $query = $this->db->prepare($sql);
        $parameters = array(":email" => strtoupper($email));
        $query->execute($parameters);

        return $query->fetch();
    }

    public function checkSession()
    {
        if (!isset($_SESSION['RR']->user->id)) {
            header("location:" . URL . "login");
            exit;
        }
    }
};
