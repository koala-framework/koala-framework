<div class="kwfUp-content kwfUp-loginError">
    <h1><?=trlKwf('Login Error')?></h1>
    <p>
        <?=htmlspecialchars($this->errorMessage)?>
    </p>
    <?php if ($this->redirect) { ?>
        <p><a href="<?=htmlspecialchars($this->redirect)?>"><?=trlKwf('Continue')?></a></p>
    <?php } ?>
</div>
