<?php if (isset($this->menu) || isset($this->displayErrors)) { ?>
<script type="text/javascript">
    Vps.Debug.displayErrors = <?= $this->displayErrors ? 'true' : 'false' ?>;
    Vps.Debug.showMenu = <?= $this->menu ? 'true' : 'false' ?>;
    Vps.Debug.js = <?= $this->js ? 'true' : 'false' ?>;
    Vps.Debug.css = <?= $this->css ? 'true' : 'false' ?>;
    Vps.Debug.querylog = <?= $this->querylog ? 'true' : 'false' ?>;
    Vps.Debug.autoClearCache = <?= $this->autoClearCache ? 'true' : 'false' ?>;
    Vps.Debug.requestNum = '<?= $this->requestNum ?>';
</script>
<?php } ?>