<?php if (isset($this->menu) || isset($this->displayErrors)) { ?>
<script type="text/javascript">
    Kwf.Debug.displayErrors = <?= $this->displayErrors ? 'true' : 'false' ?>;
    Kwf.Debug.showMenu = <?= $this->menu ? 'true' : 'false' ?>;
    Kwf.Debug.js = <?= $this->js ? 'true' : 'false' ?>;
    Kwf.Debug.css = <?= $this->css ? 'true' : 'false' ?>;
    Kwf.Debug.querylog = <?= $this->querylog ? 'true' : 'false' ?>;
    Kwf.Debug.autoClearCache = <?= $this->autoClearCache ? 'true' : 'false' ?>;
    Kwf.Debug.requestNum = '<?= $this->requestNum ?>';
</script>
<?php } ?>