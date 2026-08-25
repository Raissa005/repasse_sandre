<script>
    const url = "<?= URL ?>";
    var id_menu = "<?= (isset($menu) ? $menu->id : '') ?>";
    const userSession = JSON.parse('<?= json_encode($_SESSION['RR']) ?>');
</script>
<?= $this->renderScript() ?>
<footer class="main-footer">
    <strong>Copyright &copy; 2020-<?= date('Y') ?> <a href="#"><?= $this->system_config->footer; ?></a>.</strong> Todos os direitos reservados. <small> <?= isset($showItems) ? (!empty($showItems->total) ? " Exibindo " . $showItems->min . "-" . $showItems->max . " de " . $showItems->total . " resultados " : " Sem resultados") : "Carregado em aproximadamente" ?></small><small> (<?= number_format(microtime(true) - start_time_D46, 3) ?> segundos)</small>
</footer>
</div>
</body>

</html>