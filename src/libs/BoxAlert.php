<?php

namespace RR\libs;

class BoxAlert
{

    public function successAlert($message)
    {
        echo    '<div id="box-alert" class="box-alert">
                    <div class="alert alert-success alert-dismissible" style="margin-bottom: 10px">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> Sucesso!</h4>
                        ' . $message . '.
                    </div>
                </div>';
    }

    public function errorAlert($message)
    {
        echo    '<div id="box-alert" class="box-alert">
                    <div class="alert alert-danger alert-dismissible" style="margin-bottom: 10px">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-times"></i> Erro!</h4>
                        ' . $message . '.
                    </div>
                </div>';
    }

    public function warningAlert($message)
    {
        echo    '<div id="box-alert" class="box-alert">
                    <div class="alert alert-warning alert-dismissible" style="margin-bottom: 10px">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-exclamation-triangle"></i> Aviso!</h4>
                        ' . $message . '.
                    </div>
                </div>';
    }

    public function defaultItemAlerts()
    {
        if (isset($_GET['added']) && $_GET['added'] == 'true') {
            $this->successAlert('Item cadastrado com sucesso');
        } elseif (isset($_GET['added']) && $_GET['added'] == 'false') {
            $this->errorAlert('Não foi possível cadastrar o Item');
        } elseif (isset($_GET['edited']) && $_GET['edited'] == 'true') {
            $this->successAlert('Item alterado com sucesso');
        } elseif (isset($_GET['edited']) && $_GET['edited'] == 'false') {
            $this->errorAlert('Não foi possível editar o item');
        } elseif (isset($_GET['deleted']) && $_GET['deleted'] == 'true') {
            $this->successAlert('Item deletado com sucesso');
        } elseif (isset($_GET['deleted']) && $_GET['deleted'] == 'false') {
            $this->errorAlert('Error ao deletar esse item');
        } elseif (isset($_GET['disabled']) && $_GET['disabled'] == 'true') {
            $this->successAlert('Item desativado com sucesso');
        } elseif (isset($_GET['enabled']) && $_GET['enabled'] == 'true') {
            $this->successAlert('Item ativado com sucesso');
        } elseif (isset($_GET['exists']) && $_GET['exists'] == 'true') {
            $this->warningAlert('Não foi possível editar, este item já está cadastrado');
        } elseif (isset($_GET['authorization']) && $_GET['authorization'] == 'false') {
            $this->errorAlert('Você não tem autorização alterar e visualizar esse Item');
        } elseif (isset($_GET['error']) && $_GET['error'] == 'error') {
            $this->errorAlert('Ops! Algo de errado aconteceu');
        }
    }
}
