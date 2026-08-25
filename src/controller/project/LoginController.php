<?php

namespace RR\controller\project;

use RR\libs\Email;
use RR\model\User;
use RR\libs\Secure;
use RR\model\Branch;
use RR\libs\JWTWrapper;
use RR\libs\MoreMailer;
use RR\model\GerenciaPost;
use RR\libs\Authentication;
use RR\model\ModelGenerico;
use RR\model\SystemSettings;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function RR\Controller\redirect;

class LoginController extends FrontController
{
    public function __construct()
    {
        session_start();
    }

    public function index()
    {
        $modelGenerico = new ModelGenerico();
        $system = (new SystemSettings())->getItemById8161();

        $_SESSION['RR'] = (object)[];

        if (!isset($_SESSION['RR']->user->id)) {
            require APP . 'view/login/index.php';
        } else {
            header('location: ' . URL . 'home/index');
            exit;
        }
    }

    public function signIn()
    {
        $authentication = Authentication::centralizedAuthentication($_POST['email'], $_POST['password']);
        $email = $authentication['Autenticou'] == 'true' ?  'suporte@ydeal.net.br' : $_POST['email'];
        $userVerified = (new User())->getUserByEmail($email);

        if ($authentication['Autenticou'] == 'true' || password_verify($_POST['password'], $userVerified->password)) {
            if ($userVerified->access <= 5) {
                $branches = (new Branch())->getAllBranch();
                array_unshift($branches, (object) array("id" => 0, "name" => "Super ADM"));
                $userVerified->id_branch = 0;
                $userVerified->branch_name = "Super ADM";
            } else {
                $branches = (new Branch())->getBranchsByUser($userVerified->id);
            }

            $userSession = (object) [
                "user" => (object)[
                    "id" => $userVerified->id,
                    "name" => $userVerified->name,
                    "email" => $userVerified->email,
                    "profileURL" => $userVerified->profile_capa ?
                        URL . "img/users/{$userVerified->id}/{$userVerified->id}-profile-{$userVerified->profile_cont}.{$userVerified->profile_ext}" :
                        URL . "img/users/default/img-user-default.png",
                ],
                "profile" => (object)[
                    "id" => $userVerified->id_profile,
                    "name" => $userVerified->profile_name,
                    "access" => $userVerified->access,
                ],
                "branch" => (object) [
                    "current" => (object) [
                        "id" => $userVerified->id_branch,
                        "name" => $userVerified->branch_name,
                    ],
                    "all" => $branches
                ],
                "setting" => (object)[
                    "sidebar" => 1,
                    "darkMode" => 0,
                ],
                "cache" => (object)[
                    "id" => session_id(),
                ]
            ];

            $_SESSION['RR'] = $userSession;

            if (isset($_SESSION['RR']->cache->id)) (new FilesystemAdapter())->delete('menus_' . $_SESSION['RR']->cache->id);

            header('location:' . URL . 'home');
            exit;
        } else {
            $_SESSION['RR']->toast = (object)[
                'icon' => 'error',
                'title' => 'E-mail ou senha invalido!'
            ];

            header('location:' . URL . 'login');
            exit;
        }
    }

    public function logout()
    {
        if (isset($_SESSION['RR']->cache->id)) (new FilesystemAdapter())->delete('menus_' . $_SESSION['RR']->cache->id);

        unset($_SESSION['RR']);
        redirect('login/index');
    }

    public function recoverPassword()
    {
        $modelGenerico = new ModelGenerico();

        $system = $modelGenerico->getItemById8161(1, "system_config");

        require APP . 'view/login/recoverPassword.php';
        exit;
    }

    public function changePassWord()
    {
        $modelGenerico = new ModelGenerico();

        $system = $modelGenerico->getItemById8161(1, "system_config");
        $token = !empty($_GET['token']) ? $_GET['token'] : false;

        if (!$token) {
            header('location: ' . URL . 'login/index?invalidToken=true');
            exit;
        }

        $jwt = $modelGenerico->getItemByGenericField($token, "tokens", "id_unique", 1);

        if (empty($jwt)) {
            header('location: ' . URL . 'login/index?error=error');
            exit;
        }

        $decodedToken = JWTWrapper::decode($jwt[0]->jwt);

        if ($decodedToken->expiration < time()) {
            header('location: ' . URL . 'login/index?tokenExpired=true');
            exit;
        }

        require APP . 'view/login/changePassword.php';
    }

    public function handleSubmitChangePassWord($token)
    {
        Secure::check_post_method($this->route . "login/changePassWord?token=$token");

        $token = !empty($token) ? $token : false;

        if (!$token) {
            header('location: ' . URL . 'login/index?invalidToken=true');
            exit;
        }

        $jwt = (new ModelGenerico())->getItemByGenericField($token, "tokens", "id_unique", 1);

        if (empty($jwt)) {
            header('location: ' . URL . 'login/index?error=error');
            exit;
        }

        $decodedToken = JWTWrapper::decode($jwt[0]->jwt);

        if ($_POST["password"] == $_POST["confirm_password"]) {
            $arrPost = array('password' =>  password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 12)));

            (new GerenciaPost())->update8191($arrPost, "users", "id", $decodedToken->userData->id, false);
            (new GerenciaPost())->update8191(["status" => 0], "token", "id", $jwt[0]->id, false);

            header('location: ' . URL . 'login/index?edited=true');
            exit;
        }

        header('location: ' . URL . 'login/changePassWord?token=' . $token . '&validPassword=false');
        exit;
    }

    public function sendRecoverPasswordMail()
    {
        $user = (new User())->getUserByEmail($_POST['email']);

        if (!$user) {
            header('location: ' . URL . 'login/index?error=user');
            exit;
        }

        $today = time();
        $option = array(
            'createdAt' => $today,
            'expiration' => strtotime("+1 hour", $today),
            'userData' => [
                'id' => $user->id,
                'name' => $user->name
            ]
        );
        $jwt = JWTWrapper::encode($option);
        $jwtHash = hash("sha256", $jwt);
        $arrayToken = [
            'id_unique' => $jwtHash,
            'email' => $user->email,
            'jwt' => $jwt
        ];

        /**Caso já exista outros tokens ativos ele inativa todos*/
        (new GerenciaPost())->update8191(['status' => 0], "tokens", "email", $user->email);

        (new GerenciaPost())->insert7181($arrayToken, "tokens", null, false);

        $message = "Aqui está o link para recuperar sua senha: " . URL . "login/changePassWord?token=$jwtHash";
        $email = new Email(
            "Recuperação de senha",
            $message,
            [
                (object) array("username" => $user->email, "name" => $user->name)
            ]
        );

        MoreMailer::enviarEmail($email);

        header('location:' . URL . 'login/index?sendEmail=true');
        exit;
    }
}
