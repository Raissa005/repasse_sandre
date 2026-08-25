<?php

?>
<style>
    .a-entrar,
    .a-entrar:focus,
    .a-entrar:active {
        color: <?php echo $configStyle->topo_botao_cor; ?> !important;
    }

    .a-entrar:hover {
        color: <?php echo $configStyle->topo_botao_cor_efeito; ?> !important;
    }

    .a-menu,
    .a-menu:focus,
    .a-menu:active {
        color: <?php echo $configStyle->menu_cor_fonte; ?> !important;
    }

    .a-menu:hover {
        color: <?php echo $configStyle->menu_cor_fonte_efeito; ?> !important;
    }

    .a-menu-active {
        color: <?php echo $configStyle->menu_cor_fonte_efeito; ?> !important;
    }

    .mob-menu-active {
        color: <?php echo $configStyle->menu_cor_fonte_efeito; ?> !important;
    }

    .menu-btn a:hover {
        color: <?php echo $configStyle->menu_cor_fonte_efeito; ?> !important;
    }

    /*banner bullets*/
    .owl-controls .owl-page.active span {
        background: <?php echo $configStyle->banner_cor_fundo; ?> !important;
    }

    /*box filtros horizontais*/
    .bg-filtro {
        background: <?php echo $configStyle->filtro_cor_fundo; ?> !important;
    }

    .bx-form2 input[type="text"],
    .bx-form2 input[type="email"],
    .bx-form2 input[type="password"] {
        color: <?php echo $configStyle->filtro_campo_cor_fonte; ?> !important;
        background: <?php echo $configStyle->filtro_campo_cor_fundo; ?> !important;
    }

    .bx-form2 select {
        color: <?php echo $configStyle->filtro_campo_cor_fonte; ?> !important;
        background: <?php echo $configStyle->filtro_campo_cor_fundo; ?> !important;
    }

    .bx-form input:focus,
    .bx-form textarea:focus {
        border-color: <?php echo $configStyle->filtro_botao_cor_fundo; ?> !important
    }

    .bx-form2 input:focus,
    .bx-form2 textarea:focus {
        border-color: <?php echo $configStyle->filtro_botao_cor_fundo; ?> !important
    }

    .range-slider input[type="text"] {
        background: transparent !important;
    }

    input[type="range"]:focus::-webkit-slider-runnable-track {
        background: <?php echo $configStyle->filtro_valor_cor_fundo; ?>;
    }

    input[type="range"]:focus::-ms-fill-lower {
        background: <?php echo $configStyle->filtro_valor_cor_fundo; ?>;
    }

    input[type="range"]:focus::-ms-fill-upper {
        background: <?php echo $configStyle->filtro_valor_cor_fundo; ?>;
    }

    input[type="range"]::-webkit-slider-runnable-track {
        background: <?php echo $configStyle->filtro_valor_cor_fundo; ?>;
    }

    input[type="range"]::-webkit-slider-thumb {
        border: 1px solid <?php echo $configStyle->filtro_valor_cor_fundo; ?>;
        background: <?php echo $configStyle->filtro_valor_cor_fundo; ?>;
    }

    input[type="range"]::-moz-range-track {
        background: <?php echo $configStyle->filtro_valor_cor_fundo; ?>;
    }

    input[type="range"]::-moz-range-thumb {
        border: 1px solid <?php echo $configStyle->filtro_valor_cor_fundo; ?>;
        background: <?php echo $configStyle->filtro_valor_cor_fundo; ?>;
    }

    input[type="range"]::-ms-fill-lower,
    input[type="range"]::-ms-fill-upper {
        background: <?php echo $configStyle->filtro_valor_cor_fundo; ?>;
    }

    .a-busca-avancada,
    .a-busca-avancada:focus,
    .a-busca-avancada:active {
        color: <?php echo $configStyle->filtro_botao_avancado_cor_fonte; ?> !important;
    }

    .a-busca-avancada:hover {
        color: <?php echo $configStyle->filtro_botao_avancado_cor_fonte_efeito; ?> !important;
    }

    .a-and-obra {
        color: <?php echo $configStyle->filtro_and_cor_fonte; ?> !important;
    }

    .a-and-obra:hover {
        color: <?php echo $configStyle->filtro_and_cor_fonte_efeito; ?> !important;
    }

    .a-and-obra-active {
        color: <?php echo $configStyle->filtro_and_cor_fonte_efeito; ?> !important;
    }

    /*empresa*/
    .bg-h-empresa {
        background: <?php echo $configStyle->empresa_home_cor_fundo; ?> !important;
    }

    .bg-h-empresa .main-title {
        color: <?php echo $configStyle->empresa_home_titulo_cor_fonte; ?> !important;
    }

    .bg-h-empresa .main-title p {
        color: <?php echo $configStyle->empresa_home_apoio_titulo_cor_fonte; ?> !important;
    }

    .box-texto-max-height {
        color: <?php echo $configStyle->empresa_home_texto_cor_fonte; ?> !important;
    }

    /*contato*/
    .bg-h-contato {
        background: <?php echo $configStyle->contato_home_cor_fundo; ?> !important;
    }

    .bg-h-contato .main-title {
        color: <?php echo $configStyle->contato_home_titulo_cor_fonte; ?> !important;
    }

    .bg-h-contato .main-title p {
        color: <?php echo $configStyle->contato_home_apoio_titulo_cor_fonte; ?> !important;
    }

    .bg-h-contato .box-horarios {
        background: <?php echo $configStyle->contato_home_box_cor_fundo; ?> !important;
        box-shadow: 0 0 10px rgb(<?php echo $configStyle->contato_home_box_cor_sombra; ?> / 10%);
    }

    .bg-h-contato .box-horarios .h3-editado {
        color: <?php echo $configStyle->contato_home_box_cor_fonte; ?> !important;
    }

    /*rodape*/
    .bg-rodape {
        color: <?php echo $configStyle->rodape_cor_fonte; ?> !important;
        background: <?php echo $configStyle->rodape_cor_fundo; ?> !important;
    }

    .a-rod,
    .a-rod:focus,
    .a-rod:active {
        color: <?php echo $configStyle->rodape_cor_fonte; ?> !important;
    }

    .a-rod:hover {
        color: <?php echo $configStyle->rodape_cor_fonte_efeito; ?> !important;
    }

    .a-rod-active {
        color: <?php echo $configStyle->rodape_cor_fonte_efeito; ?> !important;
    }

    /*direito autoral*/
    .bg-direito {
        color: <?php echo $configStyle->direito_cor_fonte; ?> !important;
        background: <?php echo $configStyle->direito_cor_fundo; ?> !important;
    }

    .a-ydeal,
    .a-ydeal:focus,
    .a-ydeal:active {
        color: <?php echo $configStyle->direito_cor_fonte; ?> !important;
    }

    .a-ydeal:hover {
        color: <?php echo $configStyle->direito_cor_fonte_efeito; ?> !important;
    }

    /*imóvel*/
    .bg-h-imovel {
        background: <?php echo $configStyle->imovel_cor_fundo; ?> !important;
    }

    .bg-h-imovel .main-title p {
        color: <?php echo $configStyle->imovel_apoio_titulo_cor_fonte; ?> !important;
    }

    .bg-h-imovel .main-title h1 {
        color: <?php echo $configStyle->imovel_titulo_cor_fonte; ?> !important;
    }

    .a-imovel,
    .a-imovel:focus,
    .a-imovel:active {
        color: <?php echo $configStyle->imovel_box_cor_fonte; ?> !important;
        background: <?php echo $configStyle->imovel_box_cor_fundo; ?> !important;
    }

    .box-imovel-info h1 {
        color: <?php echo $configStyle->imovel_box_titulo_cor_fonte; ?> !important;
    }

    .box-imovel-img .tag,
    .box-imovel-img .tag-cod {
        color: <?php echo $configStyle->imovel_box_tag_cor_fonte; ?> !important;
        background: <?php echo $configStyle->imovel_box_tag_cor_fundo; ?> !important;
    }

    .box-imovel-img .tag-valor,
    .box-imovel-img .tag-cod {
        color: <?php echo $configStyle->imovel_box_tag_cor_fonte; ?> !important;
        background: <?php echo $configStyle->imovel_box_tag_cor_fundo; ?> !important;
    }

    .box-imovel-img .destaque {
        color: <?php echo $configStyle->imovel_box_tag_cor_fonte; ?> !important;
        background: <?php echo $configStyle->imovel_box_tag_cor_fundo; ?> !important;
    }

    .tag-relative,
    .big-tag-valor,
    .big-tag-cod {
        color: <?php echo $configStyle->imovel_box_tag_cor_fonte; ?> !important;
        background: <?php echo $configStyle->imovel_box_tag_cor_fundo; ?> !important;
        transition: all 0.3s ease-in-out;
    }

    .tag-relative:hover {
        color: <?php echo $configStyle->filtro_botao_cor_fonte_efeito; ?> !important;
        background: <?php echo $configStyle->filtro_botao_cor_fundo_efeito; ?> !important;
    }

    .a-compartilhar,
    .a-compartilhar:focus,
    .a-compartilhar:active {
        color: <?php echo $configStyle->imovel_box_tag_cor_fonte; ?>;
        background: <?php echo $configStyle->imovel_box_tag_cor_fundo; ?>;
    }

    .box-detalhe-imovel {
        color: <?php echo $configStyle->imovel_box_detalhe_cor_fonte; ?> !important;
        background: <?php echo $configStyle->imovel_box_detalhe_cor_fundo; ?> !important;
    }

    .box-galeria-img .owl-banner3 .item {
        background: <?php echo $configStyle->box_galeria_cor_fundo; ?> !important;
    }

    /*central de atendimento*/
    .bg-central-atendimento {
        color: <?php echo $configStyle->central_atendimento_cor_fonte; ?> !important;
        background: <?php echo $configStyle->central_atendimento_cor_fundo; ?> !important;
    }

    .a-whats-central,
    .a-whats-central:focus,
    .a-whats-central:active {
        color: <?php echo $configStyle->central_atendimento_cor_fonte; ?> !important;
    }

    .a-whats-central:hover {
        color: <?php echo $configStyle->central_atendimento_cor_fonte_efeito; ?> !important;
    }

    /*praia dos sonhos*/
    .bg-praia-sonho {
        background: <?php echo $configStyle->praia_sonho_cor_fundo; ?> !important;
    }

    .bg-praia-sonho .main-title h1 {
        color: <?php echo $configStyle->praia_sonho_cor_fonte; ?> !important;
    }

    .bg-praia-sonho .main-title p {
        color: <?php echo $configStyle->praia_sonho_cor_fonte_efeito; ?> !important;
    }

    .box-praia-img {
        background: <?php echo $configStyle->praia_sonho_cor_fundo; ?> !important;
    }

    .a-praia h1 {
        color: <?php echo $configStyle->praia_sonho_cor_fonte_efeito; ?> !important;
    }

    .a-praia:hover h1 {
        color: <?php echo $configStyle->praia_sonho_cor_fonte; ?> !important;
    }

    .box-capa-radius img {
        border: solid 4px <?php echo $configStyle->praia_sonho_cor_fundo; ?> !important;
    }

    /*outras paginas*/
    .pages {
        color: <?php echo $configStyle->pagina_cor_fonte; ?> !important;
        background: <?php echo $configStyle->pagina_cor_fundo; ?> !important;
    }

    .pages .main-title p {
        color: <?php echo $configStyle->pagina_apoio_titulo_cor; ?> !important;
    }

    .pages .main-title h1 {
        color: <?php echo $configStyle->pagina_titulo_cor; ?> !important;
    }

    /*filtro lateral*/
    .a-filtro-lateral,
    .a-filtro-lateral:focus,
    .a-filtro-lateral:active {
        color: <?php echo $configStyle->filtro_lateral_cor_fonte; ?> !important;
    }

    .a-filtro-lateral:hover {
        color: <?php echo $configStyle->filtro_lateral_cor_fonte_efeito; ?> !important;
    }

    .a-filtro-lateral-active {
        color: <?php echo $configStyle->filtro_lateral_cor_fonte_efeito; ?> !important;
    }

    /*botoes 1*/
    .btn-padrao,
    .btn-padrao:focus,
    .btn-padrao:active {
        border-color: <?php echo $configStyle->imovel_mais_imoveis_cor_fonte; ?> !important;
        color: <?php echo $configStyle->imovel_mais_imoveis_cor_fonte; ?> !important;
        background: <?php echo $configStyle->imovel_mais_imoveis_cor_fundo; ?> !important;
    }

    .btn-padrao:hover {
        border-color: <?php echo $configStyle->imovel_mais_imoveis_cor_fundo_efeito; ?> !important;
        color: <?php echo $configStyle->imovel_mais_imoveis_cor_fonte_efeito; ?> !important;
        background: <?php echo $configStyle->imovel_mais_imoveis_cor_fundo_efeito; ?> !important;
    }

    .a-imovel:hover .btn-padrao2 {
        color: <?php echo $configStyle->imovel_box_botao_cor_fonte_efeito; ?> !important;
        background: <?php echo $configStyle->imovel_box_botao_cor_fundo_efeito; ?> !important;
    }

    /*botoes 2*/
    .btn-padrao2,
    .btn-padrao2:focus,
    .btn-padrao2:active {
        color: <?php echo $configStyle->filtro_botao_cor_fonte; ?> !important;
        background: <?php echo $configStyle->filtro_botao_cor_fundo; ?> !important;
    }

    .btn-padrao2:hover {
        color: <?php echo $configStyle->filtro_botao_cor_fonte_efeito; ?> !important;
        background: <?php echo $configStyle->filtro_botao_cor_fundo_efeito; ?> !important;
    }

    .vou-me-matar {
        font-size: 200px;
        padding: 200px;
        background-color: #000000;
    }
</style>