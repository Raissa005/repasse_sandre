<?php

namespace RR\controller\project;

use RR\libs\Email;
use RR\model\User;
use RR\libs\Secure;
use RR\libs\Toast;
use RR\model\Branch;
use RR\model\LoginAttempt;
use RR\libs\JWTWrapper;
use RR\libs\MoreMailer;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\SystemSettings;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use PDOException;

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

        $pendingToast = $_SESSION['RR']->toast ?? null;
        $_SESSION['RR'] = (object)[];
        if ($pendingToast) {
            $_SESSION['RR']->toast = $pendingToast;
        }

        if (!isset($_SESSION['RR']->user->id)) {
            require APP . 'view/login/index.php';
        } else {
            header('location: ' . URL . 'home/index');
            exit;
        }
    }

    public function signIn()
    {
        // A4: bloqueio por e-mail digitado (exista ou não) — vale mesmo com a senha certa
        $now = time();
        $attemptEmail = LoginAttempt::normalizeEmail($_POST['email'] ?? '');
        $loginAttempt = new LoginAttempt();
        $attempt = $loginAttempt->getByEmail($attemptEmail);

        if (LoginAttempt::isLocked($attempt, $now)) {
            Toast::errorToast(LoginAttempt::LOCKED_MESSAGE);
            header('location:' . URL . 'login');
            exit;
        }

        $userVerified = (new User())->getUserByEmail($_POST['email']);

        if ($userVerified && password_verify($_POST['password'], $userVerified->password)) {
            if (!empty($attempt)) $loginAttempt->clear($attemptEmail);

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
            $state = LoginAttempt::nextFailure($attempt, $now);
            $loginAttempt->registerFailure($attemptEmail, $state, $now);

            Toast::errorToast(!empty($state->locked_until) ? LoginAttempt::LOCKED_MESSAGE : 'E-mail ou senha invalido!');

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
            Toast::warningToast('Token inválido!');
            header('location: ' . URL . 'login/index');
            exit;
        }

        $jwt = $modelGenerico->getItemByGenericField($token, "tokens", "id_unique", 1);

        if (empty($jwt)) {
            Toast::errorToast('Link inválido ou não encontrado!');
            header('location: ' . URL . 'login/index');
            exit;
        }

        $decodedToken = self::decodeRecoveryToken($jwt[0]->jwt);

        if ($decodedToken->expiration < time()) {
            Toast::warningToast('O tempo para redefinir a senha expirou!');
            header('location: ' . URL . 'login/index');
            exit;
        }

        require APP . 'view/login/changePassword.php';
    }

    /**
     * M4: token de recuperação ilegível (adulterado, ou assinado com uma JWT_KEY anterior) é tratado como link inválido,
     * em vez de erro fatal.
     */
    private static function decodeRecoveryToken(string $jwt)
    {
        try {
            return JWTWrapper::decode($jwt);
        } catch (\UnexpectedValueException | \DomainException | \InvalidArgumentException $error) {
            Toast::errorToast('Link inválido ou não encontrado!');
            header('location: ' . URL . 'login/index');
            exit;
        }
    }

    public function handleSubmitChangePassWord($token)
    {
        Secure::check_post_method($this->route . "login/changePassWord?token=$token");

        $token = !empty($token) ? $token : false;

        if (!$token) {
            Toast::warningToast('Token inválido!');
            header('location: ' . URL . 'login/index');
            exit;
        }

        $jwt = (new ModelGenerico())->getItemByGenericField($token, "tokens", "id_unique", 1);

        if (empty($jwt)) {
            Toast::errorToast('Link inválido ou não encontrado!');
            header('location: ' . URL . 'login/index');
            exit;
        }

        $decodedToken = self::decodeRecoveryToken($jwt[0]->jwt);

        // M4: o prazo também vale no envio, não só ao abrir a página
        if ($decodedToken->expiration < time()) {
            Toast::warningToast('O tempo para redefinir a senha expirou!');
            header('location: ' . URL . 'login/index');
            exit;
        }

        if ($passwordError = User::passwordPolicyError($_POST['password'] ?? '')) {
            Toast::warningToast($passwordError);
            header('location: ' . URL . 'login/changePassWord?token=' . $token);
            exit;
        }

        if ($_POST["password"] == $_POST["confirm_password"]) {
            $arrPost = array('password' =>  password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 12)));

            try {
                (new GerenciaPost())->update8191($arrPost, "users", "id", $decodedToken->userData->id, false);
                (new GerenciaPost())->update8191(["status" => 0], "tokens", "id", $jwt[0]->id, false);

                // A4: concluir a recuperação zera o contador de tentativas desse e-mail
                (new LoginAttempt())->clear(LoginAttempt::normalizeEmail($jwt[0]->email));

                Toast::successToast('Senha alterada com successo!');
                header('location: ' . URL . 'login/index');
                exit;
            } catch (PDOException $error) {
                Toast::genericError();
                header('location: ' . URL . 'login/index');
                exit;
            }
        }

        Toast::warningToast('Senhas diferentes!');
        header('location: ' . URL . 'login/changePassWord?token=' . $token);
        exit;
    }

    public function sendRecoverPasswordMail()
    {
        $user = (new User())->getUserByEmail($_POST['email']);

        // A4: mesma resposta exista ou não o e-mail, para não revelar quais e-mails têm conta
        $neutralMessage = 'Se o e-mail estiver cadastrado, você receberá o link de recuperação.';

        if (!$user) {
            Toast::successToast($neutralMessage);
            header('location: ' . URL . 'login/index');
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

        Toast::successToast($neutralMessage);
        header('location:' . URL . 'login/index');
        exit;
    }
}
