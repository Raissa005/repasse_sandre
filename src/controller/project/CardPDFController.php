<?php

namespace RR\controller\project;

use Dompdf\Dompdf;
use Dompdf\Options;
use RR\libs\Util;
use RR\libs\Toast;
use RR\libs\Secure;
use RR\model\DigitalCard;
use RR\model\ModelGenerico;
use RR\model\User;
use RR\model\NetworksSite;

use function RR\Controller\redirect;

class CardPDFController extends FrontController
{
    public $route;

    public function __construct()
    {
        $this->route = 'card-pdf';
        parent::__construct($this->route);
    }

    public function index($userId = null)
    {
        // N17: cada usuário gera só o próprio cartão; Superadm/Administrador/Desenvolvedor geram o de qualquer um
        if (!ctype_digit((string) $userId)) redirect('home');
        $userId = (int) $userId;

        if ($userId !== (int) $_SESSION['RR']->user->id && !Secure::access_admin()) {
            Toast::unauthorized();
            redirect('home');
        }

        $userModel = new User();
        $modelGenerico = new ModelGenerico();

        $user = $userModel->getUserById($userId);
        if (!$user) {
            Toast::warningToast('Usuário não encontrado.');
            redirect('home');
        }

        $configDigitalCard = (new DigitalCard())->getItemById8161($user->card_digital);
        if (!$configDigitalCard) {
            Toast::warningToast('Escolha um modelo de cartão antes de gerar o Cartão Digital.');
            redirect("users/digital-card/$userId");
        }

        $config = $modelGenerico->getItemById8161(1, "configuracao");
        $networks = $userModel->getAllNetworksById($userId);
        $nameCardDigital = "cartao-digital-" . Util::slugify($user->name);
        $networkCompany = (new NetworksSite())->getAndFilterAllNetworks(0, ['status' => true, 'order' => " rs.ordem ASC"], 0)->data;

        // caminhos absolutos dentro de public/ (chroot do dompdf)
        $publicDir = ROOT . 'public/';
        $userImage = $publicDir . "img/users/$user->id/$user->id-dc-$user->card_digital_cont.$user->card_digital_ext";
        $backgroundImage = $publicDir . "img/card_digital/$configDigitalCard->id/fundo-$configDigitalCard->cont_fundo.$configDigitalCard->ext_fundo";
        $footerImage = $publicDir . "img/card_digital/$configDigitalCard->id/logo-$configDigitalCard->cont_logo.$configDigitalCard->ext_logo";

        // N18: cores e tamanhos entram no <style> do PDF; só aceita cor hexadecimal e número
        foreach (['cor_fundo', 'cor_borda_usuario', 'cor_fonte_usuario', 'cor_fonte_ocupacao', 'cor_fonte_legenda', 'cor_borda_icone', 'cor_fundo_logo'] as $column) {
            $configDigitalCard->$column = preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $configDigitalCard->$column) ? $configDigitalCard->$column : 'transparent';
        }
        foreach (['tamanho_fonte_usuario', 'tamanho_fonte_ocupacao', 'tamanho_fonte_legenda'] as $column) {
            $configDigitalCard->$column = (int) $configDigitalCard->$column;
        }

        // fontes do cartão (@font-face) são instaladas no fontDir: storage/ do projeto (fora de public/), gravável pelo Apache
        $fontDir = ROOT . 'storage/dompdf-fonts';
        if (!is_dir($fontDir)) mkdir($fontDir, 0775, true);

        $options = new Options();
        $options->setChroot([$publicDir]);
        $options->setIsRemoteEnabled(false);
        $options->setIsPhpEnabled(false);
        $options->setFontDir($fontDir);
        $options->setFontCache($fontDir);

        $dompdf = new Dompdf($options);
        ob_start();
        require_once(APP . 'view/cardPDF/index.php');
        $data = ob_get_clean();
        $dompdf->loadHtml($data);
        $dompdf->setPaper([0, 0, 1080, 1920]);

        $dompdf->render();
        $dompdf->stream("{$nameCardDigital}.pdf", ["Attachment" => false]);
    }
}
