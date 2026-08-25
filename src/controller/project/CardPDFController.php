<?php

namespace RR\controller\project;

use Dompdf\Dompdf;
use RR\libs\Util;
use RR\model\DigitalCard;
use RR\model\ModelGenerico;
use RR\model\User;
use RR\model\NetworksSite;

class CardPDFController
{
    public function index($userId)
    {
        require_once APP . 'libs/dompdf/autoload.inc.php';

        $userModel = new User();
        $modelGenerico = new ModelGenerico();

        $system = $modelGenerico->getItemById8161(1, "system_config");
        $config = $modelGenerico->getItemById8161(1, "configuracao");

        $user = $userModel->getUserById($userId);
        $networks = $userModel->getAllNetworksById($userId);
        $nameCardDigital = "cartao-digital-" . Util::slugify($user->name);

        $configDigitalCard = (new DigitalCard())->getItemById8161($user->card_digital);
        $networkCompany = (new NetworksSite())->getAndFilterAllNetworks(0, ['status' => true, 'order' => " rs.ordem ASC"], 0)->data;
        $userImage = "../public/img/users/$user->id/$user->id-dc-$user->card_digital_cont.$user->card_digital_ext";

        $backgroundImage = "../public/img/card_digital/$configDigitalCard->id/fundo-$configDigitalCard->cont_fundo.$configDigitalCard->ext_fundo";
        $footerImage = "../public/img/card_digital/$configDigitalCard->id/logo-$configDigitalCard->cont_logo.$configDigitalCard->ext_logo";

        $dompdf = new Dompdf();
        ob_start();
        require_once(APP . 'view/cardPDF/index.php');
        $data = ob_get_clean();
        $dompdf->load_html($data);
        $dompdf->set_paper(array(0, 0, 1080, 1920));

        $dompdf->render();
        $dompdf->stream("{$nameCardDigital}.pdf", ["Attachment" => false]);
    }
}
