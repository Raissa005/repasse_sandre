<!-- <div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Texto</h3>
                    </div> -->
<form role="form" action="<?= URL . $this->route . "/handleSubmitEditText/$textId" ?>" enctype="multipart/form-data" method="POST">
    <div class="box-body">
        <div class="row">
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="nome">Nome <span class="" style="color: red;">*</span></label>
                    <input type="text" autocomplete="off" class="form-control" name="nome" id="nome" value="<?= $text->nome ?>" required>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="ativo">Exibir galeria (Padrão)</label>
                    <select class="form-control" name="galeria" id="ativo">
                        <option value="1" <?= $text->galeria == "1" ? "selected" : "" ?>>Ativado</option>
                        <option value="0" <?= $text->galeria == "0" ? "selected" : "" ?>>Desativado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12 col-lg-12">
                <div class="form-group">
                    <label for="descricao">Descrição <span class="" style="color: red;">*</span></label>
                    <textarea id="box-ckeditor" name="descricao"><?= $text->descricao ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
        <div class="pull-right">
            <button type="submit" class="btn btn-block btn-primary">Salvar</button>
        </div>
    </div>
</form>
<!-- </div>
            </div>
        </div>
    </section>
</div> -->

</div>
</section>
</div>

<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Deseja realmente excluir a Imagem?
            </div>
            <form method="POST" class="form-disable-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-danger" name="disable">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>
